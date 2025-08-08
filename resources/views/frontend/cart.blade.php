@extends('frontend.layouts.app')
@section('title', 'Cart')
@section('body-attr')style="background-color: #ebebf2;"@endsection

@section('content')
<!-- Breadcrumb Starts Here -->
<div class="py-0">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center bg-transparent mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="fs-6 text-secondary">{{ __('menu.home') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <a href="{{ url('/cart') }}" class="fs-6 text-secondary">{{ __('menu.cart') }}</a>
                </li>
            </ol>
        </nav>
    </div>
</div>
<!-- Breadcrumb Ends Here -->

<!-- Cart Section Starts Here -->
<section class="section cart-area pb-0">
    <div class="container">
        @if (session('cart'))
            <div class="row">
                <div class="col-lg-8">
                    <h6 class="cart-area__label">{{ __('cart.courses_in_cart', ['count' => count(session('cart', []))]) }}</h6>
                    @php $total = 0 @endphp
                    @foreach (session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                        <div class="cart-wizard-area">
                            <div class="image">
                                <img src="{{asset('public/uploads/courses/' . $details['image'])}}" alt="course image" />
                            </div>
                            <div class="text">
                                <h6><a href="{{route('courseDetails', encryptor('encrypt', $id))}}">{{$details['title_en']}}</a></h6>
                                <p>{{ __('cart.by') }} <a href="#">{{$details['instructor']}}</a></p>
                                <div class="bottom-wizard d-flex justify-content-between align-items-center">
                                    <p>
                                        {{ $details['price'] == null || $details['price'] == 0 ? __('cart.free') : '$' . $details['price'] }}
                                        <span><del>{{ $details['old_price'] ? '$' . $details['old_price'] : '' }}</del></span>
                                    </p>
                                    <div class="trash-icon">
                                        <a href="#" class="remove-from-cart" data-id="{{$id}}">
                                            <i class="far fa-trash-alt remove-from-cart"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-4">
                    <h6 class="cart-area__label">{{ __('cart.summary') }}</h6>
                    <div class="summery-wizard">
                        <div class="summery-wizard-text pt-0">
                            <h6>{{ __('cart.subtotal') }}</h6>
                            <p>{{ $currencySymbol . number_format((float) session('cart_details')['cart_total'], 2) }}</p>
                        </div>
                        <div class="summery-wizard-text">
                            <h6>{{ __('cart.coupon_discount', ['percent' => session('cart_details')['discount'] ?? 0.00]) }}</h6>
                            <p>{{ $currencySymbol . number_format((float) (session('cart_details')['discount_amount'] ?? 0.00), 2) }}</p>
                        </div>
                        <div class="summery-wizard-text">
                            <h6>{{ __('cart.taxes') }}</h6>
                            <p>{{ $currencySymbol . number_format((float) session('cart_details')['tax'], 2) }}</p>
                        </div>
                        <div class="total-wizard">
                            <h6 class="font-title--card">{{ __('cart.total') }}</h6>
                            <p class="font-title--card">{{ $currencySymbol . number_format((float) session('cart_details')['total_amount'], 2) }}</p>
                        </div>
                        <form action="{{ localeRoute('coupon_check') }}" method="post">
                            @csrf
                            <a href="{{ localeRoute('checkout') }}" class="button button-lg button--primary form-control mb-lg-3">
                                {{ __('cart.checkout') }}
                            </a>
                            <label for="coupon">{{ __('cart.apply_coupon') }}</label>
                            <div class="cart-input">
                                <input type="text" name="coupon" class="form-control" placeholder="{{ __('cart.coupon_placeholder') }}" id="coupon" />
                                <button type="submit" class="sm-button">{{ __('cart.apply_coupon') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="container text-center">
                <h1>{{ __('cart.cart_empty') }}</h1>
                <h5>{{ __('cart.no_courses') }}</h5>
            </div>
        @endif
    </div>
</section>
<!-- Cart Section Ends Here -->
@endsection

@push('scripts')
<script>
    const cartMessages = {
        confirmRemove: "{{ __('cart.confirm_remove') }}",
        emptyCart: "{{ __('cart.cart_empty') }}",
        noCourses: "{{ __('cart.no_courses') }}"
    };

    $(".remove-from-cart").click(function(e) {
        e.preventDefault();
        var ele = $(this);

        if (confirm(cartMessages.confirmRemove)) {
            $.ajax({
                url: '{{ localeRoute('remove.from.cart') }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: ele.data('id')
                },
                success: function(response) {
                    window.location.reload();
                }
            });
        }
    });

    // Если корзина пуста, показываем alert
    @if (!session('cart') || count(session('cart')) === 0)
        alert(cartMessages.emptyCart + "\n" + cartMessages.noCourses);
    @endif
</script>
@endpush


