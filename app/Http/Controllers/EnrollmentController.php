<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course', 'payment'])
            ->latest()
            ->get();

        return view('backend.enrollment.index', compact('enrollments'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'payment']);

        return view('backend.enrollment.show', compact('enrollment'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        try {
            DB::beginTransaction();

            // Удаляем связанный платеж
            if ($enrollment->payment) {
                $enrollment->payment->delete();
            }

            $enrollment->delete();

            DB::commit();

            return redirect()->route('enrollment.index')
                ->with('success', 'Enrollment deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded'
        ]);

        try {
            $enrollment->update([
                'payment_status' => $request->payment_status,
                'payment_date' => $request->payment_status === 'completed' ? now() : null
            ]);

            if ($enrollment->payment) {
                $enrollment->payment->update([
                    'status' => $this->mapPaymentStatus($request->payment_status)
                ]);
            }

            return redirect()->back()
                ->with('success', 'Payment status updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating payment status: ' . $e->getMessage());
        }
    }

    /**
     * Map payment status to integer for payments table
     */
    private function mapPaymentStatus($status)
    {
        $statusMap = [
            'pending' => 0,
            'completed' => 1,
            'failed' => 2,
            'refunded' => 3
        ];

        return $statusMap[$status] ?? 0;
    }

    /**
 * Get enrollment statistics
 */
public function statistics()
{
    // Общая статистика
    $totalEnrollments = Enrollment::count();
    $totalRevenue = Enrollment::where('payment_status', 'completed')->sum('amount_paid');
    $freeEnrollments = Enrollment::where('amount_paid', 0)->orWhereNull('amount_paid')->count();
    $paidEnrollments = Enrollment::where('amount_paid', '>', 0)->count();

    // Статистика по статусам платежей
    $paymentStatusStats = Enrollment::selectRaw('payment_status, COUNT(*) as count')
        ->groupBy('payment_status')
        ->get()
        ->pluck('count', 'payment_status');

    // Статистика по методам платежей
    $paymentMethodStats = Enrollment::selectRaw('payment_method, COUNT(*) as count')
        ->whereNotNull('payment_method')
        ->groupBy('payment_method')
        ->get()
        ->pluck('count', 'payment_method');

    // Топ курсов по зачислениям
    $popularCourses = Course::withCount('enrollments')
        ->orderBy('enrollments_count', 'desc')
        ->take(10)
        ->get();

    // Статистика по месяцам (последние 12 месяцев)
    $monthlyStats = Enrollment::selectRaw('
            YEAR(enrollment_date) as year,
            MONTH(enrollment_date) as month,
            COUNT(*) as enrollments_count,
            SUM(amount_paid) as revenue
        ')
        ->where('enrollment_date', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    // Подготовка данных для графика
    $chartLabels = [];
    $chartEnrollments = [];
    $chartRevenue = [];

    foreach ($monthlyStats->reverse() as $stat) {
        $monthName = date('M Y', mktime(0, 0, 0, $stat->month, 1, $stat->year));
        $chartLabels[] = $monthName;
        $chartEnrollments[] = $stat->enrollments_count;
        $chartRevenue[] = floatval($stat->revenue ?? 0);
    }

    return view('backend.enrollment.statistics', compact(
        'totalEnrollments',
        'totalRevenue',
        'freeEnrollments',
        'paidEnrollments',
        'paymentStatusStats',
        'paymentMethodStats',
        'popularCourses',
        'monthlyStats',
        'chartLabels',
        'chartEnrollments',
        'chartRevenue'
    ));
}
}
