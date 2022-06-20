@extends('adminlte::page')

@section('title', 'Pools')

@section('content_header')
    <!-- <h1>Songs</h1> -->
@stop

@section('content')
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Map Pools</h6>
		    </div>
		    <div class="card-body">

		        <div class="d-sm-flex mb-4 flex-row-reverse">
		            <a href="{{ route('pool.add') }}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm ml-3">
		                <i class="fas fa-plus fa-sm text-white-50"></i> Create</a>
		        </div>
	    		@include('pool.partials.list')
	    	</div>
	    </div>
	</div>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
@stop