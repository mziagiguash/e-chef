@extends('backend.layouts.app')
@section('title', 'Course Lesson List')

@push('styles')
<link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course Lesson List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('lesson.index')}}">Course Lessons</a></li>
                    <li class="breadcrumb-item active">All Course Lesson</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        {{-- Language Tabs --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="lessonLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">{{ $localeName }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- List View --}}
        <div class="row tab-content">
            <div id="list-view" class="tab-pane fade active show col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Course Lessons List</h4>
                        <a href="{{localeRoute('lesson.create')}}" class="btn btn-primary">+ Add new</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lessons as $lesson)
                                        <tr class="lesson-row"
                                            data-titles='@json($lesson->title)'
                                            data-courses='@json((array) ($lesson->course?->title ?? []))'>
                                            <td>{{ $lesson->id }}</td>
                                            <td class="lesson-title">{{ $lesson->display_title ?? 'No Title' }}</td>
                                            <td class="lesson-course">{{ $lesson->localized_title ?? 'No Course' }}</td>
                                            <td>
                                                <a href="{{localeRoute('lesson.edit', encryptor('encrypt',$lesson->id))}}" class="btn btn-sm btn-primary" title="Edit"><i class="la la-pencil"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-danger" title="Delete" onclick="$('#form{{$lesson->id}}').submit()"><i class="la la-trash-o"></i></a>
                                                <form id="form{{$lesson->id}}" action="{{localeRoute('lesson.destroy', encryptor('encrypt',$lesson->id))}}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No Course Lesson Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid View --}}
            <div id="grid-view" class="tab-pane fade col-lg-12">
                <div class="row">
                    @forelse ($lessons as $lesson)
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <div class="card card-profile h-100 lesson-card"
                                 data-titles='@json($lesson->title)'
                                 data-courses='@json((array) ($lesson->course?->title ?? []))'>
                                <div class="card-body text-center">
                                    <h5 class="card-title lesson-title">{{ $lesson->display_title ?? 'No Title' }}</h5>
                                    <p class="mb-1"><strong>Course:</strong>
                                        <span class="lesson-course">{{ $lesson->localized_title ?? 'No Course' }}</span>
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{localeRoute('lesson.edit', encryptor('encrypt',$lesson->id))}}" class="btn btn-sm btn-primary" title="Edit"><i class="la la-pencil"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger" title="Delete" onclick="$('#form{{$lesson->id}}').submit()"><i class="la la-trash-o"></i></a>
                                        <form id="form{{$lesson->id}}" action="{{localeRoute('lesson.destroy', encryptor('encrypt',$lesson->id))}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <p>No Course Lesson Found</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/plugins-init/datatables.init.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const savedLocale = localStorage.getItem('lessons_lang') || '{{ app()->getLocale() }}';

    function updateLessonLanguage(locale) {
        document.querySelectorAll('.lesson-row, .lesson-card').forEach(el => {
            const titles = JSON.parse(el.dataset.titles);
            const courses = JSON.parse(el.dataset.courses);

            const titleText = titles[locale] ?? Object.values(titles)[0] ?? 'No Title';
            const courseText = courses[locale] ?? Object.values(courses)[0] ?? 'No Course';

            el.querySelectorAll('.lesson-title').forEach(t => t.textContent = titleText);
            el.querySelectorAll('.lesson-course').forEach(c => c.textContent = courseText);
        });
    }

    // Активный таб
    document.querySelectorAll('#lessonLangTabs .lang-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.locale === savedLocale);

        tab.addEventListener('click', function(e){
            e.preventDefault();
            const locale = this.dataset.locale;
            localStorage.setItem('lessons_lang', locale);

            // Переключаем активный таб
            document.querySelectorAll('#lessonLangTabs .lang-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Обновляем тексты
            updateLessonLanguage(locale);
        });
    });

    // Применяем язык при загрузке
    updateLessonLanguage(savedLocale);
});
</script>
@endpush
