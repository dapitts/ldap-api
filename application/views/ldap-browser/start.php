<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-12">		
		<ol class="breadcrumb"></ol>	
	</div>
</div>
<div class="row">
	<div class="col-md-12 ldap-browser">
		<div class="panel panel-light">
			<div class="panel-heading">
				<div class="clearfix">
					<div class="pull-left">
						<h3>LDAP Browser</h3>
					</div>
					<div class="pull-right">
						<a href="javascript:window.close();" class="btn btn-default">Close</a>
					</div>
				</div>
			</div>
			<div id="modal-alert-container" class="bluedot-error">
				<div class="alert alert-success">
					<h4 class="alert-heading">Error</h4>
					<div class="modal-alert-content"></div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6 vr">
						<div class="row">
							<div class="col-md-6">
								<?php if (!empty($clients)) { ?>
									<div class="form-group">
										<label class="control-label" for="ldap-client-list">Customer</label>
										<select name="ldap_client_list" id="ldap-client-list" class="selectpicker form-control" data-live-search="true" data-size="8">
											<option value="">- - - Select Customer - - -</option>
											<?php foreach ($clients as $client): ?>
												<option value="<?php echo $client['code']; ?>"><?php echo $client['client']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php } else { ?>
									<p class="text-center">Sorry there are no clients with LDAP enabled.</p>
								<?php } ?>
							</div>
							<div class="col-md-6">
								<div id="ldap-query-types">
									<div class="form-group">
										<label class="control-label" for="ldap-query-type">Query Type</label>	
										<?php echo form_dropdown('ldap_query_type', $query_types, $set_query_type, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-query-type"'); ?>
									</div>
								</div>
							</div>
						</div>
						<div id="ldap-fetch-area">
							<div class="row">
								<div class="col-md-12">
									<?php echo form_open('/ldap-browser/fetch', array('id' => 'ldap-fetch-form', 'autocomplete' => 'off', 'aria-autocomplete' => 'off', 'class' => 'element-action-form')); ?>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="ldap-fetch-attr">Attribute</label>
												<?php echo form_dropdown('ldap_fetch_attr', $ldap_attributes, $set_ldap_attr, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-fetch-attr"'); ?>
											</div>
										</div>
										<div class="col-md-6">
											<div id="ldap-fetch-values">
												<div class="form-group">
													<label class="control-label" for="ldap-fetch-value">Value <span class="help-block inline">( You can use an asterisk '*' as a wildcard. )</span></label>	
													<input type="text" class="form-control" name="ldap_fetch_value" id="ldap-fetch-value" placeholder="Enter Value" value="">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
											<button id="ldap-fetch-submit-btn" type="submit" disabled="" form="ldap-fetch-form" class="btn btn-primary" data-loading-text="Fetching...">Fetch</button>
										</div>
									</div>
									<input type="hidden" name="client_code" />
									<?php echo form_close(); ?>
								</div>
							</div>
						</div>
						<div id="ldap-search-area">
							<div class="row">
								<div class="col-md-12">
									<?php echo form_open('/ldap-browser/search', array('id' => 'ldap-search-form', 'autocomplete' => 'off', 'aria-autocomplete' => 'off', 'class' => 'element-action-form')); ?>
									<div class="row">
										<div class="col-md-4">
											<div id="ldap-logical-operator">
												<div class="form-group">
													<label class="control-label" for="ldap-search-boolean">Logical Operator</label>
													<?php echo form_dropdown('ldap_search_boolean', $ldap_booleans, $set_ldap_boolean, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-search-boolean"'); ?>
												</div>
											</div>
										</div>
										<div class="col-md-8 text-right">
											<button id="delete-filter-btn" type="button" form="ldap-search-form" class="btn btn-default" data-loading-text="Deleting...">Delete Filter</button>
											<button id="add-filter-btn" type="button" form="ldap-search-form" class="btn btn-default" data-loading-text="Adding...">Add Filter</button>
										</div>
									</div>
									<div class="row" id="ldap-search-row-1">
										<div class="col-md-5">
											<div class="form-group">
												<label class="control-label" for="ldap-search-attr-1">Attribute</label>
												<?php echo form_dropdown('ldap_search_attrs[]', $ldap_attributes, $set_ldap_attr, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-search-attr-1"'); ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label" for="ldap-search-operator-1">Operator</label>
												<?php echo form_dropdown('ldap_search_operators[]', $ldap_operators, $set_ldap_operator, 'class="selectpicker form-control" data-live-search="true" data-size="8" id="ldap-search-operator-1"'); ?>
											</div>
										</div>
										<div class="col-md-5">
											<div id="ldap-search-values-1">
												<div class="form-group">
													<label class="control-label" for="ldap-search-value-1">Value <span class="help-block inline">( You can use an asterisk '*' as a wildcard. )</span></label>	
													<input type="text" class="form-control" name="ldap_search_values[]" id="ldap-search-value-1" placeholder="Enter Value" value="">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
											<button id="ldap-search-submit-btn" type="submit" form="ldap-search-form" class="btn btn-primary" data-loading-text="Searching...">Search</button>
										</div>
									</div>
									<input type="hidden" name="client_code" />
									<?php echo form_close(); ?>
								</div>
							</div>
						</div>
						<div class="alert alert-info ldap-instructions">
							<h4 class="text-center">LDAP Browser Overview</h4>
							<table class="table table-condensed">
								<tbody>
									<tr>
										<td style="width:10%;">Fetch</td>
										<td>
											Fetch is the simplest query type. To perform a fetch, select an Attribute, enter or select a Value and click 
											the Fetch button. The results will appear under the Query Results section.
										</td>
									</tr>
									<tr>
										<td>Search</td>
										<td>
											<p>Search is an advanced query type and uses search filters. A filter is comprised of the following:</p>
											<p>Filter = (&lt;Attribute&gt;&lt;Operator&gt;&lt;Value&gt;)</p>
											<p>
												If more than one filter is used, they can be concatenated by a logical AND or OR operator. To perform a 
												search, select an Attribute, select an Operator, enter or select a Value for each filter and a Logical 
												Operator (if more than one filter is used) and click the Search button. The results will appear under the 
												Query Results section.
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6 vr">
								<div class="search-area ldap show">
									<h3>Query Results</h3>														
									<div class="row">
										<div class="col-md-12">
											<div class="ldap-query-results">
												<table class="table valign-middle gray-header">
													<thead>
														<tr>
															<th class="text-left">Name</th>
															<th class="text-left">Company</th>
															<th class="text-right">&nbsp;</th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="ldap-browser-placeholder show">
									<i class="fal fa-browser"></i>
								</div>					

								<div class="ldap-data-display-area">
									<div class="row">
										<div class="col-md-12">
											<ul class="nav nav-tabs" id="ldap-tabs">
												<li class="active">
													<a href="#table" data-toggle="tab">LDAP Details</a>
												</li>     		
												<li class="pull-right">
													<a href="#json" data-toggle="tab" title="View Complete LDAP JSON">JSON</a>
												</li>   	
											</ul>
											<div class="tab-content content-container" id="ldap-tab-contents"></div>			
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/html" id="ldap-row-template">
	<tr>		
		<td class="text-left">{{name}}</td>
		<td class="text-left">{{company}}</td>
		<td class="text-right">
			<button type="button" class="btn btn-xs btn-default" data-name="{{name}}" data-client="{{client_code}}" data-loading-text="Retrieving...">Details</button>
		</td>
	</tr>
</script>

<script type="text/html" id="ldap-detail-template">
	<div class="tab-pane active" id="table">
		<div class="ldap-data-wrapper">
			<div class="row">
				<div class="col-md-12">
					{{#details.name}}
					<div class="form-group">
						<label class="control-label">Name</label>
						<div>{{details.name}}</div>
					</div>
					{{/details.name}}
					{{#details.company}}
					<div class="form-group">
						<label class="control-label">Company</label>
						<div>{{details.company}}</div>
					</div>
					{{/details.company}}
					{{#details.title}}
					<div class="form-group">
						<label class="control-label">Title</label>
						<div>{{details.title}}</div>
					</div>
					{{/details.title}}
					{{#details.telephone_number}}
					<div class="form-group">
						<label class="control-label">Telephone Number</label>
						<div>{{details.telephone_number}}</div>
					</div>
					{{/details.telephone_number}}
					{{#details.mail}}
					<div class="form-group">
						<label class="control-label">Email Address</label>
						<div>{{details.mail}}</div>
					</div>
					{{/details.mail}}
					{{#details.department}}
					<div class="form-group">
						<label class="control-label">Department</label>
						<div>{{details.department}}</div>
					</div>
					{{/details.department}}
					{{#details.description}}
					<div class="form-group">
						<label class="control-label">Description</label>
						<div>{{details.description}}</div>
					</div>
					{{/details.description}}
					{{#details.bad_pwd_count}}
					<div class="form-group">
						<label class="control-label">Incorrect Password Count</label>
						<div>{{details.bad_pwd_count}}</div>
					</div>
					{{/details.bad_pwd_count}}
					{{#details.two_letter_code}}
					<div class="form-group">
						<label class="control-label">Two-Letter Country Code</label>
						<div>{{details.two_letter_code}}</div>
					</div>
					{{/details.two_letter_code}}
					{{#details.country}}
					<div class="form-group">
						<label class="control-label">Country</label>
						<div>{{details.country}}</div>
					</div>
					{{/details.country}}
					{{#details.country_code}}
					<div class="form-group">
						<label class="control-label">Country Code</label>
						<div>{{details.country_code}}</div>
					</div>
					{{/details.country_code}}
					{{#details.employee_id}}
					<div class="form-group">
						<label class="control-label">Employee ID</label>
						<div>{{details.employee_id}}</div>
					</div>
					{{/details.employee_id}}
					{{#details.given_name}}
					<div class="form-group">
						<label class="control-label">First Name</label>
						<div>{{details.given_name}}</div>
					</div>
					{{/details.given_name}}
					{{#details.city}}
					<div class="form-group">
						<label class="control-label">City</label>
						<div>{{details.city}}</div>
					</div>
					{{/details.city}}
					{{#details.postal_code}}
					<div class="form-group">
						<label class="control-label">ZIP/Postal Code</label>
						<div>{{details.postal_code}}</div>
					</div>
					{{/details.postal_code}}
					{{#details.last_name}}
					<div class="form-group">
						<label class="control-label">Last Name</label>
						<div>{{details.last_name}}</div>
					</div>
					{{/details.last_name}}
					{{#details.state}}
					<div class="form-group">
						<label class="control-label">State/Province</label>
						<div>{{details.state}}</div>
					</div>
					{{/details.state}}
					{{#details.mobile}}
					<div class="form-group">
						<label class="control-label">Mobile</label>
						<div>{{details.mobile}}</div>
					</div>
					{{/details.mobile}}
				</div>
			</div>
		</div>
	</div>	
	<div class="tab-pane" id="json">						
		<div class="ldap-json-wrapper">					
			<pre class="ldap-data">{{json}}</pre>
		</div>
	</div>
</script>