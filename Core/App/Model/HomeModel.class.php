<?php

namespace Model;

use Model\Database;

class HomeModel extends Model {

    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    // GETTERS

    // SETTERS


    // QUERYSTRING
    public function queryStringHandler($querystrings) {

    }
}
