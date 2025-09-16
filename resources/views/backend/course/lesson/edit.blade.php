@extends('backend.layouts.app')
@section('title', 'Edit Lesson')

@push('styles')
<link href="{{ asset('vendor/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12">
                <h4>Edit Lesson</h4>
                <a href="{{ localeRoute('lesson.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();

            // Получаем переводы для текущего урока
            $translations = [];
            foreach ($lesson->translations as $translation) {
                $translations[$translation->locale] = $translation;
            }

            // Получаем переводы для квиза, если он существует
            $quizTranslations = [];
            if ($lesson->quiz) {
                foreach ($lesson->quiz->translations as $translation) {
                    $quizTranslations[$translation->locale] = $translation;
                }
            }
        @endphp

        <form action="{{ localeRoute('lesson.update', encryptor('encrypt', $lesson->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Language Tabs --}}
            <ul class="nav nav-tabs mb-3" id="lessonLangTabs" role="tablist">
                @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $localeCode === $appLocale ? 'active' : '' }}"
                           id="tab-{{ $localeCode }}" data-toggle="tab" href="#locale-{{ $localeCode }}" role="tab">
                           {{ $localeName }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Tab Contents --}}
            <div class="tab-content">
                @foreach($locales as $localeCode => $localeName)
                    @php
                        $translation = $translations[$localeCode] ?? null;
                    @endphp
                    <div class="tab-pane fade {{ $localeCode === $appLocale ? 'show active' : '' }}" id="locale-{{ $localeCode }}" role="tabpanel">
                        <div class="form-group">
                            <label>Title ({{ $localeName }}) *</label>
                            <input type="text" name="translations[{{ $localeCode }}][title]" class="form-control"
                                   value="{{ old('translations.'.$localeCode.'.title', $translation->title ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description ({{ $localeName }})</label>
                            <textarea name="translations[{{ $localeCode }}][description]" class="form-control summernote">{{ old('translations.'.$localeCode.'.description', $translation->description ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Notes ({{ $localeName }})</label>
                            <textarea name="translations[{{ $localeCode }}][notes]" class="form-control summernote">{{ old('translations.'.$localeCode.'.notes', $translation->notes ?? '') }}</textarea>
                        </div>

                        {{-- Hidden fields for translation IDs if they exist --}}
                        @if($translation)
                            <input type="hidden" name="translations[{{ $localeCode }}][id]" value="{{ $translation->id }}">
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Course Selector --}}
            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" class="form-control" required id="courseSelect">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $lesson->course_id) == $course->id ? 'selected' : '' }}
                                data-title-en="{{ $course->getTranslation('en', 'title') }}"
                                data-title-ru="{{ $course->getTranslation('ru', 'title') }}"
                                data-title-ka="{{ $course->getTranslation('ka', 'title') }}">
                            {{ $course->getTranslation($appLocale, 'title') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Current Video --}}
            @if($lesson->video_url)
            <div class="form-group">
                <label>Current Video</label>
                <div class="alert alert-info">
                    @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                        <i class="fab fa-youtube text-danger"></i> YouTube Video:
                        <a href="{{ $lesson->video_url }}" target="_blank">{{ $lesson->video_url }}</a>
                    @else
                        <i class="fas fa-video text-primary"></i> Uploaded Video:
                        <a href="{{ asset('storage/' . $lesson->video_url) }}" target="_blank">View Video</a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Video Upload --}}
            <div class="form-group">
                <label>Upload New Video File</label>
                <input type="file" name="video" class="form-control-file" accept="video/*">
                <small class="form-text text-muted">
                    Upload a new video file (MP4, AVI, MOV) to replace existing one
                </small>
            </div>

            {{-- YouTube URL --}}
            <div class="form-group">
                <label>YouTube URL</label>
                <input type="url" name="video_url" class="form-control"
                       value="{{ old('video_url', $lesson->video_url) }}"
                       placeholder="https://www.youtube.com/watch?v=...">
                <small class="form-text text-muted">
                    Enter YouTube URL if you want to use YouTube video instead of uploaded file
                </small>
            </div>

            {{-- Order --}}
            <div class="form-group">
                <label>Order *</label>
                <input type="number" name="order" class="form-control"
                       value="{{ old('order', $lesson->order) }}" required min="1">
            </div>

            {{-- Quiz Section --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Quiz</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="hasQuizToggle" name="has_quiz" value="1" {{ $lesson->quiz || old('has_quiz') ? 'checked' : '' }}>
                        <label class="form-check-label" for="hasQuizToggle">This Lesson Has Quiz</label>
                    </div>
                </div>
                <div class="card-body" id="quizSection" style="{{ $lesson->quiz || old('has_quiz') ? '' : 'display: none;' }}">
                    {{-- Quiz Language Tabs --}}
                    <ul class="nav nav-tabs mb-3" id="quizLangTabs" role="tablist">
                        @foreach($locales as $localeCode => $localeName)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $localeCode === $appLocale ? 'active' : '' }}"
                                   id="quiz-tab-{{ $localeCode }}" data-toggle="tab" href="#quiz-locale-{{ $localeCode }}" role="tab">
                                   {{ $localeName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Quiz Tab Contents --}}
                    <div class="tab-content">
                        @foreach($locales as $localeCode => $localeName)
                            @php
                                $quizTranslation = $quizTranslations[$localeCode] ?? null;
                            @endphp
                            <div class="tab-pane fade {{ $localeCode === $appLocale ? 'show active' : '' }}" id="quiz-locale-{{ $localeCode }}" role="tabpanel">
                                <div class="form-group">
                                    <label>Quiz Title ({{ $localeName }})</label>
                                    <input type="text" name="quiz_translations[{{ $localeCode }}][title]" class="form-control"
                                           value="{{ old('quiz_translations.'.$localeCode.'.title', $quizTranslation->title ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>Quiz Description ({{ $localeName }})</label>
                                    <textarea name="quiz_translations[{{ $localeCode }}][description]" class="form-control summernote">{{ old('quiz_translations.'.$localeCode.'.description', $quizTranslation->description ?? '') }}</textarea>
                                </div>

                                {{-- Hidden fields for quiz translation IDs if they exist --}}
                                @if($quizTranslation)
                                    <input type="hidden" name="quiz_translations[{{ $localeCode }}][id]" value="{{ $quizTranslation->id }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label>Passing Score (%)</label>
                        <input type="number" name="passing_score" class="form-control"
                               value="{{ old('passing_score', $lesson->quiz->passing_score ?? 70) }}" min="1" max="100">
                    </div>

                    {{-- Hidden field for quiz ID if it exists --}}
                    @if($lesson->quiz)
                        <input type="hidden" name="quiz_id" value="{{ $lesson->quiz->id }}">
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Lesson</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('.summernote').summernote({height: 150});

    // Переключение табов с повторной инициализацией Summernote
    $('#lessonLangTabs a[data-toggle="tab"], #quizLangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr('href');
        $(target+' .summernote').summernote({height:150});
    });

    // Обновление названий курсов при переключении языка
    $('#lessonLangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const locale = $(e.target).attr('id').replace('tab-', '');
        updateCourseTitles(locale);
    });

    function updateCourseTitles(locale) {
        $('#courseSelect option').each(function() {
            if ($(this).val() !== '') {
                const title = $(this).data('title-' + locale) || $(this).data('title-en');
                $(this).text(title);
            }
        });
    }

    // Toggle quiz section
    $('#hasQuizToggle').change(function() {
        if ($(this).is(':checked')) {
            $('#quizSection').slideDown();
        } else {
            $('#quizSection').slideUp();
        }
    });

    // Инициализация при загрузке
    const currentLocale = $('#lessonLangTabs .nav-link.active').attr('id').replace('tab-', '');
    updateCourseTitles(currentLocale);
});
</script>
@endpush
