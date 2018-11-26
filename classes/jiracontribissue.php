<?php

class jira_contrib_issue {

    protected $id;
    protected $contrib;
    protected $summary;
    protected $assignee;
    protected $datesentforpeereview;
    protected $reviewed = null;
    protected $datecompleted = null;

    const BASE_SCORE = 14;

    public function __construct($contrib, $summary, $assignee, $datereview = null, $id = null) {
        $this->contrib = $contrib;
        $this->summary = $summary;
        $this->assignee = $assignee;
        $this->datesentforpeereview = $datereview ?? time();
        $this->id = $id;
    }

    public static function load_from_raw_data($data) {
        $DB = new DB;

        $contrib = $data->key;
        $summary = $data->fields->summary;
        $assignee = (isset($data->fields->assignee->name)) ? $data->fields->assignee->name : '';

        // Should check to see if this issue exists and ammend that record.
        $sql = "SELECT *
                FROM plugin_issues
                WHERE contrib = :contrib AND (reviewed <> 1 OR reviewed IS NULL)";
        $records = $DB->execute_sql($sql, ['contrib' => $contrib]);

        // If we get back a record then we should load that.
        if (!empty($records)) {
            return new jira_contrib_issue($contrib, $summary, $assignee, $records[0]->datereview, $records[0]->id);
        }
        return new jira_contrib_issue($contrib, $summary, $assignee);
    }

    public static function load_from_id($idnumber) {
        $DB = new DB();
        $result = $DB->get_records('plugin_issues', ['id' => $idnumber]);
        return new jira_plugin_issue($result[0]->contrib, $result[0]->summary, $result[0]->assignee,
                $result[0]->datereview, $result[0]->id);
    }

    public function save() {
        $DB = new DB();
        $data = [];
        $data['contrib'] = $this->contrib;
        $data['summary'] = $this->summary;
        $data['assignee'] = $this->assignee;
        $data['datereview'] = $this->datesentforpeereview;
        if (isset($this->id)) {
            $data['id'] = $this->id;
            if (isset($this->reviewed)) {
                $data['reviewed'] = $this->reviewed;
                $data['datecompleted'] = $this->datecompleted;
            }
            $DB->update_record('plugin_issues', $data);
        } else {
            $this->id = $DB->insert_record('plugin_issues', $data);
        }
    }

    public function get_id() {
        return $this->id;
    }

    public function calculate_score() {
        $dayswaiting = $this->get_days_waiting();
        $score = self::BASE_SCORE + $dayswaiting;
        return $score;
    }

    public function get_contrib($url = true) {
        if ($url) {
            return '<a href="https://tracker.moodle.org/browse/' . $this->contrib . '">' . $this->contrib . '</a>';
        }
        return $this->contrib;
    }

    public function get_summary() {
        return $this->summary;
    }

    public function get_assignee() {
        return $this->assignee;
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

        $sql = "SELECT i.id, i.datereview, i.assignee, u.id as userid
                  FROM plugin_issues i
             LEFT JOIN users u ON i.assignee = u.username
                 WHERE i.contrib = :contrib  AND (i.reviewed <> 1 OR i.reviewed IS NULL)";

        $result = $DB->execute_sql($sql, ['contrib' => $this->get_contrib(false)]);
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
                    'rewardclaimed' => 0,
                    'gold' => 0
                ];
                // print_object($userissue);
                // save score to user.
                $DB->insert_record('user_plugin_issues', $userissue);
            }
            $DB->update_record('plugin_issues', $mdlissue);
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