<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'Campus Name :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Campus Name', 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('campus_code', 'Campus Code :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('campus_code', null, ['class' => 'form-control', 'placeholder' => 'Campus Code' , 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('valid_domain', 'Valid Domain :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('valid_domain', null, ['class' => 'form-control', 'placeholder' => 'Valid Domain' , 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('contact_person_name', 'Contact Person Name :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('contact_person_name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name' , 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('contact_number', 'Contact Number :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('contact_number', null, ['class' => 'form-control', 'placeholder' => 'Contact Number' , 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('email_id', 'Email Id :', ['class' => 'col-lg-2 control-label']) }}

        <div class="col-lg-10">
            {{ Form::text('email_id', null, ['class' => 'form-control', 'placeholder' => 'Email Id' , 'required' => 'required']) }}
        </div>
    </div>
</div>