@extends ('backend.layouts.app')

@section ('title', isset($title) ? $title : 'Create Event')

@section('page-header')
    <h1>
        Event
        <small>Create</small>
    </h1>
@endsection

@section('content')
    {{ Form::open(['route' => 'admin.event.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) }}

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Create Event</h3>

                <div class="box-tools pull-right">
                      @include('common.event.event-header-buttons', ['listRoute' => 'admin.event.index', 'createRoute' => 'admin.event.create'])
                </div>
            </div>

            {{-- Event Form --}}
            @include('common.event.form')
            
        </div>

        <div class="box box-info">
            <div class="box-body">
                <div class="pull-left">
                    {{ link_to_route('admin.event.index', 'Cancel', [], ['class' => 'btn btn-danger btn-xs']) }}
                </div>

                <div class="pull-right">
                    {{ Form::submit('Create', ['class' => 'btn btn-success btn-xs']) }}
                </div>

                <div class="clearfix"></div>
            </div>
        </div>

    {{ Form::close() }}
@endsection
