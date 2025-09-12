@extends('backend.layouts.app')
@section('title', __('Option List'))

@push('styles')
<!-- Datatable -->
<link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ __('Option List') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index') }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Options') }}</li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="optionLangTabs" role="tablist">
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
                        <h4 class="card-title">{{ __('All Options List') }}</h4>
                        @if(request()->has('question_id') && $question = \App\Models\Question::find(request('question_id')))
                            <div class="alert alert-info mt-2">
                                <strong>{{ __('Question') }}:</strong>
                                {{ $question->text }}
                                <a href="{{ route('option.index') }}" class="btn btn-sm btn-secondary ml-2">
                                    {{ __('Show All Options') }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if(request()->has('question_id'))
                                <a href="{{ route('option.create', ['question_id' => request('question_id')]) }}"
                                   class="btn btn-primary">+ {{ __('Add Option to this Question') }}</a>
                            @else
                                <a href="{{ route('option.create') }}"
                                   class="btn btn-primary">+ {{ __('Add new Option') }}</a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table id="optionsTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Question') }}</th>
                                        <th>{{ __('Option Text') }}</th>
                                        <th>{{ __('Is Correct') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($options as $option)
                                    <tr>
                                        <td>{{ $option->id }}</td>
                                        <td>
                                            @if($option->question)
                                                <a href="{{ route('option.index', ['question_id' => $option->question_id]) }}"
                                                   class="text-primary" title="{{ __('Filter by this question') }}">
                                                    {{ Str::limit($option->question->text, 50) }}
                                                </a>
                                                @if(config('app.debug'))
                                                <br><small class="text-muted">ID: {{ $option->question_id }}</small>
                                                @endif
                                            @else
                                                <span class="text-danger">{{ __('Question deleted') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $option->text }}</strong>

                                            @php
                                                $currentTranslation = $option->translations->where('locale', $currentLocale)->first();
                                            @endphp

                                            @if(!$currentTranslation)
                                                <span class="badge badge-warning ml-1">No {{ $currentLocale }} translation</span>
                                            @elseif(empty($currentTranslation->option_text))
                                                <span class="badge badge-warning ml-1">Empty translation</span>
                                            @endif

                                            @if(config('app.debug'))
                                            <br><small class="text-muted">Translations: {{ $option->translations->count() }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $option->is_correct ? 'badge-success' : 'badge-danger' }}">
                                                {{ $option->is_correct ? __('Correct') : __('Wrong') }}
                                            </span>
                                        </td>
                                        <td>{{ $option->order }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('option.edit', $option->id) }}"
                                                   class="btn btn-sm btn-primary mx-1" title="{{ __('Edit') }}">
                                                    <i class="la la-pencil"></i>
                                                </a>
                                                <form action="{{ route('option.destroy', $option->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mx-1"
                                                            title="{{ __('Delete') }}"
                                                            onclick="return confirm('{{ __('Are you sure you want to delete this option?') }}')">
                                                        <i class="la la-trash-o"></i>
                                                    </button>
                                                </form>
                                                @if($option->question)
                                                <form action="{{ route('option.toggle.correctness', $option->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $option->is_correct ? 'btn-warning' : 'btn-success' }} mx-1"
                                                            title="{{ $option->is_correct ? __('Mark as Wrong') : __('Mark as Correct') }}">
                                                        <i class="la {{ $option->is_correct ? 'la-times' : 'la-check' }}"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-info">
                                                @if(request()->has('question_id'))
                                                    {{ __('No options found for this question.') }}
                                                    <a href="{{ route('option.create', ['question_id' => request('question_id')]) }}" class="btn btn-sm btn-primary ml-2">
                                                        {{ __('Create First Option') }}
                                                    </a>
                                                @else
                                                    {{ __('No options found for this language.') }}
                                                    <a href="{{ route('option.create') }}" class="btn btn-sm btn-primary ml-2">
                                                        {{ __('Create First Option') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
@if($options->hasPages())
<div class="d-flex justify-content-center mt-3">
    <nav aria-label="Options pagination">
        <ul class="pagination pagination-sm">
            {{-- Previous Page Link --}}
            @if($options->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $options->appends(['lang' => $currentLocale, 'question_id' => request('question_id')])->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach($options->getUrlRange(1, $options->lastPage()) as $page => $url)
                @if($page == $options->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}?lang={{ $currentLocale }}&question_id={{ request('question_id') }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if($options->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $options->appends(['lang' => $currentLocale, 'question_id' => request('question_id')])->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Datatable -->
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script>
$(document).ready(function() {
    // Инициализация DataTable
    $('#optionsTable').DataTable({
        responsive: true,
        ordering: true,
        searching: true,
        paging: false,
        info: false,
        language: {
            search: "{{ __('Search') }}",
            searchPlaceholder: "{{ __('Search options...') }}"
        }
    });

    // AJAX подгрузка при переключении языковых табов
    $('#optionLangTabs .nav-link').on('click', function(e) {
        e.preventDefault();
        const locale = $(this).data('locale');
        const $tab = $(this);

        // Показываем индикатор загрузки
        $tab.append(' <span class="spinner-border spinner-border-sm" role="status"></span>');

        // Сохраняем текущие параметры
        const urlParams = new URLSearchParams(window.location.search);
        const questionId = urlParams.get('question_id');

        // Формируем URL для AJAX запроса
        let url = '{{ route("option.index") }}?lang=' + locale;
        if (questionId) {
            url += '&question_id=' + questionId;
        }

        // AJAX запрос
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                // Обновляем контент
                $('#optionLangTabs').html($(data).find('#optionLangTabs').html());
                $('.card-body').html($(data).find('.card-body').html());

                // Переинициализируем DataTable
                $('#optionsTable').DataTable({
                    responsive: true,
                    ordering: true,
                    searching: true,
                    paging: false,
                    info: false,
                    language: {
                        search: "{{ __('Search') }}",
                        searchPlaceholder: "{{ __('Search options...') }}"
                    }
                });

                // Обновляем URL в браузере без перезагрузки
                window.history.pushState({}, '', url);
            },
            error: function() {
                alert('Error loading data');
                $tab.find('.spinner-border').remove();
            }
        });
    });

    // Обработка кнопок пагинации через AJAX
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');

        // Показываем индикатор загрузки
        $('.card-body').append('<div class="loading-overlay"><div class="spinner-border text-primary"></div></div>');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('.card-body').html($(data).find('.card-body').html());

                // Переинициализируем DataTable
                $('#optionsTable').DataTable({
                    responsive: true,
                    ordering: true,
                    searching: true,
                    paging: false,
                    info: false,
                    language: {
                        search: "{{ __('Search') }}",
                        searchPlaceholder: "{{ __('Search options...') }}"
                    }
                });
            },
            error: function() {
                alert('Error loading page');
                $('.loading-overlay').remove();
            }
        });
    });
});
</script>

<style>
.badge-success { background-color: #28a745; }
.badge-danger { background-color: #dc3545; }
.badge-warning { background-color: #ffc107; }
.text-danger { color: #dc3545 !important; }
.text-muted { color: #6c757d !important; }

/* Стили для индикатора загрузки */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.pagination {
    margin: 0;
}

.page-item .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    border: 1px solid #dee2e6;
    color: #007bff;
    margin: 0 2px;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endpush
