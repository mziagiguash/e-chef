@php
    $question = $question ?? null;
@endphp

@section('title', isset($question) ? __('Create Option for Question') . ': ' . ($question->getTranslation('content', $locale) ?? 'N/A') : __('Create Option'))

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        @if(isset($question))
                            {{ __('Create Option for Question') }}: {{ $question->getTranslation('content', $locale) ?? 'Question #' . $question->id }}
                        @else
                            {{ __('Create Option') }}
                        @endif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index', ['lang' => $locale]) }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item">
                        @if(isset($question))
                            <a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $locale]) }}">{{ __('Options') }}</a>
                        @else
                            <a href="{{ route('option.index', ['lang' => $locale]) }}">{{ __('Options') }}</a>
                        @endif
                    </li>
                    <li class="breadcrumb-item active">{{ __('Create Option') }}</li>
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
                        <form action="{{ route('option.store') }}" method="POST">
                            @csrf

                            @if(isset($question))
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                            @else
                                <!-- Если вопрос не передан, показываем выбор вопроса -->
                                <div class="form-group">
                                    <label for="question_id">{{ __('Select Question') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="question_id" name="question_id" required>
                                        <option value="">{{ __('Select Question') }}</option>
                                        @foreach($questions as $q)
                                            <option value="{{ $q->id }}" {{ old('question_id') == $q->id ? 'selected' : '' }}>
                                                {{ $q->getTranslation('content', $locale) ?? 'Question #' . $q->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('question_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Остальная часть формы -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order">{{ __('Order') }}</label>
                                        <input type="number" class="form-control" id="order" name="order"
                                               value="{{ old('order', 0) }}" min="0">
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
                                                   name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
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
                                            <textarea class="form-control" id="option_text_{{ $code }}"
                                                      name="translations[{{ $code }}][option_text]" rows="3"
                                                      {{ $code === 'en' ? 'required' : '' }}>{{ old('translations.' . $code . '.option_text') }}</textarea>
                                            @error('translations.' . $code . '.option_text')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Create Option') }}</button>
                                @if(isset($question))
                                    <a href="{{ route('option.index', ['question_id' => $question->id, 'lang' => $locale]) }}"
                                       class="btn btn-secondary">{{ __('Cancel') }}</a>
                                @else
                                    <a href="{{ route('option.index', ['lang' => $locale]) }}"
                                       class="btn btn-secondary">{{ __('Cancel') }}</a>
                                @endif
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
