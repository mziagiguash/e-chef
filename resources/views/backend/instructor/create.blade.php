@extends('backend.layouts.app')
@section('title', 'Add Instructor')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('public/vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('public/vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')

<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Add Instructor</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('instructor.index')}}">Instructors</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('instructor.create')}}">Add Instructor</a></li>
                </ol>
            </div>
        </div>

        @php
            $name = old('name') ?? ['en' => '', 'ru' => '', 'ka' => ''];
            $designation = old('designation') ?? ['en' => '', 'ru' => '', 'ka' => ''];
            $bio = old('bio') ?? ['en' => '', 'ru' => '', 'ka' => ''];
        @endphp

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Basic Info</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('instructor.store')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <!-- Tabs navigation -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-{{ $locale }}" role="tab">{{ $lang }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $lang)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $locale }}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label">Name ({{ $lang }})</label>
                                                    <input type="text" class="form-control" name="name[{{ $locale }}]" value="{{ $name[$locale] }}">
                                                    @error('name.' . $locale)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label">Designation ({{ $lang }})</label>
                                                    <input type="text" class="form-control" name="designation[{{ $locale }}]" value="{{ $designation[$locale] }}">
                                                    @error('designation.' . $locale)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label">Bio ({{ $lang }})</label>
                                                    <textarea class="form-control" name="bio[{{ $locale }}]">{{ $bio[$locale] }}</textarea>
                                                    @error('bio.' . $locale)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="contactNumber" value="{{old('contactNumber')}}">
                                    </div>
                                    @if($errors->has('contactNumber'))
                                        <span class="text-danger"> {{ $errors->first('contactNumber') }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="emailAddress" value="{{old('emailAddress')}}">
                                    </div>
                                    @if($errors->has('emailAddress'))
                                        <span class="text-danger"> {{ $errors->first('emailAddress') }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Role</label>
                                        <select class="form-control" name="roleId">
                                            @forelse ($role as $r)
                                                <option value="{{$r->id}}" {{old('roleId')==$r->id?'selected':''}}>{{$r->name}}</option>
                                            @empty
                                                <option value="">No Role Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    @if($errors->has('roleId'))
                                        <span class="text-danger"> {{ $errors->first('roleId') }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" value="{{old('title')}}">
                                    </div>
                                    @if($errors->has('title'))
                                        <span class="text-danger"> {{ $errors->first('title') }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status">
                                            <option value="1" @if(old('status')==1) selected @endif>Active</option>
                                            <option value="0" @if(old('status')==0) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password">
    </div>
    @if($errors->has('password'))
        <span class="text-danger"> {{ $errors->first('password') }}</span>
    @endif
</div>

<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="password_confirmation">
    </div>
</div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label class="form-label">Image</label>
                                    <div class="form-group fallback w-100">
                                        <input type="file" class="dropify" data-default-file="" name="image">
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ localeRoute('instructor.index') }}" class="btn btn-light">Cancel</a>
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
<!-- pickdate -->
<script src="{{asset('public/vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('public/vendor/pickadate/picker.time.js')}}"></script>
<script src="{{asset('public/vendor/pickadate/picker.date.js')}}"></script>

<!-- Pickdate -->
<script src="{{asset('public/js/plugins-init/pickadate-init.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@endpush
