@extends('backend.layouts.app')
@section('title', __('Add Option'))

@php
    // Временное решение если переменные не переданы из контроллера
    $currentLocale = $currentLocale ?? request()->get('lang', app()->getLocale());
    $locales = $locales ?? [
        'en' => 'English',
        'ru' => 'Русский',
        'ka' => 'ქართული'
    ];
    $questions = $questions ?? \App\Models\Question::with(['quiz', 'translations'])->get();
    $questionId = $questionId ?? request('question_id', $questions->first()->id ?? null);
@endphp

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
                    <h4>{{ __('Add Option') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('option.index') }}">{{ __('Options') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Add Option') }}</li>
                </ol>
            </div>
        </div>

        @if($questions->count() === 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <h4>{{ __('No Questions Available') }}</h4>
                    <p>{{ __('Please create a question first before adding options.') }}</p>
                    <a href="{{ route('question.create') }}" class="btn btn-primary mt-2">
                        {{ __('Create First Question') }}
                    </a>
                </div>
            </div>
        </div>
        @else
        <!-- Языковые табы Bootstrap -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="optionLangTabs" role="tablist">
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
                        <form action="{{ route('option.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="current_locale" value="{{ $currentLocale }}">

                            <!-- Основные поля (не зависят от языка) -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Question') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('question_id') is-invalid @enderror" name="question_id" required>
                                            <option value="">{{ __('Select Question') }}</option>
                                            @forelse ($questions as $question)
                                            <option value="{{ $question->id }}" {{ old('question_id', $questionId) == $question->id ? 'selected' : '' }}>
                                                {{ $question->text }}
                                                @if($question->quiz)
                                                    ({{ __('Quiz') }}: {{ $question->quiz->title }})
                                                @endif
                                            </option>
                                            @empty
                                            <option value="">{{ __('No Questions Found') }}</option>
                                            @endforelse
                                        </select>
                                        @error('question_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Order') }}</label>
                                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                                               name="order" value="{{ old('order', 0) }}" min="0">
                                        @error('order')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Is Correct') }}</label>
                                        <select class="form-control @error('is_correct') is-invalid @enderror" name="is_correct">
                                            <option value="1" {{ old('is_correct') == 1 ? 'selected' : '' }}>{{ __('Correct') }}</option>
                                            <option value="0" {{ old('is_correct', 0) == 0 ? 'selected' : '' }}>{{ __('Wrong') }}</option>
                                        </select>
                                        @error('is_correct')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Содержимое табов -->
                            <div class="tab-content" id="optionTabContent">
                                @foreach($locales as $localeCode => $localeName)
                                <div class="tab-pane fade {{ $localeCode === $currentLocale ? 'show active' : '' }}"
                                     id="content-{{ $localeCode }}" role="tabpanel"
                                     aria-labelledby="tab-{{ $localeCode }}">

                                    <h6 class="text-primary mt-4">{{ $localeName }} {{ __('Translation') }}</h6>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Option Text') }} <span class="text-danger">*</span></label>
                                                <textarea class="form-control @error('translations.'.$localeCode.'.option_text') is-invalid @enderror"
                                                          name="translations[{{ $localeCode }}][option_text]"
                                                          rows="3"
                                                          placeholder="{{ __('Enter option text') }}"
                                                          {{ $localeCode === 'en' ? 'required' : '' }}>{{ old('translations.'.$localeCode.'.option_text') }}</textarea>
                                                @error('translations.'.$localeCode.'.option_text')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="translations[{{ $localeCode }}][locale]" value="{{ $localeCode }}">
                                </div>
                                @endforeach
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                <a href="{{ route('option.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обновляем hidden поле при переключении табов
    const optionTabs = document.querySelectorAll('#optionLangTabs .nav-link');
    optionTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = event.target;
            const locale = target.id.replace('tab-', '');
            document.querySelector('input[name="current_locale"]').value = locale;
        });
    });

    // Валидация формы
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const questionSelect = document.querySelector('select[name="question_id"]');

            if (!questionSelect.value) {
                questionSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                questionSelect.classList.remove('is-invalid');
            }

            // Проверка, что английский перевод заполнен (обязательное поле)
            const enTranslation = document.querySelector('textarea[name="translations[en][option_text]"]');
            if (enTranslation && !enTranslation.value.trim()) {
                enTranslation.classList.add('is-invalid');
                isValid = false;
            } else if (enTranslation) {
                enTranslation.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                alert('{{ __("Please fill required fields") }}');
            }
        });
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
</style>
@endpush
