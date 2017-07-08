<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'Group Name :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Group Name', 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('description', 'Group Description :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 8, 'cols' => 4, 'required' => 'required']) }}
        </div>
    </div>
</div>


<div class="box-body">
    <div class="form-group">
        {{ Form::label('image', 'Upload Image :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::file('image', ['class' => 'form-control']) }}
        </div>
    </div>
</div>

@if(isset($item) && $item->image && file_exists(base_path() . '/public/groups/'.$item->image))
<div class="box-body">
    <div class="form-group">
        <div class="col-lg-10 text-center">
            {{ Html::image('/groups/'.$item->image, $item->name, ['width' => 120, 'height' => 120]) }}
        </div>
    </div>
</div>
@endif

<div class="box-body">
    <div class="form-group">
        {{ Form::label('campus_id', 'Choose Campus :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::select('campus_id', $campusRepository->getSelectOptions('id', 'name') , null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>



<div class="box-body">
    <div class="form-group">
        {{ Form::label('is_private', 'Private Group :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            <label>{{ Form::radio('is_private', 1, true, ['class' => 'field']) }} Public</label>
            <label>{{ Form::radio('is_private', 0, false, ['class' => 'field']) }} Private</label>
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('group_type', 'Group Type:', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            <label>{{ Form::radio('group_type', 1, true, ['class' => 'field']) }} Regular</label>
            <label>{{ Form::radio('group_type', 0, false, ['class' => 'field']) }} Discovery</label>
        </div>
    </div>
</div>
