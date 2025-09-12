@extends('backend.layouts.app')
@section('title', 'Event List')

@push('styles')
<!-- Datatable -->
<link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
@endpush

@section('content')

<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Event List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('event.index')}}">Events</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('event.index')}}">All Event</a></li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $currentLocale = request('lang', app()->getLocale());
        @endphp

        {{-- Таб для выбора языка --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="langTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item">
                            <a href="{{ route('event.index', ['lang' => $localeCode]) }}"
                               class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}">
                               {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Events List - {{ $locales[$currentLocale] ?? 'English' }}</h4>
                        <a href="{{ route('event.create', ['lang' => $currentLocale]) }}" class="btn btn-primary">+ Add new</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>{{__('Image')}}</th>
                                        <th>{{__('Event Title')}}</th>
                                        <th>{{__('Topic')}}</th>
                                        <th>{{__('Location')}}</th>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($events as $event)
                                    <tr>
                                        <td>
                                            @if($event->image)
                                                <img src="{{ asset('uploads/events/'.$event->image) }}"
                                                     class="img-fluid" width="50" height="50" style="object-fit: cover;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $event->title }}</strong>
                                            @if($event->relationLoaded('translations') && !$event->translations->where('locale', $currentLocale)->first())
                                                <br><small class="text-warning">No {{ $locales[$currentLocale] }} translation</small>
                                            @endif
                                        </td>
                                        <td>{{ $event->topic ?? '-' }}</td>
                                        <td>{{ $event->location ?? '-' }}</td>
                                        <td>{{ $event->date ? \Carbon\Carbon::parse($event->date)->format('j F, Y') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('event.edit', ['event' => $event->id, 'lang' => $currentLocale]) }}"
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="la la-pencil"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                               title="Delete" onclick="if(confirm('Are you sure?')) $('#form{{$event->id}}').submit()">
                                                <i class="la la-trash-o"></i>
                                            </a>
                                            <form id="form{{$event->id}}"
                                                  action="{{ route('event.destroy', $event->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Events Found</td>
                                    </tr>
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
<!--**********************************
    Content body end
***********************************-->

@endsection

@push('scripts')
<!-- Datatable -->
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/plugins-init/datatables.init.js')}}"></script>

<script>
    // Подсветка активного языка
    document.addEventListener('DOMContentLoaded', function() {
        const currentLang = '{{ $currentLocale }}';
        const langLinks = document.querySelectorAll('#langTabs .nav-link');

        langLinks.forEach(link => {
            if (link.getAttribute('href').includes('lang=' + currentLang)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
@endpush
