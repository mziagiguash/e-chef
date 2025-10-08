<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $cartItems = [];

        foreach ($cart as $id => $details) {
            $course = Course::find($id);
            if ($course) {
                // Для курсов quantity всегда = 1
                $itemTotal = $course->price;

                $cartItems[$id] = [
                    'title' => $course->title,
                    'title_en' => $course->title_en ?? $course->title,
                    'price' => $course->price,
                    'old_price' => $course->old_price ?? null,
                    'thumbnail' => $course->thumbnail_image,
                    'image' => $course->thumbnail_image,
                    'instructor' => $course->instructor->name ?? 'Unknown Instructor',
                    'quantity' => 1 // Всегда 1 для курсов
                ];
                $subtotal += $itemTotal;
            }
        }

        // Получаем данные о скидке из сессии
        $discount = session()->get('coupon_discount', 0);
        $coupon_code = session()->get('coupon_code');

        // Рассчитываем налог и итого (15% налог)
        $tax = $subtotal * 0.15;
        $total_amount = $subtotal - $discount + $tax;

        // Сохраняем детали корзины в сессии для checkout
        session()->put('cart_details', [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total_amount' => $total_amount,
            'items_count' => count($cart)
        ]);

        return view('frontend.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total_amount' => $total_amount,
            'coupon_code' => $coupon_code,
            'currencySymbol' => '$'
        ]);
    }

    // Методы для бесплатных курсов
      public function enrollFreeCourse(Request $request)
    {
        try {
            $student_id = currentUserId();
            $course_id = $request->course_id;

            // Проверяем, существует ли курс
            $course = Course::find($course_id);
            if (!$course) {
                return back()->with('error', 'Course not found.');
            }

            // 🔴 ИСПРАВЛЕНО: Используем правильное название колонки
            if ($course->coursePrice > 0) {
                return back()->with('error', 'This course is not free.');
            }

            // Проверяем, не записан ли студент уже на курс
            $existingEnrollment = Enrollment::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->first();

            if ($existingEnrollment) {
                return back()->with('info', 'You are already enrolled in this course.');
            }

            // Создаем запись о зачислении
            $enrollment = Enrollment::create([
                'student_id' => $student_id,
                'course_id' => $course_id,
                'amount_paid' => 0,
                'payment_status' => Enrollment::PAYMENT_COMPLETED,
                'payment_method' => 'free',
                'enrollment_date' => now(),
                'payment_date' => now(),
            ]);

            // Удаляем курс из корзины
            $cart = session('cart', []);
            if (isset($cart[$course_id])) {
                unset($cart[$course_id]);
                session(['cart' => $cart]);
            }

            return back()->with('success', 'Successfully enrolled in the free course!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error enrolling in course: ' . $e->getMessage());
        }
    }
