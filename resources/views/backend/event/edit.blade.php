@extends ('backend.layouts.app')

@section ('title', isset($title) ? $title : 'Edit Event')

@section('page-header')
    <h1>
        Events
        <small>Edit</small>
    </h1>
@endsection

@section('content')
    {{ Form::model($item, ['route' => ['admin.event.update', $item], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) }}

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Event</h3>
                    <div class="box-tools pull-right">
                        @include('common.event.event-header-buttons', ['listRoute' => 'admin.event.index', 'createRoute' => 'admin.event.create'])
                    </div>
            </div>

            {{-- Event Form --}}
            @include('common.event.form')
            
        </div>

        <div class="box box-success">
            <div class="box-body">
                <div class="pull-left">
                    {{ link_to_route('admin.event.index', 'Cancel', [], ['class' => 'btn btn-danger btn-xs']) }}
                </div><!--pull-left-->

                <div class="pull-right">
                    {{ Form::submit('Update', ['class' => 'btn btn-success btn-xs']) }}
                </div><!--pull-right-->

                <div class="clearfix"></div>
            </div><!-- /.box-body -->
        </div><!--box-->

        

    {{ Form::close() }}
@endsection

@section('after-scripts')
    {{ Html::script('js/backend/access/users/script.js') }}
@endsection
