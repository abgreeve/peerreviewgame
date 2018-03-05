<?php


class jira {


	protected $connection = null;
	protected $curlresource = null;
	protected $fields = 'fields=key,issuetype,assignee,summary,customfield_10118,customfield_10011';

	public function __construct() {
		$this->connection = 'https://tracker.moodle.org/rest/api/2/';
		$this->curlresource = curl_init();
	}

	public function set_fields($fields) {
		$this->fields = 'fields=' . $fields;
	}

	public function search($searchstring) {
		$url = $this->connection . 'search?';
		$url = $url . $this->fields . '&maxResults=50&jql=';
		// $search = 'project = "MDL" AND status="waiting for peer review"';
		$search = urlencode($searchstring);
		$url = $url . $search;

		// print_object($url);

		curl_setopt($this->curlresource, CURLOPT_URL, $url);
		curl_setopt($this->curlresource, CURLOPT_RETURNTRANSFER, 1);

		$output = curl_exec($this->curlresource);
		return json_decode($output);
	}


	
	public static function hello() {
		echo 'hello';
	}

	public function __destruct() {
		curl_close($this->curlresource);
	}


}


?>