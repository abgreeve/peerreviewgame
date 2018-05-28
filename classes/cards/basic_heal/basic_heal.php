<?php

// include 'card_interface.php';

// class basic_heal implements cardInterface {
class basic_heal {

    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return 'Basic heal.';
    }

    public function get_description() {
        return 'Does a basic heal for this player\'s fortress';
    }

    public function play_card() {
        // Get this teams fortress hp.
        // Increase by a certain amount.
    }
}

?>