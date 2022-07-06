@extends('adminlte::page')

@section('title', 'Score Submit')

@section('content_header')
    <!-- <h1>Songs</h1> -->
@stop

@section('content')
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Score Submit</h6>
		    </div>		    
	    	@if (isset($score) && $score->id)
                <form id="frmScore" method="POST" action="{{ route('score.update', ['item_id' => $poolItem->id, 'player_id' => $player->id, 'score_id' => $score->id]) }}" enctype="multipart/form-data">
                	<input type="hidden" id="txtMapscoreId" value="{{ $score->id }}">
                    @method('POST')
            @else
                <form id="frmscore" method="POST" action="{{ route('score.store', ['item_id' => $poolItem->id, 'player_id' => $player->id]) }}" enctype="multipart/form-data">
                    @method('POST')
            @endif
            @csrf
		    <div class="card-body">
            	<div class="row">
			        <div class="col-md-6 col-12">
			            <div class="form-group">
			                <label>Pool</label>
			                <div class="input-group">
				                <input type="text" class="form-control" id="poolName" name="poolName" value="{{ $pool->name }}" readonly>
				                <input type="hidden" class="form-control" name="map_pool_id" value="{{ $pool->id }}" readonly>
				                <input type="hidden" class="form-control" name="map_pool_item_id" value="{{ $poolItem->id }}" readonly>
							</div>
			            </div>
			        </div>
			    </div>
			    <div class="row">
			        <div class="col-md-6 col-12">
			            <div class="form-group">
			                <label>Player</label>
			                <div class="input-group">
				                <input type="text" class="form-control" id="playerName" name="playerName" value="{{ $player->name }}" readonly>
				                <input type="hidden" class="form-control" name="player_id" value="{{ $player->id }}" readonly>
							</div>
			            </div>
			        </div>
			        <div class="col-md-6 col-12">
			            <div class="form-group">
			                <label>Chart</label>
			                <div class="input-group">
				                <input type="text" class="form-control" id="chart" name="chart" value="{{ $song->title }} - [{{ $chart->difficulty }}]" readonly>
				                <input type="hidden" class="form-control" name="chart_id" value="{{ $chart->id }}" readonly>
							</div>
			            </div>
			        </div>
			    </div>				    
            	<div class="row">
			        <div class="col-md-6 col-6">
			            <div class="form-group">
			                <label>Achievement Score</label>
			                <div class="input-group">
				                <input type="text" class="form-control" id="achievement_score" name="achievement_score" value="{{ $score->achievement_score ?? '' }}">
							</div>
			            </div>
			        </div>
			        <div class="col-md-6 col-6">
			            <div class="form-group">
			                <label>DX Score</label>
			                <div class="input-group">
				                <input type="text" class="form-control" id="dx_score" name="dx_score" value="{{ $score->dx_score ?? '' }}">
							</div>
			            </div>
			        </div>
			        <div class="col-md-6 col-6">
			            <div class="form-group">
			                <label>Photo</label>
			                <div class="input-group">
			                	<input type="file" id="photo_path" name="photo_path" onchange="document.getElementById('currentPhoto').src = window.URL.createObjectURL(this.files[0])">
							</div>
			            </div>
			        </div>
			        <div class="col-md-6 col-6">
			            <div class="form-group">
			                <label>Current Photo</label>
			                <div class="input-group">
			                	<img id="currentPhoto" width="100%" src="{{ isset($score->photo_path) ? '/uploads/'. $score->photo_path : '' }}">
							</div>
			            </div>
			        </div>
			    </div>
	    	</div>
	    	<div class="card-footer">
		        <input type="Submit" class="btn btn-primary float-left" value="Save">
		    </div>
            </form>
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