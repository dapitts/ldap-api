<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ldap_browser extends CI_Controller 
{
	private $redis_host;
	private $redis_port;
	private $redis_timeout;  
	private $redis_password;

	public function __construct()
	{
		parent::__construct();

		if (!$this->tank_auth->is_logged_in()) 
		{	
			if ($this->input->is_ajax_request()) 
			{
				redirect('/auth/ajax_logged_out_response');
			} 
			else 
			{
				redirect('/auth/login');
			}
		}

		$this->redis_host       = $this->config->item('redis_host');
		$this->redis_port       = $this->config->item('redis_port');
		$this->redis_password   = $this->config->item('redis_password');
		$this->redis_timeout    = $this->config->item('redis_timeout');

		$this->load->library('ldap_api');
	}

	public function index()
	{
		# Page Data
		$data['clients']            = $this->ldap_enabled_customers();
		$data['query_types']        = $this->query_type_dropdown();
		$data['set_query_type']     = '';
		$data['ldap_attributes']    = $this->ldap_attributes_dropdown();
		$data['set_ldap_attr']      = '';
		$data['ldap_booleans']      = $this->ldap_boolean_dropdown();
		$data['set_ldap_boolean']   = 'and';
		$data['ldap_operators']     = $this->ldap_operators_dropdown();
		$data['set_ldap_operator']  = '=';

		# Page View
		$this->load->view('assets/header');
		$this->load->view('ldap-browser/start', $data);
		$this->load->view('assets/footer');
	}

	public function country_info()
	{
		$query_type = $this->uri->segment(3);
		$attribute  = $this->uri->segment(4);

		switch ($attribute)
		{
			case 'c':
				$options    = $this->utility->get_country_iso2(TRUE);
				break;
			case 'co':
				$options    = $this->utility->get_country_short_name(TRUE);
				break;
			case 'countryCode':
				$options    = $this->utility->get_country_code(TRUE, TRUE);
				break;
		}

		if ($query_type === 'fetch')
		{
			$id     = 'ldap-fetch-value';
			$name   = 'ldap_fetch_value';
		}
		else if ($query_type === 'search')
		{
			$id_number  = $this->uri->segment(5);

			$id     = 'ldap-search-value-'.$id_number;
			$name   = 'ldap_search_values[]';
		}

		$data['label']      = 'Value';
		$data['id']         = $id;
		$data['name']       = $name;
		$data['options']    = $options ?? [];
		$data['selected']   = '';

		$this->load->view('ldap-browser/generic-dropdown', $data);
	}

	public function generic_input()
	{
		$query_type = $this->uri->segment(3);

		if ($query_type === 'fetch')
		{
			$id     = 'ldap-fetch-value';
			$name   = 'ldap_fetch_value';
		}
		else if ($query_type === 'search')
		{
			$id_number  = $this->uri->segment(4);

			$id     = 'ldap-search-value-'.$id_number;
			$name   = 'ldap_search_values[]';
		}

		$data['label']          = 'Value <span class="help-block inline">( You can use an asterisk \'*\' as a wildcard. )</span>';
		$data['id']             = $id;
		$data['name']           = $name;
		$data['type']           = 'text';
		$data['placeholder']    = 'Enter Value';

		$this->load->view('ldap-browser/generic-input', $data);
	}

	public function get_search_row()
	{
		$row_number = $this->uri->segment(3);

		$data['row_number']         = $row_number;
		$data['ldap_attributes']    = $this->ldap_attributes_dropdown();
		$data['set_ldap_attr']      = '';
		$data['ldap_operators']     = $this->ldap_operators_dropdown();
		$data['set_ldap_operator']  = '=';

		$this->load->view('ldap-browser/search-row', $data);
	}

	public function fetch()
	{
		$this->form_validation->set_rules('ldap_fetch_attr', 'Attribute', 'trim|required');
		$this->form_validation->set_rules('ldap_fetch_value', 'Value', 'trim|required');
		$this->form_validation->set_rules('client_code', 'Client Code', 'trim|required');

		if ($this->form_validation->run()) 
		{
			$asset  = client_by_code($this->input->post('client_code'));
			$key    = $this->input->post('ldap_fetch_attr');
			$value  = $this->input->post('ldap_fetch_value');

			$response = $this->ldap_api->fetch($asset['seed_name'], $key, $value);

			if ($response['success'])
			{
				$results        = [];
				$result_count   = count($response['response']['results']);

				foreach ($response['response']['results'] as $idx => $result)
				{
					if (isset($result['thumbnailPhoto']))
					{
						unset($response['response']['results'][$idx]['thumbnailPhoto']);
					}

					if (isset($result['msExchSafeSendersHash']))
					{
						unset($response['response']['results'][$idx]['msExchSafeSendersHash']);
					}

					if (isset($result['mSMQSignCertificates']))
					{
						unset($response['response']['results'][$idx]['mSMQSignCertificates']);
					}

					$results[] = array(
						'name'          => $result['cn'][0] ?? 'N/A',
						'company'       => $result['company'][0] ?? 'N/A',
						'client_code'   => $asset['code']
					);
				}

				$response = array(
					'success'       => true,
					'results'       => $results,
					'result_count'  => $result_count
				);
			}
			else
			{
				$message = $this->get_error_message($response['response']);

				$response = array(
					'success'       => false,
					'message'       => $message,
					'csrf_name'     => $this->security->get_csrf_token_name(),
					'csrf_value'    => $this->security->get_csrf_hash()
				);
			}
		}
		else
		{
			$response = array(
				'success'       => false,
				'message'       => validation_errors(),
				'csrf_name'     => $this->security->get_csrf_token_name(),
				'csrf_value'    => $this->security->get_csrf_hash()
			);
		}

		echo json_encode($response);
	}

	public function fetch_details()
	{
		$client_code    = $this->uri->segment(3);
		$name           = urldecode($this->uri->segment(4));

		$asset  = client_by_code($client_code);

		$response = $this->ldap_api->fetch($asset['seed_name'], 'cn', $name);

		if ($response['success'])
		{
			foreach ($response['response']['results'] as $result)
			{
				if (isset($result['thumbnailPhoto']))
				{
					unset($result['thumbnailPhoto']);
				}

				if (isset($result['msExchSafeSendersHash']))
				{
					unset($result['msExchSafeSendersHash']);
				}

				if (isset($result['mSMQSignCertificates']))
				{
					unset($result['mSMQSignCertificates']);
				}

				$results = $result;

				$details = array(
					'name'              => $result['cn'][0] ?? false,
					'company'           => $result['company'][0] ?? false,
					'title'             => $result['title'][0] ?? false,
					'telephone_number'  => $result['telephoneNumber'][0] ?? false,
					'mail'              => $result['mail'][0] ?? false,
					'department'        => $result['department'][0] ?? false,
					'description'       => $result['description'][0] ?? false,
					'bad_pwd_count'     => $result['badPwdCount'][0] ?? false,
					'two_letter_code'   => $result['c'][0] ?? false,
					'country'           => $result['co'][0] ?? false,
					'country_code'      => $result['countryCode'][0] ?? false,
					'employee_id'       => $result['employeeID'][0] ?? false,
					'given_name'        => $result['givenName'][0] ?? false,
					'city'              => $result['l'][0] ?? false,
					'postal_code'       => $result['postalCode'][0] ?? false,
					'last_name'         => $result['sn'][0] ?? false,
					'state'             => $result['st'][0] ?? false,
					'mobile'            => $result['mobile'][0] ?? false,
				);
			}

			$response = array(
				'success'       => true,
				'results'       => [[
					'details'   => $details,
					'json'      => json_encode($results, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
				]]
			);
		}
		else
		{
			$message = $this->get_error_message($response['response']);

			$response = array(
				'success'   => false,
				'message'   => $message
			);
		}

		echo json_encode($response);
	}

	public function search()
	{
		$num_of_rows = count($this->input->post('ldap_search_attrs'));

		if ($num_of_rows > 1)
		{
			$this->form_validation->set_rules('ldap_search_boolean', 'Boolean', 'trim|required');
		}

		$this->form_validation->set_rules('ldap_search_attrs[]', 'Attribute', 'trim|required');
		$this->form_validation->set_rules('ldap_search_operators[]', 'Operator', 'trim|required');
		$this->form_validation->set_rules('ldap_search_values[]', 'Value', 'trim|required');
		$this->form_validation->set_rules('client_code', 'Client Code', 'trim|required');

		if ($this->form_validation->run()) 
		{
			$asset      = client_by_code($this->input->post('client_code'));
			$boolean    = $this->input->post('ldap_search_boolean');
			$attributes = $this->input->post('ldap_search_attrs');
			$operators  = $this->input->post('ldap_search_operators');
			$values     = $this->input->post('ldap_search_values');

			$search_query = $this->build_search_query($boolean, $attributes, $operators, $values, $num_of_rows);

			$response = $this->ldap_api->search($asset['seed_name'], $search_query);

			if ($response['success'])
			{
				$results        = [];
				$result_count   = count($response['response']['results']);

				foreach ($response['response']['results'] as $idx => $result)
				{
					if (isset($result['thumbnailPhoto']))
					{
						unset($response['response']['results'][$idx]['thumbnailPhoto']);
					}

					if (isset($result['msExchSafeSendersHash']))
					{
						unset($response['response']['results'][$idx]['msExchSafeSendersHash']);
					}

					if (isset($result['mSMQSignCertificates']))
					{
						unset($response['response']['results'][$idx]['mSMQSignCertificates']);
					}

					$results[] = array(
						'name'          => $result['cn'][0] ?? 'N/A',
						'company'       => $result['company'][0] ?? 'N/A',
						'client_code'   => $asset['code']
					);
				}

				$response = array(
					'success'       => true,
					'results'       => $results,
					'result_count'  => $result_count
				);
			}
			else
			{
				$message = $this->get_error_message($response['response']);

				$response = array(
					'success'       => false,
					'message'       => $message,
					'csrf_name'     => $this->security->get_csrf_token_name(),
					'csrf_value'    => $this->security->get_csrf_hash()
				);
			}
		}
		else
		{
			$response = array(
				'success'       => false,
				'message'       => validation_errors(),
				'csrf_name'     => $this->security->get_csrf_token_name(),
				'csrf_value'    => $this->security->get_csrf_hash()
			);
		}

		echo json_encode($response);
	}

	private function ldap_enabled_customers()
	{
		$clients = NULL;

		$redis = new Redis();
		$redis->connect($this->redis_host, $this->redis_port, $this->redis_timeout);
		$redis->auth($this->redis_password);

			$results = $redis->sort('customer_list_active', [
				'by'    => '*->client',
				'alpha' => TRUE,
				'sort'  => 'asc',
				'get'   => [
					'*->client',
					'*->code',
					'*->seed_name',
					'*->ldap_enabled',
				]
			]);

		$redis->close();

		if (!empty($results))
		{
			$clients = array_chunk($results, 4);

			$clients = array_map(function($client) 
			{
				return array(
					'client'    => $client['0'],
					'code'      => $client['1'],
					'cust_seed' => $client['2'],
					'ldap'      => $client['3'],
				);
			}, $clients);

			foreach ($clients AS $key => $value)
			{
				if (!$value['ldap'])
				{
					unset($clients[$key]);
				}
			}
		}

		return $clients;
	}

	private function query_type_dropdown()
	{
		$query_types = array(
			''          => '- - - Select Query Type - - -',
			'fetch'     => 'Fetch',
			'search'    => 'Search',
		);

		return $query_types;
	}

	private function ldap_attributes_dropdown()
	{
		$ldap_attributes = array(
			''                  => '- - - Select Attribute - - -',
			'badPwdCount'       => 'badPwdCount [Incorrect Password Count]',
			'c'                 => 'c [Two-Letter Country Code]',
			'cn'                => 'cn [Common/Full Name]',
			'co'                => 'co [Country]',
			'company'           => 'company [Company]',
			'countryCode'       => 'countryCode [Country Code]',
			'department'        => 'department [Department]',
			'description'       => 'description [Description]',
			'employeeID'        => 'employeeID [Employee ID]',
			'givenName'         => 'givenName [First Name]',
			'l'                 => 'l [City]',
			'mail'              => 'mail [Email Address]',
			'name'              => 'name [Full Name]',
			'postalCode'        => 'postalCode [ZIP/Postal Code]',
			'sn'                => 'sn [Last Name]',
			'st'                => 'st [State/Province]', 
			'telephoneNumber'   => 'telephoneNumber [Telephone Number]',
			'title'             => 'title [Title]',
		);

		return $ldap_attributes;
	}

	private function ldap_boolean_dropdown()
	{
		$ldap_booleans = array(
			'and'   => 'AND',
			'or'    => 'OR'
		);

		return $ldap_booleans;
	}

	private function ldap_operators_dropdown()
	{
		$ldap_operators = array(
			'='     => '=  [eq]',
			'>='    => '>= [gte]',
			'<='    => '<= [lte]'
		);

		return $ldap_operators;
	}

	private function get_error_message($response)
	{
		$message = '';

		if (isset($response['result'], $response['http_code']))
		{
			$message    = 'Result: '.$response['result'].', HTTP Code: '.$response['http_code'];
		}
		else
		{
			$message    = 'N/A';
		}

		return $message;
	}

	private function build_search_query($boolean, $attributes, $operators, $values, $num_of_rows)
	{
		if ($num_of_rows === 1)
		{
			return $this->build_search_filter($attributes[0], $operators[0], $values[0]);
		}
		else
		{
			$search_query = '(';

			if ($boolean === 'and')
			{
				$search_query   .= '&';
			}
			else if ($boolean === 'or')
			{
				$search_query   .= '|';
			}

			for ($i = 0; $i < $num_of_rows; $i++)
			{
				$search_query .= $this->build_search_filter($attributes[$i], $operators[$i], $values[$i]);
			}

			return $search_query.')';
		}
	}

	private function build_search_filter($attribute, $operator, $value)
	{
		$filter = '';
		$value  = str_replace(['(', ')'], ['\(', '\)'], $value);

		switch ($operator)
		{
			case '=':
			case '>=':
			case '<=':
				$filter = '('.$attribute.$operator.$value.')';
				break;
			// case '!=':
			// 	$filter = '(!('.$attribute.'='.$value.'))';
			// 	break;
			// case '<':
			// 	$filter = '(!('.$attribute.'>='.$value.'))';
			// 	break;
			// case '>':
			// 	$filter = '(!('.$attribute.'<='.$value.'))';
			// 	break;
		}

		return $filter;
	}
}