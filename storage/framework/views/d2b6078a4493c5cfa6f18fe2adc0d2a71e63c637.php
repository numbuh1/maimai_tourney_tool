<?php $__env->startSection('title', 'Map Pool Edit'); ?>

<?php $__env->startSection('content_header'); ?>
    <!-- <h1>Songs</h1> -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Map Pool</h6>
		    </div>
		    <div class="card-body">
	    		<?php if(isset($pool) && $pool->id): ?>
                    <form id="frmPool" method="POST" action="<?php echo e(route('pool.update', ['id' => $pool->id])); ?>" enctype="multipart/form-data">
                        <?php echo method_field('PUT'); ?>
                <?php else: ?>
                    <form id="frmPool" method="POST" action="<?php echo e(route('pool.store')); ?>">
                        <?php echo method_field('POST'); ?>
                <?php endif; ?>
				    <div class="row">
				        <div class="col-md-6">
				            <div class="form-group">
				                <label>Name</label>
				                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name"  
				                name="name" value="<?php echo e(old('name',$pool->name)); ?>">
				                <input type="hidden" name="pool_id" id="pool_id" value="<?php echo e(old('pool_id',$pool->id)); ?>">
				                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
				                        <span class="invalid-feedback" role="alert">
				                            <strong><?php echo e($message); ?></strong>
				                        </span>
				                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
				            </div>
				        </div>
				    </div>
				    <div class="row">
				        <div class="col-md-12">
				            <div class="form-group">
				                <label>Songs</label>
				                <table class="table table-bordered">
				                	<thead>
				                		<tr>
				                			<th>Song</th>
				                			<th>Song Action</th>
				                			<th>Slot Action</th>
				                		</tr>
				                	</thead>
				                	<tbody id="songList">
				                	<?php $__currentLoopData = $pool_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				                		<tr>
				                			<td>
				                				<?php if($item->song_id): ?>
				                					<img src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/<?php echo e($jacket[$song_id] ?? ''); ?>" height="100">
				                				<?php endif; ?>
				                			</td>
				                			<td>
				                				<a href="btn btn-info">Select Song</a>
				                				<a href="btn btn-primary">Random Song</a>
				                				<a href="btn btn-danger">Remove Song</a>
				                			</td>
				                			<td>
				                				<a href="btn btn-danger">Remove Slot</a>
				                			</td>
				                		</tr>
				                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				                	</tbody>
				                </table>
				                <a id="btnAddSong" href="#" class="float-right btn btn-primary">Add Song</a>
				                <a id="btnShowList" href="#" class="mr-2 float-left btn btn-danger">Hide List</a>
				                <a id="btnHideList" href="#" class="mr-2 float-left btn btn-info">Show List</a>
				            </div>
				        </div>
				    </div>
            	</form>
	    	</div>
	    </div>
	</div>

	<?php echo $__env->make('song.partials.select_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('song.partials.search_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo e(mix('css/app.css')); ?>" >
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="/vendor/select2/css/select2.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script> console.log('Hi!'); </script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="/vendor/select2/js/select2.full.min.js"></script>
	<script src="<?php echo e(mix('js/app.js')); ?>" defer></script>
    <script src="<?php echo e(mix('js/pool/edit.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('plugins.select2', true); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pool/edit.blade.php ENDPATH**/ ?>