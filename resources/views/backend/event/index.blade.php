@extends ('backend.layouts.app')

@section ('title', 'Event Management')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@endsection

@section('page-header')
    <h1>Event Management</h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Events Listing</h3>

            <div class="box-tools pull-right">
                @include('common.event.index-header-buttons', ['createRoute' => 'admin.event.create'])
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table id="items-table" class="table table-condensed table-hover">
                    <thead>
                        <tr id="tableHeadersContainer"></tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">History</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            {!! history()->renderType('Event') !!}
        </div>
    </div>
@endsection

@section('after-scripts')
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}

    <script>

        var headers      = JSON.parse('{!! $repository->getTableHeaders() !!}'),
            columns      = JSON.parse('{!! $repository->getTableColumns() !!}');
            moduleConfig = {
                getTableDataUrl: '{!! route("admin.event.get-event-data") !!}'
            };

        jQuery(document).ready(function()
        {
            BaseCommon.Utils.setTableHeaders(document.getElementById("tableHeadersContainer"), headers);
            BaseCommon.Utils.setTableColumns(document.getElementById("items-table"), moduleConfig.getTableDataUrl, 'GET', columns);
    		
    	});
    </script>
@endsection