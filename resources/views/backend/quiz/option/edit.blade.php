@extends('backend.layouts.app')
@section('title', __('Edit Option'))

@push('styles')
<style>
    .nav-tabs .nav-link.active {
        font-weight: bold;
        border-bottom: 2px solid #007bff;
    }
</style>
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ __('Edit Option for Question') }}: {{ $question->getTranslation('content', $locale) }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index', ['lang' => $locale]) }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $locale]) }}">{{ __('Options') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Edit Option') }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Option Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('option.update', $option->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order">{{ __('Order') }}</label>
                                        <input type="number" class="form-control" id="order" name="order"
                                               value="{{ old('order', $option->order) }}" min="0">
                                        @error('order')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_correct">{{ __('Is Correct Answer') }}</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="is_correct"
                                                   name="is_correct" value="1"
                                                   {{ old('is_correct', $option->is_correct) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_correct">
                                                {{ __('Mark as correct answer') }}
                                            </label>
                                        </div>
                                        @error('is_correct')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach($locales as $code => $name)
                                            <li class="nav-item">
                                                <a class="nav-link {{ $code === $locale ? 'active' : '' }}"
                                                   data-toggle="tab" href="#tab-{{ $code }}">
                                                    {{ $name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-content">
                                @foreach($locales as $code => $name)
                                    <div class="tab-pane fade {{ $code === $locale ? 'show active' : '' }}"
                                         id="tab-{{ $code }}">
                                        <div class="form-group">
                                            <label for="option_text_{{ $code }}">
                                                {{ __('Option Text') }} ({{ $name }})
                                                @if($code === 'en') <span class="text-danger">*</span> @endif
                                            </label>
                                            @php
                                                $translation = $option->translations->where('locale', $code)->first();
                                                $text = old('option_text.' . $code, $translation->option_text ?? '');
                                            @endphp
                                            <textarea class="form-control" id="option_text_{{ $code }}"
                                                      name="option_text[{{ $code }}]" rows="3"
                                                      {{ $code === 'en' ? 'required' : '' }}>{{ $text }}</textarea>
                                            @error('option_text.' . $code)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Update Option') }}</button>
                                <a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $locale]) }}"
                                   class="btn btn-secondary">{{ __('Cancel') }}</a>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Активация табов
        $('a[data-toggle="tab"]').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
@endpush
