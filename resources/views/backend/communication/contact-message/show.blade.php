{{-- resources/views/backend/communication/contact-message/show.blade.php --}}
@extends('backend.layouts.app')
@section('title', 'View Contact Message')

@push('styles')
<style>
    .conversation-history {
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .message-bubble {
        border-left: 4px solid #007bff;
        position: relative;
        margin-bottom: 1rem;
    }

    .message-bubble.user-message {
        border-left-color: #007bff;
        background: #e3f2fd;
    }

    .message-bubble.admin-message {
        border-left-color: #28a745;
        background: #f8fff9;
    }

    .message-bubble.guest-message {
        border-left-color: #6c757d;
        background: #f8f9fa;
    }

    .admin-response {
        font-size: 0.9em;
        background: rgba(40, 167, 69, 0.1);
        padding: 8px;
        border-radius: 4px;
        margin-top: 8px;
        border-left: 3px solid #28a745;
    }
</style>
@endpush

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
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
                            {{-- В секции с отправителем --}}
<div class="col-md-6">
    <strong>From:</strong> {{ $safeSenderName }}<br>
    <strong>Email:</strong> {{ $safeSenderEmail }}<br>
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

                        {{-- История переписки --}}
                        @php
                            $conversationHistory = collect();
                            if ($contactMessage) {
                                // Находим корневое сообщение
                                $rootMessage = $contactMessage;
                                while ($rootMessage->parent_id) {
                                    $rootMessage = \App\Models\ContactMessage::find($rootMessage->parent_id);
                                    if (!$rootMessage) break;
                                }

                                // Собираем всю переписку
                                $conversationHistory = \App\Models\ContactMessage::where('id', $rootMessage->id)
                                    ->orWhere('parent_id', $rootMessage->id)
                                    ->with(['student', 'instructor'])
                                    ->orderBy('created_at', 'asc')
                                    ->get();
                            }
                        @endphp

                        @if($conversationHistory->count() > 1)
                        <div class="contact-conversation mt-4">
                            <h5 class="mb-3">
                                <i class="las la-comments me-2 text-primary"></i>
                                Conversation History
                            </h5>
                            <div class="conversation-history mb-3">
                                @foreach($conversationHistory as $message)
                                    @php
                                        // 🔴 ИСПРАВЛЕНО: Определяем тип сообщения без auth()->id()
                                        $messageType = 'guest-message';
                                        if ($message->sender_type === 'student' && $message->sender_id) {
                                            $messageType = 'user-message';
                                        } elseif ($message->admin_notes && !$message->sender_id) {
                                            $messageType = 'admin-message';
                                        } elseif ($message->sender_type === 'instructor') {
                                            $messageType = 'user-message';
                                        }
                                    @endphp

                                    <div class="message-bubble {{ $messageType }} p-3 rounded">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="{{ $messageType === 'admin-message' ? 'text-success' : 'text-dark' }}">
                                                @if($messageType === 'admin-message')
                                                    <i class="las la-user-tie me-1"></i>Admin
                                                @elseif($message->sender_type === 'student' && $message->student)
                                                    <i class="las la-user-graduate me-1"></i>{{ $message->student->name }} (Student)
                                                @elseif($message->sender_type === 'instructor' && $message->instructor)
                                                    <i class="las la-chalkboard-teacher me-1"></i>{{ $message->instructor->name }} (Instructor)
                                                @else
                                                    <i class="las la-user me-1"></i>{{ $message->name }}
                                                    @if($message->sender_type)
                                                        ({{ ucfirst($message->sender_type) }})
                                                    @else
                                                        (Guest)
                                                    @endif
                                                @endif
                                            </strong>
                                            <small class="text-muted">
                                                {{ $message->created_at->format('M j, H:i') }}
                                            </small>
                                        </div>
                                        <p class="mb-0">{{ $message->message }}</p>

                                        {{-- Показываем ответ админа --}}
                                        @if($message->admin_notes && !$message->sender_id)
                                            <div class="admin-response mt-2 pt-2">
                                                <strong class="text-success">
                                                    <i class="las la-reply me-1"></i>Admin Response:
                                                </strong>
                                                <p class="mb-0 mt-1">{{ $message->admin_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

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
                        Response sent to {{ $safeSenderName }}
                    </small>
                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Форма ответа для админа --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="las la-reply me-2"></i>Send Response
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contact-messages.send-response', $contactMessage->id) }}" method="POST" id="adminResponseForm">
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
                                          placeholder="Type your response to the user..."
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
                                    Also send email notification to user
                                </label>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success btn-lg w-100" id="submitResponseBtn">
                                    <i class="las la-paper-plane me-2"></i>Send Response
                                </button>
                                <small class="form-text text-muted text-center mt-2">
                                    This will mark the message as resolved and notify the user.
                                </small>
                            </div>
                        </form>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка формы ответа админа
    const adminResponseForm = document.getElementById('adminResponseForm');
    if (adminResponseForm) {
        adminResponseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitResponseBtn');
            const originalText = submitBtn.innerHTML;
            const formData = new FormData(this);

            // Показываем индикатор загрузки
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Sending...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Показываем успешное сообщение
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                    alert.innerHTML = `
                        <i class="las la-check-circle me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    adminResponseForm.parentNode.insertBefore(alert, adminResponseForm);

                    // Обновляем страницу через 2 секунды
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error sending response');
                }
            })
            .catch(error => {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    <i class="las la-exclamation-circle me-2"></i>
                    ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                adminResponseForm.parentNode.insertBefore(alert, adminResponseForm);
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush
