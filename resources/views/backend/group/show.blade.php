@extends ('backend.layouts.app')

@section ('title', isset($repository->moduleTitle) ? 'View - '. $repository->moduleTitle : 'View')

@section('page-header')
    <h1>
        {{ isset($repository->moduleTitle) ? $repository->moduleTitle : '' }}
        <small>View</small>
    </h1>
@endsection

@section('content')
   <section class="content">

    <div class="row">
        <div class="col-md-3">

        <!-- Profile Image -->
        <div class="box box-primary">
        	<div class="box-body box-profile">
            	<img class="profile-user-img img-responsive img-circle" src="{{ url('/groups/'.$item->image) }}" alt="User profile picture">

            	<h3 class="profile-username text-center">
              		{{$item->name}}
              	</h3>

              	<p class="text-muted text-center">
             		{{$item->description}}
              	</p>
              	<p class="text-muted text-center">
             		<strong>{{$item->is_private ? 'Private' : 'Public'}}</strong>
              	</p>

              	<ul class="list-group list-group-unbordered">
	                <li class="list-group-item">
	               		<b>Group Leaders</b> <a class="pull-right">{{ count($item->getLeaders()) }} </a>
	                </li>
	                <li class="list-group-item">
	                  	<b>Group Members</b> <a class="pull-right">{{ count($item->get_only_group_members()) }} </a>
	                </li>
	                <li class="list-group-item">
	                  	<b>Group Channels</b> <a class="pull-right">{{ count($item->group_channels()) }} </a>
	                </li>
	                <li class="list-group-item">
	                  <b>Group Events </b> <a class="pull-right">{{ count($item->group_events()) }}</a>
	                </li>
	                <li class="list-group-item">
	                  <b>Group Feeds </b> <a class="pull-right"> {{ count($item->group_feeds()) }}  </a>
	                </li>
            	</ul>
            </div>
            <!-- /.box-body -->
          </div>
        <!-- /.box -->
    </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
            	<li class="active"><a href="#leaders" data-toggle="tab">Group Leaders</a></li>
              	<li><a href="#members" data-toggle="tab">Group Members</a></li>
              	<li><a href="#channels" data-toggle="tab">Channels</a></li>
              	<li><a href="#events" data-toggle="tab">Events</a></li>
              	<li><a href="#feeds" data-toggle="tab">Feeds</a></li>
            </ul>
            <div class="tab-content">
            	<div class="active tab-pane" id="leaders">
            		<table class="table table-hover">
						<thead>
					    	<tr>
					      		<th>#</th>
						      	<th>Name</th>
						      	<th>Username</th>
						      	<th>Email Id</th>
					    	</tr>
					  	</thead>
					  	<tbody>
					  		@php $sr = 1; @endphp
               				@foreach($item->getLeaders() as $groupMember)
               					<tr>
						      		<th scope="row">{{ $sr }} </th>
						      		<td>{{ $groupMember->name }}</td>
						      		<td>{{ $groupMember->username }}</td>
						      		<td>{{ $groupMember->email }}</td>
						    	</tr>
						    	@php $sr++; @endphp
               				@endforeach
					  	</tbody>
					</table>
            	</div>

            	<div class="tab-pane" id="members">
            		<table class="table table-hover">
						<thead>
					    	<tr>
					      		<th>#</th>
						      	<th>Name</th>
						      	<th>Username</th>
						      	<th>Email Id</th>
					    	</tr>
					  	</thead>
					  	<tbody>
					  		@php $sr = 1; @endphp
               				@foreach($item->get_only_group_members() as $groupMember)
               					<tr>
						      		<th scope="row">{{ $sr }} </th>
						      		<td>{{ $groupMember->name }}</td>
						      		<td>{{ $groupMember->username }}</td>
						      		<td>{{ $groupMember->email }}</td>
						    	</tr>
						    	@php $sr++; @endphp
               				@endforeach
					  	</tbody>
					</table>
            	</div>

            	<div class="tab-pane" id="channels">
            		<table class="table table-hover">
						<thead>
					    	<tr>
					      		<th>#</th>
						      	<th>Channel Name</th>
						      	<th>Channel Owner</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@php $sr = 1; @endphp
               				@foreach($item->group_channels as $groupChannel)
               					<tr>
						      		<th scope="row">{{ $sr }} </th>
						      		<td>{{ $groupChannel->name }}</td>
						      		<td>{{ $groupChannel->user->name }} ( {{ $groupChannel->user->email }} )</td>
						      	</tr>
						    	@php $sr++; @endphp
               				@endforeach
					  	</tbody>
					</table>
              	</div>
              
              	<div class="tab-pane" id="events">
               		<table class="table table-hover">
						<thead>
					    	<tr>
					      		<th>#</th>
						      	<th>Name</th>
						      	<th>Description</th>
						      	<th>Start Time</th>
						      	<th>End Time</th>
						      	<th>Creator</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@php $sr = 1; @endphp
               				@foreach($item->group_events as $groupEvent)
               					<tr>
						      		<th scope="row">{{ $sr }} </th>
						      		<td>{{ $groupEvent->name }}</td>
						      		<td>{{ $groupEvent->title }}</td>
						      		<td>{{ date('m-d-Y H:i A', strtotime($groupEvent->start_date)) }}</td>
						      		<td>{{ date('m-d-Y H:i A', strtotime($groupEvent->end_date)) }}</td>
						      		<td>{{ $groupEvent->user->name }} ( {{ $groupEvent->user->email }} )</td>
						      	</tr>
						    	@php $sr++; @endphp
               				@endforeach
					  	</tbody>
					</table>
              	</div>

              	<div class="tab-pane" id="feeds">
              		<table class="table table-hover">
						<thead>
					    	<tr>
					      		<th>#</th>
						      	<th>Description</th>
						      	<th>Creator</th>
						      	<th>Attachment</th>
						      	<th>Attached File</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@php $sr = 1; @endphp
               				@foreach($item->group_feeds() as $groupFeed)

               					@php
               						$feedAttachment = '-';

               						if(isset($groupFeed->is_attachment) && $groupFeed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$groupFeed->user_id.'/'.$groupFeed->attachment))
               						{
               						    $feedAttachment = '<a target="_blank" class="btn btn-success" href="'.url('/feeds/'.$groupFeed->user_id.'/'.$groupFeed->attachment).'">Download</a>';
               						}
               					@endphp
               					<tr>
						      		<th scope="row">{{ $sr }} </th>
						      		<td>{{ $groupFeed->description }}</td>
						      		<td>{{ $groupFeed->user->name }} ( {{ $groupFeed->user->email }} )</td>
						      		<td>{{ $groupFeed->is_attachment ? 'Yes' : 'No' }}</td>
						      		<td>{!! $feedAttachment !!}</td>
						      	</tr>
						    	@php $sr++; @endphp
               				@endforeach
					  	</tbody>
					</table>
              	</div>

              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
@endsection
