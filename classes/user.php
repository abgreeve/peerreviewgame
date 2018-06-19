<?php
class user {

    protected $id;
    protected $username;
    protected $displayname;
    protected $xp;
    protected $gold;
    protected $hqmember;
    protected $accesslevel;

    public function __construct($userrecord) {
        foreach ($userrecord as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function load_from_id($userid) {
        $DB = new DB();
        $records = $DB->get_records('users', ['id' => $userid]);
        if (count($records) > 1) {
            throw new Exception("Too many user records returned", 1);
        }
        $userrecord = array_shift($records);
        unset($userrecord->password);
        return new user($userrecord);
    }

}
?>