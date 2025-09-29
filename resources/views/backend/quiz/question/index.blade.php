@extends('backend.layouts.app')
@section('title', isset($quiz) ? 'Questions for: ' . ($quiz->getTranslation($currentLocale)?->title ?? 'Quiz') : __('Questions'))
@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        @if($quiz)
                            Questions for: {{ $quiz->getTranslation($currentLocale)?->title ?? 'Quiz #' . $quiz->id }}
                        @else
                            {{ __('All Questions') }}
                        @endif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quiz.index') }}">{{ __('Quizzes') }}</a></li>
                    <li class="breadcrumb-item active">
                        @if($quiz)
                            {{ $quiz->getTranslation($currentLocale)?->title ?? 'Quiz #' . $quiz->id }}
                        @else
                            {{ __('Questions') }}
                        @endif
                    </li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы Bootstrap -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="questionLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                           href="{{ route('question.index', array_merge(['lang' => $localeCode], $quizId ? ['quiz_id' => $quizId] : [])) }}"
                           role="tab">
                            {{ $localeName }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if($quiz)
                                Questions for: {{ $quiz->getTranslation($currentLocale)?->title ?? 'Quiz #' . $quiz->id }}
                            @else
                                {{ __('All Questions') }}
                            @endif
                        </h4>
                        <a href="{{ route('question.create', array_merge(['lang' => $currentLocale], $quizId ? ['quiz_id' => $quizId] : [])) }}"
                           class="btn btn-primary btn-sm">
                            <i class="la la-plus"></i> {{ __('Add Question') }}
                        </a>

                        @if($quizId)
                        <a href="{{ route('quiz.index', ['lang' => $currentLocale]) }}"
                           class="btn btn-secondary btn-sm ml-2">
                            <i class="la la-list"></i> {{ __('Back to Quizzes') }}
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if(!$quizId)
                                        <th>{{ __('Quiz') }}</th>
                                        @endif
                                        <th>{{ __('Question Content') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{__('Option A')}}</th>
                                        <th>{{__('Option B')}}</th>
                                        <th>{{__('Option C')}}</th>
                                        <th>{{__('Option D')}}</th>
                                        <th>{{__('Correct Answer')}}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $question)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @if(!$quizId)
                                        <td>
                                            @if($question->quiz)
                                                {{ $question->quiz->getTranslation($currentLocale)?->title ?? 'Quiz #' . $question->quiz->id }}
                                            @else
                                                <span class="text-danger">{{ __('No Quiz') }}</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @php
                                                $questionContent = $question->getTranslation($currentLocale)?->content
                                                    ?? $question->translations->first()?->content
                                                    ?? __('No Content');
                                            @endphp
                                            {{ Str::limit($questionContent, 50) }}
                                        </td>
                                        <td>
                                            @switch($question->type)
                                                @case('single')
                                                    <span class="badge badge-primary">{{ __('Single Choice') }}</span>
                                                    @break
                                                @case('multiple')
                                                    <span class="badge badge-info">{{ __('Multiple Choice') }}</span>
                                                    @break
                                                @case('text')
                                                    <span class="badge badge-success">{{ __('Text Answer') }}</span>
                                                    @break
                                                @case('rating')
                                                    <span class="badge badge-warning">{{ __('Rating') }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $question->type }}</span>
                                            @endswitch
                                        </td>

                                        <!-- Опции A, B, C, D -->
                                        @php
                                            // Загружаем опции с переводами если не загружены
                                            if (!$question->relationLoaded('options')) {
                                                $question->load(['options.translations']);
                                            }

                                            $options = $question->options;
                                            $optionA = $options->where('key', 'a')->first();
                                            $optionB = $options->where('key', 'b')->first();
                                            $optionC = $options->where('key', 'c')->first();
                                            $optionD = $options->where('key', 'd')->first();
                                        @endphp

                                        <td>
                                            @if($optionA)
                                                @php
                                                    $optionAText = $optionA->getTranslation($currentLocale)?->text
                                                        ?? $optionA->translations->first()?->text
                                                        ?? 'N/A';
                                                @endphp
                                                <span class="{{ $optionA->is_correct ? 'text-success font-weight-bold' : '' }}">
                                                    {{ Str::limit($optionAText, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($optionB)
                                                @php
                                                    $optionBText = $optionB->getTranslation($currentLocale)?->text
                                                        ?? $optionB->translations->first()?->text
                                                        ?? 'N/A';
                                                @endphp
                                                <span class="{{ $optionB->is_correct ? 'text-success font-weight-bold' : '' }}">
                                                    {{ Str::limit($optionBText, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($optionC)
                                                @php
                                                    $optionCText = $optionC->getTranslation($currentLocale)?->text
                                                        ?? $optionC->translations->first()?->text
                                                        ?? 'N/A';
                                                @endphp
                                                <span class="{{ $optionC->is_correct ? 'text-success font-weight-bold' : '' }}">
                                                    {{ Str::limit($optionCText, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($optionD)
                                                @php
                                                    $optionDText = $optionD->getTranslation($currentLocale)?->text
                                                        ?? $optionD->translations->first()?->text
                                                        ?? 'N/A';
                                                @endphp
                                                <span class="{{ $optionD->is_correct ? 'text-success font-weight-bold' : '' }}">
                                                    {{ Str::limit($optionDText, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <!-- Правильный ответ -->
                                        <td>
                                            @if(in_array($question->type, ['single', 'multiple']))
                                                @php
                                                    $correctOptions = $options->where('is_correct', true);
                                                @endphp

                                                @if($correctOptions->count() > 0)
                                                    @foreach($correctOptions as $correctOption)
                                                        <span class="badge badge-success">
                                                            {{ strtoupper($correctOption->key) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-warning">{{ __('Not set') }}</span>
                                                @endif
                                            @elseif($question->type === 'text')
                                                @php
                                                    $correctText = $question->getTranslation($currentLocale)?->correct_option
                                                        ?? $question->translations->first()?->correct_option
                                                        ?? 'N/A';
                                                @endphp
                                                <span class="text-info" title="{{ $correctText }}">
                                                    {{ Str::limit($correctText, 15) }}
                                                </span>
                                            @elseif($question->type === 'rating')
                                                <span class="text-muted">-</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Not set') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('question.edit', ['question' => $question->id, 'lang' => $currentLocale]) }}"
                                                   class="btn btn-sm btn-primary me-1" title="{{ __('Edit') }}">
                                                    <i class="la la-pencil"></i>
                                                </a>
                                                <form action="{{ route('question.destroy', $question->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ __('Delete') }}"
                                                            onclick="return confirm('{{ __('Are you sure you want to delete this question?') }}')">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $quizId ? 10 : 11 }}" class="text-center">{{ __('No questions found') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $questions->appends(array_merge(['lang' => $currentLocale], $quizId ? ['quiz_id' => $quizId] : []))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    color: #495057;
    cursor: pointer;
    padding: 0.5rem 1rem;
}
.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 500;
}
.badge {
    font-size: 0.75rem;
}
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
.text-success {
    color: #28a745 !important;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Подсветка активной языковой вкладки
    const currentLang = '{{ $currentLocale }}';
    const navLinks = document.querySelectorAll('.nav-tabs .nav-link');

    navLinks.forEach(link => {
        if (link.getAttribute('href').includes('lang=' + currentLang)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
</script>
@endpush
