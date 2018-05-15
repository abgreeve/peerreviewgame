<?php

class jira_issue {

    protected $id;
    protected $mdl;
    protected $summary;
    protected $assignee;
    protected $peerreviewer;
    protected $type;
    protected $datesentforpeereview;
    protected $reviewed = null;
    protected $datecompleted = null;

    const ASSIGNEE_HQ = 0;
    const ASSIGNEE_OTHER = 7;
    const ISSUE_TYPE_OTHER = 0;
    const ISSUE_TYPE_IMPROVEMENT = 7;
    const BASE_SCORE = 7;

	public function __construct($mdl, $summary, $assignee, $peerreviewer, $type, $datereview = null, $id = null) {
		$this->mdl = $mdl;
		$this->summary = $summary;
		$this->assignee = $assignee;
		$this->peerreviewer = $peerreviewer;
		$this->type = $type;
		$this->datesentforpeereview = $datereview ?? time();
		$this->id = $id;
	}

	public static function load_from_raw_data($data) {
		$DB = new DB;

        $mdl = $data->key;
        $summary = $data->fields->summary;
        $type = $data->fields->issuetype->name;
        $assignee = (isset($data->fields->assignee->name)) ? $data->fields->assignee->name : '';
        $peerreviewer = (isset($data->fields->customfield_10118->name)) ? $data->fields->customfield_10118->name : '';

		// Should check to see if this issue exists and ammend that record.
		$sql = "SELECT *
				FROM issues
				WHERE mdl = :mdl AND (reviewed <> 1 OR reviewed IS NULL)";
		$records = $DB->execute_sql($sql, ['mdl' => $mdl]);

		// If we get back a record then we should load that.
		if (!empty($records)) {
			return new jira_issue($mdl, $summary, $assignee, $peerreviewer, $type, $records[0]->datereview, $records[0]->id);
		}

		return new jira_issue($mdl, $summary, $assignee, $peerreviewer, $type);
	}

    public static function load_from_id($idnumber) {
        $DB = new DB();
        $result = $DB->get_records('issues', ['id' => $idnumber]);
        return new jira_issue($result[0]->mdl, $result[0]->summary, $result[0]->assignee, $result[0]->peerreviewer,
                $result[0]->type, $result[0]->datereview, $result[0]->id);
    }

	public function save() {
		$DB = new DB();
		$data = [];
		$data['mdl'] = $this->mdl;
		$data['summary'] = $this->summary;
		$data['assignee'] = $this->assignee;
		$data['peerreviewer'] = $this->peerreviewer;
		$data['type'] = $this->type;
		$data['datereview'] = $this->datesentforpeereview;
		if (isset($this->id)) {
			$data['id'] = $this->id;
			if (isset($this->reviewed)) {
				$data['reviewed'] = $this->reviewed;
				$data['datecompleted'] = $this->datecompleted;
			}
			$DB->update_record('issues', $data); 
		} else {
			$this->id = $DB->insert_record('issues', $data);
		}
	}

	public function get_id() {
		return $this->id;
	}

	public function calculate_score() {
		$assigneescore = ($this->is_hq_member()) ? self::ASSIGNEE_HQ : self::ASSIGNEE_OTHER;
		$typescore = ($this->type == 'Improvement' || $this->type == 'New Feature') ? self::ISSUE_TYPE_IMPROVEMENT : self::ISSUE_TYPE_OTHER;
		$dayswaiting = $this->get_days_waiting();
		$score = self::BASE_SCORE + $assigneescore + $typescore + $dayswaiting;
		return $score;
	}

	public function get_mdl($url = true) {
		if ($url) {
			return '<a href="https://tracker.moodle.org/browse/' . $this->mdl . '">' . $this->mdl . '</a>';
		}
		return $this->mdl;
	}

	public function get_summary() {
		return $this->summary;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_assignee() {
		return $this->assignee;
	}

	public function get_peer_reviewer() {
		return $this->peerreviewer;
	}

	public function get_date_sent_for_review() {
		return $this->datesentforpeereview;
	}

	public function set_date_sent_for_review($date) {
		$this->datesentforpeereview = $date;
	}

	public function set_issue_as_reviewed() {
		$DB = new DB();
		$this->reviewed = 1;
		$this->datecompleted = time();

		$sql = "SELECT i.id, i.datereview, i.peerreviewer, u.id as userid
				  FROM issues i
			 LEFT JOIN users u ON i.peerreviewer = u.username
				 WHERE i.mdl = :mdl  AND (i.reviewed <> 1 OR i.reviewed IS NULL)";

		$result = $DB->execute_sql($sql, ['mdl' => $this->get_mdl(false)]);
		foreach ($result as $key => $value) {
			$this->id = $value->id;
			// Set all entries for this MDL to reviewed.
			$mdlissue = [
				'id' => $value->id,
				'reviewed' => 1,
				'datecompleted' => $this->datecompleted
			];
			if (isset($value->userid)) {
				$this->set_date_sent_for_review($value->datereview);
				$score = $this->calculate_score();

				$userissue = [
					'userid' => $value->userid,
					'issueid' => $this->id,
					'points' => $score,
					'datecompleted' => $this->datecompleted,
					'rewardid' => 0,
					'rewardclaimed' => 0
				];
				// print_object($userissue);
				// save score to user.
				$DB->insert_record('userissues', $userissue);
			}
			$DB->update_record('issues', $mdlissue);
		}
		

	}

	public function get_days_waiting() {
		$startdate = new DateTime();
		$startdate->setTimestamp($this->datesentforpeereview);
		$timenow = new DateTime('now');
		$diff = $startdate->diff($timenow);
		return $diff->format('%a');
	}

    public function is_hq_member() {
        $DB = new DB();

        $hqmembers = $DB->get_records('users', ['hqmember' => 1]);
        foreach ($hqmembers as $name) {
            if ($name->username == $this->assignee) {
                return true;
            }
        }
        return false;
    }
}


?>