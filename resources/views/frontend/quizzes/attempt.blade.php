@extends('frontend.layouts.app')

@section('title', __('Take Quiz'))

@section('content')
<div class="container py-4" style="padding-bottom: 120px !important;">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Quiz Header --}}
            <div class="card quiz-header-card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h4 mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            {{ $quiz->getTranslation($locale, 'title') ?? $quiz->title ?? __('Quiz') }}
                        </h1>
                        <small class="opacity-75">{{ __('Attempt') }} #{{ $currentAttempt }}</small>
                    </div>
                    <div class="text-end">
                        @if($quiz->time_limit)
                        <div class="timer-wrapper">
                            <i class="fas fa-clock me-1"></i>
                            <span id="timer" class="fw-bold">{{ $quiz->time_limit }}:00</span>
                        </div>
                        @endif
                        <div class="progress mt-2" style="height: 4px; width: 150px;">
                            <div id="progress-bar" class="progress-bar bg-warning" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quiz Form --}}
            <form action="{{ route('frontend.quizzes.submit', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'attempt' => $attempt->id
            ]) }}" method="POST" id="quiz-form">
                @csrf

                {{-- Questions --}}
                @foreach($questions as $index => $question)
                    @php
                        $questionTranslation = $question->translations->where('locale', $locale)->first();
                        $questionContent = $questionTranslation->content ?? $question->translations->first()->content ?? $question->content ?? __('No question content');
                    @endphp

                    <div class="card question-card mb-4" id="question-{{ $index + 1 }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 d-flex align-items-center">
                                <span class="question-number">{{ $index + 1 }}</span>
                                <span class="ms-2">{{ __('Question') }} {{ $index + 1 }} {{ __('of') }} {{ $questions->count() }}</span>
                            </h5>
                            @if($question->points > 1)
                                <span class="points-badge">{{ $question->points }} {{ __('points') }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="question-content mb-4">
                                {!! nl2br(e($questionContent)) !!}
                            </div>

                            {{-- Options --}}
                            <div class="options-container">
                                @switch($question->type)
                                    @case('single')
                                        @if($question->options->count() > 0)
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionTranslation = $option->translations->where('locale', $locale)->first();
                                                    $optionText = $optionTranslation->text ?? $option->text ?? __('Option :key', ['key' => $option->key]);

                                                    if (!$optionText && $option->translations->count() > 0) {
                                                        $optionText = $option->translations->first()->text ?? __('Option :key', ['key' => $option->key]);
                                                    }
                                                @endphp

                                                <div class="option-item single-option">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $option->id }}"
                                                           id="option_{{ $option->id }}_{{ $question->id }}"
                                                           required>
                                                    <label class="option-label" for="option_{{ $option->id }}_{{ $question->id }}">
                                                        <span class="option-key">{{ strtoupper($option->key) }}</span>
                                                        <span class="option-text">
                                                            {{ $optionText }}
                                                            @if(!$optionTranslation)
                                                                <small class="text-warning ms-1">({{ __('default translation') }})</small>
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                {{ __('No options available for this question!') }}
                                            </div>
                                        @endif
                                        @break

                                    @case('multiple')
                                        @if($question->options->count() > 0)
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionTranslation = $option->translations->where('locale', $locale)->first();
                                                    $optionText = $optionTranslation->text ?? $option->text ?? __('Option :key', ['key' => $option->key]);

                                                    if (!$optionText && $option->translations->count() > 0) {
                                                        $optionText = $option->translations->first()->text ?? __('Option :key', ['key' => $option->key]);
                                                    }
                                                @endphp

                                                <div class="option-item multiple-option">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="answers[{{ $question->id }}][]"
                                                           value="{{ $option->id }}"
                                                           id="option_{{ $option->id }}_{{ $question->id }}">
                                                    <label class="option-label" for="option_{{ $option->id }}_{{ $question->id }}">
                                                        <span class="option-key">{{ strtoupper($option->key) }}</span>
                                                        <span class="option-text">
                                                            {{ $optionText }}
                                                            @if(!$optionTranslation)
                                                                <small class="text-warning ms-1">({{ __('default translation') }})</small>
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                {{ __('No options available for this question!') }}
                                            </div>
                                        @endif
                                        @break

                                    @case('text')
                                        <div class="text-answer-container">
                                            <label for="answer_{{ $question->id }}" class="form-label fw-bold mb-3">
                                                <i class="fas fa-edit me-2"></i>{{ __('Your answer') }}:
                                            </label>
                                            <textarea class="form-control text-answer"
                                                   name="answers[{{ $question->id }}]"
                                                   id="answer_{{ $question->id }}"
                                                   required
                                                   rows="4"
                                                   placeholder="{{ __('Type your detailed answer here...') }}"></textarea>
                                        </div>
                                        @break

                                    @case('rating')
<div class="rating-radio-container">
    <label class="form-label fw-bold mb-3">
        <i class="fas fa-star me-2"></i>
        {{ __('Select rating from :min to :max', ['min' => $question->min_rating ?? 1, 'max' => $question->max_rating ?? 5]) }}:
    </label>
    <div class="rating-options-list">
        @for($i = ($question->min_rating ?? 1); $i <= ($question->max_rating ?? 5); $i++)
            <div class="rating-option-item">
                <input class="form-check-input" type="radio"
                       name="answers[{{ $question->id }}]"
                       value="{{ $i }}"
                       id="rating_{{ $question->id }}_{{ $i }}"
                       required>
                <label class="rating-option-label" for="rating_{{ $question->id }}_{{ $i }}">
                    <span class="rating-stars-display">{{ str_repeat('⭐', $i) }}</span>
                    <span class="rating-text">{{ $i }} {{ __('stars') }}</span>
                </label>
            </div>
        @endfor
    </div>
