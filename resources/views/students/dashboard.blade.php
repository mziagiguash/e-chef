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
        <button class="nav-link position-relative" id="nav-notifications-tab" data-bs-toggle="tab"
            data-bs-target="#nav-notifications" type="button" role="tab" aria-controls="nav-notifications"
            aria-selected="false">
            <i class="las la-bell me-1"></i>Notifications
            @if($unread_notifications_count > 0)
                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                    {{ $unread_notifications_count }}
                </span>
            @else
                <span class="badge bg-secondary position-absolute top-0 start-100 translate-middle" style="display: none;">
                    0
                </span>
            @endif
        </button>

        {{-- 🔴 ДОБАВЛЕНО: Кнопка для сообщений --}}
<a href="{{ localeRoute('student.my-messages') }}" class="nav-link position-relative">
    <i class="las la-envelope me-1"></i>My Messages
    @php
        $unreadMessagesCount = \App\Models\ContactMessage::where('sender_id', auth()->id())
            ->where('status', '!=', 'resolved')
            ->count();
    @endphp
    @if($unreadMessagesCount > 0)
        <span class="badge bg-warning position-absolute top-0 start-100 translate-middle">
            {{ $unreadMessagesCount }}
        </span>
    @else
        <span class="badge bg-secondary position-absolute top-0 start-100 translate-middle" style="display: none;">
            0
        </span>
    @endif
</a>

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
    <div class="notifications-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="font-title--card">My Notifications</h5>
            @if($unread_notifications_count > 0)
                <form method="POST" action="{{ route('student.notifications.read-all') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="las la-check-double me-1"></i>Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <div class="notifications-list">
            @forelse($notifications as $notification)
                <div class="notification-item card mb-3 {{ $notification->is_read ? 'bg-light' : '' }}"
                     data-notification-id="{{ $notification->id }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    @if(!$notification->is_read)
                                        <span class="notification-badge badge-unread me-2">New</span>
                                    @else
                                        <span class="notification-badge badge-read me-2">Read</span>
                                    @endif
                                    <h6 class="notification-title mb-0 {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                        {{ $notification->title }}
                                    </h6>
                                </div>
                                <p class="notification-message mb-2 {{ $notification->is_read ? 'text-muted' : '' }}">
                                    {{ $notification->message }}
                                </p>

                                {{-- Упрощенный показ контактных уведомлений --}}
                                @if($notification->type === 'contact_message_replied' && $notification->contact_message_id)
                                    @php
                                        $contactMessage = \App\Models\ContactMessage::find($notification->contact_message_id);
                                    @endphp

                                    @if($contactMessage)
                                        <div class="contact-notification mt-3 p-3 bg-light rounded">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="las la-comments text-primary me-2"></i>
                                                <strong class="text-primary">New response to your message</strong>
                                            </div>
                                            <p class="mb-2 small">
                                                <strong>Subject:</strong> {{ $contactMessage->subject }}
                                            </p>
                                            @if($contactMessage->admin_notes)
                                                <div class="admin-preview bg-white p-2 rounded border">
                                                    <strong class="text-success small">
                                                        <i class="las la-reply me-1"></i>Admin Response:
                                                    </strong>
                                                    <p class="mb-0 small text-muted">
                                                        {{ Str::limit($contactMessage->admin_notes, 100) }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="mt-2">
                                                <a href="{{ localeRoute('student.my-messages') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="las la-inbox me-1"></i>View Conversation
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="notification-meta mt-2">
                                    <i class="las la-clock me-1"></i>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="notification-actions ms-3">
                                @if(!$notification->is_read)
                                    <form method="POST" action="{{ route('student.notifications.read', $notification->id) }}" class="d-inline mark-as-read-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                title="Mark as Read" data-notification-id="{{ $notification->id }}">
                                            <i class="las la-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge badge-read bg-secondary">
                                        <i class="las la-check me-1"></i>Read
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="notifications-empty-state text-center py-5">
                    <i class="las la-bell-slash display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Notifications</h5>
                    <p class="text-muted">You don't have any notifications yet.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->count() > 10)
            <div class="mt-4 text-center">
                <p class="text-muted">Showing latest 10 notifications</p>
                <a href="{{ localeRoute('student.notifications') }}" class="btn btn-sm btn-outline-primary">
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

/* Стили для счетчика уведомлений в навигации */
.students-info-intro__nav .nav-link {
    position: relative;
    padding: 0.75rem 1.5rem;
    border: none;
    background: none;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 0;
    border-bottom: 3px solid transparent;
}

.students-info-intro__nav .nav-link:hover {
    color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
}

.students-info-intro__nav .nav-link.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
}

/* Стили для бейджа счетчика */
.students-info-intro__nav .nav-link .badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: 2px solid #fff;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.2rem 0.4rem;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    transform: scale(1);
    transition: transform 0.2s ease;
}

