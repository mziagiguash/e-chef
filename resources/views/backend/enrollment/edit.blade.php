@extends('backend.layouts.app')
@section('title', 'Edit Enrollment')

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Enrollment</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{localeRoute('enrollment.index')}}">Enrollments</a></li>
                    <li class="breadcrumb-item active">Edit Enrollment</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Enrollment Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ localeRoute('enrollment.update', $enrollment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="student_id" class="form-label">Student *</label>
                                        <select name="student_id" id="student_id" class="form-control" required>
                                            <option value="">Select Student</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" {{ $enrollment->student_id == $student->id ? 'selected' : '' }}>
                                                    {{ $student->name }} ({{ $student->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="course_id" class="form-label">Course *</label>
                                        <select name="course_id" id="course_id" class="form-control" required>
                                            <option value="">Select Course</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" data-price="{{ $course->price ?? 0 }}" {{ $enrollment->course_id == $course->id ? 'selected' : '' }}>
                                                    {{ $course->title }} - ${{ number_format($course->price ?? 0, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="amount_paid" class="form-label">Amount Paid ($) *</label>
                                        <input type="number" step="0.01" name="amount_paid" id="amount_paid"
                                               class="form-control" value="{{ old('amount_paid', $enrollment->amount_paid) }}"
                                               placeholder="0.00" required>
                                        @error('amount_paid')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="payment_method" class="form-label">Payment Method *</label>
                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                            <option value="">Select Method</option>
                                            <option value="credit_card" {{ $enrollment->payment_method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="paypal" {{ $enrollment->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                            <option value="bank_transfer" {{ $enrollment->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="sslcommerz" {{ $enrollment->payment_method == 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                                        </select>
                                        @error('payment_method')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="payment_status" class="form-label">Payment Status *</label>
                                        <select name="payment_status" id="payment_status" class="form-control" required>
                                            <option value="pending" {{ $enrollment->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="completed" {{ $enrollment->payment_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="failed" {{ $enrollment->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="refunded" {{ $enrollment->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        </select>
                                        @error('payment_status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="transaction_id" class="form-label">Transaction ID</label>
                                        <input type="text" name="transaction_id" id="transaction_id"
                                               class="form-control" value="{{ old('transaction_id', $enrollment->transaction_id) }}"
                                               placeholder="Enter transaction ID">
                                        @error('transaction_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Update Enrollment</button>
                                    <a href="{{ localeRoute('enrollment.show', $enrollment->id) }}" class="btn btn-light">Cancel</a>
                                </div>
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
    // Auto-fill amount when course is selected
    const courseSelect = document.getElementById('course_id');
    const amountInput = document.getElementById('amount_paid');

    courseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const coursePrice = selectedOption.getAttribute('data-price');

        if (coursePrice && coursePrice > 0 && !amountInput.value) {
            amountInput.value = coursePrice;
        }
    });
});
</script>
@endpush
