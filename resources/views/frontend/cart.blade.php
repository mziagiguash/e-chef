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
                    @endphp

                    @if($cartItems > 0)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Selected Courses ({{ $cartItems }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($cart as $id => $details)
                                        <div class="list-group-item">
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
                                                    @if(isset($details['old_price']) && $details['old_price'] > $details['price'])
                                                        <p class="mb-0">
                                                            <small><del>${{ number_format($details['old_price'], 2) }}</del></small>
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <strong class="text-primary">${{ number_format($details['price'], 2) }}</strong>
                                                    @if(isset($details['old_price']) && $details['old_price'] > $details['price'])
                                                        <br>
                                                        <small class="text-success">
                                                            Save ${{ number_format($details['old_price'] - $details['price'], 2) }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <form action="{{ localeRoute('remove.from.cart') }}" method="POST" class="d-inline">
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
                                foreach($cart as $id => $details) {
                                    $subtotal += $details['price']; // Только цена, без quantity
                                }
                                $discount = session('coupon_discount', 0);
                                $totalAfterDiscount = max(0, $subtotal - $discount);
                                $tax = $totalAfterDiscount * 0.15; // 15% tax
                                $finalTotal = $totalAfterDiscount + $tax;

                                // Конвертация в GEL (примерный курс: 1 USD = 2.7 GEL)
                                $exchangeRate = 2.7;
                                $subtotalGEL = $subtotal * $exchangeRate;
                                $discountGEL = $discount * $exchangeRate;
                                $taxGEL = $tax * $exchangeRate;
                                $finalTotalGEL = $finalTotal * $exchangeRate;

                                // Сохраняем детали корзины в сессии для checkout
                                session(['cart_details' => [
                                    'subtotal' => $subtotal,
                                    'discount' => $discount,
                                    'tax' => $tax,
                                    'total_amount' => $finalTotal,
                                    'items_count' => $cartItems
                                ]]);
                            @endphp

                            {{-- USD Prices --}}
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">USD (US Dollar)</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal ({{ $cartItems }} courses):</span>
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

                            {{-- GEL Prices (опционально) --}}
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="text-secondary mb-2">GEL (Georgian Lari)</h6>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>Subtotal:</span>
                                    <span>₾{{ number_format($subtotalGEL, 2) }}</span>
                                </div>

                                @if($discount > 0)
                                <div class="d-flex justify-content-between mb-1 small text-success">
                                    <span>Discount:</span>
                                    <span>-₾{{ number_format($discountGEL, 2) }}</span>
                                </div>
                                @endif

                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>Tax (15%):</span>
                                    <span>₾{{ number_format($taxGEL, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between pt-2 border-top small">
                                    <span class="fw-bold">Total (GEL):</span>
                                    <strong>₾{{ number_format($finalTotalGEL, 2) }}</strong>
                                </div>
                                <small class="text-muted d-block mt-1">Exchange rate: 1 USD = {{ $exchangeRate }} GEL</small>
                            </div>

                        {{-- Coupon Form --}}
@if(!session('coupon_code'))
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
@else
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

{{-- Checkout Button --}}
<div class="d-grid gap-2">
    <a href="{{ localeRoute('checkout') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
    </a>
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
});
</script>
@endpush
