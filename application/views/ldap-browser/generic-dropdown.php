<div class="form-group">
    <label class="control-label" for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <?php echo form_dropdown($name, $options, $selected, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="'.$id.'"'); ?>
</div>