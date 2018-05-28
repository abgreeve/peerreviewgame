<?php

include "reward.php";

class reward_manager {

    public function get_unclaimed_reward_issues($userid, $competition) {
        $DB = new DB();

        $sql = "SELECT ui.*
                  FROM users u
                  JOIN userissues ui ON ui.userid = u.id AND rewardclaimed = 0
                  JOIN competition_time ct ON ui.datecompleted BETWEEN ct.starttime AND ct.endtime
                 WHERE u.id = :userid
                   AND ct.id = :competitionid";

        $params = ['userid' => $userid, 'competitionid' => $competition->get_id()];
        $records = $DB->execute_sql($sql, $params);
        return $records;
    }

    public function get_unclaimed_reward_contrib_issues($userid, $competition) {
        
    }

    public function get_reward($issuetype, $userissueid, $userid, $competition) {
        // Double check that user was the peer reviewer and that the reward has not yet been claimed.
        // Generate a random amount of gold.
        $gold = rand(10, 25);
        $numberofcards = rand(1, 3);
        $cardmanager = new card_manager();
        $cardset = $cardmanager->get_card_set($competition);
        $cardsetcount = count($cardset);
        $cardcollection = [];
        for ($i = 0; $i <= $numberofcards; $i++) {
            $card = $cardset[rand(0,$cardsetcount - 1)];
            $cardcollection[] = $card;
            // $cardcollection[] = $card->get_id();
            // $cardcollection[] = ['id' => $card->get_id(), 'name' => $card->get_name()];
        }

        $DB = new DB();
        // Update the relevant tables.
        // User's gold is increased. Need to get the user's current gold count.
        $userrecord = $DB->get_records('users', ['id' => $userid]);
        $user = array_shift($userrecord);
        $user->gold = $user->gold + $gold;
        $DB->update_record('users', $user);

        // Add cards to user's inventory.
        foreach ($cardcollection as $card) {
            $DB->insert_record('user_inventory', ['userid' => $userid, 'cardid' => $card->get_id()]);
        }

        // Update the issue to say that it's been collected.
        if ($issuetype == 1) {
            $DB->update_record('userissues', ['id' => $userissueid, 'rewardclaimed' => 1, 'gold' => $gold]);
        }
        if ($issuetype == 2) {
            $DB->update_record('user_plugin_issues', ['id' => $userissueid, 'rewardclaimed' => 1, 'gold' => $gold]);
        }

        return ['gold' => $gold, 'cards' => $cardcollection];
    }

}

?>