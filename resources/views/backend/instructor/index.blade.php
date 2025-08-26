@extends('backend.layouts.app')
@section('title', 'Instructor List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
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
                            <a href="#" class="nav-link lang-tab {{ $code==$appLocale?'active':'' }}" data-locale="{{ $code }}">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Instructors</h4>
                        <a href="{{ route('instructor.create') }}" class="btn btn-primary">+ Add New</a>
                    </div>
                    <div class="card-body">
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
@forelse($instructor as $d)
    @php
        $locale = app()->getLocale();

        // Декодируем переводы (name и designation)
        $names = is_string($d->name) ? json_decode($d->name, true) : $d->name;
        $designations = is_string($d->designation) ? json_decode($d->designation, true) : $d->designation;

        // Картинка: ищем по ID с разными расширениями или дефолт
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
        <td>
            @foreach($locales as $code => $localeName)
                <span class="lang-{{ $code }}" style="{{ $code == $locale ? '' : 'display:none' }}">
                    {{ $names[$code] ?? '' }}
                </span>
            @endforeach
        </td>
        <td>{{ $d->email }}</td>
        <td>{{ $d->contact }}</td>
        <td>
            @foreach($locales as $code => $localeName)
                <span class="lang-{{ $code }}" style="{{ $code == $locale ? '' : 'display:none' }}">
                    {{ $designations[$code] ?? '' }}
                </span>
            @endforeach
        </td>
        <td>
            <span class="badge {{ $d->status == 1 ? 'badge-success' : 'badge-danger' }}">
                {{ $d->status == 1 ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
    <img class="rounded-circle" width="35" src="{{ $foundImage }}" alt="Instructor">
</td>
<td>
    <a href="{{ route('instructor.edit', $d->id) }}"
       class="btn btn-sm btn-primary" title="Edit">
        <i class="la la-pencil"></i>
    </a>

    <a href="javascript:void(0);" class="btn btn-sm btn-danger" title="Delete"
       onclick="$('#form{{ $d->id }}').submit()">
        <i class="la la-trash-o"></i>
    </a>
    <form id="form{{ $d->id }}"
          action="{{ route('instructor.destroy', $d->id) }}"
          method="POST" style="display:none;">
        @csrf
        @method('DELETE')
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
<script>
document.addEventListener('DOMContentLoaded',function(){
    if($('#instructorTable').length) $('#instructorTable').DataTable();

    const initialLocale = localStorage.getItem('instructors_lang') || '{{ $appLocale }}';
    function setActiveLocale(locale){
        document.querySelectorAll('.lang-tab').forEach(tab=>{ tab.classList.toggle('active',tab.dataset.locale===locale) });
        document.querySelectorAll('.lang-en,.lang-ru,.lang-ka').forEach(el=>{ el.style.display = el.classList.contains('lang-'+locale)?'':'none'; });
    }
    document.querySelectorAll('.lang-tab').forEach(tab=>tab.addEventListener('click',function(e){
        e.preventDefault(); const locale=this.dataset.locale;
        localStorage.setItem('instructors_lang',locale);
        setActiveLocale(locale);
    }));
    setActiveLocale(initialLocale);
});
</script>
@endpush
