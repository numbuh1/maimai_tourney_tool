<!-- <div id="songFilter" class="d-flex justify-content-start mb-2">
    <select id="selectCategory" class="mr-2" >
        <option value="All" >All Categories</option>
        @foreach ($categories as $key => $category)
            <option value="{{$key}}">{{$category}}</option>
        @endforeach
    </select>
    <select id="selectVersion" class="mr-2">
        <option value="All" >All Versions</option>
        @foreach ($versions as $key => $version)
            <option value="{{$key}}">{{$version}}</option>
        @endforeach
    </select>
</div> -->
<div class="table-responsive">
    <table class="table table-bordered" id="song-table" data-url="{{ $song_list_url }}" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th></th>
            <th>Cover</th>
            <th>Title</th>
            <th>Artist</th>
            <th>Category</th>
            <th>Version</th>
            <th>BPM</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th></th>
            <th>Cover</th>
            <th>Title</th>
            <th>Artist</th>
            <th>Category</th>
            <th>Version</th>
            <th>BPM</th>
        </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>