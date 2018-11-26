<?php

class card_manager {

    protected $files;

    protected $DB;

    public function __construct($database) {
        $this->DB = $database;
        // include all the card classes.
        $directory = 'classes/cards';
        $carddirectories = scandir($directory);
        foreach ($carddirectories as $carddirectory) {
            if (is_dir($directory . '/' . $carddirectory) && $carddirectory != '.' && $carddirectory != '..') {
                $filename = $directory . '/' . $carddirectory .'/'. $carddirectory . '.php';
                if (file_exists($filename)) {
                    include $filename;
                    $this->files[$carddirectory] = $filename;
                }

            }
        }
    }

    public function get_card($cardname, $id) {
        return new $cardname($id);
    }

    public function load_new_cards() {
        foreach ($this->files as $filename => $directory) {
            $check = $this->DB->get_records('cards', ['cardname' => $filename]);
            if (count($check) == 0) {
                $this->DB->insert_record('cards', ['cardname' => $filename]);
            }
        }
    }

    public function get_current_cards() {
        return array_map(function($cardname) use ($DB) {
            $records = $this->DB->get_records('cards', ['cardname' => $cardname]);
            $card = new $cardname($records[0]->id);
            return ['id' => $records[0]->id, 'Card name' => $card->get_name(), 'Description' => $card->get_description()];
        }, array_keys($this->files));
    }

    public function get_card_set(competition_time $ctime) {
        $sql = "SELECT c.*
                  FROM cards c
                  JOIN enabled_cards ec ON ec.cardid = c.id
                 WHERE ec.competitionid = :competitionid";
        $records = $this->DB->execute_sql($sql, ['competitionid' => $ctime->get_id()]);
        return array_map(function($record) {
            return new $record->cardname($record->id);
        }, $records);
    }

    public function add_active_cards($competitionid, array $cardids) {
        foreach ($cardids as $cardid) {
            $this->DB->insert_record('enabled_cards', ['cardid' => $cardid, 'competitionid' => $competitionid]);
        }
    }

}

?>