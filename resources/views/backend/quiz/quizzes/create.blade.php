@extends('backend.layouts.app')
@section('title', 'Create Quiz')

@push('styles')
<link href="{{ asset('vendor/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12">
                <h4>Create New Quiz</h4>
                <a href="{{ route('quiz.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <form action="{{ route('quiz.store') }}" method="POST" id="quizForm">
            @csrf
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
                    <div class="tab-pane fade {{ $localeCode === $appLocale ? 'show active' : '' }}" id="locale-{{ $localeCode }}" role="tabpanel">
                        <div class="form-group">
                            <label>Quiz Title ({{ $localeName }}) *</label>
                            <input type="text" name="translations[{{ $localeCode }}][title]" class="form-control"
                                   value="{{ old('translations.'.$localeCode.'.title') }}" required>
                            @error('translations.'.$localeCode.'.title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Quiz Description ({{ $localeName }})</label>
                            <textarea name="translations[{{ $localeCode }}][description]" class="form-control summernote">{{ old('translations.'.$localeCode.'.description') }}</textarea>
                            @error('translations.'.$localeCode.'.description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Lesson Selector --}}
            <div class="form-group">
                <label>Lesson *</label>
                <select name="lessonId" class="form-control" required id="lessonSelect">
                    <option value="">Select Lesson</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lessonId') == $lesson->id ? 'selected' : '' }}>
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
                               value="{{ old('time_limit', 0) }}" min="0" placeholder="0 for no limit">
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
                               value="{{ old('passing_score', 70) }}" min="0" max="100" required>
                        @error('passing_score')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Attempts</label>
                        <input type="number" name="max_attempts" class="form-control"
                               value="{{ old('max_attempts', 0) }}" min="0" placeholder="0 for unlimited">
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
                               value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        @error('is_active')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Questions Section --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Questions</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addQuestion">
                        <i class="la la-plus"></i> Add Question
                    </button>
                </div>
                <div class="card-body">
                    <div id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Create Quiz</button>
            <a href="{{ route('quiz.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('.summernote').summernote({height: 150});

    let questionCounter = 0;

    // Add question button handler
    $('#addQuestion').on('click', function() {
        questionCounter++;
        const questionHtml = `
            <div class="card mb-3 question-item" data-question-id="${questionCounter}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question #${questionCounter}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-question">
                        <i class="la la-trash"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <input type="hidden" name="questions[${questionCounter}][order]" value="${questionCounter}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Question Type *</label>
                                <select name="questions[${questionCounter}][type]" class="form-control question-type" required>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="short_answer">Short Answer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Points</label>
                                <input type="number" name="questions[${questionCounter}][points]" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Required</label>
                                <select name="questions[${questionCounter}][is_required]" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Question Content for each language --}}
                    <div class="row">
                        @foreach($locales as $localeCode => $localeName)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Question ({{ $localeName }}) *</label>
                                <textarea name="questions[${questionCounter}][translations][{{ $localeCode }}][content]"
                                          class="form-control" rows="2" placeholder="Enter question content" required></textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Options for multiple choice --}}
                    <div class="options-container" style="display: none;">
                        <h6>Options:</h6>
                        <div class="row">
                            @foreach($locales as $localeCode => $localeName)
                            <div class="col-md-6">
                                <div class="card mb-2">
                                    <div class="card-header">
                                        <small>{{ $localeName }} Options</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Option A</label>
                                            <input type="text" name="questions[${questionCounter}][translations][{{ $localeCode }}][option_a]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Option B</label>
                                            <input type="text" name="questions[${questionCounter}][translations][{{ $localeCode }}][option_b]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Option C</label>
                                            <input type="text" name="questions[${questionCounter}][translations][{{ $localeCode }}][option_c]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Option D</label>
                                            <input type="text" name="questions[${questionCounter}][translations][{{ $localeCode }}][option_d]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label>Correct Answer *</label>
                            <select name="questions[${questionCounter}][correct_answer]" class="form-control">
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#questionsContainer').append(questionHtml);

        // Initialize question type change handler
        const newQuestion = $('#questionsContainer').find('.question-item').last();
        newQuestion.find('.question-type').on('change', function() {
            toggleOptionsVisibility($(this));
        });
    });

    // Remove question handler
    $(document).on('click', '.remove-question', function() {
        $(this).closest('.question-item').remove();
        // Reorder remaining questions
        $('#questionsContainer .question-item').each(function(index) {
            $(this).find('h6').text('Question #' + (index + 1));
            $(this).find('input[name$="[order]"]').val(index + 1);
        });
    });

    // Toggle options visibility based on question type
    function toggleOptionsVisibility(selectElement) {
        const questionItem = selectElement.closest('.question-item');
        const optionsContainer = questionItem.find('.options-container');
        const correctAnswerField = questionItem.find('select[name$="[correct_answer]"]');

        if (selectElement.val() === 'multiple_choice') {
            optionsContainer.show();
            correctAnswerField.prop('required', true);
        } else {
            optionsContainer.hide();
            correctAnswerField.prop('required', false);
        }
    }

    // Initialize question type change handlers for existing questions
    $(document).on('change', '.question-type', function() {
        toggleOptionsVisibility($(this));
    });

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
    $('#quizForm').on('submit', function() {
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

        // Проверяем вопросы
        const questionItems = $('.question-item');
        if (questionItems.length === 0) {
            alert('Please add at least one question to the quiz.');
            isValid = false;
        } else {
            questionItems.each(function() {
                const questionType = $(this).find('.question-type').val();
                const questionContent = $(this).find('textarea[name$="[content]"]');

                // Проверяем содержание вопроса для всех языков
                let hasContent = true;
                questionContent.each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        hasContent = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!hasContent) {
                    isValid = false;
                    alert('Please fill in question content for all languages.');
                }

                // Для multiple choice проверяем options и correct answer
                if (questionType === 'multiple_choice') {
                    const correctAnswer = $(this).find('select[name$="[correct_answer]"]');
                    if (!correctAnswer.val()) {
                        correctAnswer.addClass('is-invalid');
                        isValid = false;
                    } else {
                        correctAnswer.removeClass('is-invalid');
                    }
                }
            });
        }

        if (!isValid) {
            alert('Please fill in all required fields.');
            return false;
        }

        return true;
    });

    // Добавляем первый вопрос автоматически
    $('#addQuestion').click();
});
</script>

<style>
.question-item {
    border-left: 4px solid #007bff;
}
.remove-question {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
.options-container {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    margin-top: 1rem;
    border: 1px solid #dee2e6;
}
</style>
@endpush
