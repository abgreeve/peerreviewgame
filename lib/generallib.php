<?php

function print_object($data) {
	echo '<pre>';
	echo print_r($data, true);
	echo '</pre>';
}

function url_redirect($url, array $params = []) {

	header('Location: ' . $url);
}

class page_head {

	protected $title;
	protected $stylesheet;

	public function __construct($title) {
		$this->title = $title;
		$this->stylesheet = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
		$this->stylesheet .= '<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>';
		$this->stylesheet .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>';
		$this->stylesheet .= '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>';
	}

	public function out() {
		$html = '<head>';
		$html .= '<meta charset="UTF-8">';
		$html .= '<title>' . $this->title . '</title>';
		$html .= $this->stylesheet;
		$html .= '</head>';
		return $html;
	}
}

class atable {

	protected $headers = [];
	protected $data = [];

	public function __construct($tabledata = null) {
		if (isset($tabledata)) {
			$this->set_data($tabledata);
		}
	}

	public function set_headers($headers) {
		$this->headers = $headers;
	}

	public function set_data($data) {
		$this->data = $data;
		if (!empty($data)) {
			foreach ($data as $key => $notused) {
				$this->headers[] = $key;
			}
		}
	}

	public function out() : string {
		$html = '<table class="table">';
		$html .= '<thead>';
		$html .= '<tr>';
		foreach ($this->headers as $header) {
			$html .= '<th scope="col">' . $header . '</th>';
		}
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ($this->data as $tableitems) {
			$html .= '<tr>';
			foreach ($tableitems as $value) {
				$html .= '<td>' . $value . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}
?>