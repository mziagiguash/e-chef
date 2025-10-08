@extends('backend.layouts.app')
@section('title', 'Review List')

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
                    <h4>Review List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('review.index')}}">Reviews</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('review.index')}}">All Review</a>
                    </li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        {{-- Language Tabs --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="reviewLangTabs" role="tablist">
                    @foreach($locales as $code => $name)
                        <li class="nav-item">
                            <a href="#" class="nav-link lang-tab {{ $code === $appLocale ? 'active' : '' }}" data-locale="{{ $code }}">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item"><a href="#list-view" data-toggle="tab"
                            class="nav-link btn-primary mr-1 show active">List View</a></li>
                    <li class="nav-item"><a href="#grid-view" data-toggle="tab" class="nav-link btn-primary">Grid View</a></li>
                </ul>
            </div>
            <div class="col-lg-12">
                <div class="row tab-content">

                    {{-- List View --}}
                    <div id="list-view" class="tab-pane fade active show col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">All Reviews List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rating</th>
                                                <th>Comment</th>
                                                <th>Course</th>
                                                <th>Student</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($reviews as $review)
                                            <tr class="review-row"
                                                data-comments='@json($review->translations->pluck("comment","locale"))'>
                                                <td>{{ $review->id }}</td>
                                                <td>
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="la la-star{{ $i <= $review->rating ? ' text-warning' : '-o' }}"></i>
                                                        @endfor
                                                        <span class="ml-1">({{ $review->rating }})</span>
                                                    </div>
                                                </td>
                                                <td class="review-comment">
                                                    {{ $review->display_comment }}
                                                </td>
                                                <td>{{ $review->course?->title ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($review->student && $review->student->image)
                                                            <img class="rounded-circle" width="35" height="35"
                                                                src="{{ asset('uploads/students/'.$review->student->image) }}"
                                                                alt="{{ $review->student->name }}">
                                                        @endif
                                                        <div class="ms-2">
                                                            <strong>{{ $review->student?->name ?? 'N/A' }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{localeRoute('review.edit', encryptor('encrypt', $review->id))}}"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="la la-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                                        title="Delete" onclick="$('#form{{$review->id}}').submit()">
                                                        <i class="la la-trash-o"></i>
                                                    </a>
                                                    <form id="form{{$review->id}}"
                                                        action="{{localeRoute('review.destroy', encryptor('encrypt', $review->id))}}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="las la-comments fa-3x text-muted mb-3"></i>
                                                        <h5>No Reviews Found</h5>
                                                        <p class="text-muted">There are no reviews in the system yet.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $reviews->links() }}
                            </div>
                        </div>
                    </div>

                    {{-- Grid View --}}
                    <div id="grid-view" class="tab-pane fade col-lg-12">
                        <div class="row">
                            @forelse($reviews as $review)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                    <div class="card review-card h-100"
                                         data-comments='@json($review->translations->pluck("comment","locale"))'>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="la la-star{{ $i <= $review->rating ? ' text-warning' : '-o' }}"></i>
                                                    @endfor
                                                    <small class="text-muted">({{ $review->rating }})</small>
                                                </div>
                                                <span class="badge badge-light">{{ $review->id }}</span>
                                            </div>

                                            <p class="review-comment mb-3">{{ $review->display_comment }}</p>

                                            <div class="course-info mb-2">
                                                <strong>Course:</strong>
                                                <span class="text-muted">{{ $review->course?->title ?? 'N/A' }}</span>
                                            </div>

                                            <div class="student-info d-flex align-items-center">
                                                @if($review->student && $review->student->image)
                                                    <img class="rounded-circle" width="40" height="40"
                                                        src="{{ asset('uploads/students/'.$review->student->image) }}"
                                                        alt="{{ $review->student->name }}">
                                                @endif
                                                <div class="ms-2">
                                                    <strong>{{ $review->student?->name ?? 'N/A' }}</strong>
                                                </div>
                                            </div>

                                            <div class="mt-3 d-flex justify-content-between">
                                                <a href="{{localeRoute('review.edit', encryptor('encrypt', $review->id))}}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="la la-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                                    onclick="$('#form{{$review->id}}').submit()">
                                                    <i class="la la-trash-o"></i> Delete
                                                </a>
                                                <form id="form{{$review->id}}"
                                                    action="{{localeRoute('review.destroy', encryptor('encrypt', $review->id))}}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <div class="empty-state">
                                        <i class="las la-comments fa-3x text-muted mb-3"></i>
                                        <h5>No Reviews Found</h5>
                                        <p class="text-muted">There are no reviews in the system yet.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<!-- Datatable -->
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/plugins-init/datatables.init.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const savedLocale = localStorage.getItem('reviews_lang') || '{{ app()->getLocale() }}';

    function updateReviewLanguage(locale) {
        document.querySelectorAll('.review-row, .review-card').forEach(el => {
            const comments = JSON.parse(el.dataset.comments || '{}');
            const commentText = comments[locale] ?? Object.values(comments)[0] ?? 'No comment';

            el.querySelectorAll('.review-comment').forEach(c => c.textContent = commentText);
        });
    }

    // Language tabs
    document.querySelectorAll('#reviewLangTabs .lang-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.locale === savedLocale);

        tab.addEventListener('click', function(e){
            e.preventDefault();
            const locale = this.dataset.locale;
            localStorage.setItem('reviews_lang', locale);

            document.querySelectorAll('#reviewLangTabs .lang-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            updateReviewLanguage(locale);
        });
    });

    updateReviewLanguage(savedLocale);

    // View toggle
    document.querySelectorAll('.nav-pills .nav-link').forEach(btn => {
        btn.addEventListener('click', function(){
            const target = this.getAttribute('href');

            document.querySelectorAll('.nav-pills .nav-link').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active', 'show'));
            document.querySelector(target).classList.add('active', 'show');
        });
    });
});
</script>
@endpush
