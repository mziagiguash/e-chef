@extends('backend.layouts.app')
@section('title', 'Edit Course')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.date.css') }}">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Course</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ localeRoute('course.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">Edit Course</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mt-3">Basic Info</h3>
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $langName)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                       data-bs-toggle="tab"
                                       href="#tab-{{ $locale }}"
                                       role="tab">
                                        {{ $langName }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body">
                        <form action="{{ localeRoute('course.updateforAdmin', encryptor('encrypt', $course->id)) }}"
                              method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="tab-content mb-4">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $langName)
                                    @php
                                        $trans = $course->translations[$locale] ?? [];
                                    @endphp
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                         id="tab-{{ $locale }}" role="tabpanel">

                                        <div class="form-group">
                                            <label class="form-label">Title ({{ $langName }})</label>
                                            <input type="text" class="form-control" required
                                                   name="translations[{{ $locale }}][title]"
                                                   value="{{ old("translations.$locale.title", $trans['title'] ?? '') }}">
                                            @error("translations.$locale.title")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Description ({{ $langName }})</label>
                                            <textarea class="form-control" required
                                                      name="translations[{{ $locale }}][description]">{{ old("translations.$locale.description", $trans['description'] ?? '') }}</textarea>
                                            @error("translations.$locale.description")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Prerequisites ({{ $langName }})</label>
                                            <textarea class="form-control"
                                                      name="translations[{{ $locale }}][prerequisites]">{{ old("translations.$locale.prerequisites", $trans['prerequisites'] ?? '') }}</textarea>
                                            @error("translations.$locale.prerequisites")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Keywords ({{ $langName }})</label>
                                            <input type="text" class="form-control"
                                                   name="translations[{{ $locale }}][keywords]"
                                                   value="{{ old("translations.$locale.keywords", $trans['keywords'] ?? '') }}">
                                            @error("translations.$locale.keywords")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                            <div class="row">

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Category</label>
                                        <select class="form-control" name="course_category_id" required>
                                            @forelse ($courseCategory as $c)
                                                <option value="{{ $c->id }}" {{ old('course_category_id', $course->course_category_id) == $c->id ? 'selected' : '' }}>
                                                    {{ $c->display_name }}
                                                </option>
                                            @empty
                                                <option value="">No Category Found</option>
                                            @endforelse
                                        </select>
                                        @error('course_category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Instructor</label>
                                        <select class="form-control" name="instructor_id" required>
                                            @forelse ($instructors as $i)
                                                <option value="{{ $i->id }}" {{ old('instructor_id', $course->instructor_id) == $i->id ? 'selected' : '' }}>
                                                    {{ $i->display_name }}
                                                </option>
                                            @empty
                                                <option value="">No Instructor Found</option>
                                            @endforelse
                                        </select>
                                        @error('instructor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- остальная часть (Type, Price, Duration, Lesson, Code, Video, Images, Tag и т.д.) полностью такая же как в "create", но со значениями $course --}}

                                <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
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
<script src="{{ asset('vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('vendor/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('vendor/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('js/plugins-init/pickadate-init.js') }}"></script>
@endpush
