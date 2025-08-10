@extends('backend.layouts.app')
@section('title', 'Add Category')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{ asset('public/vendor/pickadate/themes/default.css') }}">
<link rel="stylesheet" href="{{ asset('public/vendor/pickadate/themes/default.date.css') }}">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Add Category</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('courseCategory.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('courseCategory.create') }}">Add Category</a></li>
                </ol>
            </div>
        </div>

        @php
            $categoryNames = old('category_name') ?? ['en' => '', 'ru' => '', 'ka' => ''];
        @endphp

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Category Info</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ localeRoute('courseCategory.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <ul class="nav nav-tabs mb-3" role="tablist">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-{{ $locale }}" role="tab">{{ $lang }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mb-4">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $lang)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $locale }}" role="tabpanel">
                                        <div class="form-group">
                                            <label class="form-label">Category Name ({{ $lang }})</label>
                                            <input type="text" class="form-control" name="category_name[{{ $locale }}]" value="{{ $categoryNames[$locale] ?? '' }}">
                                            @error('category_name.' . $locale)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="category_status">
                                            <option value="1" @if(old('category_status') == 1) selected @endif>Active</option>
                                            <option value="0" @if(old('category_status') == 0) selected @endif>Inactive</option>
                                        </select>
                                        @error('category_status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label class="form-label">Image</label>
                                    <div class="form-group fallback w-100">
                                        <input type="file" class="dropify" data-default-file="" name="category_image">
                                        @error('category_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="button" onclick="window.history.back();" class="btn btn-light">Cancel</button>
                                </div>
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
<script src="{{ asset('public/vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('public/vendor/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('public/vendor/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('public/js/plugins-init/pickadate-init.js') }}"></script>
@endpush

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
