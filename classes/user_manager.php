<?php

class user_manager {

    protected $userid;

    protected $DB;

    public function __construct($userid, DB $database) {
        $this->userid = $userid;
        $this->DB = $database;
    }

    //
    public function get_completed_issues(competition_time $competition = null) {
        $params = ['userid' => $this->userid];

        $competitionsql = '';
        if (isset($competition)) {
            $competitionsql = "AND ui.datecompleted BETWEEN :starttime AND :endtime";
            $params['starttime'] = $competition->get_starttime('', false);
            $params['endtime'] = $competition->get_endtime('', false);
        }

        $sql = "SELECT ui.*, i.summary, i.mdl
                FROM userissues ui
                JOIN issues i ON ui.issueid = i.id
                WHERE ui.userid = :userid $competitionsql";
        $records = $this->DB->execute_sql($sql, $params);
        return $records;
    }

}