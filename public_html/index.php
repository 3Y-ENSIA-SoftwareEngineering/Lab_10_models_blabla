<?php

require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../utils/http.php';

$vars=get_input_vars();

$controller = ucfirst($vars['module'] ?: 'expense') . 'Controller';
$action = $vars['action'] ?: 'index';


require_once __DIR__ . '/../controllers/' . $controller . '.php';

$instance = new $controller();
call_user_func([$instance, $action], $vars);

?>
