@extends('backend.layouts.app')
@section('title', 'Course Material List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course Material List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('material.index') }}">Course Materials</a></li>
                    <li class="breadcrumb-item active">All Course Material</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        {{-- Language Tabs --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="materialLangTabs" role="tablist">
                    @foreach($locales as $code => $name)
                        <li class="nav-item">
                            <a href="#" class="nav-link lang-tab {{ $code === $appLocale ? 'active' : '' }}" data-locale="{{ $code }}">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
<div class="row mb-3">
    <div class="col-lg-12">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item"><a href="#list-view" data-toggle="tab"
                            class="nav-link btn-primary mr-1 show active">List View</a></li>
                    <li class="nav-item"><a href="#grid-view" data-toggle="tab" class="nav-link btn-primary">Grid
                            View</a></li>
                </ul>
            </div>
</div>
        {{-- Views --}}
        <div class="row tab-content">

            {{-- List View --}}
            <div id="list-view" class="tab-pane fade active show col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List View</h4>
                        <a href="{{ localeRoute('material.create') }}" class="btn btn-primary">+ Add new</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Lesson</th>
                                        <th>Type</th>
                                        <th>File</th>
                                        <th>Text</th>
                                        <th>URL</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
    @forelse($materials as $material)
        <tr class="material-row"
            data-titles='@json($material->translations->pluck("title","locale"))'
            data-content='@json($material->translations->pluck("content_text","locale"))'>
            <td>{{ $material->id }}</td>
            <td class="material-title"></td>
            <td>{{ $material->lesson?->display_title }}</td> {{-- Исправлено здесь --}}
            <td>{{ ucfirst($material->type) }}</td>
            <td>
                @if($material->type === 'video' || $material->type === 'document')
                    <embed src="{{ asset('uploads/courses/contents/'.$material->content) }}" width="200px" height="100px" />
                @endif
            </td>
            <td class="material-text"></td>
            <td>{{ $material->content_url }}</td>
            <td>
                <a href="{{ localeRoute('material.edit', encryptor('encrypt', $material->id)) }}" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>
                <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="$('#form{{$material->id}}').submit()"><i class="la la-trash-o"></i></a>
                <form id="form{{$material->id}}" action="{{ localeRoute('material.destroy', encryptor('encrypt', $material->id)) }}" method="post">
                    @csrf
                    @method('DELETE')
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">No Course Material Found</td>
        </tr>
    @endforelse
</tbody>
                            </table>
                            {{ $materials->links() }}
                        </div>
                    </div>
                </div>
            </div>

{{-- Grid View --}}
<div id="grid-view" class="tab-pane fade col-lg-12">
    <div class="row">
        @forelse($materials as $material)
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card h-100 material-card"
                     data-titles='@json($material->translations->pluck("title","locale"))'
                     data-content='@json($material->translations->pluck("content_text","locale"))'>
                    <div class="card-body">
                        <h5 class="material-title"></h5>
                        <p><strong>Lesson:</strong> {{ $material->lesson?->display_title }}</p> {{-- Исправлено здесь --}}
                        <p><strong>Type:</strong> {{ ucfirst($material->type) }}</p>
                        @if($material->type === 'video' || $material->type === 'document')
                            <embed src="{{ asset('uploads/courses/contents/'.$material->content) }}" width="200px" height="100px" />
                        @endif
                        <p class="material-text mt-2"></p>
                        <p><strong>URL:</strong> {{ $material->content_url }}</p>
                        <div class="mt-2">
                            <a href="{{ localeRoute('material.edit', encryptor('encrypt', $material->id)) }}" class="btn btn-sm btn-primary"><i class="la la-pencil"></i></a>
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="$('#form{{$material->id}}').submit()"><i class="la la-trash-o"></i></a>
                            <form id="form{{$material->id}}" action="{{ localeRoute('material.destroy', encryptor('encrypt', $material->id)) }}" method="post">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">No Course Material Found</div>
        @endforelse
    </div>
</div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const savedLocale = localStorage.getItem('materials_lang') || '{{ app()->getLocale() }}';

    function updateMaterialLanguage(locale) {
        document.querySelectorAll('.material-row, .material-card').forEach(el => {
            const titles = JSON.parse(el.dataset.titles || '{}');
            const contents = JSON.parse(el.dataset.content || '{}');

            const titleText = titles[locale] ?? Object.values(titles)[0] ?? 'No Title';
            const contentText = contents[locale] ?? Object.values(contents)[0] ?? '';

            el.querySelectorAll('.material-title').forEach(t => t.textContent = titleText);
            el.querySelectorAll('.material-text').forEach(t => t.textContent = contentText);
        });
    }

    document.querySelectorAll('#materialLangTabs .lang-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.locale === savedLocale);

        tab.addEventListener('click', function(e){
            e.preventDefault();
            const locale = this.dataset.locale;
            localStorage.setItem('materials_lang', locale);

            document.querySelectorAll('#materialLangTabs .lang-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            updateMaterialLanguage(locale);
        });
    });

    updateMaterialLanguage(savedLocale);
});
// Переключение между List и Grid View
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const view = this.dataset.view;

        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        document.querySelectorAll('#list-view, #grid-view').forEach(v => v.classList.remove('active','show'));
        document.getElementById(view).classList.add('active','show');
    });
});

</script>
@endpush
