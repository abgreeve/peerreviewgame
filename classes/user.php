<?php
class user {

    protected $id;
    protected $username;
    protected $displayname;
    protected $xp;
    protected $gold = 0;
    protected $hqmember;
    protected $accesslevel;

    protected $DB;

    public function __construct($userrecord, $database) {
        $this->DB = $database;
        foreach ($userrecord as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function load_from_id($userid, $database) {
        $records = $database->get_records('users', ['id' => $userid]);
        if (count($records) > 1) {
            throw new Exception("Too many user records returned", 1);
        }
        $userrecord = array_shift($records);
        unset($userrecord->password);
        return new user($userrecord, $database);
    }

    public function get_gold() {
        return $this->gold;
    }

    public function get_inventory() {
        if (!isset($this->id)) {
            throw new Exception('Please set ID for this user before calling this function.');
        }

        $sql = "SELECT c.id, c.cardname, COUNT(ui.cardid) as amount
                  FROM user_inventory ui
                  JOIN cards c ON c.id = ui.cardid
                 WHERE ui.userid = :userid
              GROUP BY ui.cardid";
        $records = $this->DB->execute_sql($sql, ['userid' => $this->id]);
        // This should be in the parameters.
        $cardmanager = new card_manager($this->DB);
        $cards = array_map(function($record) use ($cardmanager) {
            return [
                'card' => $cardmanager->get_card($record->cardname, $record->id),
                'amount' => $record->amount
            ];
        }, $records);
        return ['gold' => $this->get_gold(), 'cards' => $cards];
    }

    public function update_password($currentpassword, $newpassword) {

    }
}
?>