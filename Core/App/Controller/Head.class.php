<?php

namespace Controller;

use View\Render;
use Model\HeadModel;

class Head extends Render {

    public function values($params) {
        $clase = explode('\\', __CLASS__);
        $buildClass = '\\Model\\' . ucfirst($clase[1]) . 'Model';
        $load = new $buildClass;
        $valores = $load->fetchData($params);
        return $valores;
    }
}