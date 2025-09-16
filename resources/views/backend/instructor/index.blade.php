@extends('backend.layouts.app')
@section('title', 'Instructor List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@push('styles')
<style>
/* Main card styles */
.card {
    border-radius: 12px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e3e6f0;
}

/* Input group styles */
.search-input-group {
    border-radius: 8px;
    border: 1px solid #d1d3e2;
    transition: all 0.3s ease;
}

.search-input-group:hover {
    border-color: #bac8f3;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
}

.search-input-group:focus-within {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.search-input-group .input-group-text {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    border-radius: 8px 0 0 8px;
    padding: 0.5rem 1rem;
}

.search-input-group .form-control {
    border: none;
    border-radius: 0 8px 8px 0;
    background: #ffffff;
    padding: 0.5rem 1rem;
    height: 40px;
}

.search-input-group .form-control:focus {
    box-shadow: none;
    background: #ffffff;
}

/* Icons styles */
.fa-user-check, .fa-at {
    font-size: 14px;
    width: 16px;
    height: 16px;
}

/* Button styles */
.search-btn, .clear-btn {
    border-radius: 8px;
    height: 40px;
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.search-btn {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
}

.search-btn:hover {
    background: linear-gradient(135deg, #224abe 0%, #1e3a8a 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
}

.clear-btn {
    border: 1px solid #d1d3e2;
    color: #6e707e;
}

.clear-btn:hover {
    background: #eaecf4;
    border-color: #bac8f3;
    color: #4e73df;
    transform: translateY(-1px);
}

/* Responsive design */
@media (max-width: 768px) {
    .d-flex.gap-3 {
        gap: 1rem !important;
        flex-direction: column;
    }

    .flex-grow-1 {
        width: 100%;
    }

    .d-flex.gap-2 {
        width: 100%;
        justify-content: center;
    }
}

/* Placeholder style */
.form-control::placeholder {
    color: #b7b9cc;
    font-size: 0.875rem;
}

/* Focus effects */
.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
}
</style>
@endpush
@section('content')
<div class="content-body">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text"><h4>Instructor List</h4></div>
            </div>
            <div class="col-sm-6 p-md-0 d-flex justify-content-sm-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('instructor.index') }}">All Instructors</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en'=>'English','ru'=>'Русский','ka'=>'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-pills" id="instructorLangTabs">
                    @foreach ($locales as $code=>$name)
                        <li class="nav-item">
                            <a href="{{ route('instructor.index', ['lang' => $code]) }}"
                               class="nav-link lang-tab {{ $code==$appLocale?'active':'' }}">
                                {{ $name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

<!-- Ultra Compact Search Form with Custom Icons -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('instructor.index') }}" class="d-flex align-items-center gap-3">
                    <input type="hidden" name="lang" value="{{ $appLocale }}">

                    <!-- Name Search -->
                    <div class="position-relative flex-grow-1">
                        <div class="input-group input-group-sm search-input-group">
                            <span class="input-group-text bg-white border-end-0 px-3">
                                <i class="fa-solid fa-user-check text-primary"></i>
                            </span>
                            <input type="text" name="search_name"
                                   class="form-control form-control-sm border-start-0 ps-0"
                                   value="{{ request('search_name') }}"
                                   placeholder="Search by name...">
                        </div>
                    </div>

                    <!-- Email Search -->
                    <div class="position-relative flex-grow-1">
                        <div class="input-group input-group-sm search-input-group">
                            <span class="input-group-text bg-white border-end-0 px-3">
                                <i class="fa-solid fa-at text-primary"></i>
                            </span>
                            <input type="text" name="search_email"
                                   class="form-control form-control-sm border-start-0 ps-0"
                                   value="{{ request('search_email') }}"
                                   placeholder="Search by email...">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3 search-btn" title="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('instructor.index', ['lang' => $appLocale]) }}"
                           class="btn btn-sm btn-outline-secondary px-3 clear-btn" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
        <!-- Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Instructors ({{ $instructors->count() }})</h4>
                        <a href="{{ route('instructor.create', ['lang' => $appLocale]) }}" class="btn btn-primary">+ Add New</a>
                    </div>
                    <div class="card-body ">
                        @if(request()->anyFilled(['search_name', 'search_email']))
                        <div class="alert alert-info">
                            <strong>Search Results:</strong>
                            @if(request('search_name')) Name: "{{ request('search_name') }}" @endif
                            @if(request('search_email')) Email: "{{ request('search_email') }}" @endif
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table id="instructorTable" class="display" style="min-width:845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
@forelse($instructors as $d)
    @php
        $locale = app()->getLocale();
        $translation = $d->translations->firstWhere('locale', $locale);

        $imagePath = public_path('uploads/users');
        $extensions = ['jpg','jpeg','png','gif','webp'];
        $foundImage = null;

        if ($d->image && file_exists($imagePath.'/'.$d->image)) {
            $foundImage = asset('uploads/users/'.$d->image);
        } else {
            foreach ($extensions as $ext) {
                $file = $imagePath.'/instructor_'.$d->id.'.'.$ext;
                if (file_exists($file)) {
                    $foundImage = asset('uploads/users/instructor_'.$d->id.'.'.$ext);
                    break;
                }
            }
        }
        if (!$foundImage) $foundImage = asset('uploads/users/default-instructor.jpg');
    @endphp

    <tr>
        <td><strong>{{ $d->id }}</strong></td>
        <td>{{ $translation->name ?? $d->translations->first()->name ?? 'No name' }}</td>
        <td>{{ $d->email }}</td>
        <td>{{ $d->contact }}</td>
        <td>{{ $translation->designation ?? $d->translations->first()->designation ?? '' }}</td>
        <td>
            <span class="badge {{ $d->status == 1 ? 'badge-success' : 'badge-danger' }}">
                {{ $d->status == 1 ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <img class="rounded-circle" width="35" src="{{ $foundImage }}" alt="Instructor">
        </td>
        <td>
            <a href="{{ route('instructor.edit', ['id' => encryptor('encrypt', $d->id), 'lang' => $appLocale]) }}"
               class="btn btn-sm btn-primary" title="Edit">
                <i class="la la-pencil"></i>
            </a>

            <a href="javascript:void(0);" class="btn btn-sm btn-danger" title="Delete"
               onclick="$('#form{{ $d->id }}').submit()">
                <i class="la la-trash-o"></i>
            </a>
            <form id="form{{ $d->id }}"
                  action="{{ route('instructor.destroy', encryptor('encrypt', $d->id)) }}"
                  method="POST" style="display:none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="lang" value="{{ $appLocale }}">
            </form>
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="text-center">No Instructor Found</td></tr>
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
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>
@endpush
