<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Используем простые запросы без scope
        $stats = [
            'total' => Enrollment::count(),
            'completed' => Enrollment::where('payment_status', 'completed')->count(),
            'pending' => Enrollment::where('payment_status', 'pending')->count(),
            'failed' => Enrollment::where('payment_status', 'failed')->count(),
            'revenue' => Enrollment::where('payment_status', 'completed')->sum('amount_paid')
        ];

        return view('backend.enrollment.statistics', compact('stats'));
    }
}
