@extends('backend.layouts.app')
@section('title', isset($question) ? __('Options for Question') . ': ' . ($question->getTranslation('content', $locale) ?? 'N/A') : __('All Options'))

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        @if(isset($question) && $question)
                            {{ __('Options for Question') }}: {{ $question->getTranslation('content', $locale) ?? 'Question #' . $question->id }}
                        @else
                            {{ __('All Options') }}
                        @endif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index', ['lang' => $locale]) }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item active">
                        @if(isset($question) && $question)
                            {{ __('Options') }}
                        @else
                            {{ __('All Options') }}
                        @endif
                    </li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы Bootstrap -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="optionLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                           href="{{ route('option.index', array_merge(['lang' => $localeCode], isset($question) ? ['question_id' => $question->id] : [])) }}"
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
                        <h5 class="card-title">
                            @if(isset($question) && $question)
                                {{ __('Options for Question') }}: {{ $question->getTranslation('content', $currentLocale) ?? 'Question #' . $question->id }}
                            @else
                                {{ __('All Options List') }}
                            @endif
                        </h5>

                        <!-- Кнопка "Add Option" с проверкой -->
                        @if(isset($question) && $question)
                            <a href="{{ route('option.create', ['question_id' => $question->id, 'lang' => $currentLocale]) }}"
                               class="btn btn-primary btn-sm float-right">
                                <i class="fa fa-plus"></i> {{ __('Add Option') }}
                            </a>
                        @else
                            <a href="{{ route('option.create', ['lang' => $currentLocale]) }}"
                               class="btn btn-primary btn-sm float-right">
                                <i class="fa fa-plus"></i> {{ __('Add Option') }}
                            </a>
                        @endif

                        <a href="{{ route('question.index', ['lang' => $currentLocale]) }}"
                           class="btn btn-secondary btn-sm mr-2 float-right">
                            <i class="fa fa-arrow-left"></i> {{ __('Back to Questions') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if(!isset($question)) <!-- Показываем колонку "Question" только в общем списке -->
                                        <th>{{ __('Question') }}</th>
                                        @endif
                                        <th>{{ __('Option Text') }}</th>
                                        <th>{{ __('Key') }}</th>
                                        <th>{{ __('Correct') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($options as $option)
                                        <tr>
                                            <td>{{ $loop->iteration + (($options->currentPage() - 1) * $options->perPage()) }}</td>

                                            @if(!isset($question)) <!-- Показываем вопрос только в общем списке -->
                                            <td>
                                                @if($option->question)
                                                    <!-- Отображаем перевод вопроса для всех языков -->
                                                    @foreach($locales as $localeCode => $localeName)
                                                        <span class="question-content lang-{{ $localeCode }}"
                                                              style="{{ $localeCode === $currentLocale ? '' : 'display:none' }}">
                                                            {{ Str::limit($option->question->getTranslation('content', $localeCode) ?? 'Question #' . $option->question->id, 30) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-danger">{{ __('No Question') }}</span>
                                                @endif
                                            </td>
                                            @endif

                                            <td>
                                                <!-- Отображаем перевод опции для всех языков -->
                                                @foreach($locales as $localeCode => $localeName)
                                                    <span class="option-text lang-{{ $localeCode }}"
                                                          style="{{ $localeCode === $currentLocale ? '' : 'display:none' }}">
                                                        {{ $option->getTranslation('text', $localeCode) ?? $option->text ?? __('No translation') }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ strtoupper($option->key) }}</span>
                                            </td>
                                            <td>
                                                @if($option->is_correct)
                                                    <span class="badge badge-success">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $option->order }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <!-- Кнопка toggle correctness -->


                                             
                                                    <!-- Кнопка Delete -->
                                                    <form action="{{ route('option.destroy', $option->id) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger ml-1"
                                                                onclick="return confirm('{{ __('Are you sure you want to delete this option?') }}')">
                                                            <i class="fa fa-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ isset($question) ? 6 : 7 }}" class="text-center">
                                                {{ __('No options found.') }}
                                                @if(isset($question) && $question)
                                                    <a href="{{ route('option.create', ['question_id' => $question->id, 'lang' => $currentLocale]) }}">
                                                        {{ __('Create first option') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('option.create', ['lang' => $currentLocale]) }}">
                                                        {{ __('Create first option') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $options->appends(array_merge(['lang' => $currentLocale], isset($question) ? ['question_id' => $question->id] : []))->links() }}
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
.btn-group .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}
.btn-group {
    display: flex;
    flex-wrap: wrap;
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

    // Функция для переключения языков (если нужно динамическое переключение)
    function switchLanguage(locale) {
        // Скрываем все переводы
        document.querySelectorAll('.question-content, .option-text').forEach(el => {
            el.style.display = 'none';
        });

        // Показываем переводы для выбранного языка
        document.querySelectorAll('.lang-' + locale).forEach(el => {
            el.style.display = '';
        });
    }
});
</script>
@endpush
