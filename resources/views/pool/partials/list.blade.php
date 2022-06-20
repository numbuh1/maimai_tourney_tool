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
            @foreach($pools as $pool)
                <tr>
                    <td>{{ $pool->name }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-success">Show</a>
                        <a href="{{ route('pool.edit', ['id' => $pool->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>