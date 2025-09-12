@extends('backend.layouts.app')
@section('title', 'Edit Instructor')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h5>Edit Instructor</h5></div>
                    <div class="card-body">
                        <form action="{{ route('instructor.update', encryptor('encrypt', $instructor->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @php $locales = ['en'=>'English','ru'=>'Русский','ka'=>'ქართული']; @endphp

                            @foreach($locales as $code=>$name)
                                @php
                                    $translation = $instructor->translations->firstWhere('locale', $code);
                                @endphp
                                <h6 class="mt-3">{{ $name }} Translation</h6>
                                <div class="row mb-2">
                                    <div class="col-lg-6">
                                        <label>Name ({{ $name }})</label>
                                        <input type="text" name="translations[{{ $code }}][name]" class="form-control"
                                               value="{{ old('translations.'.$code.'.name', $translation->name ?? '') }}">
                                        @error('translations.'.$code.'.name')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Designation ({{ $name }})</label>
                                        <input type="text" name="translations[{{ $code }}][designation]" class="form-control"
                                               value="{{ old('translations.'.$code.'.designation', $translation->designation ?? '') }}">
                                        @error('translations.'.$code.'.designation')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-6">
                                        <label>Title ({{ $name }})</label>
                                        <input type="text" name="translations[{{ $code }}][title]" class="form-control"
                                               value="{{ old('translations.'.$code.'.title', $translation->title ?? '') }}">
                                        @error('translations.'.$code.'.title')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Bio ({{ $name }})</label>
                                        <textarea name="translations[{{ $code }}][bio]" class="form-control">{{ old('translations.'.$code.'.bio', $translation->bio ?? '') }}</textarea>
                                        @error('translations.'.$code.'.bio')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <input type="hidden" name="translations[{{ $code }}][locale]" value="{{ $code }}">
                                @if($translation)
                                    <input type="hidden" name="translations[{{ $code }}][id]" value="{{ $translation->id }}">
                                @endif
                                <hr>
                            @endforeach

                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $instructor->email) }}">
                                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-lg-6">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact" class="form-control" value="{{ old('contact', $instructor->contact) }}">
                                    @error('contact')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Role</label>
                                    <select name="role_id" class="form-control">
                                        @foreach($role as $r)
                                            <option value="{{ $r->id }}" {{ old('role_id', $instructor->role_id) == $r->id ? 'selected' : '' }}>
                                                {{ $r->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $instructor->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $instructor->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
                                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-lg-6">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                    @error('image')<span class="text-danger">{{ $message }}</span>@enderror
                                    @if($instructor->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/users/' . $instructor->image) }}" alt="Current image" height="60">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Language</label>
                                    <select name="language" class="form-control">
                                        <option value="en" {{ old('language', $instructor->language) == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ru" {{ old('language', $instructor->language) == 'ru' ? 'selected' : '' }}>Russian</option>
                                        <option value="ka" {{ old('language', $instructor->language) == 'ka' ? 'selected' : '' }}>Georgian</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('instructor.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- pickdate -->
<script src="{{asset('vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.time.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.date.js')}}"></script>

<!-- Pickdate -->
<script src="{{asset('js/plugins-init/pickadate-init.js')}}"></script>
@endpush
