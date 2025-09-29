<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // 🔴 ИСПРАВЛЕНО: Проверяем сессию studentLogin вместо Auth::check()
        if (!session('studentLogin')) {
            return redirect()->route('studentLogin', ['locale' => app()->getLocale()])->with('error', 'Please login to proceed with checkout');
        }

        $cart = session('cart', []);
        $cartDetails = session('cart_details', []);

        if (empty($cart)) {
            return redirect()->route('cart', ['locale' => app()->getLocale()])->with('error', 'Your cart is empty');
        }

        // Используем данные из сессии, которые уже рассчитаны в CartController
        $subtotal = $cartDetails['subtotal'] ?? 0;
        $discount = $cartDetails['discount'] ?? 0;
        $tax = $cartDetails['tax'] ?? 0;
        $total = $cartDetails['total_amount'] ?? 0;

        return view('frontend.checkout', compact('cart', 'subtotal', 'discount', 'tax', 'total'));
    }

    public function processPayment(Request $request)
    {
        try {
            // 🔴 ИСПРАВЛЕНО: Проверяем сессию studentLogin вместо Auth::check()
            if (!session('studentLogin')) {
                return redirect()->route('studentLogin', ['locale' => app()->getLocale()])->with('error', 'Please login to proceed with payment');
            }

            $cart = session('cart', []);
            $cartDetails = session('cart_details', []);

            if (empty($cart)) {
                return redirect()->back()->with('error', 'Your cart is empty');
            }

            // Валидация данных
            $request->validate([
                'payment_method' => 'required|in:sslcommerz,card,paypal,bank',
            ]);

            // 🔴 ИСПРАВЛЕНО: Получаем ID студента из сессии
            $studentId = session('student_id');

            // Используем рассчитанные ранее суммы
            $subtotal = $cartDetails['subtotal'] ?? 0;
            $discount = $cartDetails['discount'] ?? 0;
            $tax = $cartDetails['tax'] ?? 0;
            $totalAmount = $cartDetails['total_amount'] ?? 0;

            // Создаем запись checkout
            $checkout = Checkout::create([
                'user_id' => $studentId, // 🔴 ИСПРАВЛЕНО: Используем student_id из сессии
                'total_amount' => $totalAmount,
                'currency' => 'USD',
                'payment_method' => $request->payment_method,
                'billing_address' => [
                    'name' => session('student_name', 'Student'),
                    'email' => session('student_email', 'student@example.com')
                ],
                'cart_data' => $cart,
                'status' => 'pending'
            ]);

            // Обрабатываем платеж в зависимости от метода
            switch ($request->payment_method) {
                case 'sslcommerz':
                    return $this->processSSLCommerzPayment($checkout);
                case 'card':
                    return $this->processCardPayment($checkout, $request);
                case 'paypal':
                    return $this->processPaypalPayment($checkout, $request);
                case 'bank':
                    return $this->processBankTransfer($checkout, $request);
                default:
                    throw new Exception('Invalid payment method');
            }

        } catch (Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    private function processSSLCommerzPayment($checkout)
    {
        try {
            // 🔴 ИСПРАВЛЕНО: Получаем данные студента из сессии
            $studentId = session('student_id');

            // Создаем запись о платеже
            $payment = Payment::create([
                'checkout_id' => $checkout->id,
                'user_id' => $studentId,
                'amount' => $checkout->total_amount,
                'currency' => 'USD',
                'payment_method' => 'sslcommerz',
                'payment_status' => 'pending',
                'transaction_id' => 'ssl_' . uniqid(),
                'payer_email' => session('student_email', 'student@example.com'),
                'payer_name' => session('student_name', 'Student'),
            ]);

            // В реальной реализации здесь будет вызов API SSLCommerz
            // Пока просто редиректим на страницу успеха для тестирования
            return $this->completePayment($payment, $checkout);

        } catch (Exception $e) {
            \Log::error('SSLCommerz payment error: ' . $e->getMessage());

            // Создаем запись о неудачном платеже
            Payment::create([
                'checkout_id' => $checkout->id,
                'user_id' => session('student_id'),
                'amount' => $checkout->total_amount,
                'currency' => 'USD',
                'payment_method' => 'sslcommerz',
                'payment_status' => 'failed',
                'payer_email' => session('student_email', 'student@example.com'),
                'payer_name' => session('student_name', 'Student'),
            ]);

            $checkout->update(['status' => 'failed']);

            return redirect()->route('payment.cancel', ['locale' => app()->getLocale()])
                ->with('error', 'SSLCommerz payment failed: ' . $e->getMessage());
        }
    }

    private function processCardPayment($checkout, $request)
    {
        try {
            $studentId = session('student_id');

            // Здесь будет код интеграции с платежным шлюзом (Stripe и т.д.)
            // Пока имитируем успешный платеж для тестирования

            $payment = Payment::create([
                'checkout_id' => $checkout->id,
                'user_id' => $studentId,
                'amount' => $checkout->total_amount,
                'currency' => 'USD',
                'payment_method' => 'card',
                'payment_status' => 'completed',
                'transaction_id' => 'card_' . uniqid(),
                'payer_email' => $request->payer_email ?? session('student_email', 'student@example.com'),
                'payer_name' => $request->payer_name ?? session('student_name', 'Student'),
            ]);

            return $this->completePayment($payment, $checkout);

        } catch (Exception $e) {
            \Log::error('Card payment error: ' . $e->getMessage());

            Payment::create([
                'checkout_id' => $checkout->id,
                'user_id' => session('student_id'),
                'amount' => $checkout->total_amount,
                'currency' => 'USD',
                'payment_method' => 'card',
                'payment_status' => 'failed',
                'payer_email' => $request->payer_email ?? session('student_email', 'student@example.com'),
                'payer_name' => $request->payer_name ?? session('student_name', 'Student'),
            ]);

            return redirect()->route('payment.cancel', ['locale' => app()->getLocale()])
                ->with('error', 'Card payment failed: ' . $e->getMessage());
        }
    }

    // ... остальные методы processPaypalPayment, processBankTransfer остаются аналогичными

    private function completePayment($payment, $checkout)
    {
        try {
            // Обновляем статус платежа и заказа
            $payment->update(['payment_status' => 'completed']);
            $checkout->update(['status' => 'completed']);

            // Очищаем сессии
            session()->forget('cart');
            session()->forget('cart_details');
            session()->forget('coupon_code');
            session()->forget('coupon_discount');

            // 🔴 ИСПРАВЛЕНО: Передаем student_id в метод назначения курсов
            $this->assignCoursesToUser($checkout, session('student_id'));

            return redirect()->route('payment.success', ['locale' => app()->getLocale(), 'payment' => $payment->id])
                ->with('success', 'Payment completed successfully!');

        } catch (Exception $e) {
            \Log::error('Payment completion error: ' . $e->getMessage());
            throw new Exception('Payment completion failed: ' . $e->getMessage());
        }
    }

    // 🔴 ИСПРАВЛЕНО: Добавляем параметр $studentId
    private function assignCoursesToUser($checkout, $studentId)
    {
        try {
            // 🔴 ИСПРАВЛЕНО: Получаем студента по ID из сессии
            // Предполагая, что у вас есть модель Student
            // Если нет, адаптируйте под вашу структуру
            $student = \App\Models\Student::find($studentId);

            if (!$student) {
                \Log::error('Student not found: ' . $studentId);
                return;
            }

            $cart = $checkout->cart_data;

            foreach ($cart as $courseId => $item) {
                $course = Course::find($courseId);
                if ($course) {
                    // Проверяем, есть ли у студента уже этот курс
                    // Адаптируйте эту логику под вашу структуру
                    if (!$student->courses()->where('course_id', $courseId)->exists()) {
                        $student->courses()->attach($courseId, [
                            'purchased_at' => now(),
                            'payment_id' => $checkout->payments()->first()->id,
                            'checkout_id' => $checkout->id
                        ]);
                    }
                }
            }

            \Log::info('Courses assigned to student: ' . $studentId);

        } catch (Exception $e) {
            \Log::error('Course assignment error: ' . $e->getMessage());
            // Не прерываем процесс из-за ошибки назначения курсов
        }
    }

    public function paymentSuccess($paymentId)
    {
        try {
            $payment = Payment::with(['checkout', 'user'])->findOrFail($paymentId);

            // 🔴 ИСПРАВЛЕНО: Проверяем, что платеж принадлежит текущему студенту
            if ($payment->user_id !== session('student_id')) {
                return redirect()->route('home', ['locale' => app()->getLocale()])->with('error', 'Access denied');
            }

            return view('frontend.payment.success', compact('payment'));

        } catch (Exception $e) {
            \Log::error('Payment success page error: ' . $e->getMessage());
            return redirect()->route('home', ['locale' => app()->getLocale()])->with('error', 'Payment not found');
        }
    }

    public function paymentCancel()
    {
        return view('frontend.payment.cancel')->with('error', 'Payment was cancelled.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $cartData = session('cart') ?? [];

            if (empty($cartData)) {
                return redirect()->back()->with('error', 'Cart is empty');
            }

            $checkout = new Checkout;
            $checkout->user_id = Auth::id();
            $checkout->cart_data = $cartData;
            $checkout->payer_name = $request->payer_name;
            $checkout->payment_option = $request->payment_option;
            $checkout->status = $request->status ?? 'pending';
            $checkout->total_amount = 0; // Будет рассчитано позже

            if ($checkout->save()) {
                return redirect()->route('instructor.index', ['locale' => app()->getLocale()])->with('success', 'Checkout created successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Please try again');
            }
        } catch (Exception $e) {
            \Log::error('Checkout store error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating checkout');
        }
    }

    /**
     * Confirm SSLCommerz payment (callback от платежной системы)
     */
    public function confirmSSLCommerzPayment(Request $request)
    {
        try {
            // Здесь будет логика обработки callback от SSLCommerz
            $transactionId = $request->tran_id;
            $status = $request->status;

            // Находим платеж по transaction_id
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                \Log::error('SSLCommerz payment not found: ' . $transactionId);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            if ($status === 'VALID') {
                // Успешный платеж
                $payment->update(['payment_status' => 'completed']);
                $payment->checkout->update(['status' => 'completed']);

                // Очищаем сессии
                session()->forget('cart');
                session()->forget('cart_details');
                session()->forget('coupon_code');
                session()->forget('coupon_discount');

                $this->assignCoursesToUser($payment->checkout);

                return response()->json(['success' => true]);
            } else {
                // Неуспешный платеж
                $payment->update(['payment_status' => 'failed']);
                $payment->checkout->update(['status' => 'failed']);
                return response()->json(['error' => 'Payment failed'], 400);
            }

        } catch (Exception $e) {
            \Log::error('SSLCommerz confirmation error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
