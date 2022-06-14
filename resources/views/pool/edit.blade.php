@extends('adminlte::page')

@section('title', 'Map Pool Edit')

@section('content_header')
    <!-- <h1>Songs</h1> -->
@stop

@section('content')
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Map Pool</h6>
		    </div>
		    <div class="card-body">
	    		@if (isset($pool) && $pool->id)
                    <form id="frmPool" method="POST" action="{{ route('pool.update', ['id' => $pool->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                @else
                    <form id="frmPool" method="POST" action="{{ route('pool.store') }}">
                        @method('POST')
                @endif
				    <div class="row">
				        <div class="col-md-6">
				            <div class="form-group">
				                <label>Name</label>
				                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"  
				                name="name" value="{{ old('name',$pool->name) }}">
				                <input type="hidden" name="pool_id" id="pool_id" value="{{ old('pool_id',$pool->id) }}">
				                @error('name')
				                        <span class="invalid-feedback" role="alert">
				                            <strong>{{ $message }}</strong>
				                        </span>
				                @enderror
				            </div>
				        </div>
				    </div>
				    <div class="row">
				        <div class="col-md-12">
				            <div class="form-group">
				                <label>Songs</label>
				                <table class="table table-bordered">
				                	<thead>
				                		<tr>
				                			<th>Song</th>
				                			<th>Song Action</th>
				                			<th>Slot Action</th>
				                		</tr>
				                	</thead>
				                	<tbody id="songList">
				                	@foreach($pool_items as $items)
				                		<tr>
				                			<td>
				                				@if($item->song_id)
				                					<img src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/{{ $jacket[$song_id] ?? '' }}" height="100">
				                				@endif
				                			</td>
				                			<td>
				                				<a href="btn btn-info">Select Song</a>
				                				<a href="btn btn-primary">Random Song</a>
				                				<a href="btn btn-danger">Remove Song</a>
				                			</td>
				                			<td>
				                				<a href="btn btn-danger">Remove Slot</a>
				                			</td>
				                		</tr>
				                	@endforeach
				                	</tbody>
				                </table>
				                <a id="btnAddSong" href="#" class="float-right btn btn-primary">Add Song</a>
				                <a id="btnShowList" href="#" class="mr-2 float-left btn btn-danger">Hide List</a>
				                <a id="btnHideList" href="#" class="mr-2 float-left btn btn-info">Show List</a>
				            </div>
				        </div>
				    </div>
            	</form>
	    	</div>
	    </div>
	</div>

	@include('song.partials.select_modal')
	@include('song.partials.search_modal')
@stop

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}" >
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="/vendor/select2/css/select2.min.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="/vendor/select2/js/select2.full.min.js"></script>
	<script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/pool/edit.js') }}" defer></script>
@stop

@section('plugins.select2', true)