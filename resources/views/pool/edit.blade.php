@extends('adminlte::page')

@section('title', 'Map Pool Edit')

@section('content_header')
    <!-- <h1>Songs</h1> -->
@stop

@section('content')
    <div id="container-user">
		@if (isset($pool) && $pool->id)
            <form id="frmPool" method="POST" action="{{ route('pool.update', ['id' => $pool->id]) }}" enctype="multipart/form-data">
            	<input type="hidden" id="urlPoolUpdate" value="{{ route('pool.update', ['id' => $pool->id]) }}">
            	<input type="hidden" id="txtMapPoolId" value="{{ $pool->id }}">
                @method('PUT')
        @else
            <form id="frmPool" method="POST" action="{{ route('pool.store') }}">
                @method('POST')
        @endif
        	<input type="hidden" id="txtMapPoolLock" value="{{ $pool->is_locked }}">
		    <div class="row">
		        <div class="col-md-6">
		            <div class="form-group">
		                <label>Name</label>
		                <div class="input-group">
			                <input type="text" class="form-control submit-pool @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name',$pool->name) }}">
			                <!-- <div class="input-group-append">
								<a href="#" class="input-group-text">Save</a>
							</div> -->
						</div>
		                <input type="hidden" name="pool_id" id="pool_id" value="{{ old('pool_id',$pool->id) }}">
		            </div>
		        </div>
		    </div>
		    <div class="row">
		    	<div class="col-md-6">
		            <div class="form-group">
		                <label>Players</label>
		                <div class="input-group">
		                	<select id="players" class="form-control submit-pool selectpicker" data-actions-box="true" name="players" multiple @if($pool->is_locked == 1) disabled @endif>
		                		@foreach($players as $player)
		                			<option value="{{ $player->id }}" @if(in_array($player->id, $pool_player_ids)) selected @endif>{{ $player->name }}</option>
		                		@endforeach
		                	</select>
						</div>
		            </div>
		        </div>
		    </div>
		    <hr>
		    <div id="songList" class="row">
		        <div class="col-md-12">
		            <div class="form-group">
		            	 <div class="row">
		    				<div class="col-md-12">
		                		<label>Songs</label>
		                		
		                		<a id="btnRefreshPool" href="#" class="mr-2 mb-2 float-right btn btn-sm btn-success" data-url="{{ route('pool.refresh') }}">Refresh</a>
				                <a id="btnHideList" href="#" class="mr-2 mb-2 float-right btn btn-sm btn-danger">Hide List</a>
				                <a id="btnShowList" href="#" class="mr-2 mb-2 float-right btn btn-sm btn-info">Show List</a>
				            </div>
				        </div>
		                <table id="tblPool" class="table table-bordered" data-url="{{ route('pool.items', ['id' => $pool->id]) }}">
		                	<thead>
		                		<tr>
		                			<th>Order</th>
		                			<th>Song</th>
		                			<th>Song Action</th>
		                			<th>Slot Action</th>
		                		</tr>
		                	</thead>
		                	<tbody>
		                	</tbody>
		                </table>
		            </div>
		        </div>
		    </div>
		    <hr>
		    <div class="row">
		    	<div class="col-md-12">
		    		@if($pool->is_locked == 0)
	                	<a id="btnAddSong" href="#" class="float-right btn btn-primary">Add Song</a>
	                	<a id="btnLockPool" href="#" class="mr-2 float-right btn btn-danger" data-type="lock" data-url="{{ route('pool.lock', ['id' => $pool->id]) }}">Lock Pool</a>
	                	<a id="btnRandomList" href="#" class="mr-2 float-left btn btn-success" data-type="random" data-url="{{ route('pool.random', ['id' => $pool->id]) }}">Random List</a>
		    		@endif
	            </div>
            </div>
            @if($pool->is_locked)
            	<div id="scoreList" class="row">
			        <div class="col-md-12">
			            <div class="form-group">
			            	<div class="row">
			    				<div class="col-md-12">
			                		<label>Scores</label>
			                		<a id="btnRefreshScore" href="#" class="mr-2 mb-2 float-right btn btn-sm btn-success" data-url="{{ route('score.refresh') }}">Show In Stream</a>
					            </div>
					        </div>
			                <table id="tblScore" class="table table-bordered" >
			                	<thead>
			                		<tr>
			                			<th>Player</th>
			                			@foreach($map_pool_songs as $song)
			                				<th class="text-center">
			                					<!-- <img width="100" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/{{ $song->imageName }}"> -->
			                					{{ $song->title }} <span class="badge badge-{{ $song->difficulty }}">{{ $song->difficulty }}</span>
			                				</th>
			                			@endforeach
			                			<th>Total</th>
			                			<th>Rank</th>
			                		</tr>
			                	</thead>
			                	<tbody>
			                		@foreach($pool_players as $player)
			                			<tr>
			                				<td>{{ $player->name }}</td>
			                			@foreach($pool_items as $item_id => $item)
			                				<td>
			                					<b>Score: </b> {{ $scores[$player->id][$item_id]->achievement_score ?? '-' }}<br/>
			                					<b>DX: </b> {{ $scores[$player->id][$item_id]->dx_score ?? '-' }}<br/>
			                					<b>Image: </b>
			                						@if(isset($scores[$player->id][$item_id]->photo_path))
			                							<a href="#" class="showImage" data-url="{{ '/uploads/'. $scores[$player->id][$item_id]->photo_path }}">Link</a>
			                						@endif
			                					<br/>
			                					<a id="btnAddScore" href="{{ route('score.edit', ['item_id' => $item->id, 'player_id' => $player->id]) }}" class="mr-2 mb-2 float-right btn btn-sm btn-info">Edit</a>
			                				</td>
			                			@endforeach
			                				<td>
			                					<b>Score: </b> {{ $achievement_scores[$player->id] }}<br>
			                					<b>DX: </b> {{ $dx_scores[$player->id] }}<br>
			                				</td>
			                				<td>{{ $ranking[$player->id] + 1 }}</td>
			                			</tr>
			                		@endforeach
			                	</tbody>
			                </table>
			                <!-- <a href="{{ route('pool.showScores', ['id' => $pool->id]) }}" class="btn btn-primary btnShowScores" data-url="{{ route('pool.showScores', ['id' => $pool->id]) }}">Show Scores</a> -->
			            </div>
			        </div>
			    </div>
            @endif
    	</form>
	</div>
	@include('pool.partials.image_modal')
	@include('song.partials.select_modal')
	@include('song.partials.search_modal')
@stop

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}" >
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="/vendor/select2/js/select2.full.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
	<script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/pool/edit.js') }}" defer></script>
@stop

@section('plugins.select2', true)