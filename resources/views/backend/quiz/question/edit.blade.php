@extends('backend.layouts.app')
@section('title', __('Edit Question'))

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ __('Edit Question') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index') }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Edit Question') }}</li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы Bootstrap -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="questionLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                                id="tab-{{ $localeCode }}"
                                data-bs-toggle="tab"
                                data-bs-target="#content-{{ $localeCode }}"
                                type="button" role="tab"
                                aria-controls="content-{{ $localeCode }}"
                                aria-selected="{{ $localeCode === $currentLocale ? 'true' : 'false' }}">
                            {{ $localeName }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('question.update', $question->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="current_locale" value="{{ $currentLocale }}">

                            <!-- Основные поля (не зависят от языка) -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Quiz') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('quiz_id') is-invalid @enderror" name="quiz_id" required>
                                            <option value="">{{ __('Select Quiz') }}</option>
                                            @forelse ($quizzes as $quiz)
                                            <option value="{{ $quiz->id }}" {{ old('quiz_id', $question->quiz_id) == $quiz->id ? 'selected' : '' }}>
                                                {{ $quiz->title }}
                                            </option>
                                            @empty
                                            <option value="">{{ __('No Quizzes Found') }}</option>
                                            @endforelse
                                        </select>
                                        @error('quiz_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Question Type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" name="type" id="questionType" required>
                                            <option value="single" {{ old('type', $question->type) == 'single' ? 'selected' : '' }}>
                                                {{ __('Single Choice') }}
                                            </option>
                                            <option value="multiple" {{ old('type', $question->type) == 'multiple' ? 'selected' : '' }}>
                                                {{ __('Multiple Choice') }}
                                            </option>
                                            <option value="text" {{ old('type', $question->type) == 'text' ? 'selected' : '' }}>
                                                {{ __('Text Answer') }}
                                            </option>
                                            <option value="rating" {{ old('type', $question->type) == 'rating' ? 'selected' : '' }}>
                                                {{ __('Rating') }}
                                            </option>
                                        </select>
                                        @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Points') }}</label>
                                        <input type="number" class="form-control @error('points') is-invalid @enderror"
                                               name="points" value="{{ old('points', $question->points) }}" min="1">
                                        @error('points')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Order') }}</label>
                                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                                               name="order" value="{{ old('order', $question->order) }}" min="0">
                                        @error('order')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Required') }}</label>
                                        <select class="form-control @error('is_required') is-invalid @enderror" name="is_required">
                                            <option value="1" {{ old('is_required', $question->is_required) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                            <option value="0" {{ old('is_required', $question->is_required) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                        </select>
                                        @error('is_required')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" id="maxChoicesContainer" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Max Choices') }}</label>
                                        <input type="number" class="form-control @error('max_choices') is-invalid @enderror"
                                               name="max_choices" value="{{ old('max_choices', $question->max_choices) }}" min="1">
                                        @error('max_choices')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Содержимое табов (поля, зависящие от языка) -->
                            <div class="tab-content" id="questionTabContent">
                                @foreach($locales as $localeCode => $localeName)
                                <div class="tab-pane fade {{ $localeCode === $currentLocale ? 'show active' : '' }}"
                                     id="content-{{ $localeCode }}" role="tabpanel"
                                     aria-labelledby="tab-{{ $localeCode }}">

                                    <h6 class="text-primary mt-4">{{ $localeName }} {{ __('Translation') }}</h6>

                                    @php
                                        $translation = $question->translations->firstWhere('locale', $localeCode);
                                    @endphp

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Question Content') }} <span class="text-danger">*</span></label>
                                                <textarea class="form-control @error('translations.'.$localeCode.'.content') is-invalid @enderror"
                                                          name="translations[{{ $localeCode }}][content]"
                                                          rows="3"
                                                          placeholder="{{ __('Enter question content') }}"
                                                          {{ $localeCode === 'en' ? 'required' : '' }}>{{ old('translations.'.$localeCode.'.content', $translation->content ?? '') }}</textarea>
                                                @error('translations.'.$localeCode.'.content')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="translations[{{ $localeCode }}][locale]" value="{{ $localeCode }}">
                                    @if($translation)
                                    <input type="hidden" name="translations[{{ $localeCode }}][id]" value="{{ $translation->id }}">
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            <!-- Динамические опции (только для single и multiple choice) -->
                            <div class="row mt-4" id="optionsContainer" style="display: none;">
                                <div class="col-12">
                                    <h5 class="mb-3">{{ __('Options') }}</h5>
                                    <p class="text-muted">{{ __('Edit options in the options management section') }}</p>
                                    <a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $currentLocale]) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="la la-list"></i> {{ __('Manage Options') }}
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                <a href="{{ route('question.index', ['lang' => $currentLocale]) }}" class="btn btn-light">{{ __('Cancel') }}</a>
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
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обновляем hidden поле при переключении табов
    const questionTabs = document.querySelectorAll('#questionLangTabs .nav-link');
    questionTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = event.target;
            const locale = target.id.replace('tab-', '');
            document.querySelector('input[name="current_locale"]').value = locale;
        });
    });

    // Показ/скрытие полей в зависимости от типа вопроса
    const questionType = document.getElementById('questionType');
    const maxChoicesContainer = document.getElementById('maxChoicesContainer');
    const optionsContainer = document.getElementById('optionsContainer');

    function toggleFields() {
        const type = questionType.value;

        if (type === 'multiple') {
            maxChoicesContainer.style.display = 'block';
            optionsContainer.style.display = 'block';
        } else if (type === 'single') {
            maxChoicesContainer.style.display = 'none';
            optionsContainer.style.display = 'block';
        } else {
            maxChoicesContainer.style.display = 'none';
            optionsContainer.style.display = 'none';
        }
    }

    questionType.addEventListener('change', toggleFields);
    toggleFields(); // Инициализация при загрузке

    // Валидация формы
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const quizSelect = document.querySelector('select[name="quiz_id"]');
        const questionTypeSelect = document.querySelector('select[name="type"]');

        if (!quizSelect.value) {
            quizSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            quizSelect.classList.remove('is-invalid');
        }

        if (!questionTypeSelect.value) {
            questionTypeSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            questionTypeSelect.classList.remove('is-invalid');
        }

        // Проверка, что английский перевод заполнен (обязательное поле)
        const enTranslation = document.querySelector('textarea[name="translations[en][content]"]');
        if (!enTranslation.value.trim()) {
            enTranslation.classList.add('is-invalid');
            isValid = false;
        } else {
            enTranslation.classList.remove('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            alert('{{ __("Please fill required fields") }}');
        }
    });
});
</script>

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
.invalid-feedback {
    display: block;
}
.text-danger {
    color: #dc3545 !important;
}
.text-muted {
    color: #6c757d !important;
}
</style>
@endpush