.students-info-intro__nav .nav-link:hover .badge {
    transform: scale(1.1);
}
.conversation-history {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.message-bubble {
    border-left: 4px solid #007bff;
    position: relative;
}

.message-bubble.bg-primary {
    border-left-color: #0056b3;
}

.message-bubble.bg-light {
    border-left-color: #6c757d;
}
.message-bubble.bg-light .admin-response {
    background: rgba(0,123,255,0.1);
}

.continue-conversation {
    border: 1px solid #dee2e6;
    background: #f8f9fa !important;
}

.admin-response {
    font-size: 0.9em;
    background: rgba(255,255,255,0.1);
    padding: 8px;
    border-radius: 4px;
    margin-top: 8px;
}

.message-bubble.bg-light .admin-response {
    background: rgba(0,123,255,0.1);
}

.notification-badge {
    font-size: 0.7em;
    padding: 0.25em 0.6em;
}

.badge-unread {
    background-color: #dc3545;
}

.badge-read {
    background-color: #6c757d;
}
/* Анимация пульсации для привлечения внимания */
@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.students-info-intro__nav .nav-link .badge {
    animation: pulse 2s infinite;
}

/* Для прочитанных/нулевых уведомлений - скрываем бейдж */
.students-info-intro__nav .nav-link .badge.bg-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    animation: none;
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

/* Стили для бейджей уведомлений */
.notification-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1;
    white-space: nowrap;
    display: inline-block;
    text-align: center;
    min-width: 60px;
}

/* Статусы для уведомлений */
.badge-new {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.badge-unread {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.badge-read {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.badge-priority {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: black;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

/* Стили для кнопок действий */
.notification-actions .btn {
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.notification-actions .btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    transform: translateY(-1px);
}

.notification-actions .btn-outline-success:active {
    transform: translateY(0);
}

/* Стили для карточек уведомлений */
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    border-radius: 8px;
}

.notification-item:not(.bg-light) {
    border-left-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.notification-item.bg-light {
    border-left-color: #6c757d;
    opacity: 0.8;
}

/* Заголовки уведомлений */
.notification-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.notification-message {
    color: #495057;
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

.notification-meta {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Иконки в уведомлениях */
.notification-icon {
    width: 16px;
    height: 16px;
    margin-right: 0.5rem;
    opacity: 0.7;
}

/* Стили для контактных ответов */
.contact-response {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.contact-response strong {
    color: #495057;
    font-weight: 600;
}
/* Стили для активного таба уведомлений */
#nav-notifications-tab {
    position: relative;
}

#nav-notifications-tab .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: 2px solid white;
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .notification-badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
        min-width: 55px;
    }

    .notification-actions .btn {
        width: 28px;
        height: 28px;
    }

    .notification-item {
        margin-bottom: 1rem;
    }

    .notification-title {
        font-size: 0.95rem;
    }

    .notification-message {
        font-size: 0.9rem;
    }
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item {
    animation: fadeInUp 0.3s ease;
}

/* Стили для пустого состояния */
.notifications-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.notifications-empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

/* Стили для счетчика уведомлений в табе */
.nav-link .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    margin-left: 0.5rem;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Стили для прогресс-бара (если используется) */
.notification-progress {
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.notification-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.text-center.py-5 i {
    opacity: 0.5;
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
@push('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function() {
    // AJAX для отправки продолжения диалога
    document.querySelectorAll('.continue-dialog-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            const formAction = this.action;

            console.log('🚀 Form submission started');
            console.log('📤 Action:', formAction);
            console.log('📝 Data:', Object.fromEntries(formData));

            // Показываем индикатор загрузки
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Sending...';
            submitBtn.disabled = true;

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('📨 Response status:', response.status, response.statusText);

                if (response.status === 422) {
                    // Validation errors
                    return response.json().then(data => {
                        throw new Error('Validation error: ' + JSON.stringify(data.errors));
                    });
                }

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('❌ Server response:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('✅ Success response:', data);

                if (data.success) {
                    // Показываем успешное сообщение
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                    alert.innerHTML = `
                        <i class="las la-check-circle me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.parentNode.insertBefore(alert, form);

                    // Очищаем текстовое поле
                    form.querySelector('textarea').value = '';

                    // Обновляем страницу через 2 секунды
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('💥 Full error:', error);

                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    <i class="las la-exclamation-circle me-2"></i>
                    <strong>Error:</strong> ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                form.parentNode.insertBefore(alert, form);
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });
});
$(document).ready(function() {
    // Обработка отметки одного уведомления
    $('.mark-as-read-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = form.find('button');
        var notificationId = button.data('notification-id');

        // Блокируем кнопку
        button.prop('disabled', true).html('<i class="las la-spinner la-spin"></i>');

        // Правильный URL
        $.ajax({
            url: "{{ url('student/notification') }}/" + notificationId + "/read",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                    button.prop('disabled', false).html('<i class="las la-check"></i>');
                }
            },
            error: function(xhr) {
                alert('Request failed');
                button.prop('disabled', false).html('<i class="las la-check"></i>');
            }
        });
    });

    // Обработка "Mark All as Read"
    $('form[action*="read-all"]').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = form.find('button');

        // Блокируем кнопку
        button.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i>Processing...');

        // Правильный URL
        $.ajax({
            url: "{{ url('student/notifications/read-all') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                    button.prop('disabled', false).html('<i class="las la-check-double me-1"></i>Mark All as Read');
                }
            },
            error: function(xhr) {
                alert('Request failed');
                button.prop('disabled', false).html('<i class="las la-check-double me-1"></i>Mark All as Read');
            }
        });
    });
});
</script>
@endpush
