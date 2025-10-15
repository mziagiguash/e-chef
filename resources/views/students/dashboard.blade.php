@extends('frontend.layouts.app')
@section('title', "Student's Dashboard")
@section('body-attr') style="background-color: #ebebf2;" @endsection

@section('content')

<style>
.students-info-intro-end {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    max-width: 100%;
}

.enrolled-courses-text p,
.completed-courses-text p {
    word-break: break-word;
    white-space: normal;
    overflow-wrap: break-word;
    max-width: 100%;
    font-size: 14px;
    text-align: left;
}

.enrolled-courses,
.completed-courses {
    max-width: 220px;
    font-size: 14px;
}

.students-info-intro-end,
.students-info-intro-end * {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.3;
}
</style>

<!-- Breadcrumb Starts Here -->
<div class="py-0">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center bg-transparent mb-0">
                <li class="breadcrumb-item" ><a class="fs-6 text-secondary" href="{{ localeRoute('home') }}">{{ __('menu.home') }}</a></li>
                <li class="breadcrumb-item" aria-current="page"><a class="fs-6 text-secondary" href="{{ localeRoute('studentdashboard') }}">{{ __('menu.dashboard') }}</a></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Students Info area Starts Here -->
<section class="section students-info">
    <div class="container">
        <div class="students-info-intro">
            <!-- profile Details   -->
            <div class="students-info-intro__profile">
                <div>
                    <div class="students-info-intro-start">
                        <div class="image">
                            <img src="{{ asset('uploads/students/' . $student_info->image) }}" alt="Student" />
                        </div>
                        <div class="text">
                            <h5>{{$student_info->name}}</h5>
                            <p>{{$student_info->profession?$student_info->profession:'Student'}}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="students-info-intro-end">
                        <div class="enrolled-courses row">
                            <div class="enrolled-courses-icon">
                                <svg width="28" height="26" viewBox="0 0 28 26" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1 1.625H8.8C10.1791 1.625 11.5018 2.15764 12.477 3.10574C13.4521 4.05384 14 5.33974 14 6.68056V24.375C14 23.3694 13.5891 22.405 12.8577 21.6939C12.1263 20.9828 11.1343 20.5833 10.1 20.5833H1V1.625Z"
                                        stroke="#1089FF" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M27 1.625H19.2C17.8209 1.625 16.4982 2.15764 15.523 3.10574C14.5479 4.05384 14 5.33974 14 6.68056V24.375C14 23.3694 14.4109 22.405 15.1423 21.6939C15.8737 20.9828 16.8657 20.5833 17.9 20.5833H27V1.625Z"
                                        stroke="#1089FF" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="enrolled-courses-text">
                                <h5>{{ $enrolled_courses_count ?? 0 }}</h5>
                                <p>{{ __('stud_dashboard.enrolled_courses') }}</p>
                            </div>
                        </div>
                        <div class="completed-courses row">
                            <div class="completed-courses-icon">
                                <svg width="22" height="26" viewBox="0 0 22 26" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M19.1716 3.95235C19.715 4.14258 20.078 4.65484 20.078 5.23051V13.6518C20.078 16.0054 19.2226 18.2522 17.7119 19.9929C16.9522 20.8694 15.9911 21.552 14.9703 22.1041L10.5465 24.4938L6.11516 22.1028C5.09312 21.5508 4.13077 20.8694 3.36983 19.9916C1.85791 18.2509 1 16.0029 1 13.6468V5.23051C1 4.65484 1.36306 4.14258 1.90641 3.95235L10.0902 1.07647C10.3811 0.974511 10.6982 0.974511 10.9879 1.07647L19.1716 3.95235Z"
                                        stroke="#00AF91" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M7.30688 12.4002L9.65931 14.7538L14.5059 9.90723" stroke="#00AF91"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="completed-courses-text">
                                <h5>{{ $completed_courses_count ?? 0 }}</h5>
                                <p>{{ __('stud_dashboard.completed_courses') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Nav  -->
            <nav class="students-info-intro__nav">
    <div class="nav" id="nav-tab" role="tablist">
        <button class="nav-link" id="nav-coursesall-tab" data-bs-toggle="tab"
            data-bs-target="#nav-coursesall" type="button" role="tab" aria-controls="nav-coursesall"
            aria-selected="false">{{ __('stud_dashboard.all_courses') }}</button>

        <button class="nav-link" id="nav-activecourses-tab" data-bs-toggle="tab"
            data-bs-target="#nav-activecourses" type="button" role="tab" aria-controls="nav-activecourses"
            aria-selected="false">{{ __('stud_dashboard.active_courses') }}</button>

        <button class="nav-link" id="nav-completedcourses-tab" data-bs-toggle="tab"
            data-bs-target="#nav-completedcourses" type="button" role="tab"
            aria-controls="nav-completedcourses" aria-selected="false">{{ __('stud_dashboard.completed_courses_tab') }}</button>

        <button class="nav-link" id="nav-purchase-tab" data-bs-toggle="tab"
            data-bs-target="#nav-purchase" type="button" role="tab" aria-controls="nav-purchase"
            aria-selected="false">{{ __('stud_dashboard.purchase_history') }}</button>

{{-- Новая вкладка для уведомлений --}}
        <button class="nav-link" id="nav-notifications-tab" data-bs-toggle="tab"
            data-bs-target="#nav-notifications" type="button" role="tab" aria-controls="nav-notifications"
            aria-selected="false">
            Notifications
            @if($unread_notifications_count > 0)
                <span class="badge bg-danger ms-1">{{ $unread_notifications_count }}</span>
            @endif
        </button>
        <button class="nav-link">
            <a href="{{ localeRoute('student_profile') }}" class="text-secondary">{{ __('stud_dashboard.profile') }}</a>
        </button>
    </div>
</nav>
        </div>

        <div class="students-info-main">
            <div class="tab-content" id="nav-tabContent">
                {{-- Profile Info --}}
                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel"
                    aria-labelledby="nav-profile-tab">
                    <div class="tab-content__profile">
                        <section class="section section--bg-white calltoaction">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6 col-12 mx-auto text-center">
                                        <h5 class="font-title--sm">{{ __('stud_dashboard.invest_title') }}</h5>
                                        <p class="my-4 font-para--lg">{{ __('stud_dashboard.invest_text') }}
                                        </p>
                                        <a href="{{localeRoute('searchCourse')}}"
                                            class="button button-md button--primary">{{ __('stud_dashboard.invest_button') }}</a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
{{-- Notifications Tab --}}
<div class="tab-pane fade" id="nav-notifications" role="tabpanel" aria-labelledby="nav-notifications-tab">
        {{-- ОТЛАДОЧНАЯ ИНФОРМАЦИЯ --}}
    <div class="alert alert-info mb-4">
        <h6>Notifications Debug Info:</h6>
        <p><strong>Student ID:</strong> {{ $student_info->id }}</p>
        <p><strong>Unread Count:</strong> {{ $unread_notifications_count }}</p>
        <p><strong>Total Notifications:</strong> {{ $notifications->count() }}</p>
        <p><strong>Last Notification:</strong>
            @if($notifications->count() > 0)
                {{ $notifications->first()->created_at->diffForHumans() }}
            @else
                No notifications
            @endif
        </p>
    </div>

    <div class="notifications-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="font-title--card">My Notifications</h5>
            @if($unread_notifications_count > 0)
                <form action="{{ localeRoute('student.notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="las la-check-double me-1"></i>Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <div class="notifications-list">
            @forelse($notifications as $notification)
                <div class="notification-item card mb-3 {{ $notification->is_read ? 'bg-light' : 'border-primary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary me-2">New</span>
                                    @endif
                                    <h6 class="mb-0 {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                        {{ $notification->title }}
                                    </h6>
                                </div>
                                <p class="mb-2 {{ $notification->is_read ? 'text-muted' : '' }}">
                                    {{ $notification->message }}
                                </p>
                                <small class="text-muted">
                                    <i class="las la-clock me-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>

                                {{-- Ответ на контактное сообщение --}}
                                @if($notification->type === 'contact_message_replied' && $notification->contact_message)
                                    <div class="mt-3 p-3 bg-white border rounded">
                                        <strong class="text-primary">Admin Response:</strong>
                                        <p class="mb-1 mt-1">{{ $notification->contact_message->admin_notes }}</p>
                                        <small class="text-muted">
                                            Status:
                                            <span class="badge bg-{{ $notification->contact_message->status === 'resolved' ? 'success' : 'warning' }}">
                                                {{ ucfirst($notification->contact_message->status) }}
                                            </span>
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="notification-actions ms-3">
                                @if(!$notification->is_read)
                                    <form action="{{ localeRoute('student.notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Read">
                                            <i class="las la-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="las la-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Notifications</h5>
                    <p class="text-muted">You don't have any notifications yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Убираем пагинацию, так как показываем только последние 5 уведомлений --}}
        @if($notifications->count() >= 5)
            <div class="text-center mt-4">
                <a href="{{ localeRoute('student.notifications.all') }}" class="btn btn-outline-primary">
                    View All Notifications
                </a>
            </div>
        @endif
    </div>
</div>
    {{-- All Courses --}}
<div class="tab-pane fade" id="nav-coursesall" role="tabpanel" aria-labelledby="nav-coursesall-tab">
    <div class="row">
        @forelse ($all_enrollments_paginated as $a)
        <div class="col-lg-4 col-md-6 col-md-6 mb-4">
            <div class="contentCard contentCard--watch-course">
                <div class="contentCard-top">
                    <a href="#"><img src="{{asset('uploads/courses/'.$a->course?->image)}}"
                            alt="images" class="img-fluid" /></a>
                </div>
                <div class="contentCard-bottom">
                    <h5>
                        <a href="{{localeRoute('frontend.courses.show', $a->course?->id)}}"
                            class="font-title--card">{{$a->course?->title}}</a>
                    </h5>
                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                        <a href="{{localeRoute('instructor.show', $a->course?->instructor->id)}}"
                            class="contentCard-user d-flex align-items-center">
                            <img src="{{asset('uploads/users/'.$a->course?->instructor?->image)}}"
                                alt="client-image" class="rounded-circle" height="34" width="34" />
                            <p class="font-para--md">{{$a->course?->instructor?->name}}</p>
                        </a>
                        <div class="contentCard-course--status d-flex align-items-center">
                            <span class="percentage">{{ $a->progress_percentage ?? 0 }}%</span>
                            <p>Progress</p>
                        </div>
                    </div>

                    {{-- Отладочная информация --}}
                    @if(app()->environment('local'))
                    <div class="debug-info small text-muted mt-2 p-2 bg-light rounded">
                        Course ID: {{ $a->course->id }}<br>
                        Progress: {{ $a->progress_percentage ?? 0 }}%<br>
                        Total Lessons: {{ $a->course->lessons->count() ?? 0 }}<br>
                        Status: {{ $a->is_completed ? 'Completed' : ($a->progress_percentage > 0 ? 'Active' : 'New') }}
                    </div>
                    @endif

                    <a class="button button-md button--primary-outline w-100 my-3"
                       href="{{localeRoute('frontend.courses.show', $a->course?->id)}}">
                        @if($a->progress_percentage >= 100)
                            Review Course
                        @elseif($a->progress_percentage > 0)
                            Continue ({{ $a->progress_percentage }}%)
                        @else
                            Start Course
                        @endif
                    </a>
                    <div class="contentCard-watch--progress">
                        <span class="percentage" style="width: {{ $a->progress_percentage ?? 0 }}%;"></span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5">
            <div class="col-md-6 col-12 mx-auto text-center">
                <h5 class="font-title--sm">You Haven't Enrolled Any Course Yet...</h5>
                <p class="my-4 font-para--lg">
                    Your Course List is Empty!
                </p>
                <a href="{{localeRoute('searchCourse')}}" class="button button-md button--primary">Enroll Now!</a>
            </div>
        </div>
        @endforelse

        @if($all_enrollments_paginated && $all_enrollments_paginated->count() > 0)
        <div class="col-lg-12 mt-lg-5">
            <div class="pagination justify-content-center pb-0">
                <div class="pagination-group">
                    {{ $all_enrollments_paginated->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Active Courses --}}
<div class="tab-pane fade" id="nav-activecourses" role="tabpanel"
    aria-labelledby="nav-activecourses-tab">
    <div class="row">
        @forelse ($active_enrollments_paginated as $a)
        <div class="col-lg-4 col-md-6 col-md-6 mb-4">
            <div class="contentCard contentCard--watch-course">
                <div class="contentCard-top">
                    <a href="#"><img src="{{asset('uploads/courses/'.$a->course?->image)}}"
                            alt="images" class="img-fluid" /></a>
                </div>
                <div class="contentCard-bottom">
                    <h5>
                        <a href="{{localeRoute('frontend.courses.show', $a->course?->id)}}"
                            class="font-title--card">{{$a->course?->title}}</a>
                    </h5>
                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                        <a href="{{localeRoute('instructor.show', $a->course?->instructor->id)}}"
                            class="contentCard-user d-flex align-items-center">
                            <img src="{{asset('uploads/users/'.$a->course?->instructor?->image)}}"
                                alt="client-image" class="rounded-circle" height="34" width="34" />
                            <p class="font-para--md">{{$a->course?->instructor?->name}}</p>
                        </a>
                        <div class="contentCard-course--status d-flex align-items-center">
                            <span class="percentage">{{ $a->progress_percentage ?? 0 }}%</span>
                            <p>Progress</p>
                        </div>
                    </div>

                    <a class="button button-md button--primary-outline w-100 my-3"
                       href="{{localeRoute('frontend.courses.show', $a->course?->id)}}">
                        Continue Course ({{ $a->progress_percentage ?? 0 }}%)
                    </a>
                    <div class="contentCard-watch--progress">
                        <span class="percentage" style="width: {{ $a->progress_percentage ?? 0 }}%;"></span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5">
            <div class="col-md-6 col-12 mx-auto text-center">
                <h5 class="font-title--sm">No Active Courses</h5>
                <p class="my-4 font-para--lg">
                    You don't have any active courses at the moment.
                </p>
                <a href="{{localeRoute('searchCourse')}}" class="button button-md button--primary">
                    Browse Courses
                </a>
            </div>
        </div>
        @endforelse

        @if($active_enrollments_paginated && $active_enrollments_paginated->count() > 0)
        <div class="col-lg-12 mt-lg-5">
            <div class="pagination justify-content-center pb-0">
                <div class="pagination-group">
                    {{ $active_enrollments_paginated->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Completed Courses --}}
<div class="tab-pane fade" id="nav-completedcourses" role="tabpanel"
    aria-labelledby="nav-completedcourses-tab">
    <div class="row">
        @forelse ($completed_enrollments_paginated as $c)
        <div class="col-lg-4 col-md-6 col-md-6 mb-4">
            <div class="contentCard contentCard--watch-course">
                <div class="contentCard-top">
                    <a href="#"><img src="{{asset('uploads/courses/'.$c->course?->image)}}"
                            alt="images" class="img-fluid" /></a>
                </div>
                <div class="contentCard-bottom">
                    <h5>
                        <a href="{{localeRoute('frontend.courses.show', $c->course?->id)}}"
                            class="font-title--card">{{$c->course?->title}}</a>
                    </h5>
                    <div class="contentCard-info d-flex align-items-center justify-content-between">
                        <a href="{{localeRoute('instructor.show', $c->course?->instructor->id)}}"
                            class="contentCard-user d-flex align-items-center">
                            <img src="{{asset('uploads/users/'.$c->course?->instructor?->image)}}"
                                alt="client-image" class="rounded-circle" height="34" width="34" />
                            <p class="font-para--md">{{$c->course?->instructor?->name}}</p>
                        </a>
                        <div class="contentCard-course--status d-flex align-items-center">
                            <span class="percentage text-success">100%</span>
                            <p>Completed</p>
                        </div>
                    </div>
                    <a class="button button-md button--success w-100 my-3"
                       href="{{localeRoute('frontend.courses.show', $c->course?->id)}}">
                        Review Course
                    </a>
                    <div class="contentCard-watch--progress">
                        <span class="percentage bg-success" style="width: 100%;"></span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5">
            <div class="col-md-6 col-12 mx-auto text-center">
                <h5 class="font-title--sm">No Completed Courses Yet</h5>
                <p class="my-4 font-para--lg">
                    Complete your enrolled courses to see them here.
                </p>
                <a href="{{localeRoute('searchCourse')}}" class="button button-md button--primary">
                    Continue Learning
                </a>
            </div>
        </div>
        @endforelse

        @if($completed_enrollments_paginated && $completed_enrollments_paginated->count() > 0)
        <div class="col-lg-12 mt-lg-5">
            <div class="pagination justify-content-center pb-0">
                <div class="pagination-group">
                    {{ $completed_enrollments_paginated->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
                {{-- Purchase History --}}
                <div class="tab-pane fade" id="nav-purchase" role="tabpanel" aria-labelledby="nav-purchase-tab">
                    @forelse ($enrollment as $e)
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="purchase-area">
                                <div class="d-flex align-items-lg-center align-items-start flex-column flex-lg-row">
                                    <div class="purchase-area-items">
                                        <div class="purchase-area-items-start d-flex align-items-lg-center flex-column flex-lg-row">
                                            <div class="image">
                                                <a href="{{ localeRoute('frontend.courses.show', $e->course?->id) }}">
                                                    <img src="{{ asset('uploads/courses/'.($e->course?->image ?? 'default.jpg')) }}"
                                                         alt="{{ $e->course?->title }}"
                                                         style="width: 80px; height: 60px; object-fit: cover;"
                                                         onerror="this.src='{{ asset('uploads/courses/default.jpg') }}'" />
                                                </a>
                                            </div>
                                            <div class="text d-flex flex-column flex-lg-row">
                                                <div class="text-main">
                                                    <h6>
                                                        <a href="{{ localeRoute('frontend.courses.show', $e->course?->id) }}">
                                                            {{ $e->course?->title ?? 'Unknown Course' }}
                                                        </a>
                                                    </h6>
                                                    <p>By
                                                        <a href="{{ localeRoute('instructor.show', $e->course?->instructor->id) }}">
                                                            {{ $e->course?->instructor->name ?? 'Unknown Instructor' }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="purchase-area-items-end">
                                        <p>{{ $e->enrollment_date->format('M d, Y') }}</p>
                                        <dl class="row">
                                            <dt class="col-sm-4">Amount Paid</dt>
                                            <dd class="col-sm-8">
                                                @if($e->amount_paid && $e->amount_paid > 0)
                                                    <strong class="text-success">${{ number_format($e->amount_paid, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">Free</span>
                                                @endif
                                            </dd>
                                            <dt class="col-sm-4">Payment Status</dt>
                                            <dd class="col-sm-8">
                                                {!! $e->payment_status_badge !!}
                                            </dd>
                                            <dt class="col-sm-4">Payment Method</dt>
                                            <dd class="col-sm-8">
                                                @if($e->payment_method)
                                                    <span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $e->payment_method)) }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </dd>
                                            @if($e->transaction_id)
                                            <dt class="col-sm-4">Transaction ID</dt>
                                            <dd class="col-sm-8">
                                                <small class="text-muted">{{ $e->transaction_id }}</small>
                                            </dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <div class="empty-state">
                                <i class="las la-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5>No Purchase History Found</h5>
                                <p class="text-muted">You haven't purchased any courses yet.</p>
                                <a href="{{ localeRoute('frontend.courses') }}" class="btn btn-primary mt-2">
                                    <i class="las la-shopping-cart me-2"></i>Browse Courses
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforelse

                    @if($enrollment && $enrollment->count() > 0)
                    <div class="row mt-lg-5 mt-4">
                        <div class="col-lg-12 text-center">
                            <p style="color: #42414b !important; font-size: 18px !important;">
                                Yay! You have seen all your purchase history.
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <!-- SVG content remains the same -->
                                </svg>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<!-- Datatable -->
<link href="{{asset('public/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">

<style>
.students-info-intro-end {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    max-width: 100%;
}

.enrolled-courses-text p,
.completed-courses-text p {
    word-break: break-word;
    white-space: normal;
    overflow-wrap: break-word;
    max-width: 100%;
    font-size: 14px;
    text-align: left;
}

.enrolled-courses,
.completed-courses {
    max-width: 220px;
    font-size: 14px;
}

.students-info-intro-end,
.students-info-intro-end * {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.3;
}

/* Стили для статусов курсов */
.button--success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.button--success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.bg-success {
    background-color: #28a745 !important;
}

.text-success {
    color: #28a745 !important;
}

/* Стили для Purchase History */
.purchase-area-items-end dl {
    margin-bottom: 0;
}

.purchase-area-items-end dt {
    font-weight: 600;
    color: #495057;
    font-size: 0.85rem;
}

.purchase-area-items-end dd {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

/* Стили для статусов платежей */
.badge-warning {
    color: #212529;
}

.badge-success {
    color: #28a745;
}

.badge-danger {
    color: #dc3545;
}

.badge-info {
    color: #17a2b8;
}

.badge-light {
    color: #17a2b8;
}

/* Улучшаем внешний вид purchase area */
.purchase-area {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.purchase-area-items-start .image img {
    border-radius: 6px;
}

.text-main h6 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.text-main p {
    margin-bottom: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .purchase-area {
        padding: 1rem;
    }

    .purchase-area-items-end {
        margin-top: 1rem;
    }

    .purchase-area-items-end dl .row {
        display: block;
    }

    .purchase-area-items-end dt,
    .purchase-area-items-end dd {
        display: inline-block;
        width: auto;
    }

    .purchase-area-items-end dt {
        min-width: 120px;
    }
}

/* Улучшаем отображение текста */
.purchase-area-items-end p {
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

/* Выделяем важную информацию */
.text-success {
    font-weight: 600;
}

.text-muted {
    font-style: italic;
}
</style>
@endpush
