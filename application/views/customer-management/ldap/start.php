<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-12">		
		<ol class="breadcrumb">
			<li><a href="/customer-management">Customer Management</a></li>
			<li><a href="/customer-management/customer/<?php echo $client_code; ?>">Customer Overview</a></li>
			<li class="active">LDAP API</li>
		</ol>	
	</div>
</div>

<?php echo $sub_navigation; ?>

<div class="row">
	<div class="col-md-8">

		<div class="panel panel-light">
			<div class="panel-heading">
				<div class="clearfix">
					<div class="pull-left">
						<h3>LDAP API</h3>
						<h4>Integration</h4>
					</div>
					<div class="pull-right">
						<a href="/customer-management/ldap/<?php echo $action; ?>/<?php echo $client_code; ?>" class="btn btn-success"><?php echo ucfirst($action); ?></a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<?php if (empty($ldap_info)) { ?>

					<div class="row">
						<div class="col-md-8">
							<p class="lead">This customer does not have LDAP enabled. Click <em>Create</em> to enable.</p>
						</div>
					</div>

				<?php } else { ?>

					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<table class="table valign-middle">
										<tr>
											<td class="col-md-3"><label class="control-label">api url</label></td>
											<td class="col-md-9"><?php echo 'http://'.$ldap_info['hostname'].':'.$ldap_info['port']; ?></td>
										</tr>
										<tr>
											<td><label class="control-label">api key</label></td>
											<td><?php echo token_cloak($ldap_info['api_key']); ?></td>
										</tr>
									</table>
								</div>
							</div>	
							<div class="row">
								<div class="col-md-6">									
									<button type="button" class="btn btn-info" id="show-api-test">Test Integration</button>									
								</div>
							</div>			
						</div>

						<div class="col-md-12">
							<div class="row" id="api-test-display">
								<div class="col-md-12">
									<div class="well api-test-window">
										<p>We will try to make an API call to LDAP: <button class="btn btn-xs btn-success" id="run-api-test" data-api="ldap" data-client="<?php echo $client_code; ?>" data-loading-text="Please Wait...">Run Test</button></p>

										<p class="running-test">Running test...</p>
										<p class="test-success"><span>Successfully made API call</span></p>
										<p class="test-failed"><span>Error: API call failed</span></p>

										<div id="api-test-results">
											<pre class="api-json-results" class="wrap-json"></pre>
											<div class="machine-list">
												<p>
													Results: <strong class="result-count">0</strong>
												</p>
												<table class="table valign-middle gray-header">
													<thead>
														<tr>
															<th>Name</th>
															<th>Title</th>
															<th>Email Address</th>
															<th>Department</th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				<?php } ?>
  			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="panel panel-light api-activation-panel <?php echo (!empty($ldap_info) && $show_activation) ? 'show':''; ?>">
			<div class="panel-heading">
				<div class="clearfix">
					<div class="pull-left">
						<h3>API Activation</h3>
						<h4>check list</h4>
					</div>
					<div class="pull-right"></div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">	
					<div class="col-md-12">

						<ul class="nav api-check-list">
							<li>
								<span class="fa-stack">  
									<i class="fas fa-check fa-stack-1x icon-api-tested <?php echo ($api_tested) ? 'show' : ''; ?>"></i>
									<i class="far fa-square fa-stack-2x"></i>
								</span><span class="check-list-label"> : API Has Been Tested</span>
							</li>
							<li>
								<span class="fa-stack">  
									<i class="fas fa-check fa-stack-1x icon-api-requested <?php echo ($request_was_sent) ? 'show' : ''; ?>"></i>
									<i class="far fa-square fa-stack-2x"></i>
								</span><span class="check-list-label">  : Customer Sent Request</span>
							</li>
							<li>
								<span class="fa-stack">  
									<i class="fas fa-check fa-stack-1x icon-api-enabled <?php echo ($api_enabled) ? 'show' : ''; ?>"></i>
									<i class="far fa-square fa-stack-2x"></i>
								</span><span class="check-list-label"> : API Is Enabled</span>
							</li>
						</ul>

					</div>
				</div>	

				<div class="row">	
					<div class="col-md-10 col-md-offset-1">
						<?php if ($api_enabled) { ?>
							<a href="/customer-management/ldap/disable/<?php echo $client_code; ?>" data-toggle="modal" data-target="#decision_modal" class="btn btn-danger btn-lg btn-block">Disable API</a>
						<?php } else { ?>
							<a href="/customer-management/ldap/activate/<?php echo $client_code; ?>" data-toggle="modal" data-target="#decision_modal" class="btn btn-warning btn-lg btn-block">Activate API</a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/html" id="row-template">
	<tr>		
		<td>{{name}}</td>
		<td>{{title}}</td>
		<td>{{mail}}</td>
		<td>{{department}}</td>
	</tr>
</script>