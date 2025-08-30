@extends('backend.layouts.app')
@section('title', 'Course Material List')

@push('styles')
<link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course Material List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{localeRoute('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{localeRoute('material.index')}}">Course Materials</a></li>
                    <li class="breadcrumb-item active">All Course Material</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item"><a href="#list-view" data-toggle="tab"
                            class="nav-link btn-primary mr-1 show active">List View</a></li>
                    <li class="nav-item"><a href="javascript:void(0);" data-toggle="tab"
                            class="nav-link btn-primary">Grid View</a></li>
                </ul>
            </div>
            <div class="col-lg-12">
                <div class="row tab-content">
                    <div id="list-view" class="tab-pane fade active show col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">All Course Materials List</h4>
                                <a href="{{localeRoute('material.create')}}" class="btn btn-primary">+ Add new</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Lesson</th>
                                                <th>Material Type</th>
                                                <th>Video / Document</th>
                                                <th>Text Content</th>
                                                <th>Content URL</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($materials as $m)
                                            <tr>
                                                <td>{{$m->id}}</td>
                                                <td>{{$m->title}}</td>
                                                <td>{{$m->lesson?->displayTitle()}}</td>
                                                <td>
                                                    {{ ucfirst($m->type) }}
                                                </td>
                                                <td>
                                                    @if($m->type === 'video' || $m->type === 'document')
                                                        <embed src="{{ asset('uploads/courses/contents/'.$m->content) }}" width="200px" height="100px" />
                                                    @endif
                                                </td>
                                                <td>{{ $m->content_text }}</td>
                                                <td>{{$m->content_url}}</td>
                                                <td>
                                                    <a href="{{localeRoute('material.edit', encryptor('encrypt',$m->id))}}"
                                                        class="btn btn-sm btn-primary" title="Edit"><i class="la la-pencil"></i></a>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                                        title="Delete" onclick="$('#form{{$m->id}}').submit()"><i class="la la-trash-o"></i></a>
                                                    <form id="form{{$m->id}}"
                                                        action="{{localeRoute('material.destroy', encryptor('encrypt',$m->id))}}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No Course Material Found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    {{ $materials->links() }} <!-- Пагинация -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/plugins-init/datatables.init.js')}}"></script>
@endpush
