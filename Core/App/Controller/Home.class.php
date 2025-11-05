<?php

namespace Controller;

use View\Render;
use Model\Database;

class Home extends Render {

    private Database $db;

    public function __construct() {
        // $this->db = new Database();
    }

    public function GET_handler($route, $params, $js) {
        return $this->renderView($route, $params[0], $params[1], $params[2], $params[3], $js);
    }

    public function values($params) {
        $clase = explode('\\', __CLASS__);
        $buildClass = '\\Model\\' . ucfirst($clase[1]) . 'Model';
        $load = new $buildClass;
        $valores = $load->fetchData($params);
        return $valores;
    }
}