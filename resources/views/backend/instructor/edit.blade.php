@extends('backend.layouts.app')
@section('title', 'Edit Instructor')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('public/vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('public/vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Instructor</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('instructor.index')}}">Instructors</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Edit Instructor</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
        @endphp

        <form action="{{localeRoute('instructor.update', encryptor('encrypt', $instructor->id))}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <ul class="nav nav-tabs mb-3" role="tablist">
                @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $localeCode }}-tab" data-bs-toggle="tab" href="#tab-{{ $localeCode }}" role="tab">{{ $localeName }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mb-4">
                @foreach($locales as $localeCode => $localeName)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $localeCode }}" role="tabpanel">
                        <div class="form-group">
                            <label>Name ({{ $localeName }})</label>
                            <input type="text" name="name_{{ $localeCode }}" class="form-control" value="{{ old('name_'.$localeCode, $instructor->{'name_'.$localeCode}) }}">
                            @error('name_'.$localeCode)
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Designation ({{ $localeName }})</label>
                            <input type="text" name="designation_{{ $localeCode }}" class="form-control" value="{{ old('designation_'.$localeCode, $instructor->{'designation_'.$localeCode}) }}">
                            @error('designation_'.$localeCode)
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Title ({{ $localeName }})</label>
                            <input type="text" name="title_{{ $localeCode }}" class="form-control" value="{{ old('title_'.$localeCode, $instructor->{'title_'.$localeCode}) }}">
                            @error('title_'.$localeCode)
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <!-- Остальные поля без перевода -->
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="contact" class="form-control" value="{{ old('contact', $instructor->contact) }}">
                        @error('contact')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $instructor->email) }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role_id" class="form-control">
                            @foreach($role as $r)
                                <option value="{{ $r->id }}" {{ old('role_id', $instructor->role_id) == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $instructor->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $instructor->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Password (если менять)</label>
                        <input type="password" name="password" class="form-control" placeholder="Оставьте пустым, если не менять">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" class="form-control">{{ old('bio', $instructor->bio) }}</textarea>
                        @error('bio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-12">
                    <label>Image</label>
                    <input type="file" name="image" class="dropify" data-default-file="{{ asset('public/uploads/users/' . $instructor->image) }}">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <button type="button" onclick="window.history.back();" class="btn btn-light">Отмена</button>
                </div>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<!-- pickdate -->
<script src="{{asset('public/vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('public/vendor/pickadate/picker.time.js')}}"></script>
<script src="{{asset('public/vendor/pickadate/picker.date.js')}}"></script>
<script src="{{asset('public/js/plugins-init/pickadate-init.js')}}"></script>
@endpush
