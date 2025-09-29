@extends('backend.layouts.app')
@section('title', 'Enrollment List')

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
                    <h4>Enrollments</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('enrollment.index')}}">Enrollments</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('enrollment.index')}}">All Enrollment</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="row tab-content">
                    <div id="list-view" class="tab-pane fade active show col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">All Enrollments List </h4>
                                <a href="{{localeRoute('enrollment.statistics')}}" class="btn btn-info">Statistics</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Course Name</th>
                                                <th>Course Image</th>
                                                <th>Amount Paid</th>
                                                <th>Payment Status</th>
                                                <th>Payment Method</th>
                                                <th>Enrollment Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($enrollments as $enrollment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img class="rounded-circle" width="35" height="35"
                                                            src="{{ asset('uploads/students/'.($enrollment->student->image ?? 'default.jpg')) }}"
                                                            alt="{{ $enrollment->student->name ?? 'N/A' }}">
                                                        <div class="ms-3">
                                                            <strong>{{ $enrollment->student->name ?? 'N/A' }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ $enrollment->course->title ?? 'N/A' }}</strong>
                                                    @if($enrollment->course)
                                                    <br><small class="text-muted">By: {{ $enrollment->course->instructor->name ?? 'Unknown' }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <img class="img-fluid rounded" width="80" height="60" style="object-fit: cover;"
                                                        src="{{ asset('uploads/courses/'.($enrollment->course->image ?? 'default.jpg')) }}"
                                                        alt="{{ $enrollment->course->title ?? 'Course' }}">
                                                </td>
                                                <td>
                                                    @if($enrollment->amount_paid)
                                                        <strong class="text-success">${{ number_format($enrollment->amount_paid, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">Free</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {!! $enrollment->payment_status_badge !!}
                                                </td>
                                                <td>
                                                    @if($enrollment->payment_method)
                                                        <span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $enrollment->payment_method)) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $enrollment->enrollment_date->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $enrollment->enrollment_date->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ localeRoute('enrollment.show', $enrollment->id) }}"
                                                            class="btn btn-sm btn-info me-1" title="View">
                                                            <i class="la la-eye"></i>
                                                        </a>
                                                        <form action="{{ localeRoute('enrollment.destroy', $enrollment->id) }}"
                                                            method="post" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this enrollment?')">
                                                                <i class="la la-trash-o"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="las la-user-graduate fa-3x text-muted mb-3"></i>
                                                        <h5>No Enrollments Found</h5>
                                                        <p class="text-muted">There are no enrollments in the system yet.</p>
                                                        <p class="text-muted small">Enrollments will appear here after successful course purchases.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>
@endpush
