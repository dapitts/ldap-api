<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-12">		
		<ol class="breadcrumb">
			<li><a href="/customer-management">Customer Management</a></li>
			<li><a href="/customer-management/customer/<?php echo $client_code; ?>">Customer Overview</a></li>
			<li><a href="/customer-management/ldap/<?php echo $client_code; ?>">LDAP API</a></li>
			<li class="active">Create</li>
		</ol>	
	</div>
</div>
<div class="row">
	<div class="col-md-8">		
		<div class="panel panel-light">
			<div class="panel-heading">
				<div class="clearfix">
					<div class="pull-left">
						<h3>LDAP API Integration</h3>
						<h4>Create</h4>
					</div>
					<div class="pull-right">
						<a href="/customer-management/ldap/<?php echo $client_code; ?>" type="button" class="btn btn-default">Cancel &amp; Return</a>
					</div>
				</div>
			</div>			
			<?php echo form_open($this->uri->uri_string(), array('autocomplete' => 'off', 'aria-autocomplete' => 'off')); ?>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group<?php echo form_error('hostname') ? ' has-error':''; ?>">
								<label class="control-label" for="hostname">hostname</label>
								<input type="text" class="form-control" id="hostname" name="hostname" placeholder="Enter Hostname / IP Address" value="<?php echo set_value('hostname'); ?>">
							</div>
							<div class="form-group<?php echo form_error('port') ? ' has-error':''; ?>">
								<label class="control-label" for="port">port</label>
								<input type="text" class="form-control" id="port" name="port" placeholder="Enter Port" value="<?php echo set_value('port'); ?>">
							</div>
							<div class="form-group<?php echo form_error('api_key') ? ' has-error':''; ?>">
								<label class="control-label" for="api_key">api key</label>
								<input type="text" class="form-control" id="api_key" name="api_key" placeholder="Enter API Key" value="<?php echo set_value('api_key'); ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" class="btn btn-success" data-loading-text="Updating...">Update</button>
						</div>
					</div>
				</div>
			<?php echo form_close(); ?>			
		</div>		
	</div>
</div>