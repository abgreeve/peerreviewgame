<?php

include "jiraissue.php";
include "jiracontribissue.php";
include "competition_time.php";
include "team.php";
// include '../database/database.class.php';

class manager {

    public function update_issues() {
        // Get all of the issues numbers.
        $mdlstring = $this->get_all_active_mdls();

        $jira = new jira();
        $searchstring = 'project = "MDL" and status="waiting for peer review" and key not in ('. $mdlstring . ')';
        $result = $jira->search($searchstring);

        foreach ($result->issues as $rawdata) {
            $issue = jira_issue::load_from_raw_data($rawdata);
            $issue->save();
        }
    }

    public function update_plugin_issues() {
        $contribstring = $this->get_all_active_contrib();

        $jira = new jira();
        if (!empty($contribstring)) {
            $searchstring = 'project = "CONTRIB" and status="to do" and key not in (' . $contribstring . ')';
        } else {
            $searchstring = 'project = "CONTRIB" and status="to do"';
        }

        $result = $jira->search($searchstring);
        foreach ($result->issues as $rawdata) {
            $issue = jira_contrib_issue::load_from_raw_data($rawdata);
            $issue->save();
        }
    }

    public function get_plugin_issues($formatted = true) {
        $DB = new DB();
        $sql = "SELECT * FROM plugin_issues WHERE reviewed is null or reviewed = 0";
        $records = $DB->execute_sql($sql);
        if ($formatted) {
            $issues = array_map(function($record){
                $issue = new jira_contrib_issue($record->contrib, $record->summary, $record->assignee, $record->datereview, $record->id);
                return [
                    'Contrib' => $issue->get_contrib(),
                    'Summary' => $issue->get_summary(),
                    'Assignee' => $issue->get_assignee(),
                    'Days waiting' => $issue->get_days_waiting(),
                    'Score' => $issue->calculate_score()
                ];
            }, $records);
            return $issues;
        }
        return $records;
    }

    public function get_all_active_mdls() {
        $issues = $this->get_issues(false);
        $issuearray = array_map(function($record) {
            $issue = new jira_issue($record->mdl, $record->summary, $record->assignee, $record->peerreviewer, $record->type, $record->datereview, $record->id);
            return $issue->get_mdl(false);
        }, $issues);
        return implode(',', $issuearray);
    }

    public function get_all_active_contrib() {
        $contribrecords = $this->get_plugin_issues(false);
        $contribids = array_map(function($record) {
            return $record->contrib;
        }, $contribrecords);

        return implode(',', $contribids);
    }

    public function get_issues($formatted = true) {
        $DB = new DB();
        $sql = "SELECT * FROM issues WHERE reviewed is null or reviewed = 0";
        $records = $DB->execute_sql($sql);
        if ($formatted) {
            $issues = [];
            foreach ($records as $record) {
                $issue = new jira_issue($record->mdl, $record->summary, $record->assignee, $record->peerreviewer, $record->type, $record->datereview, $record->id);
                $tmep = [];
                $tmep['MDL'] = $issue->get_mdl();
                $tmep['Summary'] = $issue->get_summary();
                $tmep['Assignee'] = $issue->get_assignee();
                $tmep['Peer reviewer'] = $issue->get_peer_reviewer();
                $tmep['HQ'] = ($issue->is_hq_member()) ? 'TRUE' : 'FALSE';
                $tmep['type'] = $issue->get_type();
                $tmep['Days waiting'] = $issue->get_days_waiting();
                $tmep['score'] = $issue->calculate_score();
                $tmep['edit'] = '<a href="edit.php?id=' . $issue->get_id() . '">Edit</a>';
                $issues[] = $tmep;
            }
            return $issues;
        }
        return $records;
    }

    public function get_issue($id) {
        $DB = new DB();
        $record = $DB->get_records('issues', ['id' => $id]);
        $issue = new jira_issue($record[0]->mdl, $record[0]->summary, $record[0]->assignee, $record[0]->peerreviewer, $record[0]->type, $record[0]->datereview, $record[0]->id);
        return $issue;

    }

    public function check_for_peer_reviewed_issues() {
        $mdlstring = $this->get_all_active_mdls();

        $jira = new jira();
        $searchstring = 'project = "MDL" and status not in ("waiting for peer review") and key in ('. $mdlstring . ')';
        $result = $jira->search($searchstring);
        foreach ($result->issues as $rawdata) {
            $issue = jira_issue::load_from_raw_data($rawdata);
            $issue->save();
            $issue->set_issue_as_reviewed();
            $issue->save();
        }
    }

    public function check_for_peer_reviewed_plugin_issues() {
        $contribstring = $this->get_all_active_contrib();

        $jira = new jira();
        $searchstring = 'project = "CONTRIB" and status not in ("to do") and key in (' . $contribstring . ')';
        $result = $jira->search($searchstring);
        foreach ($result->issues as $rawdata) {
            $issue = jira_contrib_issue::load_from_raw_data($rawdata);
            $issue->save();
            $issue->set_issue_as_reviewed();
            $issue->save();
        }
    }

