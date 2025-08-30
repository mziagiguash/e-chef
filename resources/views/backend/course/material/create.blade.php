@extends('backend.layouts.app')
@section('title', 'Add Course Material')

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
                    <h4>Add Course Material</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('material.index')}}">Course Materials</a></li>
                    <li class="breadcrumb-item active">Add Material</li>
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
                    <div class="card-header"><h5 class="card-title">Basic Info</h5></div>
                    <div class="card-body">
                        <form action="{{ localeRoute('material.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs mb-3">
                                @foreach($locales as $code => $name)
                                    <li class="nav-item">
                                        <a href="#" class="nav-link {{ $code === $appLocale ? 'active' : '' }} lang-tab" data-locale="{{ $code }}">{{ $name }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mb-3">
                                @foreach($locales as $code => $name)
                                    <div class="locale-fields locale-{{ $code }}" style="{{ $code === $appLocale ? '' : 'display:none;' }}">
                                        <div class="form-group">
                                            <label>Title ({{ $name }})</label>
                                            <input type="text" name="materialTitle[{{ $code }}]" class="form-control" value="{{ old('materialTitle.'.$code) }}">
                                            @error('materialTitle.'.$code)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Content Text ({{ $name }})</label>
                                            <textarea name="content_text[{{ $code }}]" class="form-control">{{ old('content_text.'.$code) }}</textarea>
                                            @error('content_text.'.$code)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Other Fields -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Lesson</label>
                                        <select name="lessonId" class="form-control">
                                            @forelse($lessons as $lesson)
                                                <option value="{{ $lesson->id }}" {{ old('lessonId') == $lesson->id ? 'selected' : '' }}>
                                                    {{ $lesson->displayTitle() }}
                                                </option>
                                            @empty
                                                <option value="">No Lesson Found</option>
                                            @endforelse
                                        </select>
                                        @error('lessonId')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Material Type</label>
                                        <select name="materialType" class="form-control">
                                            <option value="video" {{ old('materialType')=='video'?'selected':'' }}>Video</option>
                                            <option value="document" {{ old('materialType')=='document'?'selected':'' }}>Document</option>
                                            <option value="quiz" {{ old('materialType')=='quiz'?'selected':'' }}>Quiz</option>
                                        </select>
                                        @error('materialType')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Content File</label>
                                        <input type="file" name="content" class="form-control">
                                        @error('content')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Content URL</label>
                                        <textarea name="contentURL" class="form-control">{{ old('contentURL') }}</textarea>
                                        @error('contentURL')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
