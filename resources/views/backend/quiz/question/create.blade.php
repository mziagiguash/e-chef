@extends('backend.layouts.app')
@section('title', __('Add Question'))

@push('styles')
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
                    <li class="breadcrumb-item"><a href="{{ route('question.index', ['lang' => $currentLocale]) }}">{{ __('Questions') }}</a></li>
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

                                        @if($quizzes->isEmpty())
                                        <div class="alert alert-warning">
                                            <strong>Warning:</strong> No quizzes found in the system.
                                            <a href="{{ route('quiz.create', ['lang' => $currentLocale]) }}" class="alert-link">
                                                Create a quiz first
                                            </a>
                                        </div>
                                        @endif

                                        <select class="form-control @error('quiz_id') is-invalid @enderror" name="quiz_id" required id="quizSelect">
                                            <option value="">{{ __('Select Quiz') }}</option>
                                            @forelse ($quizzes as $quiz)
                                            <option value="{{ $quiz->id }}" {{ old('quiz_id') == $quiz->id ? 'selected' : '' }}
        data-title-en="{{ $quiz->translations->where('locale', 'en')->first()->title ?? 'Quiz ' . $quiz->id }}"
        data-title-ru="{{ $quiz->translations->where('locale', 'ru')->first()->title ?? 'Quiz ' . $quiz->id }}"
        data-title-ka="{{ $quiz->translations->where('locale', 'ka')->first()->title ?? 'Quiz ' . $quiz->id }}">
    {{ $quiz->translations->where('locale', $currentLocale)->first()->title ?? 'Quiz ' . $quiz->id }}