    public function get_issue_scores(competition_time $comptime = null) {
        $where = '';
        $params = [];
        if ($comptime) {
            $where = 'WHERE datecompleted BETWEEN :startdate AND :enddate';
            $params = ['startdate' => $comptime->get_starttime('', false), 'enddate' => $comptime->get_endtime('', false)];
        }
        $DB = new DB();
        $sql = "SELECT u.username, u.displayname, sum(ui.points) as Points
                  FROM `userissues` ui
                  JOIN users u ON ui.userid = u.id
                  $where
              GROUP BY u.username";
        $results = $DB->execute_sql($sql, $params);
        return $results;
    }

    public function get_contrib_scores(competition_time $comptime = null) {
        $where = '';
        $params = [];
        if ($comptime) {
            $where = 'WHERE datecompleted BETWEEN :startdate AND :enddate';
            $params = ['startdate' => $comptime->get_starttime('', false), 'enddate' => $comptime->get_endtime('', false)];
        }
        $DB = new DB();
        $sql = "SELECT u.username, u.displayname ,sum(ui.points) as Points
                  FROM `user_plugin_issues` ui
                  JOIN users u ON ui.userid = u.id
                  $where
              GROUP BY u.username";
        $results = $DB->execute_sql($sql, $params);
        return $results;
    }

    public function get_active_competition_time() {
        $DB = new DB();
        $sql = "SELECT *
                  FROM competition_time
                 WHERE :currenttime BETWEEN starttime AND endtime ";
        $results = $DB->execute_sql($sql, ['currenttime' => time()]);
        if (count($results) > 1) {
            throw new Exception("Too many results for competition time, check the db for multiple records", 1);
        }
        $result = array_shift($results);
        return new competition_time($result->title, $result->starttime, $result->endtime, $result->id);
    }

    public function get_all_competition_periods($formatted = true) {
        $DB = new DB();
        $records = $DB->get_records('competition_time');
        $times = array_map(function($record) {
            return new competition_time($record->title, $record->starttime, $record->endtime, $record->id);
        }, $records);
        if ($formatted) {
            return array_map(function($time) {
                return [
                    'Title' => $time->get_title(),
                    'Start time' => $time->get_starttime(),
                    'End time' => $time->get_endtime(),
                    'Edit' => '<a href="timeedit.php?id='. $time->get_id() .'">Edit</a>'
                ];
            }, $times);
        } else {
            return $times;
        }
    }

    public function save_completion_time($title, $starttime, $endtime, $id = '') {
        if (empty($id)) {
            $id = null;
        }
        // Set dates to timestamp.
        $startdate = DateTime::createFromFormat('d-m-Y', $starttime);
        $startdate->setTime(0, 0, 0);
        $enddate = DateTime::createFromFormat('d-m-Y', $endtime);
        $enddate->setTime(23, 59, 59);
        $completiontime = new competition_time($title, $startdate->getTimestamp(), $enddate->getTimestamp(), $id);
        $completiontime->save();
    }

    public function get_hq_completed_issues() {
        $DB = new DB();
        // We need to sort out what's going on with time. I've increased the size of the fields to make sure that they
        // are not getting truncated that way.
        // print_object(time());
        $sql = "SELECT i.*, ui.points
                  FROM issues i
                  JOIN userissues ui ON ui.issueid = i.id
                 WHERE reviewed = 1";
        $results = $DB->execute_sql($sql);
        $display = array_map(function($record) {
            return [
                'MDL' => '<a href="https://tracker.moodle.org/browse/' . $record->mdl . '">' . $record->mdl . '</a>',
                'Summary' => $record->summary,
                'Peer reviewer' => $record->peerreviewer,
                'Date completed' => $record->datecompleted,
                'Points' => $record->points
            ];
        }, $results);
        return $display;
    }

    public function get_current_teams($competitiontime = null) {
        $DB = new DB();
        $competition = (isset($competitiontime)) ? $competitiontime : $this->get_active_competition_time();
        $params = ['competitionid' => $competition->get_id()];
        $teams = $DB->get_records('teams', $params);
        return array_map(function($team) {
            return new team($team->teamname, $team->competitionid, $team->fortressname, $team->hitpoints, $team->id);
        }, $teams);
    }

    

    public function get_hq_members() {
        $DB = new DB();

        return $DB->get_records('users', ['hqmember' => 1]);
    }

    public function get_other_issues() {
        // Get all of the issues numbers.
        $mdlstring = $this->get_all_active_mdls();

        $jira = new jira();
        $searchstring = 'project = "MDL" and status!="waiting for peer review" and key in ('. $mdlstring . ')';
        $result = $jira->search($searchstring);

        foreach ($result->issues as $rawdata) {
            $issue = jira_issue::load_from_raw_data($rawdata);
            print_object($issue);
        }
    }

}

?>