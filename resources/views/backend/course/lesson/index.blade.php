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
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('lesson.index')}}">Course Lessons</a></li>
                    <li class="breadcrumb-item active">All Course Lesson</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        {{-- Language Tabs and Add Button --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <ul class="nav nav-tabs" id="lessonLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">{{ $localeName }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{localeRoute('lesson.create')}}" class="btn btn-primary">+ Add new</a>
            </div>
        </div>

        {{-- List View --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="row tab-content">
                    <div id="list-view" class="tab-pane fade active show col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">All Course Lessons List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Course</th>
                                                <th>Video</th> {{-- Новая колонка --}}
                                                <th>Materials</th>
                                                <th>Quiz</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($lessons as $lesson)
                                                <tr>
                                                    <td><strong>{{ $lesson->id }}</strong></td>
                                                    <td>
                                                        @foreach ($locales as $localeCode => $localeName)
                                                            @php
                                                                $title = $lesson->getTranslation($localeCode, 'title') ?? 'Lesson #' . $lesson->id;
                                                            @endphp
                                                            <span class="lesson-title lang-{{ $localeCode }}"
                                                                  style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                                {{ $title }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach ($locales as $localeCode => $localeName)
                                                            @php
                                                                $courseTitle = $lesson->course->getTranslation($localeCode, 'title') ?? 'No Course';
                                                            @endphp
                                                            <span class="lesson-course lang-{{ $localeCode }}"
                                                                  style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                                {{ $courseTitle }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    {{-- В секции tbody --}}
<td>
    @if($lesson->video_url)
        @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
            <span class="badge badge-danger">
                <i class="fab fa-youtube"></i> YouTube
            </span>
        @else
            <span class="badge badge-primary">
                <i class="fas fa-video"></i> Uploaded
            </span>
        @endif
    @else
        <span class="badge badge-secondary">No Video</span>
    @endif
</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $lesson->materials_count }} materials
                                                        </span>
                                                        @if($lesson->material_types)
                                                            <br>
                                                            <small>
                                                                Types: {{ implode(', ', $lesson->material_types) }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lesson->has_quiz)
                                                            @php
                                                                $quizTitle = $lesson->quiz ? ($lesson->quiz->getTranslation($appLocale, 'title') ?? 'Quiz') : 'Quiz';
                                                            @endphp
                                                            <span class="badge badge-success" title="{{ $quizTitle }}">Has Quiz</span>
                                                        @else
                                                            <span class="badge badge-secondary">No Quiz</span>
                                                        @endif
                                                    </td>
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
                                                    <td colspan="6" class="text-center">No Course Lesson Found</td>
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

    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/plugins-init/datatables.init.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const initialLocale = localStorage.getItem('lessons_lang') || '{{ $appLocale }}';

    function setActiveLocale(locale) {
        // Активируем табы
        document.querySelectorAll('.lang-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.locale === locale);
        });

        // Показываем/скрываем переводы уроков
        document.querySelectorAll('.lesson-title').forEach(el => {
            el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
        });

        // Показываем/скрываем переводы курсов
        document.querySelectorAll('.lesson-course').forEach(el => {
            el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
        });

        // Обновляем заголовки квизов (если нужно)
        document.querySelectorAll('.badge-success').forEach(badge => {
            // Здесь можно добавить логику для обновления заголовков квизов
            // если они отображаются в разных языках
        });
    }

    // Обработчик клика по табам
    document.querySelectorAll('.lang-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const locale = this.dataset.locale;
            localStorage.setItem('lessons_lang', locale);
            setActiveLocale(locale);
        });
    });

    // Устанавливаем начальную локаль
    setActiveLocale(initialLocale);
});
</script>
@endpush
