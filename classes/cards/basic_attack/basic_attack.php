<?php

// include_once 'cardInterface.php';
// use cardInterface as cInterface;
// 
// interface cardInterface {

//     public function get_description();

// }

class basic_attack {

    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return 'Basic attack.';
    }

    public function get_description() {
        return 'Does a basic attack against the oppositions fortress';
    }

    public function play_card() {
        
    }

}


?>