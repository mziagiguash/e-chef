@extends('frontend.layouts.app')
@section('title', 'Contact Us')
@section('header-attr') class="nav-shadow" @endsection

@section('content')

<!-- Breadcrumb Starts Here -->
<div class="py-0 section--bg-white">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb pb-0 mb-0">
                <li class="breadcrumb-item"><a href="index.html" class="fs-6 text-secondary">Home</a></li>
                <li class="breadcrumb-item active"><a href="contact.html" class="fs-6 text-secondary">Contact</a></li>
            </ol>
        </nav>
    </div>
</div>
<!-- Breadcrumb Ends Here -->

<!-- Уведомления -->
@if(session('success'))
<div class="container mt-4">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="las la-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if($errors->any())
<div class="container mt-4">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="las la-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

<!-- Contact Hero Area Starts Here -->
<section class="section section--bg-white hero hero--one">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero__img-content">
                    <div class="hero__img-content--main">
                        <img src="{{asset('frontend/dist/images/contact/image.jpg')}}" alt="image" />
                    </div>
                    <img src="{{asset('frontend/dist/images/shape/dots/dots-img-02.png')}}" alt="shape"
                        class="hero__img-content--shape-01" />
                    <img src="{{asset('frontend/dist/images/shape/rec05.png')}}" alt="shape"
                        class="hero__img-content--shape-02" />
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero__content-info">
                    <h2 class="font-title--md mb-0">Our Branches</h2>
                    <p class="font-para--lg">
                        Mauris eu fringilla lorem. Phasellus a sem nisl. Sed tempor arcu ac condimentum molestie. Morbi
                        ullamcorper eleifend scelerisque. Aliquam venenatis eros elementum felis tincidunt scelerisque.
                    </p>
                    <ul class="hero__content-location">
                        <li>
                            <span><i class="fas fa-map-marker-alt fa-2x"></i></span>
                            <p>Chikago, USA</p>
                        </li>
                        <li>
                            <span><i class="fas fa-map-marker-alt fa-2x"></i></span>
                            <p>Mumbai, India</p>
                        </li>
                        <li>
                            <span><i class="fas fa-map-marker-alt fa-2x"></i></span>
                            <p>Rome, italy</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Get in Touch Area Starts Here -->
<section class="section getin-touch overflow-hidden"
    style="background-image: url({{asset('frontend/dist/images/contact/bg.png')}});">
    <div class="container">
        <div class="row">
            <h2 class="font-title--md text-center">Get in Touch</h2>
            <div class="col-lg-5 pe-lg-4 position-relative mb-4 mb-lg-0">
                <div class="contact-feature d-flex align-items-center">
                    <div class="contact-feature-icon primary-bg">
                     <i class="fas fa-map-marked-alt fa-2x"></i>
                    </div>
                    <div class="contact-feature-text">
                        <h6>Address</h6>
                        <p>Street NO. #15, House NO.</p>
                        <p>#15/B Chicago-60827, USA</p>
                    </div>
                </div>

                <div class="contact-feature d-flex align-items-center my-lg-4 my-3">
                    <div class="contact-feature-icon tertiary-bg">
                        <i class="far fa-envelope fa-2x"></i>
                    </div>
                    <div class="contact-feature-text">
                        <h6>Email</h6>
                        <h5>Edu.bd@gmail.com</h5>
                    </div>
                </div>

                <div class="contact-feature d-flex align-items-center">
                    <div class="contact-feature-icon success-bg">
                       <i class="fas fa-phone-alt fa-2x"></i>
                    </div>
                    <div class="contact-feature-text">
                        <h6>Phone</h6>
                        <h5>+1202-555-0621</h5>
                    </div>
                </div>
                <img src="{{asset('frontend/dist/images/shape/dots/dots-img-03.png')}}" alt="Shape"
                    class="img-fluid contact-feature-shape" />
            </div>
            <div class="col-lg-7 form-area">
                <form action="{{ localeRoute('contact.submit') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" id="name"
                                   value="{{ old('name',
                                       auth()->check() && auth()->user()->student_id ?
                                           (App\Models\Student::find(auth()->user()->student_id)->name ?? '') :
                                       (auth()->check() && auth()->user()->instructor_id ?
                                           (App\Models\Instructor::find(auth()->user()->instructor_id)->name ?? '') :
                                       (auth()->check() ? auth()->user()->name : ''))) }}"
                                   required placeholder="Your full name">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" id="email"
                                   value="{{ old('email',
                                       auth()->check() && auth()->user()->student_id ?
                                           (App\Models\Student::find(auth()->user()->student_id)->email ?? '') :
                                       (auth()->check() && auth()->user()->instructor_id ?
                                           (App\Models\Instructor::find(auth()->user()->instructor_id)->email ?? '') :
                                       (auth()->check() ? auth()->user()->email : ''))) }}"
                                   required placeholder="your.email@example.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                   name="subject" id="subject"
                                   value="{{ old('subject') }}"
                                   required placeholder="What is this regarding?">
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror"
                                      name="message" id="message"
                                      rows="6" required
                                      placeholder="Please describe your inquiry in detail...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 characters required.</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="button button-lg button--primary fw-normal">
                                <i class="las la-paper-plane me-2"></i>
                                Send Message
                            </button>
                        </div>
                    </div>
                </form>
                <div class="form-area-shape">
                    <img src="{{asset('frontend/dist/images/shape/circle3.png')}}" alt="Shape"
                        class="img-fluid shape-01" />
                    <img src="{{asset('frontend/dist/images/shape/circle5.png')}}" alt="Shape"
                        class="img-fluid shape-02" />
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Get in Touch Area Ends Here -->

<!-- Map Area Starts Here -->
<div class="map-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="map-area-frame">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d201876.9935430553!2d-122.37539090724721!3d37.75890609140571!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80857d8b28aaed03%3A0x71b415d535759367!2sOakland%2C%20CA%2C%20USA!5e0!3m2!1sen!2sbd!4v1613897278642!5m2!1sen!2sbd"
                        allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Map Area Ends Here -->

@endsection
