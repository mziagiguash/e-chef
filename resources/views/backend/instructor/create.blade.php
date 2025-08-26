@extends('backend.layouts.app')
@section('title', 'Add Instructor')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row"><div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h5>Add Instructor</h5></div>
                <div class="card-body">
                    <form action="{{ route('instructor.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @php $locales = ['en'=>'English','ru'=>'Русский','ka'=>'ქართული']; @endphp
                        @foreach($locales as $code=>$name)
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Name ({{ $name }})</label>
                                    <input type="text" name="name[{{ $code }}]" class="form-control" value="{{ old('name.'.$code) }}">
                                    @error('name.'.$code)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-lg-6">
                                    <label>Designation ({{ $name }})</label>
                                    <input type="text" name="designation[{{ $code }}]" class="form-control" value="{{ old('designation.'.$code) }}">
                                    @error('designation.'.$code)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label>Title ({{ $name }})</label>
                                    <input type="text" name="title[{{ $code }}]" class="form-control" value="{{ old('title.'.$code) }}">
                                </div>
                                <div class="col-lg-6">
                                    <label>Bio ({{ $name }})</label>
                                    <textarea name="bio[{{ $code }}]" class="form-control">{{ old('bio.'.$code) }}</textarea>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label>Contact Number</label>
                                <input type="text" name="contact" class="form-control" value="{{ old('contact') }}">
                                @error('contact')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Role</label>
                                <select name="role_id" class="form-control">
                                    @foreach($role as $r)<option value="{{ $r->id }}" {{ old('role_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status')==1?'selected':'' }}>Active</option>
                                    <option value="0" {{ old('status')==0?'selected':'' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                @error('image')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </form>
                </div>
            </div>
        </div></div>
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
