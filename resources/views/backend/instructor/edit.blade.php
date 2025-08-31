@extends('backend.layouts.app')
@section('title', 'Edit Instructor')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.date.css') }}">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row"><div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h5>Edit Instructor</h5></div>
                <div class="card-body">
                    @php
                        $d = $instructor;
                        $locales = ['en'=>'English','ru'=>'Русский','ka'=>'ქართული'];
                        $translations = $d->translations->keyBy('locale');
                    @endphp

                    <form action="{{ route('instructor.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach($locales as $code=>$name)
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Name ({{ $name }})</label>
                                    <input type="text" name="name[{{ $code }}]" class="form-control"
                                           value="{{ old('name.'.$code, $translations[$code]->name ?? '') }}">
                                    @error('name.'.$code)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-lg-6">
                                    <label>Designation ({{ $name }})</label>
                                    <input type="text" name="designation[{{ $code }}]" class="form-control"
                                           value="{{ old('designation.'.$code, $translations[$code]->designation ?? '') }}">
                                    @error('designation.'.$code)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Title ({{ $name }})</label>
                                    <input type="text" name="title[{{ $code }}]" class="form-control"
                                           value="{{ old('title.'.$code, $translations[$code]->title ?? '') }}">
                                </div>
                                <div class="col-lg-6">
                                    <label>Bio ({{ $name }})</label>
                                    <textarea name="bio[{{ $code }}]" class="form-control">{{ old('bio.'.$code, $translations[$code]->bio ?? '') }}</textarea>
                                </div>
                            </div>
                            <hr>
                        @endforeach

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $d->email) }}">
                                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label>Contact Number</label>
                                <input type="text" name="contact" class="form-control"
                                       value="{{ old('contact', $d->contact) }}">
                                @error('contact')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Role</label>
                                <select name="role_id" class="form-control">
                                    @foreach($role as $r)
                                        <option value="{{ $r->id }}" {{ old('role_id', $d->role_id)==$r->id ? 'selected':'' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', $d->status)==1 ? 'selected':'' }}>Active</option>
                                    <option value="0" {{ old('status', $d->status)==0 ? 'selected':'' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Password (leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                @if($d->image)
                                    <small class="d-block mt-1">Current: <img src="{{ asset('uploads/users/'.$d->image) }}" alt="" width="60"></small>
                                @endif
                                @error('image')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" onclick="window.history.back();" class="btn btn-secondary">Cancel</button>
                    </form>
                </div>
            </div>
        </div></div>
    </div>
</div>
@endsection



