@extends('backend.layouts.app')
@section('title', 'Edit Lesson')

@push('styles')
<link href="{{ asset('vendor/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12">
                <h4>Edit Lesson</h4>
                <a href="{{ localeRoute('lesson.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <form action="{{ localeRoute('lesson.update', encryptor('encrypt', $lesson->id)) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Language Tabs --}}
            <ul class="nav nav-tabs mb-3" id="lessonLangTabs" role="tablist">
                @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $localeCode === $appLocale ? 'active' : '' }}"
                           id="tab-{{ $localeCode }}" data-toggle="tab" href="#locale-{{ $localeCode }}" role="tab">
                           {{ $localeName }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Tab Contents --}}
            <div class="tab-content">
                @foreach($locales as $localeCode => $localeName)
                    @php
                        $title = old('lessonTitle.'.$localeCode, $lesson->title[$localeCode] ?? '');
                        $description = old('lessonDescription.'.$localeCode, $lesson->description[$localeCode] ?? '');
                        $notes = old('lessonNotes.'.$localeCode, $lesson->notes[$localeCode] ?? '');
                    @endphp
                    <div class="tab-pane fade {{ $localeCode === $appLocale ? 'show active' : '' }}" id="locale-{{ $localeCode }}" role="tabpanel">
                        <div class="form-group">
                            <label>Title ({{ $localeName }})</label>
                            <input type="text" name="lessonTitle[{{ $localeCode }}]" class="form-control" value="{{ $title }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description ({{ $localeName }})</label>
                            <textarea name="lessonDescription[{{ $localeCode }}]" class="form-control summernote">{{ $description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Notes ({{ $localeName }})</label>
                            <textarea name="lessonNotes[{{ $localeCode }}]" class="form-control summernote">{{ $notes }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Course Selector --}}
            <div class="form-group">
                <label>Course</label>
                <select name="courseId" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('courseId', $lesson->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->getTranslation('title', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Lesson</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('.summernote').summernote({height: 150});

    $('#lessonLangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr('href');
        $(target+' .summernote').summernote({height:150});
    });
});
</script>
@endpush
