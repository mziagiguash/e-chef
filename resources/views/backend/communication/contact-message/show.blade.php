{{-- resources/views/backend/communication/contact-message/show.blade.php --}}
@extends('backend.layouts.app')
@section('title', 'View Contact Message')

@section('content')

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Contact Message #{{ $contactMessage->id }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('contact-messages.index') }}">Contact Messages</a></li>
                    <li class="breadcrumb-item active">View Message</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Message Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>From:</strong> {{ $contactMessage->name }}<br>
                                <strong>Email:</strong> {{ $contactMessage->email }}<br>
                                @if($contactMessage->sender_type)
                                    <strong>Type:</strong> {{ ucfirst($contactMessage->sender_type) }}<br>
                                @endif
                            </div>
                            <div class="col-md-6">
    <strong>Date:</strong> {{ $contactMessage->created_at->format('M d, Y H:i') }}<br>
    <strong>Status:</strong>
    <span class="badge badge-{{ $contactMessage->status }}">
        {{ ucfirst(str_replace('_', ' ', $contactMessage->status)) }}
    </span><br>
    @if($contactMessage->resolved_at)
        <strong>Resolved:</strong> {{ $contactMessage->resolved_at->format('M d, Y H:i') }}
    @else
        <strong>Resolved:</strong> <span class="text-muted">Not resolved yet</span>
    @endif
</div>
                        </div>
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">
            <i class="las la-reply me-2"></i>Send Response to Student
        </h4>
    </div>
    <div class="card-body">
        <form action="{{ route('contact-messages.send-response', $contactMessage->id) }}" method="POST">
    @csrf

    {{-- Отладочная информация --}}
    <div class="alert alert-info mb-3">
        <h6>Debug Info:</h6>
        <p><strong>Contact Message ID:</strong> {{ $contactMessage->id }}</p>
        <p><strong>Sender Type:</strong> {{ $contactMessage->sender_type }}</p>
        <p><strong>Sender ID:</strong> {{ $contactMessage->sender_id }}</p>
        <p><strong>Student ID:</strong> {{ $contactMessage->sender_type === 'student' ? $contactMessage->sender_id : 'N/A' }}</p>
    </div>
    @csrf

            <div class="form-group">
                <label for="response_subject"><strong>Subject *</strong></label>
                <input type="text" name="response_subject" id="response_subject"
                       class="form-control @error('response_subject') is-invalid @enderror"
                       value="{{ old('response_subject', 'Re: ' . $contactMessage->subject) }}"
                       required>
                @error('response_subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-3">
                <label for="response_message"><strong>Response Message *</strong></label>
                <textarea name="response_message" id="response_message"
                          rows="6" class="form-control @error('response_message') is-invalid @enderror"
                          placeholder="Type your response to the student..."
                          required>{{ old('response_message') }}</textarea>
                @error('response_message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 10 characters required.</small>
            </div>

            <div class="form-check mt-3">
                <input type="checkbox" name="also_send_email" id="also_send_email"
                       class="form-check-input" value="1" {{ old('also_send_email', true) ? 'checked' : '' }}>
                <label for="also_send_email" class="form-check-label">
                    Also send email notification to student
                </label>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="las la-paper-plane me-2"></i>Send Response to Student
                </button>
                <small class="form-text text-muted text-center mt-2">
                    This will mark the message as resolved and notify the student.
                </small>
            </div>
        </form>
    </div>
</div>

            <div class="form-check mt-3">
                <input type="checkbox" name="also_send_email" id="also_send_email"
                       class="form-check-input" value="1" checked>
                <label for="also_send_email" class="form-check-label">
                    Also send email notification to student
                </label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="las la-paper-plane me-2"></i>Send Response to Student
                </button>
            </div>
        </form>
    </div>
</div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Subject:</strong>
                                <h5>{{ $contactMessage->subject }}</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <strong>Message:</strong>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($contactMessage->message)) !!}
                                </div>
                            </div>
                        </div>

                        {{-- В секции с ответом админа --}}
@if($contactMessage->admin_notes)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="las la-reply me-2"></i>Your Response
                    @if($contactMessage->resolved_at)
                        <small class="float-end">{{ $contactMessage->resolved_at->format('M d, Y H:i') }}</small>
                    @else
                        <small class="float-end">Just now</small>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="response-content">
                    {!! nl2br(e($contactMessage->admin_notes)) !!}
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="las la-check-circle me-1 text-success"></i>
                        Response sent to student
                        @if($contactMessage->student)
                            - {{ $contactMessage->student->name }}
                        @else
                            - {{ $contactMessage->name }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Manage Message</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contact-messages.update-status', $contactMessage->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="status"><strong>Status</strong></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="new" {{ $contactMessage->status == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="in_progress" {{ $contactMessage->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $contactMessage->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin_notes"><strong>Admin Notes</strong></label>
                                <textarea name="admin_notes" id="admin_notes" rows="4"
                                          class="form-control" placeholder="Add internal notes...">{{ old('admin_notes', $contactMessage->admin_notes) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}"
                               class="btn btn-success btn-sm" target="_blank">
                                <i class="la la-reply"></i> Reply via Email
                            </a>

                            <form action="{{ route('contact-messages.destroy', $contactMessage->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="la la-trash-o"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
