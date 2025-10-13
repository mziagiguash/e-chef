@extends('backend.layouts.app')
@section('title', 'Answer List')

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
                    <h4>Answer List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('answer.index')}}">Answers</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('answer.index')}}">All Answer</a>
                    </li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close h-100" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close h-100" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Answers List </h4>
                        <div class="card-tools">
                            <div class="btn-group">
                                <a href="javascript:void(0)" class="btn btn-info btn-sm">
                                    <i class="la la-filter"></i> Filters
                                </a>
                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm">
                                    <i class="la la-download"></i> Export
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{__('#')}}</th>
                                        <th>{{__('Student')}}</th>
                                        <th>{{__('Quiz Attempt')}}</th>
                                        <th>{{__('Question')}}</th>
                                        <th>{{__('Answer')}}</th>
                                        <th>{{__('Correct')}}</th>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($answers as $a)
                                    <tr>
                                        <td>{{ $a->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <img src="{{ $a->attempt->student->avatar ?? asset('images/default-avatar.png') }}"
                                                         class="rounded-circle" width="35" height="35" alt="Avatar">
                                                </div>
                                                <div>
                                                    <strong>{{ $a->attempt->student->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $a->attempt->student->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>Attempt #{{ $a->attempt->id }}</small>
                                            <br>
                                            <small class="text-muted">Score: {{ $a->attempt->score }}%</small>
                                        </td>
                                        <td>
                                            @php
                                                $questionTranslation = $a->question->translations->firstWhere('locale', $locale);
                                                $questionText = $questionTranslation->content ?? $a->question->translations->first()->content ?? $a->question->question_text ?? 'N/A';
                                            @endphp
                                            {{ Str::limit($questionText, 50) }}
                                        </td>
                                        <td>
                                            @if($a->text_answer)
                                                {{ Str::limit($a->text_answer, 30) }}
                                            @elseif($a->rating_answer)
                                                Rating: {{ $a->rating_answer }}
                                            @else
                                                <em>No answer provided</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($a->is_correct)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $a->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $a->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <!-- Delete Button -->
                                            <form action="{{ route('answer.destroy', encryptor('encrypt', $a->id)) }}"
                                                  method="POST" id="delete-form-{{ $a->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp"
                                                        title="Delete Permanently"
                                                        onclick="confirmDelete({{ $a->id }})">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="la la-inbox" style="font-size: 3rem;"></i>
                                                <h3>No Answers Found</h3>
                                                <p class="text-muted">There are no answers available at the moment.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Info -->
                        @if($answers->hasPages())
                        <div class="pagination-info text-center mb-2">
                            Showing {{ $answers->firstItem() }} to {{ $answers->lastItem() }} of {{ $answers->total() }} results
                        </div>
                        @endif

                        <!-- Pagination -->
                        @if($answers->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            <nav aria-label="Answers pagination">
                                <ul class="pagination pagination-sm">
                                    {{-- Previous Page Link --}}
                                    @if($answers->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $answers->previousPageUrl() }}" rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- First Page --}}
                                    @if($answers->currentPage() > 3)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $answers->url(1) }}">1</a>
                                        </li>
                                        @if($answers->currentPage() > 4)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max(1, $answers->currentPage() - 2);
                                        $end = min($answers->lastPage(), $answers->currentPage() + 2);
                                    @endphp

                                    @for($page = $start; $page <= $end; $page++)
                                        @if($page == $answers->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $answers->url($page) }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    {{-- Last Page --}}
                                    @if($answers->currentPage() < $answers->lastPage() - 2)
                                        @if($answers->currentPage() < $answers->lastPage() - 3)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $answers->url($answers->lastPage()) }}">{{ $answers->lastPage() }}</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if($answers->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $answers->nextPageUrl() }}" rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
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
<script>
function confirmDelete(answerId) {
    if (confirm('Are you sure you want to delete this answer permanently? This action cannot be undone.')) {
        document.getElementById('delete-form-' + answerId).submit();
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#example3').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "language": {
            "emptyTable": "No answers available",
            "zeroRecords": "No matching answers found"
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Actions column
        ]
    });
});
</script>

<style>
.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.shadow {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Стили для пагинации */
.pagination {
    margin: 0;
}

.page-item .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    border: 1px solid #dee2e6;
    color: #007bff;
    margin: 0 2px;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}
</style>
@endpush
