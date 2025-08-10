@extends('backend.layouts.app')
@section('title', 'Instructor List')

@push('styles')
<!-- Datatable -->
<link href="{{ asset('public/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <!-- row -->
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Instructor List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('instructor.index') }}">Instructors</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('instructor.index') }}">All Instructors</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <div class="row">
            <div class="col-lg-12 mb-3">
                <ul class="nav nav-pills" id="instructorLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link lang-tab {{ $localeCode === $appLocale ? 'active' : '' }}" data-locale="{{ $localeCode }}">
                                {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Instructors List</h4>
                        <a href="{{ route('instructor.create') }}" class="btn btn-primary">+ Add New</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Contact') }}</th>
                                        <th>{{ __('Designation') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($instructor as $d)
                                        <tr>
                                            <td>
                                                <img class="rounded-circle" width="35" height="35"
                                                    src="{{ asset('public/uploads/instructors/' . ($d->image ?? 'default.png')) }}" alt="Instructor Image">
                                            </td>
                                            <td><strong>{{ $d->name }}</strong></td>
                                            <td>{{ $d->email }}</td>
                                            <td>{{ $d->contact }}</td>
                                            <td>{{ $d->designation }}</td>
                                            <td>
                                                <span class="badge {{ $d->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $d->status == 1 ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('instructor.edit', encryptor('encrypt', $d->id)) }}" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="la la-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('delete-form-{{ $d->id }}').submit(); }">
                                                    <i class="la la-trash-o"></i>
                                                </button>
                                                <form id="delete-form-{{ $d->id }}" action="{{ route('instructor.destroy', encryptor('encrypt', $d->id)) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No Instructor Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
    $(document).ready(function () {
        $('#example3').DataTable();

        // Если нужно реализовать переключение по языкам в таблице — добавь здесь JS
        // $('.lang-tab').on('click', function(e) {
        //     e.preventDefault();
        //     // Ваш код переключения данных по языку
        // });
    });
</script>
@endpush
