<?php

class competition_time {

	protected $id = null;
	protected $title;
	protected $starttime;
	protected $endtime;

	public function __construct($title, $starttime, $endtime, $id = null) {
		$this->title = $title;
		$this->starttime = $starttime;
		$this->endtime = $endtime;
		if (isset($id)) {
			$this->id = $id;
		}
	}

	public function save() {
		$DB = new DB();
		$data = [
			'title' => $this->title,
			'starttime' => $this->starttime,
			'endtime' => $this->endtime
		];
		if (isset($this->id)) {
			$data['id'] = $this->id;
			$DB->update_record('competition_time', $data);
		} else {
			$this->id = $DB->insert_record('competition_time', $data);
		}
	}

	public function set_id($id) {
		$this->id = $id;
	}

	public function set_title($title) {
		$this->title = $title;
	}

	public function set_starttime($starttime) {
		$this->starttime = $starttime;
	}

	public function set_endtime($endtime) {
		$this->endtime = $endtime;
	}

	public function get_starttime($format = 'd-m-Y', $formatted = true) {
		if (!$formatted) {
			return $this->starttime;
		}
		$starttime = new DateTime();
		$starttime->setTimeStamp($this->starttime);
		return $starttime->format($format);
	}

	public function get_endtime($format = 'd-m-Y', $formatted = true) {
		if (!$formatted) {
			return $this->endtime;
		}
		$endtime = new DateTime();
		$endtime->setTimeStamp($this->endtime);
		return $endtime->format($format);
	}

	public function get_title() {
		return $this->title;
	}

	public function get_id() {
		return $this->id;
	}

	public static function load_from_id($id) {
		$DB = new DB();
		$result = $DB->get_records('competition_time', ['id' => $id]);
		if (isset($result[0])) {
			return new competition_time($result[0]->title, $result[0]->starttime, $result[0]->endtime, $result[0]->id);
		}
		return false;
	}
}

?>