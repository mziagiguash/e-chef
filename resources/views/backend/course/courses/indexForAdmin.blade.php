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
                    <h4>Course List for Admin</h4>
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

        <div class="row mb-3">
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
                <a href="{{ localeRoute('course.create') }}" class="btn btn-primary">+ Add new</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Courses List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{ __('Image') }}</th>
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
                                            // Проверяем существование изображения
                                            $imagePath = $d->image ? 'uploads/courses/'.$d->image : 'uploads/courses/default-course.jpg';
                                            $imageUrl = file_exists(public_path($imagePath)) ? asset($imagePath) : asset('uploads/courses/default-course.jpg');
                                        @endphp
                                        <tr>
                                            <td>
                                                <img class="img-fluid rounded" width="80" src="{{ $imageUrl }}" alt="Course Image">
                                            </td>
                                            <td>
                                                @foreach ($locales as $localeCode => $localeName)
                                                    <span class="course-title lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                        {{ $d->translations->firstWhere('locale', $localeCode)->title ?? 'No Title' }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($locales as $localeCode => $localeName)
                                                    <span class="instructor-name lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                        @if ($d->instructor)
                                                            {{ $d->instructor->translations->firstWhere('locale', $localeCode)->name ?? 'No Instructor' }}
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
                                                        @if ($d->courseCategory)
                                                            {{ $d->courseCategory->translations->firstWhere('locale', $localeCode)->category_name ?? 'No Category' }}
                                                        @else
                                                            No Category
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <strong>{{ $d->price ? $currentCurrency . number_format($d->price * $currencyRate, 2) : 'Free' }}</strong>
                                            </td>
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
                                                <div class="d-flex">
                                                    <a href="{{ localeRoute('course.edit', encryptor('encrypt', $d->id)) }}"
                                                       class="btn btn-sm btn-primary me-2" title="Edit">
                                                        <i class="la la-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                                       title="Delete" onclick="if(confirm('Are you sure?')) {$('#form{{$d->id}}').submit()}">
                                                       <i class="la la-trash-o"></i>
                                                    </a>
                                                    <form id="form{{$d->id}}" action="{{ localeRoute('course.destroy', encryptor('encrypt',$d->id)) }}" method="post" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No Courses Found</td>
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
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const initialLocale = localStorage.getItem('courses_lang') || '{{ $appLocale }}';

        function setActiveLocale(locale) {
            // Обновляем активную вкладку
            document.querySelectorAll('.lang-tab').forEach(tab => {
                tab.classList.toggle('active', tab.dataset.locale === locale);
            });

            // Обновляем отображение названий курсов
            document.querySelectorAll('.course-title').forEach(el => {
                el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
            });

            // Обновляем отображение имен инструкторов
            document.querySelectorAll('.instructor-name').forEach(el => {
                el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
            });

            // Обновляем отображение названий категорий
            document.querySelectorAll('.category-name').forEach(el => {
                el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
            });
        }

        // Обработчик клика по вкладкам
        document.querySelectorAll('.lang-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const locale = this.dataset.locale;
                localStorage.setItem('courses_lang', locale);
                setActiveLocale(locale);
            });
        });

        // Устанавливаем начальный язык
        setActiveLocale(initialLocale);
    });
</script>
@endpush
