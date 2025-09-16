@extends('backend.layouts.app')
@section('title', __('Add Question'))

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
                    <h4>{{ __('Add Question') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index') }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Add Question') }}</li>
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
                        <form action="{{ route('question.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="current_locale" value="{{ $currentLocale }}">

                            <!-- Основные поля (не зависят от языка) -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">{{ __('Quiz') }} <span class="text-danger">*</span></label>
        <select class="form-control @error('quiz_id') is-invalid @enderror" name="quiz_id" required id="quizSelect">
            <option value="">{{ __('Select Quiz') }}</option>
            @forelse ($quizzes as $quiz)
            <option value="{{ $quiz->id }}" {{ old('quiz_id') == $quiz->id ? 'selected' : '' }}
                    data-title-en="{{ $quiz->getTranslation('en', 'title') }}"
                    data-title-ru="{{ $quiz->getTranslation('ru', 'title') }}"
                    data-title-ka="{{ $quiz->getTranslation('ka', 'title') }}">
                {{ $quiz->getTranslation($currentLocale, 'title') }}
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
                                            <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>
                                                {{ __('Single Choice') }}
                                            </option>
                                            <option value="multiple" {{ old('type') == 'multiple' ? 'selected' : '' }}>
                                                {{ __('Multiple Choice') }}
                                            </option>
                                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>
                                                {{ __('Text Answer') }}
                                            </option>
                                            <option value="rating" {{ old('type') == 'rating' ? 'selected' : '' }}>
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
                                               name="points" value="{{ old('points', 1) }}" min="1">
                                        @error('points')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Order') }}</label>
                                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                                               name="order" value="{{ old('order', 0) }}" min="0">
                                        @error('order')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Required') }}</label>
                                        <select class="form-control @error('is_required') is-invalid @enderror" name="is_required">
                                            <option value="1" {{ old('is_required', 1) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                            <option value="0" {{ old('is_required', 1) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
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
                                               name="max_choices" value="{{ old('max_choices', 1) }}" min="1">
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

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Question Content') }} <span class="text-danger">*</span></label>
                                                <textarea class="form-control @error('translations.'.$localeCode.'.content') is-invalid @enderror"
                                                          name="translations[{{ $localeCode }}][content]"
                                                          rows="3"
                                                          placeholder="{{ __('Enter question content') }}"
                                                          {{ $localeCode === 'en' ? 'required' : '' }}>{{ old('translations.'.$localeCode.'.content') }}</textarea>
                                                @error('translations.'.$localeCode.'.content')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="translations[{{ $localeCode }}][locale]" value="{{ $localeCode }}">
                                </div>
                                @endforeach
                            </div>

                            <!-- Динамические опции (только для single и multiple choice) -->
                            <div class="row mt-4" id="optionsContainer" style="display: none;">
                                <div class="col-12">
                                    <h5 class="mb-3">{{ __('Options') }}</h5>

                                    <div id="optionsList">
                                        <!-- Опции будут добавляться динамически -->
                                    </div>

                                    <button type="button" class="btn btn-sm btn-primary" id="addOption">
                                        <i class="la la-plus"></i> {{ __('Add Option') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
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

    // Добавление опций
    const addOptionBtn = document.getElementById('addOption');
    const optionsList = document.getElementById('optionsList');
    let optionCount = 0;

    addOptionBtn.addEventListener('click', function() {
        optionCount++;
        const optionHtml = `
            <div class="card mb-3 option-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">{{ __('Option Text') }}</label>
                            <input type="text" class="form-control" name="options[${optionCount}][text]" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('Correct') }}</label>
                            <select class="form-control" name="options[${optionCount}][is_correct]">
                                <option value="0">{{ __('No') }}</option>
                                <option value="1">{{ __('Yes') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('Order') }}</label>
                            <input type="number" class="form-control" name="options[${optionCount}][order]" value="${optionCount}" min="0">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2 remove-option">
                        <i class="la la-trash"></i> {{ __('Remove') }}
                    </button>
                </div>
            </div>
        `;
        optionsList.insertAdjacentHTML('beforeend', optionHtml);
    });

    // Удаление опций
    optionsList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-option')) {
            e.target.closest('.option-item').remove();
        }
    });

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
// Обновление названий квизов при переключении языковых табов
document.addEventListener('DOMContentLoaded', function() {
    const questionTabs = document.querySelectorAll('#questionLangTabs .nav-link');
    const quizSelect = document.getElementById('quizSelect');

    function updateQuizTitles(locale) {
        if (!quizSelect) return;

        const options = quizSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value !== '') {
                const title = option.getAttribute('data-title-' + locale) ||
                             option.getAttribute('data-title-en');
                option.textContent = title;
            }
        });
    }

    questionTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = event.target;
            const locale = target.id.replace('tab-', '');
            updateQuizTitles(locale);
        });
    });

    // Инициализация при загрузке
    const activeTab = document.querySelector('#questionLangTabs .nav-link.active');
    if (activeTab) {
        const currentLocale = activeTab.id.replace('tab-', '');
        updateQuizTitles(currentLocale);
    }
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
.option-item {
    border-left: 3px solid #007bff;
}
</style>
@endpush
