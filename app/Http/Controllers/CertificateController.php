<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function generate(Request $request, $locale, Course $course)
    {
        \Log::info('Certificate generation request', [
            'course_id' => $course->id,
            'student_id' => $request->input('student_id'),
            'format' => $request->input('format')
        ]);

        $studentId = $request->input('student_id');
        $format = $request->input('format', 'pdf');

        if (!$studentId && session('student_id')) {
            $studentId = session('student_id');
        }

        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Проверяем завершение курса
        $completedLessons = $student->lessonProgress()
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        $totalLessons = $course->lessons()->count();

        if ($totalLessons === 0 || $completedLessons < $totalLessons) {
            return response()->json([
                'success' => false,
                'message' => 'Course not completed. Progress: ' . $completedLessons . '/' . $totalLessons
            ], 400);
        }

        try {
            $certificateData = [
                'student' => $student,
                'course' => $course,
                'completion_date' => now(),
                'certificate_id' => 'CERT-' . strtoupper(uniqid()),
                'currentTitle' => $this->getCourseTitle($course, $locale)
            ];

            $filename = "certificate_{$course->course_code}_{$student->name}_" . time();
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

            if ($format === 'pdf') {
                return $this->generatePdf($certificateData, $filename);
            } else {
                // Для изображений возвращаем информативное сообщение
                return response()->json([
                    'success' => false,
                    'message' => 'PNG/JPG certificates are not available. Please download PDF format.'
                ], 501);
            }

        } catch (\Exception $e) {
            \Log::error('Certificate generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating certificate: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCourseTitle($course, $locale)
    {
        $translation = $course->translations->where('locale', $locale)->first();
        return $translation->title ?? $course->translations->first()->title ?? $course->title ?? 'No Title';
    }

private function generatePdf($data, $filename)
{
    try {
        $pdf = PDF::loadView('certificates.template', $data)
                 ->setPaper('a4', 'portrait')
                 ->setOptions([
                     'dpi' => 96,
                     'defaultFont' => 'dejavu sans',
                     'isHtml5ParserEnabled' => true,
                     'isRemoteEnabled' => true,
                     'isPhpEnabled' => false,
                     'chroot' => realpath(base_path()),
                     // Критически важные настройки
                     'margin_top' => 0,
                     'margin_right' => 0,
                     'margin_bottom' => 0,
                     'margin_left' => 0,
                     'viewportSize' => ['width' => 794, 'height' => 1123],
                     'enable_javascript' => false,
                     'enable_smart_shrinking' => false,
                     'debugCss' => false,
                     'debugLayout' => false,
                 ]);

        $pdfContent = $pdf->output();

        return response()->json([
            'success' => true,
            'download_url' => "data:application/pdf;base64," . base64_encode($pdfContent),
            'filename' => $filename . '.pdf'
        ]);

    } catch (\Exception $e) {
        \Log::error('PDF generation failed', ['error' => $e->getMessage()]);
        return $this->generateCompactPdf($data, $filename);
    }
}
}