</div>
@break
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Submit Button --}}
                <div class="text-center mt-5 mb-5">
                    <button type="submit" class="btn btn-success btn-lg submit-quiz-btn">
                        <i class="fas fa-paper-plane me-2"></i> {{ __('Submit Quiz') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.rating-radio-container {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.rating-options-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.rating-option-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.rating-option-item:hover {
    border-color: #007bff;
    background: #f0f8ff;
    transform: translateY(-1px);
}

.rating-option-item:has(.form-check-input:checked) {
    border-color: #28a745;
    background: #f8fff9;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
}

.rating-option-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    margin: 0;
    width: 100%;
}

.rating-stars-display {
    font-size: 1.4em;
    min-width: 120px;
}

.rating-text {
    font-weight: 500;
    color: #495057;
    font-size: 1.1em;
}

.rating-option-item .form-check-input {
    margin-right: 1rem;
    margin-top: 0;
}

/* Остальные стили остаются без изменений */
.quiz-header-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.quiz-header-card .card-header {
    border-bottom: none;
    padding: 1.25rem 1.5rem;
}

.timer-wrapper {
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 1.1em;
    backdrop-filter: blur(10px);
}

.question-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.question-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.question-card .card-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.question-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 8px;
    font-size: 0.95em;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.points-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.question-content {
    font-size: 1.15em;
    line-height: 1.7;
    color: #2c3e50;
    padding: 1.25rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 5px solid #007bff;
    margin-bottom: 0;
    font-weight: 500;
}

.options-container {
    padding: 0.5rem;
}

.option-item {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem;
    margin-bottom: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: #ffffff;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    gap: 1rem;
}

.option-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 123, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.option-item:hover::before {
    left: 100%;
}

.option-item:hover {
    border-color: #007bff;
    background: #f8fbff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.option-item:has(.form-check-input:checked) {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9, #e8f5e8);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
}

.option-label {
    display: flex;
    align-items: flex-start;
    width: 100%;
    cursor: pointer;
    margin: 0;
    font-weight: 500;
    gap: 1rem;
}

.option-key {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: #6c757d;
    color: white;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9em;
    flex-shrink: 0;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
    margin-left: 0.5rem;
}

.option-item:has(.form-check-input:checked) .option-key {
    background: linear-gradient(135deg, #28a745, #20c997);
    transform: scale(1.05);
}

.option-text {
    flex: 1;
    line-height: 1.6;
    color: #495057;
    padding-top: 0.25rem;
    font-size: 1.05em;
    margin-left: 0.5rem;
    word-break: break-word;
}

.form-check-input {
    width: 22px !important;
    height: 22px !important;
    margin-top: 0.5rem !important;
    flex-shrink: 0;
    border: 2px solid #adb5bd;
    transition: all 0.3s ease;
    margin-left: 0.5rem !important;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
    transform: scale(1.1);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.text-answer-container {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    margin: 1rem 0;
}

.text-answer {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1em;
    transition: all 0.3s ease;
    resize: vertical;
    min-height: 120px;
    padding: 1rem;
}

.text-answer:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

.submit-quiz-btn {
    padding: 1rem 3rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.2em;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
    border: none;
    margin: 2rem 0;
}

.submit-quiz-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #28a745, #20c997);
}

.timer-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
    color: white !important;
}

.timer-critical {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .container.py-4 {
        padding-bottom: 100px !important;
    }

    .option-item {
        padding: 1rem;
        gap: 0.75rem;
    }

    .option-label {
        gap: 0.75rem;
    }

    .option-key {
        width: 32px;
        height: 32px;
        font-size: 0.85em;
        margin-left: 0.25rem;
    }

    .option-text {
        margin-left: 0.25rem;
        font-size: 1em;
    }

    .question-content {
        font-size: 1.1em;
        padding: 1rem;
    }

    .quiz-header-card .card-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .timer-wrapper {
        width: 100%;
        justify-content: center;
    }

    .form-check-input {
        width: 20px !important;
        height: 20px !important;
        margin-left: 0.25rem !important;
    }

    .submit-quiz-btn {
        padding: 0.85rem 2rem;
        font-size: 1.1em;
        margin: 1rem 0;
    }
}
</style>

@if($quiz->time_limit)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('timer');
    const progressBar = document.getElementById('progress-bar');
    let timeLeft = {{ $quiz->time_limit * 60 }};
    const totalTime = timeLeft;

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        const progress = ((totalTime - timeLeft) / totalTime) * 100;
        progressBar.style.width = `${progress}%`;

        if (timeLeft <= 120) {
            timerElement.parentElement.classList.add('timer-warning');
        }

        if (timeLeft <= 30) {
            timerElement.parentElement.classList.add('timer-critical');
            progressBar.classList.add('bg-danger');
        }

        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('quiz-form').submit();
        }
    }

    updateTimer();

    const timer = setInterval(function() {
        timeLeft--;
        updateTimer();

        if (timeLeft <= 0) {
            clearInterval(timer);
        }
    }, 1000);

    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.submit-quiz-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __("Submitting...") }}';
    });
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.submit-quiz-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __("Submitting...") }}';
    });
});
</script>
@endif
@endsection
