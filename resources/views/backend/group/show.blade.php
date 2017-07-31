@extends ('backend.layouts.app')

@section ('title', isset($repository->moduleTitle) ? 'View - '. $repository->moduleTitle : 'View')

@section('page-header')
    <h1>
        {{ isset($repository->moduleTitle) ? $repository->moduleTitle : '' }}
        <small>Create</small>
    </h1>
@endsection

@section('content')
   
@endsection
