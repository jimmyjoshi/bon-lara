@extends ('backend.layouts.app')

@section ('title',  isset($repository->moduleTitle) ? $repository->moduleTitle. ' Management' : 'Management')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@endsection

@section('page-header')
    <h1>{{ isset($repository->moduleTitle) ? $repository->moduleTitle : '' }} Management</h1>
@endsection

@section('content')
    <style>
       .modal-backdrop.in { z-index: auto;}

    </style>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ isset($repository->moduleTitle) ? str_plural($repository->moduleTitle) : '' }} Listing</h3>

            <div class="box-tools pull-right">
                @include('common.event.index-header-buttons', ['createRoute' => $repository->getActionRoute('createRoute')])
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
            {!! history()->renderType('Group') !!}
        </div>
    </div>

<!-- The Modal -->
<div id="groupMembers" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">View Group Members</h4>
        </div>
        <div class="modal-body" id="modalBoxBody">
            <p><center><strong> No group Members Found !</strong></center></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
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
                getTableDataUrl: '{!! route($repository->getActionRoute("dataRoute")) !!}'
            };

        jQuery(document).ready(function()
        {
            BaseCommon.Utils.setTableHeaders(document.getElementById("tableHeadersContainer"), headers);
            BaseCommon.Utils.setTableColumns(document.getElementById("items-table"), moduleConfig.getTableDataUrl, 'GET', columns);

            jQuery(document).on('click', '.group-members', function()
            {
                showGroupMembers(this);

                jQuery('#groupMembers').modal('show');
            });
    	});

    function showGroupMembers(element)
    {
        if(typeof jQuery(element).attr('data-group-id') == 'undefined' || jQuery(element).attr('data-group-id') == '')
        {
            return;
        }

        jQuery('#modalBoxBody').html('');

        var url = '{!! route('admin.group.get-group-members') !!}';

        jQuery.ajax(
        {
            url:        url,
            method:     'GET',
            dataType:   'JSON',
            data: {
                'groupId': jQuery(element).attr('data-group-id')
            },
            success: function(data)
            {
                if(data.status == true)
                {
                    var html = '';
                    
                    html += '<table align="center" class="table table-hover">';
                        
                        html += '<tr>';

                            html += '<td> Sr </td>';

                            html += '<td> Name </td>';

                            html += '<td> Username </td>';

                            html += '<td> Email Id </td>';

                        html += '</tr>';

                        for(var i = 0; i < data.members.length; i++)
                        {
                            html += '<tr>';

                                html += '<td> '+ (parseInt(i) + 1) +' </td>';

                                html += '<td> ' + data.members[i].name  + ' </td>';

                                html += '<td> ' + data.members[i].username  + ' </td>';

                                html += '<td> ' + data.members[i].email  + ' </td>';

                            html += '</tr>';
                        }

                    html += '</table>';

                    jQuery('#modalBoxBody').html(html);
                }
                else
                {
                    jQuery('#modalBoxBody').html('<p><center><strong> No Group Members Found !</strong></center></p>');    
                }
            
            },
            error: function(data)
            {
                jQuery('#modalBoxBody').html('<p><center><strong> Somethin went Wrong !</strong></center></p>');    
            }
        });
    }

    </script>
@endsection