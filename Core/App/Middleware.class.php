<?php
class Middleware {
    public function Credencial($tipo) {
        switch ($tipo) {
            case 'loggedin':
                if (!empty($_SESSION['iduser'])) {
                    header('Location: ' . URL_BASE);
                    exit();
                }
                break;
            case 'login':
                if (empty($_SESSION['iduser'])) {
                    header("Location: " . URL_BASE . 'login');
                    exit();
                }
                break;
        }
    }
}