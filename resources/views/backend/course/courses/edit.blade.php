@extends('backend.layouts.app')
@section('title', 'Edit Course')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.date.css') }}">
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Course</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">Edit Course</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en'=>'English','ru'=>'Русский','ka'=>'ქართული'];
        @endphp

        {{-- Форма редактирования --}}
        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mt-3">Basic Info</h3>
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach(['en'=>'English','ru'=>'Русский','ka'=>'ქართული'] as $locale=>$langName)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-{{ $locale }}" role="tab">
                                        {{ $langName }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body">
                       <form action="{{ route('courses.update', encryptor('encrypt', $course->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Локализации --}}
                            <div class="tab-content mb-4">
                                @foreach(['en'=>'English','ru'=>'Русский','ka'=>'ქართული'] as $locale=>$langName)
                                    @php
                                        $translation = $course->translations->firstWhere('locale', $locale);
                                    @endphp
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $locale }}" role="tabpanel">
                                        <input type="hidden" name="translations[{{ $locale }}][locale]" value="{{ $locale }}">
                                        @if($translation)
                                            <input type="hidden" name="translations[{{ $locale }}][id]" value="{{ $translation->id }}">
                                        @endif

                                        <div class="form-group">
                                            <label class="form-label">Title ({{ $langName }})</label>
                                            <input type="text" class="form-control"
                                                   name="translations[{{ $locale }}][title]"
                                                   value="{{ old("translations.$locale.title", $translation->title ?? '') }}"
                                                   required>
                                            @error("translations.$locale.title") <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Description ({{ $langName }})</label>
                                            <textarea class="form-control" name="translations[{{ $locale }}][description]" required>{{ old("translations.$locale.description", $translation->description ?? '') }}</textarea>
                                            @error("translations.$locale.description") <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Prerequisites ({{ $langName }})</label>
                                            <textarea class="form-control" name="translations[{{ $locale }}][prerequisites]">{{ old("translations.$locale.prerequisites", $translation->prerequisites ?? '') }}</textarea>
                                            @error("translations.$locale.prerequisites") <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Keywords ({{ $langName }})</label>
                                            <input type="text" class="form-control"
                                                   name="translations[{{ $locale }}][keywords]"
                                                   value="{{ old("translations.$locale.keywords", $translation->keywords ?? '') }}">
                                            @error("translations.$locale.keywords") <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Общие поля --}}
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label">Category</label>
                                        <select class="form-control" name="course_category_id" required>
                                            <option value="">Select Category</option>
                                            @forelse($courseCategory as $c)
                                                <option value="{{ $c->id }}" {{ old('course_category_id', $course->course_category_id) == $c->id ? 'selected' : '' }}>
                                                   {{ $c->getTranslation('category_name') ?? $c->translated_category_name ?? 'No Category' }}
                                                </option>
                                            @empty
                                                <option value="">No Category Found</option>
                                            @endforelse
                                        </select>
                                        @error('course_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label">Instructor</label>
                                        <select class="form-control" name="instructor_id" required>
                                            <option value="">Select Instructor</option>
                                            @forelse($instructors as $i)
                                                <option value="{{ $i->id }}" {{ old('instructor_id', $course->instructor_id) == $i->id ? 'selected' : '' }}>
                                                    {{ $i->getTranslation('name') ?? $i->translated_name ?? 'No Instructor' }}
                                                </option>
                                            @empty
                                                <option value="">No Instructor Found</option>
                                            @endforelse
                                        </select>
                                        @error('instructor_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Type</label>
                                    <select class="form-control" name="courseType" required>
                                        <option value="free" {{ old('courseType', $course->courseType) == 'free' ? 'selected' : '' }}>Free</option>
                                        <option value="paid" {{ old('courseType', $course->courseType) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="subscription" {{ old('courseType', $course->courseType) == 'subscription' ? 'selected' : '' }}>Subscription-based</option>
                                    </select>
                                    @error('courseType') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Price ({{ $currentCurrency ?? '$' }})</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="coursePrice"
                                           value="{{ old('coursePrice', $course->coursePrice) }}" placeholder="0.00" required>
                                    @error('coursePrice') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Old Price</label>
                                    <input type="number" min="0" class="form-control" name="courseOldPrice"
                                           value="{{ old('courseOldPrice', $course->courseOldPrice) }}">
                                    @error('courseOldPrice') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Subscription Price</label>
                                    <input type="number" min="0" class="form-control" name="subscription_price"
                                           value="{{ old('subscription_price', $course->subscription_price) }}">
                                    @error('subscription_price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Start From</label>
                                    <input type="date" class="form-control" name="start_from"
                                           value="{{ old('start_from', $course->formatted_start_from) }}" required>
                                    @error('start_from') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Duration (days)</label>
                                    <input type="number" min="1" max="365" class="form-control" name="duration"
                                           value="{{ old('duration', $course->duration) }}" placeholder="30" required>
                                    @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Number of Lessons</label>
                                    <input type="number" min="1" max="500" class="form-control" name="lesson"
                                           value="{{ old('lesson', $course->lesson) }}" placeholder="10" required>
                                    @error('lesson') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Course Code</label>
                                    <input type="text" class="form-control" name="course_code"
                                           value="{{ old('course_code', $course->course_code) }}" required>
                                    @error('course_code') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">YouTube Video URL</label>
                                    <input type="url" class="form-control" name="thumbnail_video_url"
                                           value="{{ old('thumbnail_video_url', $course->thumbnail_video_url) }}">
                                    @error('thumbnail_video_url') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Upload Video</label>
                                    <input type="file" class="form-control" name="thumbnail_video_file" accept="video/*">
                                    <small class="text-muted">Max size: 10MB, Formats: MP4, MOV, AVI</small>
                                    @if($course->thumbnail_video_file && file_exists(public_path($course->thumbnail_video_file)))
                                        <small class="text-muted">Current file: {{ basename($course->thumbnail_video_file) }}</small>
                                    @endif
                                    @error('thumbnail_video_file') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Course Tag</label>
                                    <select class="form-control" name="tag">
                                        <option value="popular" {{ old('tag', $course->tag) == 'popular' ? 'selected' : '' }}>Popular</option>
                                        <option value="featured" {{ old('tag', $course->tag) == 'featured' ? 'selected' : '' }}>Featured</option>
                                        <option value="upcoming" {{ old('tag', $course->tag) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    </select>
                                    @error('tag') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="0" {{ old('status', $course->status) == 0 ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ old('status', $course->status) == 1 ? 'selected' : '' }}>Inactive</option>
                                        <option value="2" {{ old('status', $course->status) == 2 ? 'selected' : '' }}>Active</option>
                                    </select>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="dropify" name="image" accept="image/*"
                                           data-allowed-file-extensions="jpg jpeg png gif"
                                           data-default-file="{{ $course->image && file_exists(public_path('uploads/courses/' . $course->image)) ? asset('uploads/courses/' . $course->image) : '' }}">
                                    <small class="text-muted">Max size: 2MB, Formats: JPG, JPEG, PNG, GIF</small>
                                    @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" class="dropify" name="thumbnail_image" accept="image/*"
                                           data-allowed-file-extensions="jpg jpeg png gif"
                                           data-default-file="{{ $course->thumbnail_image && file_exists(public_path('uploads/courses/' . $course->thumbnail_image)) ? asset('uploads/courses/' . $course->thumbnail_image) : '' }}">
                                    <small class="text-muted">Max size: 2MB, Formats: JPG, JPEG, PNG, GIF</small>
                                    @error('thumbnail_image') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Превью текущих изображений --}}
                                @if($course->image && file_exists(public_path('uploads/courses/' . $course->image)))
                                <div class="col-lg-6">
                                    <label class="form-label">Current Image</label>
                                    <img src="{{ asset('uploads/courses/' . $course->image) }}" alt="Current course image" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                @endif

                                @if($course->thumbnail_image && file_exists(public_path('uploads/courses/' . $course->thumbnail_image)))
                                <div class="col-lg-6">
                                    <label class="form-label">Current Thumbnail</label>
                                    <img src="{{ asset('uploads/courses/' . $course->thumbnail_image) }}" alt="Current thumbnail" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                @endif

                                <div class="col-lg-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('courses.index') }}" class="btn btn-light">Cancel</a>
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@endpush
