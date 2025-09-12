@extends('backend.layouts.app')
@section('title', 'Create Quiz')

@section('content')

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Create New Quiz</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('quiz.index')}}">Quizzes</a></li>
                    <li class="breadcrumb-item active">Create Quiz</li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы Bootstrap -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="langTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $localeCode]) }}"
                               class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">
                                {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Quiz</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ localeRoute('quiz.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="current_locale" value="{{ $currentLocale }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lessonId">Lesson *</label>
                                        <select class="form-control" id="lessonId" name="lessonId" required>
                                            <option value="">Select Lesson</option>
                                            @foreach($lessons as $lesson)
                                                @php
                                                    // Получаем перевод урока для текущей локали
                                                    $lessonTranslation = $lesson->translations->firstWhere('locale', $currentLocale);
                                                    $lessonTitle = $lessonTranslation ? $lessonTranslation->title : ($lesson->translations->first() ? $lesson->translations->first()->title : 'Lesson #' . $lesson->id);

                                                    // Получаем перевод курса для текущей локали
                                                    $courseTitle = 'No Course';
                                                    if ($lesson->course) {
                                                        $courseTranslation = $lesson->course->translations->firstWhere('locale', $currentLocale);
                                                        $courseTitle = $courseTranslation ? $courseTranslation->title : ($lesson->course->translations->first() ? $lesson->course->translations->first()->title : 'Course #' . $lesson->course->id);
                                                    }
                                                @endphp
                                                <option value="{{ $lesson->id }}">
                                                    {{ $lessonTitle }} (ID: {{ $lesson->id }}) - {{ $courseTitle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($lessons->isEmpty())
                                            <small class="text-danger">No lessons available. Please create lessons first.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order">Order</label>
                                        <input type="number" class="form-control" id="order" name="order"
                                               value="{{ old('order', 0) }}" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time_limit">Time Limit (minutes)</label>
                                        <input type="number" class="form-control" id="time_limit" name="time_limit"
                                               value="{{ old('time_limit', 0) }}" min="0" placeholder="0 for no limit">
                                        <small class="form-text text-muted">0 = No time limit</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="passing_score">Passing Score (%) *</label>
                                        <input type="number" class="form-control" id="passing_score" name="passing_score"
                                               value="{{ old('passing_score', 70) }}" min="0" max="100" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_attempts">Max Attempts</label>
                                        <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                               value="{{ old('max_attempts', 0) }}" min="0" placeholder="0 for unlimited">
                                        <small class="form-text text-muted">0 = Unlimited attempts</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                           value="1" {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Quiz Translations</h5>

                            <ul class="nav nav-tabs" id="translationTabs" role="tablist">
                                @foreach($locales as $localeCode => $localeName)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                                           id="{{ $localeCode }}-tab" data-toggle="tab" href="#{{ $localeCode }}"
                                           role="tab" aria-controls="{{ $localeCode }}" aria-selected="{{ $localeCode === $currentLocale ? 'true' : 'false' }}">
                                            {{ $localeName }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="translationTabsContent">
                                @foreach($locales as $localeCode => $localeName)
                                    <div class="tab-pane fade {{ $localeCode === $currentLocale ? 'show active' : '' }}"
                                         id="{{ $localeCode }}" role="tabpanel" aria-labelledby="{{ $localeCode }}-tab">
                                        <div class="form-group mt-3">
                                            <label for="title_{{ $localeCode }}">Title ({{ $localeName }}) *</label>
                                            <input type="text" class="form-control" id="title_{{ $localeCode }}"
                                                   name="title_{{ $localeCode }}"
                                                   value="{{ old("title_$localeCode", '') }}"
                                                   required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description_{{ $localeCode }}">Description ({{ $localeName }})</label>
                                            <textarea class="form-control" id="description_{{ $localeCode }}"
                                                      name="description_{{ $localeCode }}" rows="4">{{ old("description_$localeCode", '') }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Quiz</button>
                                <a href="{{ localeRoute('quiz.index', ['lang' => $currentLocale]) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Активация табов переводов квиза
        $('#translationTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Валидация формы
        $('form').on('submit', function() {
            let isValid = true;

            @foreach($locales as $localeCode => $localeName)
                const titleField = $('#title_{{ $localeCode }}');
                if (!titleField.val().trim()) {
                    isValid = false;
                    titleField.addClass('is-invalid');
                } else {
                    titleField.removeClass('is-invalid');
                }
            @endforeach

            if (!isValid) {
                alert('Please fill in all required fields for all languages.');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
