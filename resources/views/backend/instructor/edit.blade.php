@extends('backend.layouts.app')
@section('title', 'Edit Instructor')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.date.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Instructor</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.index') }}">Instructors</a></li>
                    <li class="breadcrumb-item active">Edit Instructor</li>
                </ol>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Instructor Info</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('instructor.update', encryptor('encrypt', $instructor->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @php
                                $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
                                $name = json_decode($instructor->name, true);
                                $designation = json_decode($instructor->designation, true);
                                $title = json_decode($instructor->title, true);
                                $bio = json_decode($instructor->bio, true);
                            @endphp

                            @foreach ($locales as $localeCode => $localeName)
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Name ({{ $localeName }})</label>
                                            <input type="text" name="name[{{ $localeCode }}]" class="form-control" value="{{ old('name.' . $localeCode, $name[$localeCode] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Designation ({{ $localeName }})</label>
                                            <input type="text" name="designation[{{ $localeCode }}]" class="form-control" value="{{ old('designation.' . $localeCode, $designation[$localeCode] ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Title ({{ $localeName }})</label>
                                            <input type="text" name="title[{{ $localeCode }}]" class="form-control" value="{{ old('title.' . $localeCode, $title[$localeCode] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Bio ({{ $localeName }})</label>
                                            <textarea name="bio[{{ $localeCode }}]" class="form-control">{{ old('bio.' . $localeCode, $bio[$localeCode] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            @endforeach

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $instructor->email) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="tel" name="contact" class="form-control" value="{{ old('contact', $instructor->contact) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role_id" class="form-control selectpicker">
                                            @foreach($role as $r)
                                                <option value="{{ $r->id }}" {{ old('role_id', $instructor->role_id) == $r->id ? 'selected' : '' }}>
                                                    {{ $r->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="1" {{ old('status', $instructor->status) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $instructor->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Password (оставьте пустым, если не хотите менять)</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label>Image</label>
                                    <div class="form-group fallback w-100">
                                        <input type="file" class="dropify" data-default-file="{{ $instructor->image ? asset('uploads/users/'.$instructor->image) : '' }}" name="image">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>
@endpush