</option>
                                            @empty
                                            <option value="">{{ __('No Quizzes Available') }}</option>
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
                                <div class="col-lg-3 col-md-6 col-sm-12" id="correctOptionContainer" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Correct Option') }} <span class="text-danger">*</span></label>
                                        <div id="singleCorrectOptions" style="display: none;">
                                            <select class="form-control @error('correct_option') is-invalid @enderror" name="correct_option" id="correctOptionSelect">
                                                <option value="">{{ __('Select Option') }}</option>
                                                <option value="a" {{ old('correct_option') == 'a' ? 'selected' : '' }}>A</option>
                                                <option value="b" {{ old('correct_option') == 'b' ? 'selected' : '' }}>B</option>
                                                <option value="c" {{ old('correct_option') == 'c' ? 'selected' : '' }}>C</option>
                                                <option value="d" {{ old('correct_option') == 'd' ? 'selected' : '' }}>D</option>
                                            </select>
                                        </div>
                                        <div id="multipleCorrectOptions" style="display: none;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="correct_options[]" value="a" id="correctA" {{ is_array(old('correct_options')) && in_array('a', old('correct_options')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="correctA">A</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="correct_options[]" value="b" id="correctB" {{ is_array(old('correct_options')) && in_array('b', old('correct_options')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="correctB">B</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="correct_options[]" value="c" id="correctC" {{ is_array(old('correct_options')) && in_array('c', old('correct_options')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="correctC">C</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="correct_options[]" value="d" id="correctD" {{ is_array(old('correct_options')) && in_array('d', old('correct_options')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="correctD">D</label>
                                            </div>
                                        </div>
                                        @error('correct_option')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        @error('correct_options')
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
                                                <textarea class="form-control @error('content.'.$localeCode) is-invalid @enderror"
                                                          name="content[{{ $localeCode }}]"
                                                          rows="3"
                                                          placeholder="{{ __('Enter question content') }}"
                                                          {{ $localeCode === 'en' ? 'required' : '' }}>{{ old('content.'.$localeCode) }}</textarea>
                                                @error('content.'.$localeCode)
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Options for single and multiple choice questions -->
                                    <div class="row mt-4 options-container" id="options-container-{{ $localeCode }}" style="display: none;">
                                        <div class="col-12">
                                            <h6 class="mb-3">{{ __('Options') }} ({{ $localeName }})</h6>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Option A') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('options.'.$localeCode.'.a') is-invalid @enderror"
                                                               name="options[{{ $localeCode }}][a]" value="{{ old('options.'.$localeCode.'.a') }}" required>
                                                        @error('options.'.$localeCode.'.a')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Option B') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('options.'.$localeCode.'.b') is-invalid @enderror"
                                                               name="options[{{ $localeCode }}][b]" value="{{ old('options.'.$localeCode.'.b') }}" required>
                                                        @error('options.'.$localeCode.'.b')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Option C') }}</label>
                                                        <input type="text" class="form-control @error('options.'.$localeCode.'.c') is-invalid @enderror"
                                                               name="options[{{ $localeCode }}][c]" value="{{ old('options.'.$localeCode.'.c') }}">
                                                        @error('options.'.$localeCode.'.c')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Option D') }}</label>
                                                        <input type="text" class="form-control @error('options.'.$localeCode.'.d') is-invalid @enderror"
                                                               name="options[{{ $localeCode }}][d]" value="{{ old('options.'.$localeCode.'.d') }}">
                                                        @error('options.'.$localeCode.'.d')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="locale[{{ $localeCode }}]" value="{{ $localeCode }}">
                                </div>
                                @endforeach
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document loaded, initializing question form...');

    // Обновляем hidden поле при переключении табов
    const questionTabs = document.querySelectorAll('#questionLangTabs .nav-link');
    questionTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = event.target;
            const locale = target.id.replace('tab-', '');
            document.querySelector('input[name="current_locale"]').value = locale;
            updateQuizTitles(locale);
        });
    });

    // Показ/скрытие полей в зависимости от типа вопроса
    const questionType = document.getElementById('questionType');
    const correctOptionContainer = document.getElementById('correctOptionContainer');
    const singleCorrectOptions = document.getElementById('singleCorrectOptions');
    const multipleCorrectOptions = document.getElementById('multipleCorrectOptions');

    function toggleFields() {
        const type = questionType.value;
        const optionContainers = document.querySelectorAll('.options-container');
        const correctOptionSelect = document.getElementById('correctOptionSelect');

        console.log('Question type changed to:', type);

        if (type === 'single' || type === 'multiple') {
            correctOptionContainer.style.display = 'block';
            optionContainers.forEach(container => {
                container.style.display = 'block';
                // Делаем опции A и B обязательными
                const inputs = container.querySelectorAll('input[type="text"]');
                inputs[0].setAttribute('required', 'required'); // Option A
                inputs[1].setAttribute('required', 'required'); // Option B
            });

            // Показываем соответствующий тип выбора правильного ответа
            if (type === 'single') {
                singleCorrectOptions.style.display = 'block';
                multipleCorrectOptions.style.display = 'none';
                if (correctOptionSelect) {
                    correctOptionSelect.setAttribute('required', 'required');
                }
            } else {
                singleCorrectOptions.style.display = 'none';
                multipleCorrectOptions.style.display = 'block';
                if (correctOptionSelect) {
                    correctOptionSelect.removeAttribute('required');
                }
            }
        } else {
            correctOptionContainer.style.display = 'none';
            singleCorrectOptions.style.display = 'none';
            multipleCorrectOptions.style.display = 'none';
            if (correctOptionSelect) {
                correctOptionSelect.removeAttribute('required');
            }
            optionContainers.forEach(container => {
                container.style.display = 'none';
                // Убираем обязательность опций
                const inputs = container.querySelectorAll('input[type="text"]');
                inputs.forEach(input => input.removeAttribute('required'));
            });
        }
    }

    if (questionType) {
        questionType.addEventListener('change', toggleFields);
        toggleFields(); // Инициализация при загрузке
    }

    // Валидация формы
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const quizSelect = document.querySelector('select[name="quiz_id"]');
            const questionTypeSelect = document.querySelector('select[name="type"]');

            // Проверка выбора квиза
            if (!quizSelect || !quizSelect.value) {
                if (quizSelect) quizSelect.classList.add('is-invalid');
                isValid = false;
                console.error('Quiz selection is required');
            } else {
                quizSelect.classList.remove('is-invalid');
            }

            // Проверка типа вопроса
            if (!questionTypeSelect || !questionTypeSelect.value) {
                if (questionTypeSelect) questionTypeSelect.classList.add('is-invalid');
                isValid = false;
                console.error('Question type is required');
            } else {
                questionTypeSelect.classList.remove('is-invalid');
            }

            // Проверка, что английский перевод заполнен
            const enTranslation = document.querySelector('textarea[name="content[en]"]');
            if (enTranslation && !enTranslation.value.trim()) {
                enTranslation.classList.add('is-invalid');
                isValid = false;
                console.error('English translation is required');
            } else if (enTranslation) {
                enTranslation.classList.remove('is-invalid');
            }

            // Проверка правильного ответа для single и multiple choice
            const type = questionTypeSelect ? questionTypeSelect.value : '';

            if (type === 'single') {
                const correctOptionSelect = document.getElementById('correctOptionSelect');
                if (correctOptionSelect && !correctOptionSelect.value) {
                    correctOptionSelect.classList.add('is-invalid');
                    isValid = false;
                    console.error('Correct option is required for single choice questions');
                } else if (correctOptionSelect) {
                    correctOptionSelect.classList.remove('is-invalid');
                }
            } else if (type === 'multiple') {
                const correctOptions = document.querySelectorAll('input[name="correct_options[]"]:checked');
                if (correctOptions.length === 0) {
                    document.getElementById('multipleCorrectOptions').classList.add('is-invalid');
                    isValid = false;
                    console.error('At least one correct option is required for multiple choice questions');
                } else {
                    document.getElementById('multipleCorrectOptions').classList.remove('is-invalid');
                }
            }

            // Проверка опций для single и multiple choice
            if (type === 'single' || type === 'multiple') {
                const activeTab = document.querySelector('.tab-pane.show.active');
                if (activeTab) {
                    const optionA = activeTab.querySelector('input[name*="[a]"]');
                    const optionB = activeTab.querySelector('input[name*="[b]"]');

                    if (optionA && !optionA.value.trim()) {
                        optionA.classList.add('is-invalid');
                        isValid = false;
                        console.error('Option A is required');
                    }
                    if (optionB && !optionB.value.trim()) {
                        optionB.classList.add('is-invalid');
                        isValid = false;
                        console.error('Option B is required');
                    }
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('{{ __("Please fill all required fields") }}');

                // Показываем первую вкладку с ошибкой
                const firstErrorTab = document.querySelector('.is-invalid');
                if (firstErrorTab) {
                    firstErrorTab.closest('.tab-pane').classList.add('show', 'active');
                    const tabId = firstErrorTab.closest('.tab-pane').id.replace('content-', 'tab-');
                    document.getElementById(tabId).classList.add('active');
                }
            }
        });
    }

    // Обновление названий квизов при переключении языковых табов
    function updateQuizTitles(locale) {
        const quizSelect = document.getElementById('quizSelect');
        if (!quizSelect) {
            console.error('Quiz select element not found');
            return;
        }

        const options = quizSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value !== '') {
                const title = option.getAttribute('data-title-' + locale) ||
                             option.getAttribute('data-title-en') ||
                             option.textContent;
                option.textContent = title;
            }
        });
    }

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
.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
.form-check {
    margin-bottom: 0.5rem;
}
</style>
@endpush
