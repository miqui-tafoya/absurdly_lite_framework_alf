<?php

namespace Model;

class HeadModel extends Model {
    private Database $db;
    public $notificacionessifico;

    public function __construct() {
        $this->db = new Database();
        $this->setNotificacionesSifico();
    }

    // GETTERS
    private function getNotificacionesSifico() {
        $result = 5;
        return $result;
    }
    // SETTERS   
    public function setNotificacionesSifico() {
        $data = $this->getNotificacionesSifico();
        $this->notificacionessifico = $data;
    }
}
