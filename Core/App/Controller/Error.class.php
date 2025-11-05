<?php
namespace Controller;

use View\Render;
class Error extends Render {
    public function GET_handler($route, $params, $js) {
        $query = $params[2][1]['query'];
        $status = preg_replace('/[^0-9]/', '', $query);
        \http_response_code($status);
        return $this->renderView($route, $params[0], $params[1], $params[2], $params[3], $js);
    }

    public function values($params, $query) {
        $clase = explode('\\', __CLASS__);
        $buildClass = '\\Model\\' . ucfirst($clase[1]) . 'Model';
        $load = new $buildClass($query);
        $valores = $load->fetchData($params);
        return $valores;
    }
}