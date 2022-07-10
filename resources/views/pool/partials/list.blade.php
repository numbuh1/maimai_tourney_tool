<div class="table-responsive">
    <table class="table table-bordered" id="pool-table" data-url="" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Show</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Show</th>
            <th>Action</th>
        </tr>
        </tfoot>
        <tbody>
            @foreach($pools as $pool)
                <tr>
                    <td>{{ $pool->name }}</td>
                    <td>
                        <a target="blank" href="{{ route('pool.show', ['id' => $pool->id]) }}" class="btn btn-sm btn-info">Show Pool</a>
                        @if($pool->allow_scores)
                            <a target="blank" href="{{ route('pool.showScores', ['id' => $pool->id]) }}" class="btn btn-sm btn-success">Show Scores</a>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('pool.edit', ['id' => $pool->id]) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger btnDeletePool" data-name="{{ $pool->name }}" data-id="{{ $pool->id }}" data-url="{{ route('pool.delete', ['id' => $pool->id]) }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>