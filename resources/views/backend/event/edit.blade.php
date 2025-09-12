@extends('backend.layouts.app')
@section('title', 'Edit Event')

@push('styles')
<!-- Pick date -->
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.css')}}">
<link rel="stylesheet" href="{{asset('vendor/pickadate/themes/default.date.css')}}">
@endpush

@section('content')

<!--**********************************
            Content body start
***********************************-->
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Event</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('event.index')}}">Events</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Edit Event</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $currentLocale = request('lang', app()->getLocale());

            // Получаем переводы для текущего события
            $translations = [];
            foreach ($event->translations as $translation) {
                $translations[$translation->locale] = $translation;
            }
        @endphp

        {{-- Таб для выбора языка --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="langTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item">
                            <a href="{{ route('event.edit', ['event' => $event->id, 'lang' => $localeCode]) }}"
                               class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}">
                               {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Event Info - {{ $locales[$currentLocale] }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('event.update', $event->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Скрытое поле для текущего языка --}}
                            <input type="hidden" name="current_locale" value="{{ $currentLocale }}">

                            {{-- Поля для каждого языка --}}
                            @foreach($locales as $localeCode => $localeName)
                            <div class="locale-section mb-4 p-3 border rounded {{ $localeCode !== $currentLocale ? 'd-none' : '' }}"
                                 id="locale-{{ $localeCode }}">
                                <h6 class="text-primary mb-3">{{ $localeName }} Translation</h6>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Event Title ({{ $localeCode }})</label>
                                            <input type="text" class="form-control"
                                                   name="title_{{ $localeCode }}"
                                                   value="{{ old('title_'.$localeCode, $translations[$localeCode]->title ?? '') }}">
                                        </div>
                                        @if($errors->has('title_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('title_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Location ({{ $localeCode }})</label>
                                            <input type="text" class="form-control"
                                                   name="location_{{ $localeCode }}"
                                                   value="{{ old('location_'.$localeCode, $translations[$localeCode]->location ?? '') }}">
                                        </div>
                                        @if($errors->has('location_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('location_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Topic ({{ $localeCode }})</label>
                                            <input type="text" class="form-control"
                                                   name="topic_{{ $localeCode }}"
                                                   value="{{ old('topic_'.$localeCode, $translations[$localeCode]->topic ?? '') }}">
                                        </div>
                                        @if($errors->has('topic_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('topic_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Hosted By ({{ $localeCode }})</label>
                                            <input type="text" class="form-control"
                                                   name="hosted_by_{{ $localeCode }}"
                                                   value="{{ old('hosted_by_'.$localeCode, $translations[$localeCode]->hosted_by ?? '') }}">
                                        </div>
                                        @if($errors->has('hosted_by_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('hosted_by_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Description ({{ $localeCode }})</label>
                                            <textarea name="description_{{ $localeCode }}"
                                                      class="form-control"
                                                      rows="3">{{ old('description_'.$localeCode, $translations[$localeCode]->description ?? '') }}</textarea>
                                        </div>
                                        @if($errors->has('description_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('description_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Goal ({{ $localeCode }})</label>
                                            <textarea name="goal_{{ $localeCode }}"
                                                      class="form-control"
                                                      rows="3">{{ old('goal_'.$localeCode, $translations[$localeCode]->goal ?? '') }}</textarea>
                                        </div>
                                        @if($errors->has('goal_'.$localeCode))
                                        <span class="text-danger"> {{ $errors->first('goal_'.$localeCode) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- Общие поля (не зависящие от языка) --}}
                            <div class="common-fields mt-4 p-3 border rounded">
                                <h6 class="text-primary mb-3">General Information</h6>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control" name="date"
                                                   value="{{ old('date', $event->date ? \Carbon\Carbon::parse($event->date)->format('Y-m-d') : '') }}">
                                        </div>
                                        @if($errors->has('date'))
                                        <span class="text-danger"> {{$errors->first('date')}}</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Image</label>
                                            @if($event->image)
                                            <div class="mb-2">
                                                <img src="{{ asset('uploads/events/'.$event->image) }}"
                                                     class="img-thumbnail" width="100" height="100"
                                                     style="object-fit: cover;">
                                                <br>
                                                <small class="text-muted">Current image</small>
                                            </div>
                                            @endif
                                            <input type="file" class="form-control" name="image">
                                            <small class="form-text text-muted">Leave empty to keep current image</small>
                                        </div>
                                        @if($errors->has('image'))
                                        <span class="text-danger"> {{$errors->first('image')}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('event.index', ['lang' => $currentLocale]) }}"
                                       class="btn btn-light">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--**********************************
            Content body end
***********************************-->

@endsection

@push('scripts')
<!-- pickdate -->
<script src="{{asset('vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.time.js')}}"></script>
<script src="{{asset('vendor/pickadate/picker.date.js')}}"></script>

<!-- Pickdate -->
<script src="{{asset('js/plugins-init/pickadate-init.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение между языковыми вкладками
    const langTabs = document.querySelectorAll('#langTabs .nav-link');
    const localeSections = document.querySelectorAll('.locale-section');

    langTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            // Убираем активный класс у всех вкладок
            langTabs.forEach(t => t.classList.remove('active'));
            // Добавляем активный класс текущей вкладке
            this.classList.add('active');

            // Скрываем все языковые секции
            localeSections.forEach(section => section.classList.add('d-none'));

            // Показываем только нужную секцию
            const locale = this.getAttribute('href').split('lang=')[1];
            document.getElementById('locale-' + locale)?.classList.remove('d-none');
        });
    });

    // Инициализация текущего языка
    const currentLang = '{{ $currentLocale }}';
    document.getElementById('locale-' + currentLang)?.classList.remove('d-none');
});
</script>
@endpush
