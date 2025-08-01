@extends('frontend.layouts.app')
@section('title', 'Sign Up')
@section('header-attr') class="nav-shadow" @endsection

@section('content')
<!-- SignUp Area Starts Here -->
<section class="signup-area signin-area p-3">
    <div class="container">
        <div class="row align-items-center justify-content-md-center">
            <div class="col-lg-5 order-2 order-lg-0">
                <div class="signup-area-textwrapper">
                    <h2 class="font-title--md mb-0">@lang('auth.sign_up')</h2>
                    <p class="mt-2 mb-lg-4 mb-3">
                        @lang('auth.have_account')
                        <a href="{{route('studentLogin', ['locale' => app()->getLocale()])}}" class="text-black-50">@lang('auth.sign_in')</a>
                    </p>
                    <form action="{{route('studentRegister.store', ['locale' => app()->getLocale()])}}" method="POST">
                        @csrf
                        <div class="form-element">
                            <label for="name">@lang('auth.full_name')</label>
                            <input type="text" placeholder="@lang('auth.enter_name')" id="name" value="{{old('name')}}" name="name" />
                            @error('name') <small class="d-block text-danger">{{$message}}</small> @enderror
                        </div>

                        <div class="form-element">
                            <label for="email">@lang('auth.email')</label>
                            <input type="email" placeholder="example@email.com" id="email" value="{{old('email')}}" name="email" />
                            @error('email') <small class="d-block text-danger">{{$message}}</small> @enderror
                        </div>

                        <div class="form-element">
                            <label for="password">@lang('auth.password')</label>
                            <div class="form-alert-input">
                                <input type="password" placeholder="Type here..." id="password" name="password" />
                                <div class="form-alert-icon" onclick="showPassword('password',this)">
                                    @include('partials.eye-icon')
                                </div>
                            </div>
                            @error('password') <small class="d-block text-danger">{{$message}}</small> @enderror
                        </div>

                        <div class="form-element">
                            <label for="password_confirmation">@lang('auth.confirm_password')</label>
                            <div class="form-alert-input">
                                <input type="password" placeholder="Type here..." name="password_confirmation" id="password_confirmation" />
                                <div class="form-alert-icon" onclick="showPassword('password_confirmation',this)">
                                    @include('partials.eye-icon')
                                </div>
                            </div>
                        </div>

                        <div class="form-element d-flex align-items-center terms">
                            <input class="checkbox-primary me-1" type="checkbox" id="agree" />
                            <label for="agree" class="text-secondary mb-0">@lang('auth.accept_terms')</label>
                        </div>

                        <div class="form-element">
                            <button type="submit" class="button button-lg button--primary w-100">@lang('auth.sign_up_button')</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 order-1 order-lg-0">
                <div class="signup-area-image">
                    <img src="{{asset('public/frontend/dist/images/signup/Illustration.png')}}" alt="Illustration Image" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SignUp Area Ends Here -->

@endsection
