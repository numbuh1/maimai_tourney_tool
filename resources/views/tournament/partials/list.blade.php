<div class="table-responsive">
    <table class="table table-bordered" id="pool-table" data-url="" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </tfoot>
        <tbody>
            @foreach($tourneys as $tourney)
                <tr>
                    <td><a href="{{ route('pool.index', ['tourney_id' => $tourney->id]) }}">{{ $tourney->name }}</a></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-secondary btnEditTourney" data-name="{{ $tourney->name }}" data-id="{{ $tourney->id }}" data-elim="{{ $tourney->is_eliminated }}">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger btnDeleteTourney" data-name="{{ $tourney->name }}" data-id="{{ $tourney->id }}" data-url="{{ route('tournament.delete', ['id' => $tourney->id]) }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>