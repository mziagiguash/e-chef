@extends('backend.layouts.app')
@section('title', 'Enrollment Statistics')

@push('styles')
<link href="{{asset('vendor/chartist/css/chartist.min.css')}}" rel="stylesheet">
<style>
    .stat-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.7;
    }
    .revenue-card { background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); }
    .enrollment-card { background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); }
    .paid-card { background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%); }
    .free-card { background: linear-gradient(45deg, #43e97b 0%, #38f9d7 100%); }
</style>
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Enrollment Statistics</h4>
                    <p class="mb-0">Comprehensive overview of enrollment data and trends</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('enrollment.index')}}">Enrollments</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Statistics</a></li>
                </ol>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card text-white revenue-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white">${{ number_format($totalRevenue, 2) }}</h2>
                                <h6 class="text-white">Total Revenue</h6>
                            </div>
                            <div class="stat-icon">
                                <i class="las la-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card text-white enrollment-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white">{{ number_format($totalEnrollments) }}</h2>
                                <h6 class="text-white">Total Enrollments</h6>
                            </div>
                            <div class="stat-icon">
                                <i class="las la-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card text-white paid-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white">{{ number_format($paidEnrollments) }}</h2>
                                <h6 class="text-white">Paid Enrollments</h6>
                            </div>
                            <div class="stat-icon">
                                <i class="las la-cart-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card text-white free-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white">{{ number_format($freeEnrollments) }}</h2>
                                <h6 class="text-white">Free Enrollments</h6>
                            </div>
                            <div class="stat-icon">
                                <i class="las la-gift"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Charts -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Enrollment Trends (Last 12 Months)</h4>
                    </div>
                    <div class="card-body">
                        <div id="enrollment-chart"></div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment Status Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div id="payment-status-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Popular Courses -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Most Popular Courses</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Enrollments</th>
                                        <th>Instructor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($popularCourses as $course)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('uploads/courses/'.($course->image ?? 'default.jpg')) }}"
                                                     alt="{{ $course->title }}"
                                                     class="rounded me-3" width="40" height="30" style="object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ \Str::limit($course->title, 30) }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $course->enrollments_count }}</span>
                                        </td>
                                        <td>{{ $course->instructor->name ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment Methods</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalWithMethod = $paymentMethodStats->sum();
                                    @endphp
                                    @foreach($paymentMethodStats as $method => $count)
                                    <tr>
                                        <td>
                                            <span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                                        </td>
                                        <td>{{ $count }}</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ ($count / $totalWithMethod) * 100 }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ number_format(($count / $totalWithMethod) * 100, 1) }}%</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Monthly Enrollment Statistics</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Enrollments</th>
                                <th>Revenue</th>
                                <th>Average Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyStats as $stat)
                            <tr>
                                <td>
                                    {{ date('F Y', mktime(0, 0, 0, $stat->month, 1, $stat->year)) }}
                                </td>
                                <td>{{ $stat->enrollments_count }}</td>
                                <td>${{ number_format($stat->revenue, 2) }}</td>
                                <td>
                                    ${{ $stat->enrollments_count > 0 ? number_format($stat->revenue / $stat->enrollments_count, 2) : '0.00' }}
                                </td>
                            </tr>
                            @endforeach
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

@push('scripts')
<!-- Chartist -->
<script src="{{ asset('vendor/chartist/js/chartist.min.js') }}"></script>
<script src="{{ asset('vendor/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Enrollment Trends Chart
    new Chartist.Line('#enrollment-chart', {
        labels: @json($chartLabels),
        series: [
            {
                name: 'Enrollments',
                data: @json($chartEnrollments)
            },
            {
                name: 'Revenue ($)',
                data: @json($chartRevenue)
            }
        ]
    }, {
        axisY: {
            onlyInteger: true
        },
        plugins: [
            Chartist.plugins.tooltip()
        ],
        lineSmooth: Chartist.Interpolation.cardinal({
            tension: 0.4
        }),
        fullWidth: true,
        chartPadding: {
            right: 40
        }
    });

    // Payment Status Pie Chart
    new Chartist.Pie('#payment-status-chart', {
        labels: @json(array_keys($paymentStatusStats->toArray())),
        series: @json(array_values($paymentStatusStats->toArray()))
    }, {
        donut: true,
        donutWidth: 60,
        donutSolid: true,
        startAngle: 270,
        showLabel: true,
        plugins: [
            Chartist.plugins.tooltip()
        ]
    });
});
</script>
@endpush
