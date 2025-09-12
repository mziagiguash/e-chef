@extends('backend.layouts.app')
@section('title', 'Quiz List')

@push('styles')
<!-- Datatable -->
<link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <!-- row -->
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Quiz List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('quiz.index')}}">Quizzes</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">All Quiz</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a href="#list-view" data-toggle="tab" class="nav-link btn-primary mr-1 show active">List View</a>
                    </li>
                    <li class="nav-item">
                        <a href="#grid-view" data-toggle="tab" class="nav-link btn-primary">Grid View</a>
                    </li>
                </ul>
            </div>

            <!-- Языковые табы -->
            <div class="col-lg-12 mb-3">
                <ul class="nav nav-tabs" id="quizLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $localeCode]) }}"
                               class="nav-link {{ $localeCode === $locale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">
                                {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row tab-content">
            <div id="list-view" class="tab-pane fade active show col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Quizzes List</h4>
                        <a href="{{ localeRoute('quiz.create', ['lang' => $locale]) }}" class="btn btn-primary">+ Add new</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Lesson</th>
                                        <th>Questions Count</th>
                                        <th>Time Limit</th>
                                        <th>Passing Score</th>
                                        <th>Max Attempts</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quizzes as $quiz)
                                        <tr>
                                            <td>{{ $quiz->id }}</td>
                                            <td>
                                                @php
                                                    $translation = $quiz->translations->firstWhere('locale', $locale);
                                                    $quizTitle = $translation ? $translation->title : ($quiz->translations->first() ? $quiz->translations->first()->title : 'No Title');
                                                @endphp

                                                <strong>{{ $quizTitle }}</strong>

                                                @if(!$translation && $quiz->translations->count() > 0)
                                                    <span class="badge badge-warning ml-1">No {{ $locale }} translation</span>
                                                @elseif($quiz->translations->count() === 0)
                                                    <span class="badge badge-danger ml-1">No translations</span>
                                                @endif

                                                @if(config('app.debug'))
                                                <br><small class="text-muted">Translations: {{ $quiz->translations->count() }}</small>
                                                @endif
                                            </td>
                                            <td>
    @if($quiz->lesson)
        @php
            $lessonTranslation = $quiz->lesson->translations->firstWhere('locale', $locale);
            $lessonTitle = $lessonTranslation ? $lessonTranslation->title : ($quiz->lesson->translations->first() ? $quiz->lesson->translations->first()->title : 'No Lesson Title');
            $hasLessonTranslation = (bool)$lessonTranslation;
        @endphp
        {{ $lessonTitle }}
        @if(!$hasLessonTranslation)
            <span class="badge badge-warning ml-1">No {{ $locale }} translation</span>
        @endif
        <br>
        <small class="text-muted">Lesson ID: {{ $quiz->lesson->id }}</small>
        <br>
        <small class="text-muted">Course:
            @if($quiz->lesson->course)
                @php
                    $courseTranslation = $quiz->lesson->course->translations->firstWhere('locale', $locale);
                    $courseTitle = $courseTranslation ? $courseTranslation->title : ($quiz->lesson->course->translations->first() ? $quiz->lesson->course->translations->first()->title : 'No Course Title');
                @endphp
                {{ $courseTitle }} (ID: {{ $quiz->lesson->course->id }})
            @else
                No Course
            @endif
        </small>
    @else
        <span class="text-danger">No lesson assigned</span>
    @endif
</td>
                                            <td>
                                                <span class="badge badge-info">{{ $quiz->questions_count }}</span>
                                            </td>
                                            <td>
                                                {{ $quiz->time_limit > 0 ? $quiz->time_limit . ' min' : 'No limit' }}
                                            </td>
                                            <td>
                                                {{ $quiz->passing_score }}%
                                            </td>
                                            <td>
                                                {{ $quiz->max_attempts > 0 ? $quiz->max_attempts : 'Unlimited' }}
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $quiz->is_active ? 'success' : 'secondary' }}">
                                                    {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ localeRoute('quiz.show', ['quiz' => encryptor('encrypt', $quiz->id), 'lang' => $locale]) }}"
                                                       class="btn btn-info btn-sm mr-1" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ localeRoute('quiz.edit', ['quiz' => encryptor('encrypt', $quiz->id), 'lang' => $locale]) }}"
                                                       class="btn btn-primary btn-sm mr-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ localeRoute('quiz.destroy', ['quiz' => encryptor('encrypt', $quiz->id)]) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this quiz?')"
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No quizzes found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($quizzes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $quizzes->appends(['lang' => $locale])->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div id="grid-view" class="tab-pane fade col-lg-12">
                <div class="row">
                    @forelse($quizzes as $quiz)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                @php
                                    $translation = $quiz->translations->firstWhere('locale', $locale);
                                    $quizTitle = $translation ? $translation->title : ($quiz->translations->first() ? $quiz->translations->first()->title : 'No Title');
                                @endphp

                                <h5 class="card-title">{{ $quizTitle }}</h5>

                                @if(!$translation && $quiz->translations->count() > 0)
                                    <span class="badge badge-warning">No {{ $locale }} translation</span>
                                @elseif($quiz->translations->count() === 0)
                                    <span class="badge badge-danger">No translations</span>
                                @endif

                                <p class="card-text">
                                    <strong>Lesson:</strong>
                                    @if($quiz->lesson)
                                        @php
                                            $lessonTranslation = $quiz->lesson->translations->firstWhere('locale', $locale);
                                            $lessonTitle = $lessonTranslation ? $lessonTranslation->title : ($quiz->lesson->translations->first() ? $quiz->lesson->translations->first()->title : 'No Lesson');
                                            $hasLessonTranslation = (bool)$lessonTranslation;
                                        @endphp
                                        {{ $lessonTitle }}
                                        @if(!$hasLessonTranslation)
                                            <span class="badge badge-warning">No {{ $locale }} translation</span>
                                        @endif
                                    @else
                                        No lesson
                                    @endif
                                    <br>
                                    <strong>Questions:</strong> <span class="badge badge-info">{{ $quiz->questions_count }}</span><br>
                                    <strong>Time Limit:</strong> {{ $quiz->time_limit > 0 ? $quiz->time_limit . ' min' : 'No limit' }}<br>
                                    <strong>Passing Score:</strong> {{ $quiz->passing_score }}%<br>
                                    <strong>Max Attempts:</strong> {{ $quiz->max_attempts > 0 ? $quiz->max_attempts : 'Unlimited' }}<br>
                                    <strong>Status:</strong>
                                    <span class="badge badge-{{ $quiz->is_active ? 'success' : 'secondary' }}">
                                        {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                <div class="btn-group">
                                    <a href="{{ localeRoute('quiz.show', ['quiz' => encryptor('encrypt', $quiz->id), 'lang' => $locale]) }}"
                                       class="btn btn-info btn-sm">View</a>
                                    <a href="{{ localeRoute('quiz.edit', ['quiz' => encryptor('encrypt', $quiz->id), 'lang' => $locale]) }}"
                                       class="btn btn-primary btn-sm">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info">No quizzes found</div>
                    </div>
                    @endforelse
                </div>

                @if($quizzes->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $quizzes->appends(['lang' => $locale])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
