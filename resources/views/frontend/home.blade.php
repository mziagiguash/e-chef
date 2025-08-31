@extends('frontend.layouts.app')
@section('title', 'Home')
@section('footer-class') footer--two @endsection


@section('content')

<!-- Banner Starts Here -->
<section class="main-banner" style="background-image: url({{asset('frontend/dist/images/banner/banner.jpg')}});">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 mb-lg-0 order-2 order-lg-0 d-flex align-items-center">
                <div class="banner-two-start">
                    <h1 class="font-title--lg">Unlock Knowledge Anywhere, Anytime with Experts.</h1>
                    <p>
                       Our commitment is to guide you to the finest online courses, offering expert insights whenever and wherever you are.
                    </p>
                    <form>
                        <div class="banner-input">
                            <div class="main-input">
                                <input type="text" placeholder="what do you want to learn today..." />
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-search">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <div class="search-button">
                                <button class="button button-lg button--primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 order-1 order-lg-0">
                <div class="main-banner-end">
                    <img src="{{asset('frontend/dist/images/banner/banner-image-01.png')}}" alt="image"
                        class="img-fluid" width="515" height="700"/>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Browse Categories Starts Here -->
<section class="section browse-categories">
    <div class="container">
        <h2 class="font-title--md text-center mb-0">Browse Course with Top Categories</h2>
        <div class="browse-categories__wrapper position-relative">
            <div class="categories--box">
                @forelse ($category as $cat)
                    @php
                        $courseCount = $cat->courses()->count();
                        $categoryName = $cat->getTranslation('category_name');
                    @endphp

                    <div class="browse-categories-item default-item-one mb-2">
                        <div class="browse-categories-item-icon">
                            <div class="categories-one default-categories">
                                <img src="{{ asset('uploads/courseCategories/' . $cat->category_image) }}"
                                     class="rounded-circle" width="80" height="80" alt="{{ $categoryName }}">
                            </div>
                        </div>
                        <div class="browse-categories-item-text">
                            <h6 class="font-title--card">
                                <a href="{{ route('courseDetails', ['locale' => app()->getLocale(), 'id' => $cat->id]) }}">
                                    {{ $categoryName }}
                                </a>
                            </h6>
                            <p>{{ $courseCount }} {{ Str::plural('Course', $courseCount) }}</p>
                        </div>
                    </div>

                @empty
                    <p>No categories found</p>
                @endforelse
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary mt-5">Browse all Courses</a>
            </div>
        </div>
    </div>
    <div class="browse-categories-shape">
        <img src="{{asset('frontend/dist/images/shape/dots/dots-img-11.png')}}" alt="shape"
            class="img-fluid shape-01" />
        <img src="{{asset('frontend/dist/images/shape/line01.png')}}" alt="shape" class="img-fluid shape-02" />
    </div>
</section>

<!--  Popular Courses Starts Here -->
<section class="section section--bg-offwhite-three featured-popular-courses main-popular-course">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="featured-popular-courses-heading d-flex align-content-center justify-content-between">
                    <div class="main-heading">
                        <h3 class="font-title--md">Our Popular Courses</h3>
                    </div>
                    <div class="nav-button featured-popular-courses-tabs">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active ps-0" id="pills-all-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all"
                                    aria-selected="true">
                                    All
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-design-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-design" type="button" role="tab" aria-controls="pills-design"
                                    aria-selected="false">
                                    Design
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-dev-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-dev" type="button" role="tab" aria-controls="pills-dev"
                                    aria-selected="false">
                                    Development
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-bus-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-bus" type="button" role="tab" aria-controls="pills-bus"
                                    aria-selected="false">
                                    Business
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link me-0" id="pills-its-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-its" type="button" role="tab" aria-controls="pills-its"
                                    aria-selected="false">
                                    IT & Software
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                    <div class="row">
                        @forelse ($popularCourses as $pc)
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <div class="contentCard-top">
                                    <a href="#"><img src="{{asset('uploads/courses/'.$pc->image)}}" alt="images"
                                            class="img-fluid" /></a>
                                </div>
                                <div class="contentCard-bottom">
                                    <h5>
    <a href="{{ localeRoute('courseDetails', ['id' => encryptor('encrypt', $pc->id)]) }}"
       class="font-title--card">
       {{ $pc->getTranslation('title') ?: 'No Title' }}
    </a>
