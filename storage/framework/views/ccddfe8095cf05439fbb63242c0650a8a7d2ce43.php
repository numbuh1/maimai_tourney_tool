<div id="songFilter" class="d-flex justify-content-start mb-2">
    <select id="selectCategory" class="mr-2" >
        <option value="All" >All Categories</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>"><?php echo e($category); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select id="selectVersion" class="mr-2">
        <option value="All" >All Versions</option>
        <?php $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>"><?php echo e($version); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div class="table-responsive">
    <table class="table table-bordered" id="song-table" data-url="<?php echo e($song_list_url); ?>" width="100%" cellspacing="0">
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
</div><?php /**PATH /var/www/resources/views/song/partials/list.blade.php ENDPATH**/ ?>