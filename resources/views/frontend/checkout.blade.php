@extends('frontend.layouts.app')
@section('title', 'Checkout')
@section('body-attr') style="background-color: #ebebf2;" @endsection

@section('content')

<!-- Breadcrumb Starts Here -->
<div class="py-0">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 align-items-center">
                <li class="breadcrumb-item">
                    <a href="{{ localeRoute('home') }}" class="fs-6 text-secondary">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <span class="fs-6 text-secondary">Checkout</span>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Checkout Area Starts Here -->
<section class="section checkout-area">
    <div class="container">
        @php
            $cart = session('cart', []);
            $cartDetails = session('cart_details', []);
            $cartItems = count($cart);
            $studentLogin = session('studentLogin');
        @endphp

        @if (!$studentLogin)
        <!-- Redirect to Login Page -->
        <div class="row justify-content-center">
            <div class="col-md-8 text-center py-5">
                <div class="empty-cart-icon mb-4">
                    <i class="fas fa-user-lock fa-4x text-muted"></i>
                </div>
                <h3 class="mb-3">Authentication Required</h3>
                <p class="text-muted mb-4">Please login or register to proceed with checkout.</p>
                <div class="d-grid gap-2 d-md-block">
                    <a href="{{ localeRoute('studentLogin') }}" class="btn btn-primary btn-lg me-md-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                    <a href="{{ localeRoute('studentSignUp') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">You will be redirected back to checkout after authentication</small>
                </div>
            </div>
        </div>
        @elseif($cartItems === 0)
        <!-- Empty Cart Message -->
        <div class="row justify-content-center">
            <div class="col-md-8 text-center py-5">
                <div class="empty-cart-icon mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                </div>
                <h3 class="mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Add some courses to your cart before proceeding to checkout.</p>
                <a href="{{ localeRoute('frontend.courses') }}" class="button button--primary">
                    <i class="fas fa-arrow-left me-2"></i>Browse Courses
                </a>
            </div>
        </div>
        @else
        <!-- Checkout Form for Logged-in Users with Items in Cart -->
        <div class="row">
            <div class="col-lg-6 checkout-area-checkout">
                <h6 class="checkout-area__label">Payment Information</h6>
                <div class="checkout-tab">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-checkout" role="tabpanel"
                            aria-labelledby="pills-checkout-tab">
                            <form action="{{localeRoute('payment.ssl.submit')}}" method="post" id="payment-form">
                                @csrf

                                <div class="mb-4">
                                    <div class="ps-0">
                                        <label class="text-danger"> *
                                            Please review your courses and payment details before confirming
                                        </label>
                                    </div>
                                </div>

                                <!-- Payment Method Selection -->
                                <div class="mb-4">
                                    <h6 class="mb-3">Select Payment Method</h6>
                                    <div class="payment-methods">
<div class="form-check payment-method-card mb-3">
    <input class="form-check-input" type="radio" name="payment_method"
           id="sslcommerz" value="sslcommerz" checked>
    <label class="form-check-label w-100" for="sslcommerz">
        <div class="d-flex align-items-center">
            <div class="payment-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="ms-2 flex-grow-1" style="min-width: 0;">
                <div class="d-flex align-items-center">
                    <strong class="me-2">Credit/Debit Card</strong>
                    <div class="payment-logos">
                        <i class="fab fa-cc-visa fa-xs me-1" title="Visa"></i>
                        <i class="fab fa-cc-mastercard fa-xs me-1" title="Mastercard"></i>
                        <i class="fab fa-cc-amex fa-xs" title="American Express"></i>
                    </div>
                </div>
                <p class="mb-0 small text-muted text-truncate">Pay with Visa, Mastercard, American Express</p>
            </div>
        </div>
    </label>
</div>

<div class="form-check payment-method-card mb-3">
    <input class="form-check-input" type="radio" name="payment_method"
           id="paypal" value="paypal">
    <label class="form-check-label w-100" for="paypal">
        <div class="d-flex align-items-center">
            <div class="payment-icon">
                <i class="fab fa-paypal"></i>
            </div>
            <div class="ms-2 flex-grow-1" style="min-width: 0;">
                <div class="d-flex align-items-center">
                    <strong class="me-2">PayPal</strong>
                    <div class="payment-logos">
                        <i class="fab fa-cc-paypal fa-xs" title="PayPal"></i>
                    </div>
                </div>
                <p class="mb-0 small text-muted text-truncate">Pay with your PayPal account</p>
            </div>
        </div>
    </label>
</div>
                                        <div class="form-check payment-method-card mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                   id="bank" value="bank">
                                            <label class="form-check-label w-100" for="bank">
                                                <div class="d-flex align-items-center">
                                                    <div class="payment-icon">
                                                        <i class="fas fa-university"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <strong>Bank Transfer</strong>
                                                        <p class="mb-0 small text-muted">Direct bank transfer</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Payment Form (shown when card is selected) -->
                                <div class="payment-form-section" id="card-payment-form">
                                    <h6 class="mb-3">Card Details</h6>
                                    <div class="card payment-card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="card-number" class="form-label">Card Number</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="card-number"
                                                               placeholder="1234 5678 9012 3456" maxlength="19">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-credit-card text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="expiry-date" class="form-label">Expiry Date</label>
                                                    <input type="text" class="form-control" id="expiry-date"
                                                           placeholder="MM/YY" maxlength="5">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="cvv" class="form-label">CVV</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="cvv"
                                                               placeholder="123" maxlength="4">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-question-circle text-muted"
                                                               title="3-digit code on back of card"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="cardholder-name" class="form-label">Cardholder Name</label>
                                                    <input type="text" class="form-control" id="cardholder-name"
                                                           placeholder="John Doe">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Info (shown when PayPal is selected) -->
                                <div class="payment-form-section d-none" id="paypal-payment-form">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fab fa-paypal fa-2x me-3 text-primary"></i>
                                            <div>
                                                <strong>You will be redirected to PayPal</strong>
                                                <p class="mb-0">After confirming payment, you will be securely redirected to PayPal to complete your transaction.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Info (shown when bank is selected) -->
                                <div class="payment-form-section d-none" id="bank-payment-form">
                                    <div class="alert alert-warning">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university fa-2x me-3 text-warning"></i>
                                            <div>
                                                <strong>Bank Transfer Instructions</strong>
                                                <p class="mb-2">Please transfer the amount to the following bank account:</p>
                                                <div class="bank-details">
                                                    <p class="mb-1"><strong>Bank:</strong> TBC Bank</p>
                                                    <p class="mb-1"><strong>Account:</strong> 123456789</p>
                                                    <p class="mb-1"><strong>Name:</strong> Your Company Name</p>
                                                    <p class="mb-0"><strong>Reference:</strong> Order #{{ time() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Billing Information -->
                                <div class="mb-4">
                                    <h6 class="mb-3">Billing Information</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing-first-name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="billing-first-name"
                                                   value="{{ session('student_name', '') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing-last-name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="billing-last-name" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="billing-email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="billing-email"
                                                   value="{{ session('student_email', '') }}" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="billing-address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="billing-address" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="billing-city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="billing-city" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="billing-country" class="form-label">Country</label>
                                            <select class="form-select" id="billing-country" required>
                                                <option value="">Select Country</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="United States">United States</option>
                                                <option value="United Kingdom">United Kingdom</option>
                                                <option value="Germany">Germany</option>
                                                <option value="France">France</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="billing-zip" class="form-label">ZIP Code</label>
                                            <input type="text" class="form-control" id="billing-zip" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">Order Summary</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal ({{ $cartItems }} courses):</span>
                                            <span>${{ number_format($cartDetails['subtotal'] ?? 0, 2) }}</span>
                                        </div>
                                        @if(($cartDetails['discount'] ?? 0) > 0)
                                        <div class="d-flex justify-content-between mb-2 text-success">
                                            <span>Discount:</span>
                                            <span>-${{ number_format($cartDetails['discount'] ?? 0, 2) }}</span>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax (15%):</span>
                                            <span>${{ number_format($cartDetails['tax'] ?? 0, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount:</span>
                                            <span>${{ number_format($cartDetails['total_amount'] ?? 0, 2) }}</span>
                                        </div>
                                        <small class="text-muted">Amount in US Dollars (USD)</small>
                                    </div>
                                </div>

                                <button type="submit" class="button button-lg button--primary w-100">
                                    <i class="fas fa-lock me-2"></i>Pay ${{ number_format($cartDetails['total_amount'] ?? 0, 2) }}
                                </button>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>Your payment information is secure and encrypted
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Details -->
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="checkout-area-summery">
                    <h6 class="checkout-area__label">Order Details</h6>

                    <div class="cart">
                        <div class="cart__includes-info cart__includes-info--bordertop-0">
                            <div class="productContent__wrapper">
                                @foreach ($cart as $id => $details)
                                <div class="productContent mb-3">
                                    <div class="productContent-item__img productContent-item">
                                        <img src="{{ asset('uploads/courses/' . ($details['thumbnail'] ?? $details['image'] ?? 'default.jpg')) }}"
                                            alt="{{ $details['title'] ?? 'Course' }}"
                                            style="width: 80px; height: 60px; object-fit: cover;" />
                                    </div>
                                    <div class="productContent-item__info productContent-item">
                                        <h6 class="font-para--lg">
                                            <a href="{{ localeRoute('frontend.courses.show', $id) }}" class="text-decoration-none">
                                                {{ $details['title'] ?? $details['title_en'] ?? 'Unknown Course' }}
                                            </a>
                                        </h6>
                                        <p class="mb-1">
                                            <small>by {{ $details['instructor'] ?? 'Unknown Instructor' }}</small>
                                        </p>
                                        <div class="price">
                                            <h6 class="font-para--md text-primary">
                                                @if(($details['price'] == null || $details['price'] == 0))
                                                    Free
                                                @else
                                                    ${{ number_format($details['price'], 2) }}
                                                @endif
                                            </h6>
                                            @if(isset($details['old_price']) && $details['old_price'] > 0 && $details['old_price'] > $details['price'])
                                                <p class="mb-0">
                                                    <small><del>${{ number_format($details['old_price'], 2) }}</del></small>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Order Summary in Sidebar -->
                        <div class="cart__checkout-process">
                            <ul>
                                <li class="d-flex justify-content-between">
                                    <p>Subtotal</p>
                                    <p>${{ number_format($cartDetails['subtotal'] ?? 0, 2) }}</p>
                                </li>

                                @if(($cartDetails['discount'] ?? 0) > 0)
                                <li class="d-flex justify-content-between text-success">
                                    <p>Discount</p>
                                    <p>-${{ number_format($cartDetails['discount'] ?? 0, 2) }}</p>
                                </li>
                                @endif

                                <li class="d-flex justify-content-between">
                                    <p>Taxes (15%)</p>
                                    <p>${{ number_format($cartDetails['tax'] ?? 0, 2) }}</p>
                                </li>

                                <li class="d-flex justify-content-between border-top pt-2">
                                    <p class="font-title--card fw-bold">Total:</p>
                                    <p class="font-title--card fw-bold">${{ number_format($cartDetails['total_amount'] ?? 0, 2) }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
<!-- Checkout Area Ends Here -->

@endsection

@push('styles')
<style>
.empty-cart-icon {
    opacity: 0.5;
}
.productContent {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.productContent:last-child {
    border-bottom: none;
}
.price h6 {
    color: #28a745;
    font-weight: bold;
}

/* Payment Method Styles */
.payment-method-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.payment-method-card:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

/* Стили для радио-кнопок */
.payment-method-card .form-check-input {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    margin: 0;
    cursor: pointer;
    border-radius: 50% !important;
    border: 2px solid #adb5bd;
    background-color: white;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.payment-method-card .form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.payment-method-card .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    border-color: #007bff;
}

.payment-method-card .form-check-label {
    margin-left: 30px;
    cursor: pointer;
    width: calc(100% - 30px);
}

.payment-method-card .form-check-input:checked + .form-check-label {
    color: inherit;
}

.payment-method-card.active {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.payment-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 20px;
}

.payment-icon .fab.fa-paypal {
    background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
}

.payment-logos .fab {
    font-size: 1.5rem;
    margin-left: 8px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.payment-logos .fab:hover {
    opacity: 1;
}

/* Цвета для конкретных иконок */
.fa-cc-visa { color: #1a1f71; }
.fa-cc-mastercard { color: #eb001b; }
.fa-cc-amex { color: #2e77bc; }
.fa-cc-paypal { color: #003087; }

.payment-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.payment-form-section {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.bank-details {
    background: #fff3cd;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #ffc107;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Убираем стандартное выравнивание Bootstrap для лучшего контроля */
.form-check {
    padding-left: 0;
    margin-bottom: 0;
}

</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentForms = {
        'sslcommerz': document.getElementById('card-payment-form'),
        'paypal': document.getElementById('paypal-payment-form'),
        'bank': document.getElementById('bank-payment-form')
    };

    function showPaymentForm(method) {
        // Hide all forms first
        Object.values(paymentForms).forEach(form => {
            if (form) {
                form.classList.add('d-none');
            }
        });

        // Show selected form
        if (paymentForms[method]) {
            paymentForms[method].classList.remove('d-none');
        }
    }

    function updateActiveCards() {
        // Remove active class from all cards
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.classList.remove('active');
        });

        // Add active class to selected card
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedMethod) {
            selectedMethod.closest('.payment-method-card').classList.add('active');
        }
    }

    // Add event listeners to payment method radios
    paymentMethods.forEach(radio => {
        radio.addEventListener('change', function() {
            showPaymentForm(this.value);
            updateActiveCards();
        });
    });

    // Initialize
    updateActiveCards();
    showPaymentForm(document.querySelector('input[name="payment_method"]:checked').value);

    // Format card number
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let matches = value.match(/\d{4,16}/g);
            let match = matches && matches[0] || '';
            let parts = [];

            for (let i = 0; i < match.length; i += 4) {
                parts.push(match.substring(i, i + 4));
            }

            if (parts.length) {
                e.target.value = parts.join(' ');
            } else {
                e.target.value = value;
            }
        });
    }

    // Format expiry date
    const expiryInput = document.getElementById('expiry-date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
        });
    }

    // Only allow numbers in CVV
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    console.log('Checkout page loaded');
    console.log('Cart items:', {{ $cartItems }});
    console.log('Student logged in:', {{ $studentLogin ? 'true' : 'false' }});
});
</script>
@endpush