</h5>
                                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                                        <a href="{{localeRoute('instructorProfile', encryptor('encrypt', $pc->instructor?->id))}}"
                                            class="contentCard-user d-flex align-items-center">
                                            <img src="{{asset('uploads/users/'.$pc?->instructor->image)}}"
                                                alt="client-image" class="rounded-circle" height="34" width="34" />
                                            <p class="font-para--md">
    @if($pc->instructor)
        {{ $pc->instructor->getTranslation()?->name ?? 'No Instructor' }} — {{ $pc->instructor->getTranslation()?->designation ?? '' }}
    @else
        No Instructor
    @endif
</p>

</a>
                                        <div class="price">
                                             <del>$950</del>
                                             <span>$120</span>
                                        </div>
                                    </div>
                                    <div class="contentCard-more">
                                        <div class="d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/star.png')}}"
                                                    alt="star" />
                                            </div>
                                            <span>4.5</span>
                                        </div>
                                        <div class="eye d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/eye.png')}}"
                                                    alt="eye" />
                                            </div>
                                            <span>24,517</span>
                                        </div>
                                        <div class="book d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/book.png')}}"
                                                    alt="location" />
                                            </div>
                                            <span>{{$pc->lesson?$pc->lesson:0}} Lesson</span>
                                        </div>
                                        <div class="clock d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/Clock.png')}}"
                                                    alt="clock" />
                                            </div>
                                            <span>{{$pc->duration?$pc->duration:0}} Hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <h3>No Courses Available</h3>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary">Browse all
                                Courses</a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-design" role="tabpanel" aria-labelledby="pills-design-tab">
                    <div class="row">
                        @forelse ($designCourses as $dc)
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <div class="contentCard-top">
                                    <a href="#"><img src="{{asset('uploads/courses/'.$dc->image)}}" alt="images" class="img-fluid" /></a>
                                </div>
                                <div class="contentCard-bottom">
                                    <h5>
                                        <a href="{{localeRoute('courseDetails', ['id' => encryptor('encrypt', $dc->id)])}}"
                                            class="font-title--card">{{$dc->title}}</a>
                                    </h5>
                                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                                        <a href="{{localeRoute('instructorProfile', encryptor('encrypt', $dc->instructor?->id))}}"
                                            class="contentCard-user d-flex align-items-center">
                                            <img src="{{ $instructor->image
    ? asset('uploads/users/' . $instructor->image)
    : asset('images/default.png') }}"
    alt="{{ $instructor->getTranslation('name', app()->getLocale()) }}"
                                                class="rounded-circle" height="34" width="34" />
                                            <p class="font-para--md">{{ $instructor->getTranslation('designation', app()->getLocale()) }}</p>
                                        </a>
                                        <div class="price">
                                            <del>$95</del>
                                            <span>$12</span>
                                        </div>
                                    </div>
                                    <div class="contentCard-more">
                                        <div class="d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/star.png')}}" alt="star" />
                                            </div>
                                            <span>4.5</span>
                                        </div>
                                        <div class="eye d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/eye.png')}}" alt="eye" />
                                            </div>
                                            <span>24,517</span>
                                        </div>
                                        <div class="book d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/book.png')}}" alt="location" />
                                            </div>
                                            <span>{{$dc->lesson?$dc->lesson:0}} Lesson</span>
                                        </div>
                                        <div class="clock d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/Clock.png')}}" alt="clock" />
                                            </div>
                                            <span>{{$dc->duration?$dc->duration:0}} Hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <h3>No Courses Available</h3>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary">Browse all
                                Courses</a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-dev" role="tabpanel" aria-labelledby="pills-dev-tab">
                    <div class="row">
                        @forelse ($developmentCourses as $dv)
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <div class="contentCard-top">
                                    <a href="#"><img src="{{asset('uploads/courses/'.$dv->image)}}" alt="images"
                                            class="img-fluid" /></a>
                                </div>
                                <div class="contentCard-bottom">
                                    <h5>
                                        <a href="{{localeRoute('courseDetails', ['id' => encryptor('encrypt', $dv->id)])}}"
                                            class="font-title--card">{{$dv->title}}</a>
                                    </h5>
                                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                                        <a href="{{localeRoute('instructorProfile', encryptor('encrypt', $dv->instructor?->id))}}"
                                            class="contentCard-user d-flex align-items-center">
                                            <img src="{{asset('uploads/users/'.$dv?->instructor->image)}}" alt="client-image"
                                                class="rounded-circle" height="34" width="34" />
                                            <p class="font-para--md">{{$dv?->instructor->name}}</p>
                                        </a>
                                        <div class="price">
                                            <del>$95</del>
                                            <span>$12</span>
                                        </div>
                                    </div>
                                    <div class="contentCard-more">
                                        <div class="d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/star.png')}}" alt="star" />
                                            </div>
                                            <span>4.5</span>
                                        </div>
                                        <div class="eye d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/eye.png')}}" alt="eye" />
                                            </div>
                                            <span>24,517</span>
                                        </div>
                                        <div class="book d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/book.png')}}" alt="location" />
                                            </div>
                                            <span>{{$dv->lesson?$dv->lesson:0}} Lesson</span>
                                        </div>
                                        <div class="clock d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/Clock.png')}}" alt="clock" />
                                            </div>
                                            <span>{{$dv->duration?$dv->duration:0}} Hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <h3>No Courses Available</h3>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary">Browse all
                                Courses</a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-bus" role="tabpanel" aria-labelledby="pills-bus-tab">
                    <div class="row">
                        @forelse ($businessCourses as $bc)
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <div class="contentCard-top">
                                    <a href="#"><img src="{{asset('uploads/courses/'.$bc->image)}}" alt="images"
                                            class="img-fluid" /></a>
                                </div>
                                <div class="contentCard-bottom">
                                    <h5>
                                        <a href="{{localeRoute('courseDetails', ['id' => encryptor('encrypt', $bc->id)])}}"
                                            class="font-title--card">{{$bc->title}}</a>
                                    </h5>
                                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                                        <a href="{{localeRoute('instructorProfile', encryptor('encrypt', $bc->instructor?->id))}}"
                                            class="contentCard-user d-flex align-items-center">
                                            <img src="{{asset('uploads/users/'.$bc?->instructor->image)}}" alt="client-image"
                                                class="rounded-circle" height="34" width="34" />
                                            <p class="font-para--md">{{$bc?->instructor->name}}</p>
                                        </a>
                                        <div class="price">
                                            <del>$95</del>
                                            <span>$12</span>
                                        </div>
                                    </div>
                                    <div class="contentCard-more">
                                        <div class="d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/star.png')}}" alt="star" />
                                            </div>
                                            <span>4.5</span>
                                        </div>
                                        <div class="eye d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/eye.png')}}" alt="eye" />
                                            </div>
                                            <span>24,517</span>
                                        </div>
                                        <div class="book d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/book.png')}}" alt="location" />
                                            </div>
                                            <span>{{$bc->lesson?$bc->lesson:0}} Lesson</span>
                                        </div>
                                        <div class="clock d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/Clock.png')}}" alt="clock" />
                                            </div>
                                            <span>{{$bc->duration?$bc->duration:0}} Hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <h3>No Courses Available</h3>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary">Browse all
                                Courses</a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-its" role="tabpanel" aria-labelledby="pills-its-tab">
                    <div class="row">
                        @forelse ($itCourses as $ic)
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <div class="contentCard-top">
                                    <a href="#"><img src="{{asset('uploads/courses/'.$ic->image)}}" alt="images"
                                            class="img-fluid" /></a>
                                </div>
                                <div class="contentCard-bottom">
                                    <h5>
                                        <a href="{{localeRoute('courseDetails', ['id' => encryptor('encrypt', $ic->id)])}}"
                                            class="font-title--card">{{$ic->title}}</a>
                                    </h5>
                                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                                        <a href="{{localeRoute('instructorProfile', encryptor('encrypt', $ic->instructor?->id))}}"
                                            class="contentCard-user d-flex align-items-center">
                                            <img src="{{asset('uploads/users/'.$ic?->instructor->image)}}" alt="client-image"
                                                class="rounded-circle" height="34" width="34" />
                                            <p class="font-para--md">{{$ic?->instructor->name}}</p>
                                        </a>
                                        <div class="price">
                                            <del>$95</del>
                                            <span>$12</span></div>
                                    </div>
                                    <div class="contentCard-more">
                                        <div class="d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/star.png')}}" alt="star" />
                                            </div>
                                            <span>4.5</span>
                                        </div>
                                        <div class="eye d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/eye.png')}}" alt="eye" />
                                            </div>
                                            <span>24,517</span>
                                        </div>
                                        <div class="book d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/book.png')}}" alt="location" />
                                            </div>
                                            <span>{{$ic->lesson?$ic->lesson:0}} Lesson</span>
                                        </div>
                                        <div class="clock d-flex align-items-center">
                                            <div class="icon">
                                                <img src="{{asset('frontend/dist/images/icon/Clock.png')}}" alt="clock" />
                                            </div>
                                            <span>{{$ic->duration?$ic->duration:0}} Hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-xl-4 col-md-6">
                            <div class="contentCard contentCard--course">
                                <h3>No Courses Available</h3>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary">Browse all
                                Courses</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="featured-popular-courses-shape">
        <img src="{{asset('frontend/dist/images/shape/dots/dots-img-12.png')}}" alt="Shape"
            class="img-fluid dot-06" />
        <img src="{{asset('frontend/dist/images/shape/triangel.png')}}" alt="Shape" class="img-fluid dot-07" />
    </div>
