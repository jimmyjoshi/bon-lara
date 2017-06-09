<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'Interest Name :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Interest Name', 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('image', 'Upload Image :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::file('image', ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>

@if(isset($item) && $item->image && file_exists(base_path() . '/public/interests/'.$item->image))
<div class="box-body">
    <div class="form-group">
        <div class="col-lg-10 text-center">
            {{ Html::image('/interests/'.$item->image, $item->name, ['width' => 120, 'height' => 120]) }}
        </div>
    </div>
</div>
@endif
