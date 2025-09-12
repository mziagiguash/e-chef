@extends('backend.layouts.app')
@section('title', __('Question List'))

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
                    <h4>{{ __('Question List') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Questions') }}</li>
                </ol>
            </div>
        </div>

        <!-- Языковые табы -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="questionLangTabs" role="tablist">
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
                        <h4 class="card-title">{{ __('All Questions List') }}</h4>
                        <a href="{{ route('question.create', ['lang' => $currentLocale]) }}" class="btn btn-primary">+ {{ __('Add new') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="questionsTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{ __('Quiz') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Question') }}</th>
                                        <th>{{ __('Options') }}</th>
                                        <th>{{ __('Correct Answers') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($questions as $question)
                                    <tr>
                                        <td>
                                            @if($question->quiz)
                                                {{ $question->quiz->title }}
                                            @else
                                                <span class="text-danger">{{ __('No quiz') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->type == 'single')
                                                <span class="badge badge-info">{{ __('Single Choice') }}</span>
                                            @elseif($question->type == 'multiple')
                                                <span class="badge badge-warning">{{ __('Multiple Choice') }}</span>
                                            @elseif($question->type == 'text')
                                                <span class="badge badge-success">{{ __('Text Answer') }}</span>
                                            @elseif($question->type == 'rating')
                                                <span class="badge badge-primary">{{ __('Rating') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $question->type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>
                                                @php
                                                    $translation = $question->translations->where('locale', $currentLocale)->first();
                                                @endphp
                                                {{ $translation->content ?? $question->content }}
                                            </strong>
                                            @if(!$translation)
                                                <span class="badge badge-warning ml-1">No {{ $currentLocale }} translation</span>
                                            @endif
                                            @if(config('app.debug'))
                                            <br><small class="text-muted">ID: {{ $question->id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->options->count() > 0)
                                                <span class="badge badge-light">{{ $question->options->count() }} {{ __('options') }}</span>
                                            @else
                                                <span class="text-muted">{{ __('No options') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->correctOptions->count() > 0)
                                                <span class="badge badge-success">{{ $question->correctOptions->count() }} {{ __('correct') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('No correct answers') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('question.edit', ['question' => $question->id, 'lang' => $currentLocale]) }}"
                                                   class="btn btn-sm btn-primary mx-1" title="{{ __('Edit') }}">
                                                    <i class="la la-pencil"></i>
                                                </a>
                                                <form action="{{ route('question.destroy', $question->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mx-1"
                                                            title="{{ __('Delete') }}"
                                                            onclick="return confirm('{{ __('Are you sure you want to delete this question?') }}')">
                                                        <i class="la la-trash-o"></i>
                                                    </button>
                                                </form>
                                                @if($question->options->count() > 0)
                                                <a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $currentLocale]) }}"
                                                   class="btn btn-sm btn-info mx-1" title="{{ __('View Options') }}">
                                                    <i class="la la-list"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-info">
                                                {{ __('No questions found for this language.') }}
                                                <a href="{{ route('question.create', ['lang' => $currentLocale]) }}" class="btn btn-sm btn-primary ml-2">
                                                    {{ __('Create First Question') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->

<div class="d-flex justify-content-center mt-3">
<nav aria-label="Questions pagination">
    <ul class="pagination pagination-sm">
        {{-- Previous Page Link --}}
        @if($questions->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $questions->appends(['lang' => $currentLocale])->previousPageUrl() }}" rel="prev">&laquo;</a>
            </li>
        @endif
        {{-- Pagination Elements --}}
        @foreach($questions->getUrlRange(1, $questions->lastPage()) as $page => $url)
            @if($page == $questions->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $url }}?lang={{ $currentLocale }}">{{ $page }}</a>
                </li>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($questions->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $questions->appends(['lang' => $currentLocale])->nextPageUrl() }}" rel="next">&raquo;</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">&raquo;</span>
            </li>
        @endif
    </ul>
</nav>
            </div>        </div>
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
    $('#questionsTable').DataTable({
        responsive: true,
        ordering: true,
        searching: true,
        paging: false,
        info: false,
        language: {
            search: "{{ __('Search') }}",
            searchPlaceholder: "{{ __('Search questions...') }}"
        }
    });

    // AJAX подгрузка при переключении языковых табов
    $('#questionLangTabs').on('click', '.nav-link', function(e) {
        e.preventDefault();
        const locale = $(this).data('locale');
        const $tab = $(this);

        // Показываем индикатор загрузки
        showLoading();

        // Формируем URL для AJAX запроса
        const url = '{{ route("question.index") }}?lang=' + locale + '&ajax=1';

        // AJAX запрос
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                // Обновляем таблицу и пагинацию
                updateContent(data, locale, $tab);
            },
            error: function() {
                hideLoading();
                alert('Error loading data');
            }
        });
    });

    // Обработка кнопок пагинации через AJAX
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href') + '&ajax=1';

        // Показываем индикатор загрузки
        showLoading();

        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                // Обновляем таблицу и пагинацию
                updateContent(data);
            },
            error: function() {
                hideLoading();
                alert('Error loading page');
            }
        });
    });

    // Функция показа загрузки
    function showLoading() {
        $('.card-body').append('<div class="loading-overlay"><div class="spinner-border text-primary"></div></div>');
    }

    // Функция скрытия загрузки
    function hideLoading() {
        $('.loading-overlay').remove();
    }

    // Функция обновления контента
    function updateContent(data, locale = null, $tab = null) {
        try {
            // Если это AJAX ответ (только часть страницы)
            if (typeof data === 'object' && data.table && data.pagination) {
                $('.table-responsive').html(data.table);
                $('.pagination').html(data.pagination);
            }
            // Если это HTML страница (извлекаем нужные части)
            else if (typeof data === 'string') {
                const $data = $(data);
                $('.table-responsive').html($data.find('.table-responsive').html());
                $('.pagination').html($data.find('.pagination').html());
            }

            // Переинициализируем DataTable
            $('#questionsTable').DataTable({
                responsive: true,
                ordering: true,
                searching: true,
                paging: false,
                info: false,
                language: {
                    search: "{{ __('Search') }}",
                    searchPlaceholder: "{{ __('Search questions...') }}"
                }
            });

            // Обновляем активный таб если переключение языка
            if (locale && $tab) {
                $('#questionLangTabs .nav-link').removeClass('active');
                $tab.addClass('active');
                window.history.pushState({}, '', '{{ route("question.index") }}?lang=' + locale);
            }

        } catch (error) {
            console.error('Error updating content:', error);
            // Если AJAX fails, делаем обычную перезагрузку
            if (locale) {
                window.location.href = '{{ route("question.index") }}?lang=' + locale;
            } else {
                window.location.reload();
            }
        } finally {
            hideLoading();
        }
    }
});
</script>

<style>
.badge-info { background-color: #17a2b8; }
.badge-warning { background-color: #ffc107; }
.badge-success { background-color: #28a745; }
.badge-primary { background-color: #007bff; }
.badge-secondary { background-color: #6c757d; }
.badge-light { background-color: #f8f9fa; color: #212529; }
.badge-danger { background-color: #dc3545; }
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

/* Стили для пагинации */
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
