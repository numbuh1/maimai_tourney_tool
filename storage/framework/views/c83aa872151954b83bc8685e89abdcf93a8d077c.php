<!-- Select Modal-->
<div class="modal fade" id="selectSongModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Song</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <select id="selectCategory" class="form-control select2" multiple>
                            <?php $__currentLoopData = config('game.categories'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Version</label>
                        <select id="selectVersion" class="form-control select2" multiple>
                            <?php $__currentLoopData = config('game.versions'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($version); ?>"><?php echo e($version); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Difficulty</label>
                        <select id="selectDiff" class="form-control select2" multiple>
                            <?php $__currentLoopData = config('game.difficulties'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $diff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($diff); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Level Min</label>
                        <input type="number" id="selectLevelMin" class="form-control">
                        <!-- <select id="selectLevelMin" class="form-control">
                            <option value="">All</option>
                            <?php $__currentLoopData = config('game.levels'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($level); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> -->
                    </div>
                    <div class="form-group col-md-3">
                        <label>Level Max</label>
                        <input type="number" id="selectLevelMax" class="form-control">
                        <!-- <select id="selectLevelMax" class="form-control">
                            <option value="">All</option>
                            <?php $__currentLoopData = config('game.levels'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($level); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> -->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <a id="btnShowSongs" href="#" class="btn btn-info col-md-12" data-action="<?php echo e(route('song.search', false)); ?>">Show All</a>
                    </div>
                    <div class="form-group col-md-4">
                        <a id="btnRandomSongs" href="#" class="btn btn-warning col-md-12" data-action="<?php echo e(route('song.random', false)); ?>">Random</a>
                    </div>                    
                    <div class="form-group col-md-2">
                        <div class="input-group">
                            <input type="number" id="selectRandomNumber" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text">song(s)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="song-pane">
                    <div id="song-pane" class="row text-center">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/resources/views/song/partials/search_modal.blade.php ENDPATH**/ ?>