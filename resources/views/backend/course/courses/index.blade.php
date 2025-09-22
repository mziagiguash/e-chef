@extends('backend.layouts.app')
@section('title', 'Admin Course List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course Management</h4>
                    <p class="mb-0">Manage all courses in the system</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ url('admin/courses') }}">Courses</a></li>
                    <li class="breadcrumb-item active">All Courses</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
            // Добавляем переменные по умолчанию если они не определены
            $currentCurrency = $currentCurrency ?? '$';
            $currencyRate = $currencyRate ?? 1;
        @endphp

        <div class="row mb-4">
            <div class="col-md-6">
                <ul class="nav nav-tabs" id="courseLangTabs" role="tablist">
                    @foreach ($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">{{ $localeName }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-primary view-toggle active" data-view="table">
                        <i class="la la-table"></i> Table View
                    </button>
                    <button type="button" class="btn btn-outline-primary view-toggle" data-view="grid">
                        <i class="la la-th-large"></i> Grid View
                    </button>
                </div>
                <a href="{{ url('admin/courses/create') }}" class="btn btn-primary">
                    <i class="la la-plus"></i> Add New Course
                </a>
            </div>
        </div>

        <!-- Table View -->
        <div class="row table-view">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Courses List</h4>
                        <div class="ms-auto">
                            <span class="badge bg-primary">Total: {{ $courses->total() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="coursesTable" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="80px">{{ __('Image') }}</th>
                                        <th>{{ __('Course Name') }}</th>
                                        <th>{{ __('Instructor') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Lessons') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th width="120px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($courses as $course)
                                        @php
                                            $imagePath = $course->image ? 'uploads/courses/'.$course->image : 'uploads/courses/default-course.jpg';
                                            $imageUrl = file_exists(public_path($imagePath)) ? asset($imagePath) : asset('uploads/courses/default-course.jpg');
                                        @endphp
                                        <tr>
                                            <td>
                                                <img class="img-fluid rounded" width="60" src="{{ $imageUrl }}" alt="Course Image" style="object-fit: cover;">
                                            </td>
                                            <td>
                                                @foreach ($locales as $localeCode => $localeName)
                                                    <span class="course-title lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                        {{ $course->translations->firstWhere('locale', $localeCode)->title ?? 'No Title' }}
                                                    </span>
                                                @endforeach
                                                <br>
                                                <small class="text-muted">Code: {{ $course->course_code }}</small>
                                            </td>
                                            <td>
                                                @foreach ($locales as $localeCode => $localeName)
                                                    <span class="instructor-name lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                        @if ($course->instructor)
                                                            {{ $course->instructor->translations->firstWhere('locale', $localeCode)->name ?? 'No Instructor' }}
                                                        @else
                                                            No Instructor
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($locales as $localeCode => $localeName)
                                                    <span class="category-name lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                        @if ($course->courseCategory)
                                                            {{ $course->courseCategory->translations->firstWhere('locale', $localeCode)->category_name ?? 'No Category' }}
                                                        @else
                                                            No Category
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($course->courseType === 'free')
                                                    <span class="badge bg-success">Free</span>
                                                @elseif($course->courseType === 'paid')
                                                    <strong>{{ $currentCurrency }}{{ number_format($course->coursePrice * $currencyRate, 2) }}</strong>
                                                    @if($course->courseOldPrice > 0)
                                                        <br><small class="text-danger text-decoration-line-through">
                                                            {{ $currentCurrency }}{{ number_format($course->courseOldPrice * $currencyRate, 2) }}
                                                        </small>
                                                    @endif
                                                @elseif($course->courseType === 'subscription')
                                                    <strong>{{ $currentCurrency }}{{ number_format($course->subscription_price * $currencyRate, 2) }}/month</strong>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $course->lessons_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @if($course->status == 0) bg-warning
                                                    @elseif($course->status == 1) bg-danger
                                                    @elseif($course->status == 2) bg-success
                                                    @endif">
                                                    @if($course->status == 0) {{ __('Pending') }}
                                                    @elseif($course->status == 1) {{ __('Inactive') }}
                                                    @elseif($course->status == 2) {{ __('Active') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ url('admin/courses/' . encryptor('encrypt', $course->id)) }}"
                                                       class="btn btn-sm btn-info me-1" title="View Details">
                                                        <i class="la la-eye"></i>
                                                    </a>
                                                    <a href="{{ url('admin/courses/' . encryptor('encrypt', $course->id) . '/edit') }}"
                                                       class="btn btn-sm btn-primary me-1" title="Edit">
                                                        <i class="la la-pencil"></i>
                                                    </a>
                                                    <form action="{{ url('admin/courses/' . encryptor('encrypt', $course->id)) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete" onclick="return confirm('Are you sure you want to delete this course?')">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="alert alert-info">
                                                    <i class="la la-info-circle"></i> No courses found.
                                                    <a href="{{ url('admin/courses/create') }}" class="alert-link">Create your first course</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($courses->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} entries
                            </div>
                            <div>
                                {{ $courses->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div class="row grid-view" style="display: none;">
            @forelse ($courses as $course)
                @php
                    $translations = [];
                    foreach($locales as $localeCode => $localeName) {
                        $translations[$localeCode] = [
                            'title' => addslashes($course->translations->firstWhere('locale', $localeCode)->title ?? 'No Title'),
                            'category' => addslashes($course->courseCategory?->translations->firstWhere('locale', $localeCode)->category_name ?? 'No Category'),
                            'instructor' => addslashes($course->instructor?->translations->firstWhere('locale', $localeCode)->name ?? 'No Instructor'),
                        ];
                    }
                    $imagePath = $course->image ? 'uploads/courses/'.$course->image : 'uploads/courses/default-course.jpg';
                    $imageUrl = file_exists(public_path($imagePath)) ? asset($imagePath) : asset('uploads/courses/default-course.jpg');
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card course-card h-100" data-translations="{{ json_encode($translations) }}">
                        <div class="card-header position-relative">
                            <img src="{{ $imageUrl }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Course Image">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge
                                    @if($course->courseType === 'free') bg-success
                                    @elseif($course->courseType === 'paid') bg-primary
                                    @elseif($course->courseType === 'subscription') bg-info
                                    @endif">
                                    {{ ucfirst($course->courseType) }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <h6 class="course-title mb-2">{{ $course->translations->firstWhere('locale', $appLocale)->title ?? 'No Title' }}</h6>

                            <div class="course-meta mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Instructor:</small>
                                    <strong class="course-instructor text-end">
                                        {{ $course->instructor?->translations->firstWhere('locale', $appLocale)->name ?? 'No Instructor' }}
                                    </strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Category:</small>
                                    <strong class="course-category text-end">
                                        {{ $course->courseCategory?->translations->firstWhere('locale', $appLocale)->category_name ?? 'No Category' }}
                                    </strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Lessons:</small>
                                    <span class="badge bg-info">{{ $course->lessons_count }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Price:</small>
                                    <strong>
                                        @if($course->courseType === 'free')
                                            Free
                                        @elseif($course->courseType === 'paid')
                                            {{ $currentCurrency }}{{ number_format($course->coursePrice * $currencyRate, 2) }}
                                        @elseif($course->courseType === 'subscription')
                                            {{ $currentCurrency }}{{ number_format($course->subscription_price * $currencyRate, 2) }}/mo
                                        @endif
                                    </strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Status:</small>
                                    <span class="badge
                                        @if($course->status == 0) bg-warning
                                        @elseif($course->status == 1) bg-danger
                                        @elseif($course->status == 2) bg-success
                                        @endif">
                                        @if($course->status == 0) Pending
                                        @elseif($course->status == 1) Inactive
                                        @elseif($course->status == 2) Active
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ url('admin/courses/' . encryptor('encrypt', $course->id)) }}"
                                   class="btn btn-sm btn-outline-info" title="View Details">
                                    <i class="la la-eye"></i>
                                </a>
                                <a href="{{ url('admin/courses/' . encryptor('encrypt', $course->id) . '/edit') }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="la la-pencil"></i>
                                </a>
                                <form action="{{ url('admin/courses/' . encryptor('encrypt', $course->id)) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="la la-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="la la-book la-3x text-muted mb-3"></i>
                            <h5>No Courses Found</h5>
                            <p class="text-muted">Get started by creating your first course</p>
                            <a href="{{ url('admin/courses/create') }}" class="btn btn-primary">
                                <i class="la la-plus"></i> Create First Course
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Grid View Pagination -->
        @if($courses->hasPages())
        <div class="row grid-view" style="display: none;">
            <div class="col-12">
                <div class="d-flex justify-content-center mt-3">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Обработка переключения между табличным и сеточным представлением
        $('.view-toggle').click(function() {
            const view = $(this).data('view');
            $('.view-toggle').removeClass('active');
            $(this).addClass('active');

            if (view === 'table') {
                $('.table-view').show();
                $('.grid-view').hide();
            } else {
                $('.table-view').hide();
                $('.grid-view').show();
            }
        });

        // Обработка переключения языков
        $('.lang-tab').click(function(e) {
            e.preventDefault();
            const locale = $(this).data('locale');

            $('.lang-tab').removeClass('active');
            $(this).addClass('active');

            // Обновляем тексты в табличном представлении
            $('.course-title, .instructor-name, .category-name').hide();
            $(`.lang-${locale}`).show();

            // Обновляем тексты в сеточном представлении
            $('.course-card').each(function() {
                const translations = $(this).data('translations');
                $(this).find('.course-title').text(translations[locale].title);
                $(this).find('.course-instructor').text(translations[locale].instructor);
                $(this).find('.course-category').text(translations[locale].category);
            });
        });
    });
</script>
@endpush
