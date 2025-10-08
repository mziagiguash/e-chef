{{-- resources/views/frontend/cart.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ localeRoute('home') }}" class="fs-6 text-secondary">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Shopping Cart</li>
                </ol>
            </nav>

            <h1 class="h2 mb-4">Shopping Cart</h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    @php
                        $cart = session('cart', []);
                        $cartItems = count($cart);
                        $hasFreeCourses = false;
                        $hasPaidCourses = false;

                        // Проверяем типы курсов в корзине
                        foreach($cart as $id => $details) {
                            if(($details['price'] ?? 0) == 0) {
                                $hasFreeCourses = true;
                            } else {
                                $hasPaidCourses = true;
                            }
                        }
                    @endphp

                    @if($cartItems > 0)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Selected Courses ({{ $cartItems }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($cart as $id => $details)
                                        @php
                                            $isFreeCourse = ($details['price'] ?? 0) == 0;
                                        @endphp
                                        <div class="list-group-item {{ $isFreeCourse ? 'bg-light' : '' }}">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    @if(isset($details['thumbnail']) || isset($details['image']))
                                                        <img src="{{ asset('uploads/courses/' . ($details['thumbnail'] ?? $details['image'] ?? 'default.jpg')) }}"
                                                             alt="{{ $details['title'] }}"
                                                             class="img-fluid rounded"
                                                             style="width: 80px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                             style="height: 60px; width: 80px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1">{{ $details['title'] ?? $details['title_en'] ?? 'Unknown Course' }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        Instructor: {{ $details['instructor'] ?? 'Unknown' }}
                                                    </p>
                                                    @if($isFreeCourse)
                                                        <span class="badge bg-success">Free Course</span>
                                                    @else
                                                        @if(isset($details['old_price']) && $details['old_price'] > $details['price'])
                                                            <p class="mb-0">
                                                                <small><del>${{ number_format($details['old_price'], 2) }}</del></small>
                                                            </p>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    @if($isFreeCourse)
                                                        <strong class="text-success">FREE</strong>
                                                    @else
                                                        <strong class="text-primary">${{ number_format($details['price'], 2) }}</strong>
                                                        @if(isset($details['old_price']) && $details['old_price'] > $details['price'])
                                                            <br>
                                                            <small class="text-success">
                                                                Save ${{ number_format($details['old_price'] - $details['price'], 2) }}
                                                            </small>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    {{-- Кнопка "Добавить в мои курсы" для бесплатных курсов --}}
                                                    @if($isFreeCourse)
                                                        <div class="mb-2">
                                                            <form action="{{ localeRoute('courses.enroll.free') }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="course_id" value="{{ $id }}">
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="fas fa-user-graduate me-1"></i> Add to My Courses
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-info-circle me-1"></i>Free - add directly
                                                        </small>
                                                    @endif

                                                    <form action="{{ localeRoute('remove.from.cart') }}" method="POST" class="d-inline mt-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="id" value="{{ $id }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="Remove from cart" onclick="return confirm('Are you sure you want to remove this course from your cart?')">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Уведомление о бесплатных курсах --}}
                        @if($hasFreeCourses)
                        <div class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Free Courses Available</h6>
                                    <p class="mb-0">You can add free courses directly to "My Courses" without going through checkout.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="card border-0 shadow-sm text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="mb-3">Your cart is empty</h5>
                                <p class="text-muted mb-4">Add some courses to get started</p>
                                <a href="{{ localeRoute('frontend.courses') }}" class="btn btn-primary">
                                    <i class="fas fa-book me-2"></i>Browse Courses
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                @if($cartItems > 0)
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $subtotal = 0;
                                $freeCoursesCount = 0;
                                $paidCoursesCount = 0;

                                foreach($cart as $id => $details) {
                                    if(($details['price'] ?? 0) == 0) {
                                        $freeCoursesCount++;
                                    } else {
                                        $subtotal += $details['price'];
                                        $paidCoursesCount++;
                                    }
                                }

                                $discount = session('coupon_discount', 0);
                                $totalAfterDiscount = max(0, $subtotal - $discount);
                                $tax = $totalAfterDiscount * 0.15; // 15% tax
                                $finalTotal = $totalAfterDiscount + $tax;

                                // Сохраняем детали корзины в сессии для checkout
                                session(['cart_details' => [
                                    'subtotal' => $subtotal,
                                    'discount' => $discount,
                                    'tax' => $tax,
                                    'total_amount' => $finalTotal,
                                    'items_count' => $paidCoursesCount,
                                    'free_courses_count' => $freeCoursesCount
                                ]]);
                            @endphp

                            {{-- Информация о курсах --}}
                            <div class="mb-3">
                                @if($freeCoursesCount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Free Courses:</span>
                                    <strong>{{ $freeCoursesCount }}</strong>
                                </div>
                                @endif

                                @if($paidCoursesCount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Paid Courses:</span>
                                    <strong>{{ $paidCoursesCount }}</strong>
                                </div>
                                @endif
                            </div>

                            {{-- USD Prices (только для платных курсов) --}}
                            @if($paidCoursesCount > 0)
                            <div class="mb-3 border-top pt-3">
                                <h6 class="text-primary mb-2">USD (US Dollar)</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal ({{ $paidCoursesCount }} paid courses):</span>
                                    <strong>${{ number_format($subtotal, 2) }}</strong>
                                </div>

                                @if($discount > 0)
                                <div class="d-flex justify-content-between mb-1 text-success">
                                    <span>Discount ({{ session('coupon_code') }}):</span>
                                    <strong>-${{ number_format($discount, 2) }}</strong>
                                </div>
                                @endif

                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tax (15%):</span>
                                    <strong>${{ number_format($tax, 2) }}</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-3 pt-2 border-top">
                                    <span class="fw-bold">Total (USD):</span>
                                    <strong class="h5 text-primary">${{ number_format($finalTotal, 2) }}</strong>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-success text-center">
                                <i class="fas fa-gift fa-2x mb-2"></i>
                                <h6 class="mb-1">All courses are free!</h6>
                                <p class="mb-0">Add them directly to your courses.</p>
                            </div>
                            @endif

                        {{-- Coupon Form (только для платных курсов) --}}
                        @if($paidCoursesCount > 0 && !session('coupon_code'))
                        <form action="{{ localeRoute('coupon.check') }}" method="POST" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control"
                                       placeholder="Enter coupon code"
                                       value="{{ old('coupon_code') }}" required>
                                <button type="submit" class="btn btn-outline-primary">
                                    Apply
                                </button>
                            </div>
                        </form>
                        @elseif(session('coupon_code') && $paidCoursesCount > 0)
                        <div class="alert alert-success mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Coupon Applied:</strong> {{ session('coupon_code') }}
                                    <br>
                                    <small>Discount: ${{ number_format($discount, 2) }}</small>
                                </div>
                                <form action="{{ localeRoute('coupon.remove') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif

{{-- Checkout Button (только если есть платные курсы) --}}
<div class="d-grid gap-2">
    @if($paidCoursesCount > 0)
        <a href="{{ localeRoute('checkout') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout ({{ $paidCoursesCount }})
        </a>
    @endif

    {{-- Кнопка для добавления всех бесплатных курсов --}}
    @if($freeCoursesCount > 0)
        <form action="{{ localeRoute('courses.enroll.free.all') }}" method="POST" class="d-grid">
            @csrf
            @foreach($cart as $id => $details)
                @if(($details['price'] ?? 0) == 0)
                    <input type="hidden" name="course_ids[]" value="{{ $id }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-user-graduate me-2"></i>Add All Free Courses ({{ $freeCoursesCount }})
            </button>
        </form>
    @endif

    <a href="{{ localeRoute('frontend.courses') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
    </a>
</div>

                    {{-- Course Benefits --}}
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-shield-alt me-2 text-primary"></i>What You Get</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Lifetime access to courses</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Certificate of completion</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>30-day money-back guarantee</small>
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Access on mobile and TV</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Подтверждение удаления курса из корзины
    document.querySelectorAll('form[action*="remove.from.cart"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to remove this course from your cart?')) {
                e.preventDefault();
            }
        });
    });

    // Подтверждение добавления всех бесплатных курсов
    document.querySelectorAll('form[action*="enroll.free.all"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const freeCoursesCount = this.querySelectorAll('input[name="course_ids[]"]').length;
            if (!confirm(`Are you sure you want to add all ${freeCoursesCount} free courses to your account?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
