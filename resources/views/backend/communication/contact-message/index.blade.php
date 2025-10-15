{{-- resources/views/backend/communication/contact-message/index.blade.php --}}
@extends('backend.layouts.app')
@section('title', 'Contact Messages')

@push('styles')
<!-- Datatable -->
<link href="{{asset('public/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    .badge-new { background-color: #007bff; color: white; }
    .badge-in_progress { background-color: #ffc107; color: black; }
    .badge-resolved { background-color: #28a745; color: white; }
</style>
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Contact Messages</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Contact Messages</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Contact Messages</h4>
                        <div class="stats">
    <span class="badge badge-new mr-2">New: {{ $stats['new'] }}</span>
    <span class="badge badge-in_progress mr-2">In Progress: {{ $stats['in_progress'] }}</span>
    <span class="badge badge-resolved">Resolved: {{ $stats['resolved'] }}</span>
</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="contactMessagesTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sender</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
{{-- resources/views/backend/communication/contact-message/index.blade.php --}}

<tbody>
    @forelse ($contactMessages as $message)
    <tr>
        <td>{{ $message->id }}</td>
        <td>
            <strong>{{ $message->name }}</strong>
            <br>
            <small class="text-muted">
                {{-- 🔴 ИСПРАВЛЕНО: Используем безопасные методы --}}
                {{ $message->sender_display_name }}
            </small>
        </td>
        <td>
            {{ $message->sender_display_email }}
        </td>
        <td>{{ Str::limit($message->subject, 50) }}</td>
        <td>
            <span class="badge status-badge badge-{{ $message->status }}">
                {{ ucfirst(str_replace('_', ' ', $message->status)) }}
            </span>
        </td>
        <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
        <td>
            <a href="{{ route('contact-messages.show', $message->id) }}"
               class="btn btn-sm btn-primary" title="View">
                <i class="la la-eye"></i>
            </a>
            <form action="{{ route('contact-messages.destroy', $message->id) }}"
                  method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure?')" title="Delete">
                    <i class="la la-trash-o"></i>
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">
            <h5>No contact messages found</h5>
        </td>
    </tr>
    @endforelse
</tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $contactMessages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<style>
    .stats .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
    .badge-new { background-color: #007bff; color: white; }
    .badge-in_progress { background-color: #ffc107; color: black; }
    .badge-resolved { background-color: #28a745; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
</style>

@push('scripts')
<!-- Datatable -->
<script src="{{asset('public/vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/js/plugins-init/datatables.init.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#contactMessagesTable').DataTable({
            paging: false, // Disable DataTable pagination since we have Laravel pagination
            searching: true,
            ordering: true,
            info: false
        });
    });
</script>
@endpush
