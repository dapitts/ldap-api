<div class="row" id="ldap-search-row-<?php echo $row_number; ?>">
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label" for="ldap-search-attr-<?php echo $row_number; ?>">Attribute</label>
            <?php echo form_dropdown('ldap_search_attrs[]', $ldap_attributes, $set_ldap_attr, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-search-attr-'.$row_number.'"'); ?>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="ldap-search-operator-<?php echo $row_number; ?>">Operator</label>
            <?php echo form_dropdown('ldap_search_operators[]', $ldap_operators, $set_ldap_operator, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-search-operator-'.$row_number.'"'); ?>
        </div>
    </div>
    <div class="col-md-5">
        <div id="ldap-search-values-<?php echo $row_number; ?>">
            <div class="form-group">
                <label class="control-label" for="ldap-search-value-<?php echo $row_number; ?>">Value <span class="help-block inline">( You can use an asterisk '*' as a wildcard. )</span></label>	
                <input type="text" class="form-control" name="ldap_search_values[]" id="ldap-search-value-<?php echo $row_number; ?>" placeholder="Enter Value" value="">
            </div>
        </div>
    </div>
</div>