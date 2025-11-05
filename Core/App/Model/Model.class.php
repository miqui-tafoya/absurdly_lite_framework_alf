<?php

namespace Model;

abstract class Model {

    public function fetchData($data) {
        $response = [];
        foreach ($data as $key => $value) {
            if (property_exists($this, $value)) {
                $response[$value] = $this->$value;
            }
        }
        return $response;
    }
    public function fetchQueryStringData($key,$value) {
        $response = [];
        if (method_exists($this, $key)) {
            $response = $this->$key($value);
        } else { // si no existe método válido para string query realizar la siguiente acción:
            header("Location: " . URL_BASE);
        }
        return $response;
    }
}
