@extends('backend.layouts.app')
@section('title', 'Edit Course Material')

@push('styles')
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Course Material</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('material.index')}}">Course Materials</a></li>
                    <li class="breadcrumb-item active">Edit Material</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Basic Info</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ localeRoute('material.update', encryptor('encrypt', $material->id)) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                @foreach($locales as $code => $name)
                                    <li class="nav-item">
                                        <a href="#" class="nav-link {{ $code === $appLocale ? 'active' : '' }} lang-tab" data-locale="{{ $code }}">{{ $name }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mb-3">
                                @foreach($locales as $code => $name)
                                    @php
                                        $translation = $material->translations()->where('locale', $code)->first();
                                    @endphp
                                    <div class="locale-fields locale-{{ $code }}" style="{{ $code === $appLocale ? '' : 'display:none;' }}">
                                        <div class="form-group">
                                            <label class="form-label">Title ({{ $name }})</label>
                                            <input type="text" class="form-control" name="materialTitle[{{ $code }}]" value="{{ old('materialTitle.'.$code, $translation?->title) }}">
                                            @if($errors->has('materialTitle.'.$code))
                                                <span class="text-danger">{{ $errors->first('materialTitle.'.$code) }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Content Text ({{ $name }})</label>
                                            <textarea class="form-control" name="content_text[{{ $code }}]">{{ old('content_text.'.$code, $translation?->content_text) }}</textarea>
                                            @if($errors->has('content_text.'.$code))
                                                <span class="text-danger">{{ $errors->first('content_text.'.$code) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <!-- Lesson -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Lesson</label>
                                        <select class="form-control" name="lessonId">
                                            @forelse ($lessons as $l)
                                                <option value="{{ $l->id }}" {{ old('lessonId', $material->lesson_id ?? '') == $l->id ? 'selected' : '' }}>
                                                    {{ $l->display_title }}
                                                </option>
                                            @empty
                                                <option value="">No Lesson Found</option>
                                            @endforelse
                                        </select>
                                        @if($errors->has('lessonId'))
                                            <span class="text-danger">{{ $errors->first('lessonId') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Material Type -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Material Type</label>
                                        <select class="form-control" name="materialType">
                                            <option value="video" {{ old('materialType', $material->type) == 'video' ? 'selected' : '' }}>Video</option>
                                            <option value="document" {{ old('materialType', $material->type) == 'document' ? 'selected' : '' }}>Document</option>
                                            <option value="quiz" {{ old('materialType', $material->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Existing File & Upload -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Content File</label>
                                        @if($material->content)
                                            <p>Current file: <a href="{{ asset('uploads/courses/contents/'.$material->content) }}" target="_blank">{{ $material->content }}</a></p>
                                        @endif
                                        <input type="file" class="form-control" name="content">
                                    </div>
                                </div>

                                <!-- Content URL -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Content Url</label>
                                        <textarea class="form-control" name="contentURL">{{ old('contentURL', $material->content_url) }}</textarea>
                                        @if($errors->has('contentURL'))
                                            <span class="text-danger">{{ $errors->first('contentURL') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button type="reset" class="btn btn-light">Cancel</button>
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
<script src="{{asset('vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.time.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.date.js')}}"></script>
<script src="{{asset('js/plugins-init/pickadate-init.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lang-tab').forEach(tab => {
        tab.addEventListener('click', function(e){
            e.preventDefault();
            const locale = this.dataset.locale;
            document.querySelectorAll('.lang-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.locale-fields').forEach(box => box.style.display = 'none');
            document.querySelector('.locale-' + locale).style.display = '';
        });
    });
});
</script>
@endpush
