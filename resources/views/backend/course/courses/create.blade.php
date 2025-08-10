@extends('backend.layouts.app')
@section('title', 'Add Course')

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
                    <h4>Add Course</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ localeRoute('course.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">Add Course</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mt-3 ">Basic Info</h3>
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
                        <form action="{{ localeRoute('course.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="tab-content mb-4">
                                @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $locale => $langName)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $locale }}" role="tabpanel">

                                        <div class="form-group">
                                            <label class="form-label">Title ({{ $langName }})</label>
                                            <input type="text" class="form-control"
                                                   name="translations[{{ $locale }}][title]"
                                                   value="{{ old("translations.$locale.title") }}">
                                            @error("translations.$locale.title")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Description ({{ $langName }})</label>
                                            <textarea class="form-control"
                                                      name="translations[{{ $locale }}][description]">{{ old("translations.$locale.description") }}</textarea>
                                            @error("translations.$locale.description")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Prerequisites ({{ $langName }})</label>
                                            <textarea class="form-control"
                                                      name="translations[{{ $locale }}][prerequisites]">{{ old("translations.$locale.prerequisites") }}</textarea>
                                            @error("translations.$locale.prerequisites")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Keywords ({{ $langName }})</label>
                                            <input type="text" class="form-control"
                                                   name="translations[{{ $locale }}][keywords]"
                                                   value="{{ old("translations.$locale.keywords") }}">
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
                                        <select class="form-control" name="categoryId">
                                            @forelse ($courseCategory as $c)
                                                <option value="{{ $c->id }}" {{ old('categoryId') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->category_name }}
                                                </option>
                                            @empty
                                                <option value="">No Category Found</option>
                                            @endforelse
                                        </select>
                                        @error('categoryId')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Instructor</label>
                                        <select class="form-control" name="instructorId">
                                            @forelse ($instructor as $i)
                                                <option value="{{ $i->id }}" {{ old('instructorId') == $i->id ? 'selected' : '' }}>
                                                    {{ $i->name }}
                                                </option>
                                            @empty
                                                <option value="">No Instructor Found</option>
                                            @endforelse
                                        </select>
                                        @error('instructorId')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Type</label>
                                        <select class="form-control" name="courseType">
                                            <option value="free" {{ old('courseType') == 'free' ? 'selected' : '' }}>Free</option>
                                            <option value="paid" {{ old('courseType') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="subscription" {{ old('courseType') == 'subscription' ? 'selected' : '' }}>Subscription-based</option>
                                        </select>
                                        @error('courseType')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Price ({{ $currentCurrency }})</label>
                                        <input type="number" step="0.01" class="form-control" name="coursePrice" value="{{ old('coursePrice') }}">
                                        @error('coursePrice')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Old Price</label>
                                        <input type="number" class="form-control" name="courseOldPrice" value="{{ old('courseOldPrice') }}">
                                        @error('courseOldPrice')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Subscription Price</label>
                                        <input type="number" class="form-control" name="subscription_price" value="{{ old('subscription_price') }}">
                                        @error('subscription_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Start From</label>
                                        <input type="date" class="form-control" name="start_from" value="{{ old('start_from') }}">
                                        @error('start_from')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Duration</label>
                                        <input type="number" class="form-control" name="duration" value="{{ old('duration') }}">
                                        @error('duration')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Number of Lessons</label>
                                        <input type="number" class="form-control" name="lesson" value="{{ old('lesson') }}">
                                        @error('lesson')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Course Code</label>
                                        <input type="text" class="form-control" name="course_code" value="{{ old('course_code') }}">
                                        @error('course_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">YouTube Video URL</label>
                                        <input type="text" class="form-control" name="thumbnail_video_url" value="{{ old('thumbnail_video_url') }}">
                                        @error('thumbnail_video_url')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Upload Video from Computer</label>
                                        <input type="file" class="form-control" name="thumbnail_video_file" accept="video/*">
                                        @error('thumbnail_video_file')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Course Tag</label>
                                        <select class="form-control" name="tag">
                                            <option value="popular" {{ old('tag') == 'popular' ? 'selected' : '' }}>Popular</option>
                                            <option value="featured" {{ old('tag') == 'featured' ? 'selected' : '' }}>Featured</option>
                                            <option value="upcoming" {{ old('tag') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        </select>
                                        @error('tag')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status">
                                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label class="form-label">Image</label>
                                    <div class="form-group fallback w-100">
                                        <input type="file" class="dropify" data-default-file="" name="image">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label class="form-label">Thumbnail Image</label>
                                    <div class="form-group fallback w-100">
                                        <input type="file" class="dropify" data-default-file="" name="thumbnail_image">
                                        @error('thumbnail_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
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
<!-- pickdate -->
<script src="{{ asset('public/vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('public/vendor/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('public/vendor/pickadate/picker.date.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


<!-- Pickdate -->
<script src="{{ asset('public/js/plugins-init/pickadate-init.js') }}"></script>
@endpush
