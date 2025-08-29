{{-- Общие поля курса (использовать для create и edit) --}}
@php
    $course ??= null;
@endphp

<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Category</label>
        <select class="form-control" name="course_category_id">
            @forelse ($courseCategory as $c)
                <option value="{{ $c->id }}"
                    {{ old('course_category_id', $course?->course_category_id) == $c->id ? 'selected' : '' }}>
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
        <select class="form-control" name="instructor_id">
            @forelse ($instructors as $i)
                <option value="{{ $i->id }}"
                    {{ old('instructor_id', $course?->instructor_id) == $i->id ? 'selected' : '' }}>
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

{{-- Тип курса --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Type</label>
        <select class="form-control" name="courseType">
            @foreach(['free'=>'Free','paid'=>'Paid','subscription'=>'Subscription-based'] as $key=>$val)
                <option value="{{ $key }}" {{ old('courseType', $course?->courseType) == $key ? 'selected' : '' }}>
                    {{ $val }}
                </option>
            @endforeach
        </select>
        @error('courseType')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Цена --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Price ({{ $currentCurrency }})</label>
        <input type="number" step="0.01" class="form-control" name="coursePrice"
            value="{{ old('coursePrice', $course?->coursePrice) }}">
        @error('coursePrice')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Old Price</label>
        <input type="number" class="form-control" name="courseOldPrice"
            value="{{ old('courseOldPrice', $course?->courseOldPrice) }}">
        @error('courseOldPrice')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Подписка --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Subscription Price</label>
        <input type="number" class="form-control" name="subscription_price"
            value="{{ old('subscription_price', $course?->subscription_price) }}">
        @error('subscription_price')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Дата начала --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Start From</label>
        <input type="date" class="form-control" name="start_from"
            value="{{ old('start_from', $course?->start_from?->format('Y-m-d')) }}">
        @error('start_from')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Продолжительность и уроки --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Duration</label>
        <input type="number" class="form-control" name="duration"
            value="{{ old('duration', $course?->duration) }}">
        @error('duration')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">Number of Lessons</label>
        <input type="number" class="form-control" name="lesson"
            value="{{ old('lesson', $course?->lesson) }}">
        @error('lesson')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Видео и изображения --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="form-group">
        <label class="form-label">YouTube Video URL</label>
        <input type="text" class="form-control" name="thumbnail_video_url"
            value="{{ old('thumbnail_video_url', $course?->thumbnail_video_url) }}">
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
            @foreach(['popular'=>'Popular','featured'=>'Featured','upcoming'=>'Upcoming'] as $key=>$val)
                <option value="{{ $key }}" {{ old('tag', $course?->tag) == $key ? 'selected' : '' }}>
                    {{ $val }}
                </option>
            @endforeach
        </select>
        @error('tag')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Изображения --}}
<div class="col-lg-6 col-md-6 col-sm-12">
    <label class="form-label">Image</label>
    <div class="form-group fallback w-100">
        <input type="file" class="dropify" name="image"
            data-default-file="{{ $course?->image_url ?? '' }}">
        @error('image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-12">
    <label class="form-label">Thumbnail Image</label>
    <div class="form-group fallback w-100">
        <input type="file" class="dropify" name="thumbnail_image"
            data-default-file="{{ $course?->thumbnail_image_url ?? '' }}">
        @error('thumbnail_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
