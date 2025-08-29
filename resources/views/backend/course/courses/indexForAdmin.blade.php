@extends('backend.layouts.app')
@section('title', 'Admin Course List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('course.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('course.index') }}">All Course</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="courseLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">{{ $localeName }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Course List</h4>
                        <a href="{{ localeRoute('course.create') }}" class="btn btn-primary">+ Add new</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('Course Name') }}</th>
                                        <th>{{ __('Instructor') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
@forelse ($courses as $d)
    @php
        $locale = app()->getLocale();
        $courseTitle = $d->translations->where('locale', $locale)->first()?->title ?? 'No Title';
        $instructorName = $d->instructor?->getTranslation('name') ?? 'No Instructor';
        $categoryName = $d->courseCategory?->getTranslation('category_name') ?? 'No Category';
    @endphp
    <tr>
        <td>
            <img class="img-fluid" width="100" src="{{ asset('uploads/courses/'.$d->image) }}" alt="">
        </td>
        <td><strong>{{ $courseTitle }}</strong></td>
        <td><strong>{{ $instructorName }}</strong></td>
        <td><strong>{{ $categoryName }}</strong></td>
        <td><strong>{{ $d->price ? $currentCurrency . number_format($d->price * $currencyRate, 2) : 'Free' }}</strong></td>
        <td>
            <span class="badge
                @if($d->status == 0) badge-warning
                @elseif($d->status == 1) badge-danger
                @elseif($d->status == 2) badge-success
                @endif">
                @if($d->status == 0) {{ __('Pending') }}
                @elseif($d->status == 1) {{ __('Inactive') }}
                @elseif($d->status == 2) {{ __('Active') }}
                @endif
            </span>
        </td>
        <td>
            <a href="{{ localeRoute('course.edit', encryptor('encrypt', $d->id)) }}"
               class="btn btn-sm btn-primary" title="Edit">
                <i class="la la-pencil"></i>
            </a>
            <a href="javascript:void(0);" class="btn btn-sm btn-danger"
               title="Delete" onclick="$('#form{{$d->id}}').submit()">
               <i class="la la-trash-o"></i>
            </a>
            <form id="form{{$d->id}}" action="{{ localeRoute('course.destroy', encryptor('encrypt',$d->id)) }}" method="post">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <th colspan="7" class="text-center">No Courses Found</th>
    </tr>
@endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.lang-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const locale = this.dataset.locale;
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Переключение языков — перезагрузить страницу с параметром locale
            const url = new URL(window.location.href);
            url.searchParams.set('locale', locale);
            window.location.href = url.toString();
        });
    });
});
</script>

<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>
@endpush
