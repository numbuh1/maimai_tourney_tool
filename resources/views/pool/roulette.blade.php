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
		    	<a href="#" id="btnRoulette" data-id="{{ $key_id }}">Click</a>
		    	<div class="roulette" style="display:none;" data-id="{{ $key_id }}">
		    		@foreach($songs as $songImg)
						<img src="{{ url($songImg) }}"/>
					@endforeach
				</div> 
		        
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
	<script type="text/javascript" charset="utf8" src="/js/roulette.min.js"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/pool/edit.js') }}" defer></script>
@stop