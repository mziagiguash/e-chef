@extends('backend.layouts.app')
@section('title', 'Edit Quiz')

@push('styles')
<link href="{{ asset('vendor/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12">
                <h4>Edit Quiz</h4>
                <a href="{{ route('quiz.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();

            // Получаем переводы для текущего квиза
            $translations = [];
            foreach ($quiz->translations as $translation) {
                $translations[$translation->locale] = $translation;
            }
        @endphp

        <form action="{{ route('quiz.update', encryptor('encrypt', $quiz->id)) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_locale" value="{{ $appLocale }}">

            {{-- Language Tabs --}}
            <ul class="nav nav-tabs mb-3" id="quizLangTabs" role="tablist">
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
                            <label>Quiz Title ({{ $localeName }}) *</label>
                            <input type="text" name="translations[{{ $localeCode }}][title]" class="form-control"
                                   value="{{ old('translations.'.$localeCode.'.title', $translation->title ?? '') }}" required>
                            @error('translations.'.$localeCode.'.title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Quiz Description ({{ $localeName }})</label>
                            <textarea name="translations[{{ $localeCode }}][description]" class="form-control summernote">{{ old('translations.'.$localeCode.'.description', $translation->description ?? '') }}</textarea>
                            @error('translations.'.$localeCode.'.description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Hidden field for translation ID if exists --}}
                        @if($translation)
                            <input type="hidden" name="translations[{{ $localeCode }}][id]" value="{{ $translation->id }}">
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Lesson Selector --}}
            <div class="form-group">
                <label>Lesson *</label>
                <select name="lessonId" class="form-control" required id="lessonSelect">
                    <option value="">Select Lesson</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lessonId', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
                            @foreach($locales as $localeCode => $localeName)
                                @php
                                    $lessonTitle = $lesson->getTranslation($localeCode, 'title') ?? 'Lesson #' . $lesson->id;
                                    $courseTitle = $lesson->course ? ($lesson->course->getTranslation($localeCode, 'title') ?? 'Course #' . $lesson->course->id) : 'No Course';
                                @endphp
                                [{{ strtoupper($localeCode) }}: {{ $lessonTitle }} - {{ $courseTitle }}]
                            @endforeach
                        </option>
                    @endforeach
                </select>
                @error('lessonId')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Time Limit (minutes)</label>
                        <input type="number" name="time_limit" class="form-control"
                               value="{{ old('time_limit', $quiz->time_limit) }}" min="0" placeholder="0 for no limit">
                        <small class="form-text text-muted">0 = No time limit</small>
                        @error('time_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Passing Score (%) *</label>
                        <input type="number" name="passing_score" class="form-control"
                               value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required>
                        @error('passing_score')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Attempts</label>
                        <input type="number" name="max_attempts" class="form-control"
                               value="{{ old('max_attempts', $quiz->max_attempts) }}" min="0" placeholder="0 for unlimited">
                        <small class="form-text text-muted">0 = Unlimited attempts</small>
                        @error('max_attempts')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="order" class="form-control"
                               value="{{ old('order', $quiz->order) }}" min="0">
                        @error('order')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        @error('is_active')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Quiz</button>
            <a href="{{ route('quiz.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('.summernote').summernote({height: 150});

    // Показываем первую вкладку с ошибкой если есть
    @if($errors->any())
        @foreach($locales as $localeCode => $localeName)
            @if($errors->has('translations.'.$localeCode.'.*'))
                $('#tab-{{ $localeCode }}').tab('show');
                break;
            @endif
        @endforeach
    @endif

    // Переключение табов с повторной инициализацией Summernote
    $('#quizLangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr('href');
        $(target+' .summernote').summernote({height:150});
    });

    // Валидация формы
    $('form').on('submit', function() {
        let isValid = true;

        // Проверяем все обязательные заголовки
        @foreach($locales as $localeCode => $localeName)
            const titleField{{ $localeCode }} = $('input[name="translations[{{ $localeCode }}][title]"]');
            if (!titleField{{ $localeCode }}.val().trim()) {
                isValid = false;
                titleField{{ $localeCode }}.addClass('is-invalid');
                // Переключаемся на вкладку с ошибкой
                $('#tab-{{ $localeCode }}').tab('show');
            } else {
                titleField{{ $localeCode }}.removeClass('is-invalid');
            }
        @endforeach

        if (!isValid) {
            alert('Please fill in all required title fields for all languages.');
            return false;
        }

        return true;
    });
});
</script>
@endpush
