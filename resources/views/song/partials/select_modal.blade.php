<!-- Select Modal-->
<div class="modal fade" id="confirmSelectSongModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 18rem;">
        <div class="modal-content" style="background: none; box-shadow: none; border: none">
            <div class="modal-body">
                <div class="row">
                    <div class="card" style="width: 18rem;">
                        <img id="songImage" class="card-img-top" src="#">
                        <div class="card-body">
                            <h5 id="songName"><b>Card title</b></h5>
                            <p id="songArtist" class="card-text">Some quick example text </p>
                            <hr>
                            <a id="btnConfirmSelectSong" data-id="" data-action="{{ route('pool.storeItems', ['id' => $pool->id]) }}" href="#" class="btn btn-info">Confirm</a>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>