</section>

{{-- Why You'll Learn With Eduguard --}}
<section class="section feature section section--bg-offwhite-one">
    <div class="container">
        <h2 class="font-title--md text-center">Why You'll Learn with Eduguard</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="cardFeature">
                    <div class="cardFeature__icon cardFeature__icon--bg-g">
                        <svg width="32" height="28" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2 2H10.4C11.8852 2 13.3096 2.5619 14.3598 3.5621C15.41 4.56229 16 5.91885 16 7.33333V26C16 24.9391 15.5575 23.9217 14.7699 23.1716C13.9822 22.4214 12.9139 22 11.8 22H2V2Z"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M30 2H21.6C20.1148 2 18.6904 2.5619 17.6402 3.5621C16.59 4.56229 16 5.91885 16 7.33333V26C16 24.9391 16.4425 23.9217 17.2302 23.1716C18.0178 22.4214 19.0861 22 20.2 22H30V2Z"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5 class="font-title--xs">250k online course</h5>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce sed commodo enim Fusce sed.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="cardFeature">
                    <div class="cardFeature__icon cardFeature__icon--bg-b">
                        <svg width="28" height="27" viewBox="0 0 28 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.3855 12.224C21.8743 12.224 23.8915 10.2067 23.8915 7.71794C23.8915 5.23054 21.8743 3.21191 19.3855 3.21191"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M21.4575 17.1211C22.201 17.1717 22.939 17.2783 23.6675 17.4395C24.6775 17.6404 25.8938 18.0546 26.3257 18.9607C26.6018 19.5415 26.6018 20.218 26.3257 20.7989C25.8952 21.705 24.6775 22.1191 23.6675 22.3269"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.5994 18.0913C15.6425 18.0913 19.9504 18.8553 19.9504 21.9071C19.9504 24.9604 15.6699 25.7503 10.5994 25.7503C5.55624 25.7503 1.24976 24.9877 1.24976 21.9345C1.24976 18.8813 5.52891 18.0913 10.5994 18.0913Z"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.5993 13.7349C7.27274 13.7349 4.60767 11.0684 4.60767 7.74188C4.60767 4.41669 7.27274 1.75024 10.5993 1.75024C13.9259 1.75024 16.5923 4.41669 16.5923 7.74188C16.5923 11.0684 13.9259 13.7349 10.5993 13.7349Z"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5 class="font-title--xs">Expert Instructors</h5>
                    <p>
                        Vivamus interdum neque massa, eget mattis mi gravida eget. Donec et dictum justo. Vivamus
                        interdum.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="cardFeature">
                    <div class="cardFeature__icon cardFeature__icon--bg-r">
                        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M25.2502 13.2495C25.2502 19.8774 19.8781 25.2495 13.2502 25.2495C6.62235 25.2495 1.25024 19.8774 1.25024 13.2495C1.25024 6.62162 6.62235 1.24951 13.2502 1.24951C19.8781 1.24951 25.2502 6.62162 25.2502 13.2495Z"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M17.7021 17.0667L12.8113 14.1491V7.86108" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5 class="font-title--xs">Lifetime Access</h5>
                    <p>
                        Vivamus cursus libero quis lobortis mattis. Suspendisse in malesuada mi. Maecenas vel
                        euismod turpis.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!--  Learning Rules Starts Here -->
<section class="section learning-rules">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-2 order-lg-0">
                <div class="learning-rules-starts">
                    <h2 class="font-title--md">
                        Eduguard Simple <br class="d-none d-md-block" />
                        Learning Steps
                    </h2>
                    <div class="learning-rules__wrapper">
                        <div class="learning-rules-item">
                            <div class="item-number"><span>01.</span></div>
                            <div class="item-text">
                                <h6>Make Your Own Place.</h6>
                                <p>
                                    Fusce dictum, velit eu placerat consectetur, ante nisl auctor magna, sit amet
                                    fringilla urna nibh a risus.
                                </p>
                            </div>
                        </div>
                        <div class="learning-rules-item">
                            <div class="item-number"><span>02.</span></div>
                            <div class="item-text">
                                <h6>Find Best Course With Better Filtter.</h6>
                                <p>
                                    Morbi id est a risus sollicitudin maximus. Fusce lorem neque, tincidunt vel
                                    rhoncus eget, convallis ullamcorper sem.
                                </p>
                            </div>
                        </div>
                        <div class="learning-rules-item">
                            <div class="item-number"><span>03.</span></div>
                            <div class="item-text">
                                <h6>And Become a Master in Your Field.</h6>
                                <p>
                                    Sed pulvinar dignissim neque, ac consectetur urna tincidunt vel. Sed congue
                                    nulla sed tempus ultrices.
                                </p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="button button-lg button--primary">Start Learning</a>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-0">
                <div class="learning-rules-ends">
                    <img src="{{asset('frontend/dist/images/hero/hero-img-01.jpg')}}" alt="img"
                        class="img-fluid rounded"/>
                    <div class="learning-rules-ends-circle">
                        <img src="{{asset('frontend/dist/images/shape/l03.png')}}" alt="shape"
                            class="img-fluid" />
                    </div>
                    <div class="earning-rules-ends-shape">
                        <img src="{{asset('frontend/dist/images/shape/l04.png')}}" alt="shape"
                            class="img-fluid shape-1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="learning-rules-shape">
        <img src="{{asset('frontend/dist/images/shape/dots/dots-img-16.png')}}" alt="shape"
            class="img-fluid shape-01" />
        <img src="{{asset('frontend/dist/images/shape/l02.png')}}" alt="shape" class="img-fluid shape-02" />
    </div>
</section>

<!--  About Services Starts Here -->
<section class="section about-services section section--bg-offgradient">
    <div class="container about-services-area">
        <div class="row">
            <div class="col-lg-6 text-center mx-auto">
                <h2 class="font-title--md">What Our Students Says About our Services</h2>
            </div>
        </div>
        <div class="testimonial testimonial--one testimonial__slider--one">
            <div class="testimonial__item">
                <p>
                    “Nam hendrerit quam eu neque egestas, nec lobortis enim rutrum. Quisque ligula tortor, mollis a
                    efficitur vitae, imperdiet et mauris. Nam in orci quis risus dapibus mollis.“
                </p>
                <div class="testimonial__user-wrapper d-flex justify-content-between">
                    <div class="testimonial__user d-flex align-items-center">
                        <div class="testimonial__user-img">
                            <img src="{{asset('frontend/dist/images/avatar/avatar-img-01.png')}}" alt="Client" />
                        </div>
                        <div class="testimonial__user-info">
                            <h6>Sheikh Rashed</h6>
                            <span class="font-para--md">UI/UX Student</span>
                        </div>
                    </div>
                    <ul class="testimonial__item-star d-flex align-items-center">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="testimonial__item">
                <p>
                    “Nam hendrerit quam eu neque egestas, nec lobortis enim rutrum. Quisque ligula tortor, mollis a
                    efficitur vitae, imperdiet et mauris. Nam in orci quis risus dapibus mollis.“
                </p>
                <div class="testimonial__user-wrapper d-flex justify-content-between">
                    <div class="testimonial__user d-flex align-items-center">
                        <div class="testimonial__user-img">
                            <img src="{{asset('frontend/dist/images/avatar/avatar-img-02.png')}}" alt="Client" />
                        </div>
                        <div class="testimonial__user-info">
                            <h6>Dev Zakir</h6>
                            <span class="font-para--md">UI/UX Student</span>
                        </div>
                    </div>
                    <ul class="testimonial__item-star d-flex align-items-center">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="testimonial__item">
                <p>
                    “Nam hendrerit quam eu neque egestas, nec lobortis enim rutrum. Quisque ligula tortor, mollis a
                    efficitur vitae, imperdiet et mauris. Nam in orci quis risus dapibus mollis.“
                </p>
                <div class="testimonial__user-wrapper d-flex justify-content-between">
                    <div class="testimonial__user d-flex align-items-center">
                        <div class="testimonial__user-img">
                            <img src="{{asset('frontend/dist/images/avatar/avatar-img-03.png')}}" alt="Client" />
                        </div>
                        <div class="testimonial__user-info">
                            <h6>Dev Kate</h6>
                            <span class="font-para--md">UI/UX Student</span>
                        </div>
                    </div>
                    <ul class="testimonial__item-star d-flex align-items-center">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="about-services-shape">
        <img src="{{asset('frontend/dist/images/shape/line02.png')}}" alt="shape"
            class="img-fluid img-shape-01" />
        <img src="{{asset('frontend/dist/images/shape/dots/dots-img-13.png')}}" alt="shape"
            class="img-fluid img-shape-02" />
        <img src="{{asset('frontend/dist/images/shape/l02.png')}}" alt="shape" class="img-fluid img-shape-03" />
    </div>
    <div class="container overflow-hidden">
        <div class="row mb-40">
            <div class="col-lg-6 mx-auto text-center brands-area-two-heading">
                <h4>
                    Over 30,000+ Schools & College Learning With Us.
                </h4>
                <p>
                    Proin euismod elementum dolor, non iaculis velit mollis sed. In eleifend urna sit amet purus
                    congue.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="brand-area">
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/1.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/2.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/3.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/4.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/2.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                    <div class="brand-area-image">
                        <img src="{{asset('frontend/dist/images/versity/5.png')}}" alt="Brand"
                            class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Best Instructors Starts Here -->
<section class="section best-instructor-featured overflow-hidden main-instructor-featured">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 position-relative">
                <h3 class="text-center mb-40 font-title--md">{{ __('Meet Our Best Instructor') }}</h3>
                <div class="ourinstructor__wrapper mt-lg-5 mt-0">
                    <div class="ourinstructor-active">
                        @forelse ($instructor as $i)
                            <div class="mentor">
                                <div class="mentor__img">
                                    <img
                                        src="{{ $i->image ? asset('uploads/users/'.$i->image) : asset('images/default-user.png') }}"
                                        alt="{{ $i->getTranslation()?->name ?? 'No Instructor' }}"
                                        class="img-fluid"
                                    />
                                    {{-- соцсети пока статично --}}
                                    <ul class="list-inline">
                                        <li class="list-inline-item"><a href="#"><i class="fab fa-instagram"></i></a></li>
                                        <li class="list-inline-item"><a href="#"><i class="fab fa-linkedin"></i></a></li>
                                        <li class="list-inline-item"><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li class="list-inline-item"><a href="#"><i class="fab fa-youtube"></i></a></li>
                                        <li class="list-inline-item"><a href="#"><i class="fab fa-facebook"></i></a></li>
                                    </ul>
                                </div>

                                <div class="mentor__title text-center mt-3">
                                    <a href="{{ localeRoute('instructorProfile', encryptor('encrypt', $i->id)) }}">
                                        <h5 class="mb-1">{{ $i->getTranslation()?->name ?? 'No Instructor' }}</h5>
                                        <p class="mb-0">{{ $i->getTranslation()?->designation ?? '' }}</p>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">{{ __('No Instructors found.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-instructor-featured-shape">
        <img src="{{ asset('frontend/dist/images/shape/dots/dots-img-14.png') }}" alt="shape" class="img-fluid shape01" />
        <img src="{{ asset('frontend/dist/images/shape/triangel2.png') }}" alt="shape" class="img-fluid shape02" />
    </div>
</section>


<!--  Latest Events Featured Starts Here -->
<section class="section section--bg-offwhite-three latest-events-featured main-events-featured">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="font-title--md">Latest Events</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12 position-relative px-0 mx-0">
                <div class="eventsSlider">
                    @forelse ($course as $c)
                    <div class="contentCard contentCard--event contentCard--space">
                        <div class="contentCard-top">
                            <a href="#"><img src="{{asset('uploads/courses/'.$c->image)}}" alt="images"
                                    class="img-fluid" /></a>
                        </div>
                        <div class="contentCard-bottom">
                            <h5>
                                <a href="{{localeRoute('courseDetails', encryptor('encrypt', $c->id))}}"
                                    class="font-title--card">{{$c->title}}</a>
                            </h5>
                            <div class="contentCard-more">
                                <div class="d-flex align-items-center">
                                    <div class="icon">
                                        <img src="{{asset('frontend/dist/images/icon/location.png')}}"
                                            alt="location" />
                                    </div>
                                    <span>Chicago, Illinois</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon">
                                        <img src="{{asset('frontend/dist/images/icon/calendar.png')}}"
                                            alt="calendar" />
                                    </div>
                                    <span>29th jan, 2020</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <a href="{{localeRoute('searchCourse')}}" class="button button-lg button--primary mt-lg-5 mt-5">Browse all
                    events</a>
            </div>
        </div>
    </div>
    <div class="main-events-featured-shape">
        <img src="{{asset('frontend/dist/images/shape/triangel3.png')}}" alt="shape" class="img-fluid shape01" />
    </div>
</section>

<!--  Main Become Instructor Starts Here -->
<section class="section main-become-instructor">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="main-become-instructor-item me-12">
                    <div class="main-image">
                        <img src="{{asset('frontend/dist/images/event/image01.png')}}" alt="image"
                            class="img-fluid" />
                    </div>
                    <div class="main-text">
                        <h6 class="font-title--sm">Become an Instructor</h6>
                        <p>
                            Praesent ultricies nulla ac congue bibendum. Aliquam tempor euismod purus posuere
                            gravida. Praesent augue sapien, vulputate eu imperdiet eget, tempor at enim.
                        </p>
                        <div class="text-center">
                            <a href="become-instructor.html" class="green-btn">Apply as Instructor</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="main-become-instructor-item ms-12 mb-0">
                    <div class="main-image">
                        <img src="{{asset('frontend/dist/images/event/image02.png')}}" alt="image"
                            class="img-fluid" />
                    </div>
                    <div class="main-text">
                        <h6 class="font-title--sm">Use Eduguard For Business</h6>
                        <p>
                            Praesent ultricies nulla ac congue bibendum. Aliquam tempor euismod purus posuere
                            gravida. Praesent augue sapien, vulputate eu imperdiet eget, tempor at enim.
                        </p>
                        <div class="text-center">
                            <a href="#" class="green-btn">Get Eduguard For Business</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-become-instructor-shape">
        <img src="{{asset('frontend/dist/images/shape/line03.png')}}" alt="shape" class="img-fluid" />
    </div>
</section>

<!-- News Letter Starts Here -->
<section style="background-color: #ebebf2;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="newsletter-area">
                    <h4>Subscribe our Newsletter</h4>
                    <p class="mt-2 mb-lg-4 mb-3">
                        Duis posuere maximus arcu eu tincidunt. Nam rutrum, nibh vitae tempus venenatis, ex tortor
                        ultricies magna, et faucibus magna eros quis arcu.
                    </p>
                    <form>
                        <div class="input-group">
                            <input type="email" class="form-control border-lowBlack" placeholder="Your email" />
                            <button class="button button-lg button--primary" type="button">
                                Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded',function(){
    if($('#instructorTable').length) $('#instructorTable').DataTable();

    const initialLocale = localStorage.getItem('instructors_lang') || '{{ $appLocale }}';
    function setActiveLocale(locale){
        document.querySelectorAll('.lang-tab').forEach(tab=>{ tab.classList.toggle('active',tab.dataset.locale===locale) });
        document.querySelectorAll('.lang-en,.lang-ru,.lang-ka').forEach(el=>{ el.style.display = el.classList.contains('lang-'+locale)?'':'none'; });
    }
    document.querySelectorAll('.lang-tab').forEach(tab=>tab.addEventListener('click',function(e){
        e.preventDefault(); const locale=this.dataset.locale;
        localStorage.setItem('instructors_lang',locale);
        setActiveLocale(locale);
    }));
    setActiveLocale(initialLocale);
});
    function drop() {
                    const dropBox = document.querySelector(".categoryDrop");
                    const arrow = document.querySelector(".select-button button svg");
                    arrow.classList.toggle("appear");
                    dropBox.classList.toggle("appear");
                }
</script>

@endpush
