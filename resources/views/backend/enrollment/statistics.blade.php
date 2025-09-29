@extends('backend.layouts.app')
@section('title', 'Enrollment Statistics')

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Enrollment Statistics</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('enrollment.index')}}">Enrollments</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <div class="card-body">
                        <h3 class="card-title text-white">{{ $stats['total'] }}</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">Total Enrollments</h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="las la-user-graduate"></i></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <div class="card-body">
                        <h3 class="card-title text-white">{{ $stats['completed'] }}</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">Completed Payments</h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="las la-check-circle"></i></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <div class="card-body">
                        <h3 class="card-title text-white">{{ $stats['pending'] }}</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">Pending Payments</h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="las la-clock"></i></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <div class="card-body">
                        <h3 class="card-title text-white">${{ number_format($stats['revenue'], 2) }}</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">Total Revenue</h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="las la-dollar-sign"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Enrollment Overview</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped verticle-middle">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                        <th>Revenue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>{{ $stats['pending'] }}</td>
                                        <td>{{ $stats['total'] > 0 ? number_format(($stats['pending'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                        <td>$0.00</td>
                                        <td>
                                            <a href="{{ localeRoute('enrollment.index') }}?status=pending" class="btn btn-sm btn-light">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-success">Completed</span></td>
                                        <td>{{ $stats['completed'] }}</td>
                                        <td>{{ $stats['total'] > 0 ? number_format(($stats['completed'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                        <td>${{ number_format($stats['revenue'], 2) }}</td>
                                        <td>
                                            <a href="{{ localeRoute('enrollment.index') }}?status=completed" class="btn btn-sm btn-light">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-danger">Failed</span></td>
                                        <td>{{ $stats['failed'] }}</td>
                                        <td>{{ $stats['total'] > 0 ? number_format(($stats['failed'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                        <td>$0.00</td>
                                        <td>
                                            <a href="{{ localeRoute('enrollment.index') }}?status=failed" class="btn btn-sm btn-light">View</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.gradient-1 {
    background: linear-gradient(87deg, #f093fb 0, #f5576c 100%) !important;
}
.gradient-2 {
    background: linear-gradient(87deg, #4facfe 0, #00f2fe 100%) !important;
}
.gradient-3 {
    background: linear-gradient(87deg, #43e97b 0, #38f9d7 100%) !important;
}
.gradient-4 {
    background: linear-gradient(87deg, #fa709a 0, #fee140 100%) !important;
}
</style>
@endpush
