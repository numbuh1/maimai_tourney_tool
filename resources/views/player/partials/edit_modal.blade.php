<!-- User Modal-->
<div class="modal fade" id="playerModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create/Edit Player</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="playerEditId" value="">
                    <div class="form-group col-md-12 col-12">
                        <label>Name</label>
                        <input type="text" id="txtPlayerName" class="form-control">
                    </div>                    
                </div>
                <div class="row">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="chkEliminated">
                      <label class="form-check-label" for="flexCheckDefault">
                        Is eliminated
                      </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="btnSubmitPlayer" data-url="{{ route('player.store') }}">Submit</a>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>