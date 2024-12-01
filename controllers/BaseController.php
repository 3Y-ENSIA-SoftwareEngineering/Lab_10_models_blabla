<?php
class Controller {
    public function loadModel($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    public function loadView($view, $data = []) {
        require_once __DIR__ . '/../views/' . $view . '.php';
    }
}
?>