public function enrollAllFreeCourses(Request $request)
    {
        try {
            $student_id = currentUserId();
            $course_ids = $request->course_ids ?? [];

            if (empty($course_ids)) {
                return back()->with('error', 'No free courses selected.');
            }

            $enrolledCount = 0;
            $cart = session('cart', []);

            foreach ($course_ids as $course_id) {
                // Проверяем, существует ли курс
                $course = Course::find($course_id);
                // 🔴 ИСПРАВЛЕНО: Используем правильное название колонки
                if (!$course || $course->coursePrice > 0) {
                    continue; // Пропускаем если курс не найден или не бесплатный
                }

                // Проверяем, не записан ли студент уже на курс
                $existingEnrollment = Enrollment::where('student_id', $student_id)
                    ->where('course_id', $course_id)
                    ->first();

                if ($existingEnrollment) {
                    continue; // Уже записан
                }

                // Создаем запись о зачислении
                Enrollment::create([
                    'student_id' => $student_id,
                    'course_id' => $course_id,
                    'amount_paid' => 0,
                    'payment_status' => Enrollment::PAYMENT_COMPLETED,
                    'payment_method' => 'free',
                    'enrollment_date' => now(),
                    'payment_date' => now(),
                ]);

                // Удаляем курс из корзины
                if (isset($cart[$course_id])) {
                    unset($cart[$course_id]);
                }

                $enrolledCount++;
            }

            // Обновляем корзину в сессии
            session(['cart' => $cart]);

            if ($enrolledCount > 0) {
                return back()->with('success', "Successfully enrolled in {$enrolledCount} free course(s)!");
            } else {
                return back()->with('info', 'No new free courses were enrolled.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error enrolling in free courses: ' . $e->getMessage());
        }
    }

    public function addToCart($locale, $id)
    {
        $course = Course::find($id);

        if(!$course) {
            return redirect()->back()->with('error', 'Course not found!');
        }

        $cart = session()->get('cart', []);

        // Если курс уже в корзине
        if(isset($cart[$id])) {
            return redirect()->back()->with('info', 'Course is already in your cart!');
        }

        // 🔴 ИСПРАВЛЕНО: Используем правильные названия колонок
        $cart[$id] = [
            "title" => $course->title,
            "title_en" => $course->title_en ?? $course->title,
            "price" => $course->coursePrice, // Исправлено с price на coursePrice
            "old_price" => $course->courseOldPrice ?? null, // Исправлено с old_price на courseOldPrice
            "thumbnail" => $course->thumbnail_image,
            "image" => $course->thumbnail_image,
            "instructor" => $course->instructor->name ?? 'Unknown Instructor',
            "quantity" => 1
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart', ['locale' => $locale])
            ->with('success', 'Course added to cart successfully!');
    }

    public function updateCart(Request $request, $locale)
    {
        // Для курсов обновление количества не требуется
        // Оставляем метод для совместимости, но всегда возвращаем успех
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_total' => $this->calculateCartTotal(session()->get('cart', []))
        ]);
    }

    public function removeFromCart(Request $request, $locale)
    {
        $cart = session()->get('cart', []);
        $courseId = $request->id;

        if(isset($cart[$courseId])) {
            unset($cart[$courseId]);
            session()->put('cart', $cart);

            // Также удаляем купон если корзина пуста
            if(empty($cart)) {
                session()->forget('coupon_code');
                session()->forget('coupon_discount');
            }

            return redirect()->route('cart', ['locale' => $locale])
                ->with('success', 'Course removed from cart successfully!');
        }

        return redirect()->route('cart', ['locale' => $locale])
            ->with('error', 'Course not found in cart!');
    }

    // Альтернативный метод remove (для разных роутов)
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $courseId = $request->id;

        if(isset($cart[$courseId])) {
            unset($cart[$courseId]);
            session()->put('cart', $cart);

            if(empty($cart)) {
                session()->forget('coupon_code');
                session()->forget('coupon_discount');
            }

            return redirect()->route('cart', ['locale' => app()->getLocale()])
                ->with('success', 'Course removed from cart successfully!');
        }

        return redirect()->route('cart', ['locale' => app()->getLocale()])
            ->with('error', 'Course not found in cart!');
    }

    // Метод для AJAX удаления
    public function ajaxRemoveFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
        $courseId = $request->id;

        if(isset($cart[$courseId])) {
            unset($cart[$courseId]);
            session()->put('cart', $cart);

            $subtotal = $this->calculateCartTotal($cart);
            $discount = session()->get('coupon_discount', 0);
            $tax = $subtotal * 0.15;
            $total_amount = $subtotal - $discount + $tax;

            // Обновляем детали корзины в сессии
            session()->put('cart_details', [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $total_amount,
                'items_count' => count($cart)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course removed successfully',
                'cart_count' => count($cart),
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'total_amount' => number_format($total_amount, 2),
                'discount' => number_format($discount, 2)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Course not found in cart'
        ], 404);
    }

    public function clearCart(Request $request)
    {
        session()->forget('cart');
        session()->forget('cart_details');
        session()->forget('coupon_code');
        session()->forget('coupon_discount');

        return redirect()->route('cart', ['locale' => app()->getLocale()])
            ->with('success', 'Cart cleared successfully!');
    }

    public function getCartCount()
    {
        $cart = session()->get('cart', []);
        return response()->json([
            'count' => count($cart)
        ]);
    }

    public function coupon_check(Request $request)
    {
        $couponCode = $request->coupon_code;

        if ($couponCode) {
            $cart = session()->get('cart', []);
            $subtotal = 0;

            foreach ($cart as $id => $details) {
                $subtotal += $details['price']; // Только цена, quantity всегда 1
            }

            $discount = $subtotal * 0.1; // 10% скидка

            session()->put('coupon_code', $couponCode);
            session()->put('coupon_discount', $discount);

            // Обновляем детали корзины с учетом скидки
            $tax = $subtotal * 0.15;
            $total_amount = $subtotal - $discount + $tax;

            session()->put('cart_details', [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $total_amount,
                'items_count' => count($cart)
            ]);

            return redirect()->back()->with('success', 'Coupon applied successfully! Discount: $' . number_format($discount, 2));
        }

        return redirect()->back()->with('error', 'Invalid coupon code!');
    }

    public function remove_coupon(Request $request)
    {
        session()->forget('coupon_code');
        session()->forget('coupon_discount');

        // Пересчитываем корзину без скидки
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateCartTotal($cart);
        $tax = $subtotal * 0.15;
        $total_amount = $subtotal + $tax;

        session()->put('cart_details', [
            'subtotal' => $subtotal,
            'discount' => 0,
            'tax' => $tax,
            'total_amount' => $total_amount,
            'items_count' => count($cart)
        ]);

        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }

    public function getCartSummary()
    {
        $cart = session()->get('cart', []);
        $cartDetails = session()->get('cart_details', []);

        return response()->json([
            'cart_count' => count($cart),
            'cart_details' => $cartDetails
        ]);
    }

    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price']; // quantity всегда 1 для курсов
        }
        return $total;
    }

    // Метод для проверки состояния корзины
    public function checkCart()
    {
        $cart = session()->get('cart', []);
        $cartDetails = session()->get('cart_details', []);

        return response()->json([
            'has_items' => !empty($cart),
            'items_count' => count($cart),
            'cart_details' => $cartDetails,
            'coupon_applied' => session()->has('coupon_code')
        ]);
    }

    // Метод для добавления нескольких курсов сразу
    public function addMultipleToCart(Request $request)
    {
        $courseIds = $request->input('course_ids', []);
        $addedCount = 0;

        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);
            if ($course && !session()->has("cart.{$courseId}")) {
                $cart = session()->get('cart', []);
                $cart[$courseId] = [
                    "title" => $course->title,
                    "title_en" => $course->title_en ?? $course->title,
                    "price" => $course->price,
                    "old_price" => $course->old_price ?? null,
                    "thumbnail" => $course->thumbnail_image,
                    "image" => $course->thumbnail_image,
                    "instructor" => $course->instructor->name ?? 'Unknown Instructor',
                    "quantity" => 1
                ];
                session()->put('cart', $cart);
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            return redirect()->route('cart', ['locale' => app()->getLocale()])
                ->with('success', "{$addedCount} courses added to cart successfully!");
        }

        return redirect()->back()->with('info', 'No new courses were added to cart.');
    }
}
