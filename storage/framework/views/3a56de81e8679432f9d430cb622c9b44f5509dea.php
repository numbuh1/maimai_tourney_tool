<?php $__env->startSection('title', 'Songs'); ?>

<?php $__env->startSection('content_header'); ?>
    <!-- <h1>Songs</h1> -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="container-user">
	    <div class="card shadow mb-4">
		    <div class="card-header py-3">
		        <h6 class="m-0 font-weight-bold text-primary">Songs</h6>
		    </div>
		    <div class="card-body">
	    		<?php echo $__env->make('song.partials.list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	    	</div>
	    </div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script> console.log('Hi!'); </script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script src="<?php echo e(mix('js/app.js')); ?>" defer></script>
    <script src="<?php echo e(mix('js/song/index.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/song/index.blade.php ENDPATH**/ ?>