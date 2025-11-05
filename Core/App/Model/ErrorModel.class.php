<?php
namespace Model;

use Model\Database;

class ErrorModel extends Model {

    private Database $db;
    public array $existe;

    public function __construct($query = null) {
        $this->db = new Database();
        $this->setIds();
    }

    // SETTERS
    public function setIds() {
        $data = [403, 404, 405, 500];
        $this->existe = $data;
    }
    public function validateId($id) {
        if (empty($id)) {
            header('Location:' . URL_BASE);
            exit;
        }
        $validation = in_array((int) $id, $this->existe, true);
        return $validation;
    }
}