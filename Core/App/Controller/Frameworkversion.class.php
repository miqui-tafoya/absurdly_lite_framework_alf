<?php
namespace Controller;
use Middleware;

class Frameworkversion {
    public function GET_handler($route, $params, $js) {
        $mid = new Middleware;
        header("Access-Control-Allow-Origin: " . $mid->getCorsOrigin('*'));
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        $status = (file_exists(APP_ROOT . DIRECTORY_SEPARATOR . 'man.on')) ? 'down' : 'up';
        $environment = (PRODUCTION === false) ? 'development' : 'production';
        $version = [
            "version" => ALF_VERSION,
            "status" => $status,
            "environment" => $environment,
            "api_family" => "v1"
        ];
        echo json_encode(['success' => true, 'data' => $version]);
    }

    public function values($params) {
        $clase = explode('\\', __CLASS__);
        $buildClass = '\\Model\\' . ucfirst($clase[1]) . 'Model';
        $load = new $buildClass();
        $valores = $load->fetchData($params);
        return $valores;
    }
}