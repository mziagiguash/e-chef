@extends('backend.layouts.app')
@section('title', 'Enrollment Details')

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Enrollment Details</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('enrollment.index')}}">Enrollments</a></li>
                    <li class="breadcrumb-item active">Enrollment Details</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Enrollment Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6>Student Information</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <img class="rounded-circle me-3" width="60" height="60"
                                            src="{{ asset('uploads/students/'.($enrollment->student->image ?? 'default.jpg')) }}"
                                            alt="{{ $enrollment->student->name ?? 'N/A' }}">
                                        <div>
                                            <strong class="d-block">{{ $enrollment->student->name ?? 'N/A' }}</strong>
                                            <small class="text-muted">{{ $enrollment->student->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6>Course Information</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <img class="rounded me-3" width="60" height="60" style="object-fit: cover;"
                                            src="{{ asset('uploads/courses/'.($enrollment->course->image ?? 'default.jpg')) }}"
                                            alt="{{ $enrollment->course->title ?? 'N/A' }}">
                                        <div>
                                            <strong class="d-block">{{ $enrollment->course->title ?? 'N/A' }}</strong>
                                            <small class="text-muted">By: {{ $enrollment->course->instructor->name ?? 'Unknown' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <strong>Enrollment Date:</strong>
                                    <p class="mb-0">{{ $enrollment->enrollment_date->format('F d, Y h:i A') }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Amount Paid:</strong>
                                    <p class="mb-0">
                                        @if($enrollment->amount_paid)
                                            <span class="text-success">${{ number_format($enrollment->amount_paid, 2) }}</span>
                                        @else
                                            <span class="text-muted">Free</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <strong>Payment Status:</strong>
                                    <p class="mb-0">{!! $enrollment->payment_status_badge !!}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Payment Method:</strong>
                                    <p class="mb-0">
                                        @if($enrollment->payment_method)
                                            {{ ucfirst(str_replace('_', ' ', $enrollment->payment_method)) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </p>
                                </div>

                                @if($enrollment->transaction_id)
                                <div class="mb-3">
                                    <strong>Transaction ID:</strong>
                                    <p class="mb-0">{{ $enrollment->transaction_id }}</p>
                                </div>
                                @endif

                                @if($enrollment->payment_date)
                                <div class="mb-3">
                                    <strong>Payment Date:</strong>
                                    <p class="mb-0">{{ $enrollment->payment_date->format('F d, Y h:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ localeRoute('enrollment.edit', $enrollment->id) }}"
                               class="btn btn-primary btn-block">
                                <i class="las la-edit me-2"></i>Edit Enrollment
                            </a>

                            <form action="{{ localeRoute('enrollment.destroy', $enrollment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block w-100"
                                        onclick="return confirm('Are you sure you want to delete this enrollment?')">
                                    <i class="las la-trash me-2"></i>Delete Enrollment
                                </button>
                            </form>

                            <a href="{{ localeRoute('enrollment.index') }}" class="btn btn-light btn-block">
                                <i class="las la-arrow-left me-2"></i>Back to List
                            </a>
                        </div>

                        <hr>

                        <div class="mt-3">
                            <h6>Update Payment Status</h6>
                            <form action="{{ localeRoute('enrollment.update-payment-status', $enrollment->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <select name="payment_status" class="form-control">
                                        <option value="pending" {{ $enrollment->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ $enrollment->payment_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ $enrollment->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ $enrollment->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
