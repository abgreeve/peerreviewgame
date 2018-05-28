<?php

include_once dirname(dirname(__FILE__)) . '/session/lib.php';

class navigation {

    protected $html;

    public function __construct($currentpage) {
        $this->html = '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
        $this->html .= '<div class="collapse navbar-collapse">';
        $this->html .= '<ul class="navbar-nav">';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="reviewlist.php">Issues</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="scores.php">Scores</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="game.php">Game</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="management.php">Management</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="teams.php">Teams</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="cards.php">Cards</a>';
        $this->html .= '</li>';
        $this->html .= '<li class="nav-item">';
        $this->html .= '<a class="nav-link" href="users.php">Users</a>';
        $this->html .= '</li>';
        $this->html .= '</ul>';
        $this->html .= '</div>';
        $this->html .= '</nav>';
    }

    public function out() {
        return $this->html;
    }

}

?>
