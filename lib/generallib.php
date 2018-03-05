<?php


function print_object($data) {
	echo '<pre>';
	echo print_r($data);
	echo '</pre>';
}


class atable {

	protected $headers = '';
	protected $data = '';

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
		foreach ($data[0] as $key => $notused) {
			$this->headers[] = $key;
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