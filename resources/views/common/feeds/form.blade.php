<div class="box-body">
    <div class="form-group">
        {{ Form::label('description', 'Feed Description :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 8, 'cols' => 4, 'required' => 'required']) }}
        </div>
    </div>
</div>


<div class="box-body">
    <div class="form-group">
        {{ Form::label('image', 'Attachment :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::file('attachment', ['class' => 'form-control']) }}
        </div>
    </div>
</div>

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
        {{ Form::label('interests', 'Select Interest Campus :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::select('interests[]', $interestRepository->getSelectOptions('id', 'name') , null, ['class' => 'form-control', 'required', 'multiple']) }}
        </div>
    </div>
</div>