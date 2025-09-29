@extends('backend.layouts.app')
@section('title', 'Quiz Details')

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Quiz Details</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('quiz.index')}}">Quizzes</a></li>
                    <li class="breadcrumb-item active">Quiz Details</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quiz Information</h4>
                        <a href="{{ route('quiz.edit', ['quiz' => encryptor('encrypt', $quiz->id), 'lang' => app()->getLocale()]) }}"
                           class="btn btn-primary btn-sm">Edit Quiz</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>ID:</strong></label>
                                    <p>{{ $quiz->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Status:</strong></label>
                                    <p>
                                        <span class="badge badge-{{ $quiz->is_active ? 'success' : 'secondary' }}">
                                            {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Time Limit:</strong></label>
                                    <p>{{ $quiz->time_limit > 0 ? $quiz->time_limit . ' minutes' : 'No limit' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Passing Score:</strong></label>
                                    <p>{{ $quiz->passing_score }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Max Attempts:</strong></label>
                                    <p>{{ $quiz->max_attempts > 0 ? $quiz->max_attempts : 'Unlimited' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Order:</strong></label>
                                    <p>{{ $quiz->order }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Questions Count:</strong></label>
                                    <p><span class="badge badge-info">{{ $quiz->questions_count }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Lesson:</strong></label>
                            <p>
                                @if($quiz->lesson)
                                    @php
                                        $lessonTranslation = $quiz->lesson->translations->firstWhere('locale', app()->getLocale());
                                        $lessonTitle = $lessonTranslation ? $lessonTranslation->title : ($quiz->lesson->translations->first() ? $quiz->lesson->translations->first()->title : 'No lesson title');
                                        $hasLessonTranslation = (bool)$lessonTranslation;
                                    @endphp
                                    {{ $lessonTitle }}
                                    @if(!$hasLessonTranslation)
                                        <span class="badge badge-warning ml-1">No {{ app()->getLocale() }} translation</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">Lesson ID: {{ $quiz->lesson->id }}</small>
                                @else
                                    <span class="text-danger">No lesson assigned</span>
                                @endif
                            </p>
                        </div>

                        <div class="form-group">
                            <label><strong>Translations:</strong></label>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Language</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quiz->translations as $translation)
                                            <tr>
                                                <td>
                                                    {{ $locales[$translation->locale] ?? $translation->locale }}
                                                    @if($translation->locale === app()->getLocale())
                                                        <span class="badge badge-primary">Current</span>
                                                    @endif
                                                </td>
                                                <td>{{ $translation->title }}</td>
                                                <td>{{ $translation->description ?? 'No description' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $translation->title ? 'success' : 'warning' }}">
                                                        {{ $translation->title ? 'Translated' : 'Empty' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($quiz->translations->count() === 0)
                                            <tr>
                                                <td colspan="4" class="text-center">No translations found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Questions ({{ $quiz->questions->count() }}):</strong></label>
                            @if($quiz->questions->count() > 0)
                                <div class="accordion" id="questionsAccordion">
                                    @foreach($quiz->questions as $index => $question)
                                        <div class="card">
                                            <div class="card-header" id="heading{{ $question->id }}">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                                            data-target="#collapse{{ $question->id }}"
                                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                            aria-controls="collapse{{ $question->id }}">
                                                        Question #{{ $index + 1 }} (ID: {{ $question->id }}) - Type: {{ $question->type }}
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapse{{ $question->id }}"
                                                 class="collapse {{ $index === 0 ? 'show' : '' }}"
                                                 aria-labelledby="heading{{ $question->id }}"
                                                 data-parent="#questionsAccordion">
                                                <div class="card-body">
                                                    <h6>Question Translations:</h6>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Language</th>
                                                                <th>Question Text</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($question->translations as $translation)
                                                                <tr>
                                                                    <td>{{ $translation->locale }}</td>
                                                                    <td>{{ $translation->question_text }}</td>
                                                                </tr>
                                                            @endforeach
                                                            @if($question->translations->count() === 0)
                                                                <tr>
                                                                    <td colspan="2" class="text-center">No translations</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>

                                                    <h6>Options ({{ $question->options->count() }}):</h6>
                                                    @if($question->options->count() > 0)
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Option</th>
                                                                    <th>Is Correct</th>
                                                                    <th>Translations</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($question->options as $option)
                                                                    <tr>
                                                                        <td>Option #{{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <span class="badge badge-{{ $option->is_correct ? 'success' : 'secondary' }}">
                                                                                {{ $option->is_correct ? 'Correct' : 'Incorrect' }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            @foreach($option->translations as $translation)
                                                                                <small>{{ $translation->locale }}: {{ $translation->option_text }}</small><br>
                                                                            @endforeach
                                                                            @if($option->translations->count() === 0)
                                                                                <small class="text-muted">No translations</small>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <p>No options found for this question.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p>No questions found for this quiz.</p>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Created At:</strong></label>
                                    <p>{{ $quiz->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Updated At:</strong></label>
                                    <p>{{ $quiz->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('quiz.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Аккордеон для вопросов
    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.accordion .btn-link');
        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const collapseElement = document.querySelector(target);

                // Закрываем все другие открытые элементы
                document.querySelectorAll('.accordion .collapse.show').forEach(item => {
                    if (item.id !== collapseElement.id) {
                        item.classList.remove('show');
                    }
                });
            });
        });
    });
</script>
@endpush
