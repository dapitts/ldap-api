<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Ldap_api 
{
	private $ch;
	private $redis_host;
	private $redis_port;
	private $redis_timeout;  
	private $redis_password;
	private $client_redis_key;

	public function __construct()
	{
		$CI =& get_instance();

		$this->redis_host       = $CI->config->item('redis_host');
		$this->redis_port       = $CI->config->item('redis_port');
		$this->redis_timeout    = $CI->config->item('redis_timeout');
		$this->redis_password   = $CI->config->item('redis_password');
		$this->client_redis_key = 'ldap_';
	}

	public function redis_info($client, $field = NULL, $action = 'GET', $data = NULL)
	{
		$client_info    = client_redis_info($client);
		$client_key     = $this->client_redis_key.$client;

		$redis = new Redis();
		$redis->connect($client_info['redis_host'], $client_info['redis_port'], $this->redis_timeout);
		$redis->auth($client_info['redis_password']);

		if ($action === 'SET')
		{
			$check = $redis->hMSet($client_key, $data);
		}
		else
		{
			if (is_null($field))
			{
				$check = $redis->hGetAll($client_key);
			}
			else
			{
				$check = $redis->hGet($client_key, $field);
			}
		}     

		$redis->close();

		if (empty($check))
		{
			$check = NULL;
		}

		return $check;		
	}

	public function client_config($client, $field = NULL, $action = 'GET', $data = NULL)
	{
		$client_info    = client_redis_info($client);
		$client_key     = $client.'_configurations';

		$redis = new Redis();
		$redis->connect($client_info['redis_host'], $client_info['redis_port'], $this->redis_timeout);
		$redis->auth($client_info['redis_password']);

		if ($action === 'SET')
		{
			$check = $redis->hMSet($client_key, $data);
		}
		else
		{
			if (is_null($field))
			{
				$check = $redis->hGetAll($client_key);
			}
			else
			{
				$check = $redis->hGet($client_key, $field);
			}
		}   

		$redis->close();

		if (empty($check))
		{
			$check = NULL;
		}

		return $check;		
	}

	public function create_ldap_redis_key($client, $data = NULL)
	{
		$client_info    = client_redis_info($client);
		$client_key     = $this->client_redis_key.$client;

		$redis = new Redis();
		$redis->connect($client_info['redis_host'], $client_info['redis_port'], $this->redis_timeout);
		$redis->auth($client_info['redis_password']);

		$check = $redis->hMSet($client_key, [
			'hostname'      => $data['hostname'],
			'port'          => $data['port'],
			'api_key'       => $data['api_key'],
			'tested'        => '0',
			'request_sent'  => '0',
			'enabled'       => '0',
			'terms_agreed'  => '0'
		]);

		$redis->close();

		return $check;		
	}

	public function change_api_activation_status($client, $requested, $status)
	{
		$set_activation = ($status) ? 1 : 0;
		$check          = FALSE;

		#set soc redis keys
		$redis = new Redis();
		$redis->connect($this->redis_host, $this->redis_port, $this->redis_timeout);
		$redis->auth($this->redis_password);

		$check = $redis->hSet($client.'_information', 'ldap_enabled', $set_activation);

		$redis->close();

		# set client redis keys
		if (is_int($check))
		{
			$status_data = array(
				'enabled'       => $set_activation,
				'request_sent'  => $set_activation,
				'request_user'  => $requested,
				'terms_agreed'  => $set_activation
			);

			$config_data = array(
				'ldap_enabled'  => $set_activation
			);

			if ($this->redis_info($client, NULL, 'SET', $status_data))
			{
				if ($this->client_config($client, NULL, 'SET', $config_data))
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	public function fetch($client, $key, $value)
	{
		$ldap_info  = $this->redis_info($client);
		$url        = 'http://'.$ldap_info['hostname'].':'.$ldap_info['port'].'/';
		$query_str  = http_build_query(['apikey' => $ldap_info['api_key'], 'type' => 'fetch', 'key' => $key, 'val' => $value, 'nc' => 1]);

		$header_fields = array(
			'Accept: application/json'
		);

		$response = $this->call_api('GET', $url.'?'.$query_str, $header_fields);

		if ($response['result'] !== FALSE)
		{
			if ($response['http_code'] === 200)
			{
				return array(
					'success'   => TRUE,
					'response'  => $response['result']
				);
			}
			else
			{
				return array(
					'success'   => FALSE,
					'response'  => $response
				);
			}
		}
		else
		{
			return array(
				'success'   => FALSE,
				'response'  => array(
					'status'    => 'cURL returned false',
					'message'   => 'errno = '.$response['errno'].', error = '.$response['error']
				)
			);
		}
	}

	public function search($client, $search_term)
	{
		$ldap_info  = $this->redis_info($client);
		$url        = 'http://'.$ldap_info['hostname'].':'.$ldap_info['port'].'/';
		$query_str  = http_build_query(['apikey' => $ldap_info['api_key'], 'type' => 'search', 'search' => $search_term, 'nc' => 1]);

		$header_fields = array(
			'Accept: application/json'
		);

		$response = $this->call_api('GET', $url.'?'.$query_str, $header_fields);

		if ($response['result'] !== FALSE)
		{
			if ($response['http_code'] === 200)
			{
				return array(
					'success'   => TRUE,
					'response'  => $response['result']
				);
			}
			else
			{
				return array(
					'success'   => FALSE,
					'response'  => $response
				);
			}
		}
		else
		{
			return array(
				'success'   => FALSE,
				'response'  => array(
					'status'    => 'cURL returned false',
					'message'   => 'errno = '.$response['errno'].', error = '.$response['error']
				)
			);
		}
	}

	private function call_api($method, $url, $header_fields, $post_fields = NULL)
	{
		$this->ch = curl_init();

		switch ($method)
		{
			case 'POST':
				curl_setopt($this->ch, CURLOPT_POST, true);

				if (isset($post_fields))
				{
					curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_fields);
				}

				break;
			case 'PUT':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');

				if (isset($post_fields))
				{
					curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_fields);
				}

				break;
			case 'DELETE':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}

		if (is_array($header_fields))
		{
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header_fields);
		}

		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
		//curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);

		if (($response['result'] = curl_exec($this->ch)) !== FALSE)
		{
			if (($response['http_code'] = curl_getinfo($this->ch, CURLINFO_HTTP_CODE)) === 200)
			{
				// Make sure the size of the response is non-zero prior to json_decode()
				if (curl_getinfo($this->ch, CURLINFO_SIZE_DOWNLOAD_T))
				{
					$response['result'] = json_decode($response['result'], TRUE);
				}
			}
		}
		else
		{
			$response['errno']  = curl_errno($this->ch);
			$response['error']  = curl_error($this->ch);
		}

		curl_close($this->ch);

		return $response;
	}

}