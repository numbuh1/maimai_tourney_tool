@extends('adminlte::page')

@section('title', 'Songs')

@section('content_header')
    <!-- <h1>Songs</h1> -->
@stop

@section('content')
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Songs</h6>
		    </div>
		    <div class="card-body">
	    		@include('song.partials.list')
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
    <script src="{{ mix('js/song/index.js') }}" defer></script>
@stop