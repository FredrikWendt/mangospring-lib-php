<?php

class Mango {

	/**
	 * Password should be base64 encoded.
	 */
	function __construct($domain, $username, $password, $api_key) {
		$this->username = $username;
		$this->password = $password;
		$this->api_key = $api_key;
		$this->is_logged_in = FALSE;
		$this->base_url = "https://". $domain. "/api/";
	}
	
	function __destruct() {
		if ($this->is_logged_in) {
			$this->logout();
		}
	}

	function post_status_update_on_group_wall($group_id, $message) {
		return $this->post_json("feeds.json", 
		array(
			"ms_request" => array(
				"feed" => array(
					"group_id" => $group_id,
					"feed_type" => "group",
					"body" => $message
				)
			)
		));
	}

	function post_status_update_on_project_wall($project_id, $message) {
		return $this->post_json("feeds.json", 
		array(
			"ms_request" => array(
				"feed" => array(
					"project_id" => $project_id,
					"attachments" => array(),
					"feed_type" => "project",
					"body" => $message
				)
			)
		));
	}

	function post_status_update($message) {
		return $this->post_json("feeds.json", 
		array(
			"ms_request" => array(
				"feed" => array(
					"feed_type" => "status",
					"body" => $message
				)
			)
		));
	}

	function get_all_users() {
		$result = $this->post_json("users.json", "");
		return $result;
	}

	function logout() {
		if ($this->is_logged_in) {
			$result = $this->post_json("logout.json", "");
			$this->is_logged_in = FALSE;
			unlink($this->cookie_file);
			unset($this->cookie_file);
		}
	}

	function login() {
		if ($this->is_logged_in === TRUE) {
			return TRUE;
		}
		$tmp_dir = sys_get_temp_dir();
		$this->cookie_file = tempnam($tmp_dir, "mango_curl_cookie_");
		$data = array(
			"ms_request" => array(
				"user" => array(
					"username" => $this->username,
					"password" => $this->password,
					"api_key" => $this->api_key
				)
			)
		);
		$result = $this->post_json("login.json", $data);
		if ($result->http_status === 200) {
			$this->is_logged_in = TRUE;
			return $result;
		} else {
			throw new Exception("Failed to login: ". print_r($result, TRUE));
		}
	}

	function post_json($url, $data) {
		$data_string = json_encode($data); 
		$ch = curl_init($this->base_url . $url);

		// @curl_setopt ($this -> ch , CURLOPT_COOKIE, $params['cookie']);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   

		$response = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$result = new stdClass;
		$result->response = $response;
		$result->header = substr($response, 0, $header_size);
		$result->body = substr($response, $header_size);
		$result->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result->json = json_decode($result->response);
		curl_close($ch);

		if (isset($result->json->ms_errors)) {
			throw new Exception("Communication error: " . print_r($result->json->ms_errors->error->message, TRUE));
		}

		return $result;
	}
}

