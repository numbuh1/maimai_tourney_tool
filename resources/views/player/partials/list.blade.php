<div class="table-responsive">
    <table class="table table-bordered" id="pool-table" data-url="" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Eliminated</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Eliminated</th>
            <th>Action</th>
        </tr>
        </tfoot>
        <tbody>
            @foreach($players as $player)
                <tr>
                    <td>{{ $player->name }}</td>
                    <td>
                        @if($player->is_eliminated)
                            <span class="badge badge-danger">True</span>
                        @else
                            <span class="badge badge-success">False</span>
                        @endif
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-secondary btnEditPlayer" data-name="{{ $player->name }}" data-id="{{ $player->id }}" data-elim="{{ $player->is_eliminated }}">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger btnDeletePlayer" data-name="{{ $player->name }}" data-id="{{ $player->id }}" data-url="{{ route('player.delete', ['id' => $player->id]) }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>