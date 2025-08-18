@extends('backend.layouts.app')
@section('title', 'Category List')

@push('styles')
    <!-- Datatable -->
    <link href="{{ asset('public/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="content-body">
        <div class="container-fluid">

            <div class="row page-titles mx-0">
                <div class="col-sm-6 p-md-0">
                    <div class="welcome-text">
                        <h4>Category List</h4>
                    </div>
                </div>
                <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('courseCategory.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('courseCategory.index') }}">All Categories</a>
                        </li>
                    </ol>
                </div>
            </div>

            @php
                // Список локалей (если нужно — вынеси в config)
                $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
                $appLocale = app()->getLocale();
            @endphp

            <div class="row mb-3">
                <div class="col-md-6">
                    {{-- Переключатель языков для просмотра --}}
                    <ul class="nav nav-tabs" id="categoryLangTabs" role="tablist">
                        @foreach ($locales as $localeCode => $localeName)
                            <li class="nav-item" role="presentation">
                                <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}"
                                    data-locale="{{ $localeCode }}">{{ $localeName }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-md-6 text-end">
                    <a href="{{ route('courseCategory.create') }}" class="btn btn-primary">+ Add new</a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row tab-content">
                        <div id="list-view" class="tab-pane fade active show col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">All Categories List </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example3" class="display" style="min-width: 845px">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('#') }}</th>
                                                    <th>{{ __('Category Name') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Category Image') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($data as $d)
                                                    @php
                                                        // защищённое декодирование JSON -> массив переводов
                                                        $defaultTranslations = ['en' => '', 'ru' => '', 'ka' => ''];
                                                        $decoded = json_decode($d->category_name, true);
                                                        $names = is_array($decoded)
                                                            ? array_merge($defaultTranslations, $decoded)
                                                            : $defaultTranslations;
                                                    @endphp
                                                    <tr>
                                                        <td><strong>{{ $d->id }}</strong></td>

                                                        {{-- Вставляем все переводы (спрятаны/показаны JS'ом по выбранной локали) --}}
                                                        <td>
                                                            @foreach ($locales as $localeCode => $localeName)
                                                                <span class="cat-name lang-{{ $localeCode }}"
                                                                    style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                                    {{ $names[$localeCode] ?? '' }}
                                                                </span>
                                                            @endforeach
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="badge {{ $d->category_status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                                @if ($d->category_status == 1)
                                                                    {{ __('Active') }}@else{{ __('Inactive') }}
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <img class="rounded" width="200" height="100"
                                                                src="{{ asset('public/uploads/courseCategories/' . ($d->category_image ?? '')) }}"
                                                                alt="">
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('courseCategory.edit', $d->id) }}"
                                                                class="btn btn-sm btn-primary" title="Edit"><i
                                                                    class="la la-pencil"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                                                title="Delete"
                                                                onclick="$('#form{{ $d->id }}').submit()"><i
                                                                    class="la la-trash-o"></i></a>
                                                            <form id="form{{ $d->id }}"
                                                                action="{{ route('courseCategory.destroy', $d->id) }}"
                                                                method="post" style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <th colspan="7" class="text-center">No Category Found</th>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Grid view --}}
                        <div id="grid-view" class="tab-pane fade col-lg-12">
                            <div class="row">
                                @forelse ($data as $d)
                                    @php
                                        $defaultTranslations = ['en' => '', 'ru' => '', 'ka' => ''];
                                        $decoded = json_decode($d->category_name, true);
                                        $names = is_array($decoded)
                                            ? array_merge($defaultTranslations, $decoded)
                                            : $defaultTranslations;
                                    @endphp

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
                                        <div class="card card-profile">
                                            <div class="card-header justify-content-end pb-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-link" type="button" data-toggle="dropdown">
                                                        <span class="dropdown-dots fs--1"></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right border py-0">
                                                        <div class="py-2">
                                                            <a class="dropdown-item"
                                                                href="{{ route('courseCategory.edit', $d->id) }}">Edit</a>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                                onclick="$('#form{{ $d->id }}').submit()">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-2">
                                                <div class="text-center">
                                                    <div class="profile-photo">
                                                        <img src="{{ asset('public/uploads/courseCategories/' . ($d->category_image ?? '')) }}"
                                                            class="w-100" alt="">
                                                    </div>

                                                    {{-- все переводы, видимый тот, что активен --}}
                                                    @foreach ($locales as $localeCode => $localeName)
                                                        <h3 class="mt-4 mb-1 cat-name-grid lang-{{ $localeCode }}"
                                                            style="{{ $localeCode === $appLocale ? '' : 'display:none' }}">
                                                            {{ $names[$localeCode] ?? '' }}</h3>
                                                    @endforeach

                                                    <ul class="list-group mb-3 list-group-flush">
                                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                                            <span>#Sl.</span><strong>{{ $d->id }}</strong>
                                                        </li>
                                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                                            <span class="mb-0">Status:</span>
                                                            <strong><span
                                                                    class="badge {{ $d->category_status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                                    @if ($d->category_status == 1)
                                                                        {{ __('Active') }}@else{{ __('Inactive') }}
                                                                    @endif
                                                                </span></strong>
                                                        </li>
                                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                                            <span class="mb-0">Created At :</span>
                                                            <strong>{{ $d->created_at }}</strong>
                                                        </li>
                                                    </ul>
                                                    <a class="btn btn-outline-primary btn-rounded mt-3 px-4"
                                                        href="javascript:void(0);">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="form{{ $d->id }}"
                                        action="{{ route('courseCategory.destroy', $d->id) }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @empty
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="card card-profile">
                                            <div class="card-body pt-2">
                                                <div class="text-center">
                                                    <p class="mt-3 px-4">Category Not Found</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <!-- Datatable -->
    <script src="{{ asset('public/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins-init/datatables.init.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // локаль по умолчанию (серверная)
            const initialLocale = localStorage.getItem('categories_lang') || '{{ $appLocale }}';

            function setActiveLocale(locale) {
                // переключаем активный таб
                document.querySelectorAll('.lang-tab').forEach(tab => {
                    tab.classList.toggle('active', tab.dataset.locale === locale);
                });

                // показываем/скрываем имена в таблице
                document.querySelectorAll('.cat-name').forEach(el => {
                    el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
                });

                // показываем/скрываем имена в grid
                document.querySelectorAll('.cat-name-grid').forEach(el => {
                    el.style.display = el.classList.contains('lang-' + locale) ? '' : 'none';
                });
            }

            // назначаем обработчики на табы
            document.querySelectorAll('.lang-tab').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const locale = this.dataset.locale;
                    localStorage.setItem('categories_lang', locale);
                    setActiveLocale(locale);
                });
            });

            // инициализация
            setActiveLocale(initialLocale);
        });
    </script>
@endpush
