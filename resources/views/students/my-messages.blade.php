@extends('frontend.layouts.app')
@section('title', 'My Contact Messages')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>My Contact Messages</h4>
                    <p class="mb-0">Manage your conversations with administration</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('studentdashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Messages</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="las la-envelope me-2"></i>My Conversations
                        </h4>
                        <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="btn btn-primary btn-sm">
                            <i class="las la-plus me-1"></i>New Message
                        </a>
                    </div>
                    <div class="card-body">
                        @if($messages->count() > 0)
                            @foreach($messages as $conversation)
                                @php
                                    $mainMessage = $conversation->firstWhere('parent_id', null) ?: $conversation->first();
                                    $replies = $conversation->where('parent_id', '!=', null)->sortBy('created_at');
                                @endphp

                                <div class="conversation-card card mb-4 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 text-dark">{{ $mainMessage->subject }}</h5>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <small class="text-muted">
                                                    <i class="las la-clock me-1"></i>{{ $mainMessage->created_at->format('M d, Y H:i') }}
                                                </small>
                                                <span class="badge badge-status bg-{{ $mainMessage->status === 'resolved' ? 'success' : ($mainMessage->status === 'in_progress' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($mainMessage->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="action-buttons ms-3">
                                            @if($replies->count() === 0)
                                                <form action="{{ route('student.delete-message', ['locale' => app()->getLocale(), 'id' => $mainMessage->id]) }}" method="POST" class="d-inline delete-message-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                                            onclick="return confirm('Are you sure you want to delete this message?')">
                                                        <i class="las la-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <!-- Основное сообщение -->
                                        <div class="message-bubble user-message mb-4">
                                            <div class="message-header d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary me-3">
                                                        <i class="las la-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-dark">You</strong>
                                                        <div class="message-time text-muted small">{{ $mainMessage->created_at->format('M j, H:i') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="message-content">
                                                <p class="mb-0">{{ $mainMessage->message }}</p>
                                            </div>
                                        </div>

                                        <!-- Ответы -->
                                        @foreach($replies as $reply)
                                            <div class="message-bubble {{ $reply->sender_id === auth()->id() ? 'user-message' : 'admin-message' }} mb-4">
                                                <div class="message-header d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle {{ $reply->sender_id === auth()->id() ? 'bg-primary' : 'bg-success' }} me-3">
                                                            <i class="las {{ $reply->sender_id === auth()->id() ? 'la-user' : 'la-user-tie' }} text-white"></i>
                                                        </div>
                                                        <div>
                                                            <strong class="{{ $reply->sender_id === auth()->id() ? 'text-dark' : 'text-success' }}">
                                                                {{ $reply->sender_id === auth()->id() ? 'You' : 'Admin' }}
                                                            </strong>
                                                            <div class="message-time text-muted small">{{ $reply->created_at->format('M j, H:i') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="message-content">
                                                    <p class="mb-0">{{ $reply->message }}</p>

                                                    @if($reply->admin_notes && !$reply->sender_id)
                                                        <div class="admin-response mt-3 p-3 bg-light rounded">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="las la-reply text-success me-2"></i>
                                                                <strong class="text-success">Admin Response:</strong>
                                                            </div>
                                                            <p class="mb-0 text-dark">{{ $reply->admin_notes }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Форма ответа -->
                                        @if($mainMessage->status !== 'resolved')
                                            <div class="continue-conversation mt-4 p-4 bg-light rounded">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="las la-comments text-primary me-2 fs-5"></i>
                                                    <h6 class="mb-0 text-primary">Continue Conversation</h6>
                                                </div>
                                                <form method="POST" action="{{ route('contact.continue') }}" class="continue-dialog-form">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $mainMessage->id }}">
                                                    <input type="hidden" name="subject" value="Re: {{ $mainMessage->subject }}">

                                                    <div class="mb-3">
                                                        <label class="form-label small text-muted fw-semibold">
                                                            Your message to admin:
                                                        </label>
                                                        <textarea
                                                            class="form-control message-textarea"
                                                            name="message"
                                                            rows="4"
                                                            placeholder="Type your response here..."
                                                            required
                                                        ></textarea>
                                                        <div class="form-text text-muted">
                                                            <i class="las la-info-circle me-1"></i>Your response will reopen this conversation.
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="status-info">
                                                            <small class="text-muted me-2">Current Status:</small>
                                                            <span class="badge bg-{{ $mainMessage->status === 'in_progress' ? 'warning' : 'info' }}">
                                                                {{ ucfirst($mainMessage->status) }}
                                                            </span>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-send">
                                                            <i class="las la-paper-plane me-1"></i>Send Reply
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <div class="resolved-alert alert alert-success mt-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="las la-check-circle me-2 fs-5"></i>
                                                    <div>
                                                        <strong>Conversation Resolved</strong>
                                                        <div class="small">This conversation has been marked as resolved.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon mb-4">
                                    <i class="las la-envelope-open-text display-1 text-muted opacity-50"></i>
                                </div>
                                <h5 class="text-muted mb-3">No messages yet</h5>
                                <p class="text-muted mb-4">You haven't sent any contact messages yet.</p>
                                <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="btn btn-primary btn-lg">
                                    <i class="las la-plus me-2"></i>Send New Message
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Основные стили контейнера */
.conversation-card {
    border: 1px solid #e3e6f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.conversation-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Стили для сообщений */
.message-bubble {
    position: relative;
    padding: 0;
}

.user-message {
    margin-left: 20%;
}

.admin-message {
    margin-right: 20%;
}

.message-header {
    padding-bottom: 8px;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.message-content {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.user-message .message-content {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
}

.admin-message .message-content {
    background: white;
    border: 1px solid #e3e6f0;
}

.message-time {
    font-size: 0.75rem;
}

/* Стили для формы ответа */
.continue-conversation {
    border: 1px solid #d1e7ff;
    background: linear-gradient(135deg, #f8fbff, #e3f2fd);
    border-radius: 12px;
}

.message-textarea {
    border-radius: 8px;
    border: 1px solid #d1e7ff;
    resize: vertical;
    min-height: 100px;
}

.message-textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.btn-send {
    border-radius: 8px;
    padding: 8px 20px;
    font-weight: 500;
}

/* Стили для бейджей */
.badge-status {
    font-size: 0.7rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 500;
}

/* Стили для админского ответа */
.admin-response {
    border-left: 4px solid #28a745;
    background: rgba(40, 167, 69, 0.05);
}

/* Стили для пустого состояния */
.empty-state {
    background: #f8f9fa;
    border-radius: 12px;
    margin: 20px 0;
}

.empty-state-icon {
    opacity: 0.7;
}

/* Стили для алерта resolved */
.resolved-alert {
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
}

/* Анимации */
.btn-delete {
    transition: all 0.3s ease;
}

.btn-delete:hover {
    transform: scale(1.05);
}

/* Адаптивность */
@media (max-width: 768px) {
    .user-message,
    .admin-message {
        margin-left: 0;
        margin-right: 0;
    }

    .conversation-card .card-body {
        padding: 1rem;
    }

    .message-header {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .action-buttons {
        margin-top: 10px;
        margin-left: 0 !important;
    }
}

/* Плавные переходы */
.continue-dialog-form {
    transition: all 0.3s ease;
}

/* Стили для статусов */
.bg-new { background-color: #17a2b8 !important; }
.bg-in_progress { background-color: #ffc107 !important; color: #000 !important; }
.bg-resolved { background-color: #28a745 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX для отправки продолжения диалога
    document.querySelectorAll('.continue-dialog-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('.btn-send');
            const originalText = submitBtn.innerHTML;

            // Показываем индикатор загрузки
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Sending...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error('Validation error: ' + JSON.stringify(data.errors));
                    });
                }

                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        <i class="las la-check-circle me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.parentNode.insertBefore(alert, form);

                    form.querySelector('textarea').value = '';

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                alert.innerHTML = `
                    <i class="las la-exclamation-circle me-2"></i>
                    <strong>Error:</strong> ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                form.parentNode.insertBefore(alert, form);
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });

    // AJAX для удаления сообщений
    document.querySelectorAll('.delete-message-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this message?')) {
                return;
            }

            const submitBtn = this.querySelector('.btn-delete');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Deleting...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.conversation-card').style.opacity = '0';
                    setTimeout(() => {
                        this.closest('.conversation-card').remove();

                        // Показать сообщение об успехе
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="las la-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.card-body').prepend(alert);
                    }, 300);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });
});
</script>
@endsection
