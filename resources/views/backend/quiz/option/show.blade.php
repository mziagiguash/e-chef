@extends('backend.layouts.app')
@section('title', __('View Option'))

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ __('View Option') }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('question.index', ['lang' => $locale]) }}">{{ __('Questions') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('option.index', ['question_id' => $option->question_id, 'lang' => $locale]) }}">{{ __('Options') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('View Option') }}</li>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Question') }}</label>
                                    <p class="form-control-plaintext">
                                        {{ $option->question->getTranslation('content', $locale) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Order') }}</label>
                                    <p class="form-control-plaintext">{{ $option->order }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Correct Answer') }}</label>
                                    <p class="form-control-plaintext">
                                        @if($option->is_correct)
                                            <span class="badge badge-success">{{ __('Yes') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('No') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>{{ __('Translations') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Language') }}</th>
                                                <th>{{ __('Option Text') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($locales as $code => $name)
                                                @php
                                                    $translation = $option->translations->where('locale', $code)->first();
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $name }}</strong></td>
                                                    <td>
                                                        @if($translation)
                                                            {{ $translation->option_text }}
                                                        @else
                                                            <span class="text-muted">{{ __('No translation') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <a href="{{ route('option.edit', ['id' => $option->id, 'lang' => $locale]) }}"
                               class="btn btn-primary">{{ __('Edit') }}</a>
                            <a href="{{ route('option.index', ['question_id' => $option->question_id, 'lang' => $locale]) }}"
                               class="btn btn-secondary">{{ __('Back to Options') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
