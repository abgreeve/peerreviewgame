<?php

include "jiraissue.php";
include "jiracontribissue.php";
include "competition_time.php";

class manager {


    public function __construct() {
        // Not much.
    }

    public function update_issues() {
        // Get all of the issues numbers.
        $mdlstring = $this->get_all_active_mdls();

        $jira = new jira();
        $searchstring = 'project = "MDL" and status="waiting for peer review" and key not in ('. $mdlstring . ')';
        $result = $jira->search($searchstring);
        // print_object($result);
        foreach ($result->issues as $rawdata) {
            $issue = jira_issue::load_from_raw_data($rawdata);
            $issue->save();
        }
    }

    public function update_plugin_issues() {
        $contribrecords = $this->get_plugin_issues(false);
        $contribids = array_map(function($record) {
            return $record->contrib;
        }, $contribrecords);

        $contribstring = implode(',', $contribids);

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

    public function get_scores() {
        $DB = new DB();
        $sql = "SELECT u.displayname, sum(ui.points) as Points
                  FROM `userissues` ui
                  JOIN users u ON ui.userid = u.id
              GROUP BY u.displayname";
        $results = $DB->execute_sql($sql);
        return $results;
    }

    public function get_active_competition_time() {
        $DB = new DB();
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

}


?>