<?php

class team {

    protected $id;
    protected $teamname;
    protected $fortressname;
    protected $hitpoints;
    protected $competitionid;

    public function __construct($teamname, $competitionid, $fortressname, $hitpoints, $id = null) {
        $this->teamname = $teamname;
        $this->competitionid = $competitionid;
        $this->fortressname = $fortressname;
        $this->hitpoints = $hitpoints;
        $this->id = $id;
    }

    public function set_team_name($teamname) {
        $this->teamname = $teamname;
    }

    public function get_all_params() {
        return [
            'id' => $this->id,
            'Team name' => $this->teamname,
            'Fortress name' => $this->fortressname,
            'Hit points' => $this->hitpoints,
            'competition id' => $this->competitionid
        ];
    }

    public function get_fortress_name() {
        return $this->fortressname;
    }

    public function get_hp() {
        return $this->hitpoints;
    }

    public function get_team_name() {
        return $this->teamname;
    }


    // public function add_team_member(int $userid) {

    // }

    public function add_team_members(array $userids) {
        $DB = new DB();
        $DB->delete_records('team_members', ['teamid' => $this->id]);
        $data = ['teamid' => $this->id];
        foreach ($userids as $userid) {
            $data['userid'] = $userid;
            $DB->insert_record('team_members', $data);
        }
    }

    public function get_team_members() {
        $DB = new DB();
        
        $sql = "SELECT u.*
                  FROM teams t
                  JOIN team_members tm ON tm.teamid = t.id
                  JOIN users u ON u.id = tm.userid
                 WHERE t.id = :id";
        $params = ['id' => $this->id];
        return $DB->execute_sql($sql, $params);
    }

    public function save() {
        $DB = new DB();
        $data = [
            'teamname' => $this->teamname,
            'competitionid' => $this->competitionid,
            'fortressname' => $this->fortressname,
            'hitpoints' => $this->hitpoints
        ];
        if (isset($this->id)) {
            $data['id'] = $this->id;
            $DB->update_record('teams', $data);
        } else {
            $this->id = $DB->insert_record('teams', $data);
        }
    }
}

?>