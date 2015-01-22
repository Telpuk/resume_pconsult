<?php
header('Content-Type: text/html;charset=UTF-8');
require_once __DIR__ . '/source/includes_class.php';

$app = FrontController::getInstance();

$app->run();
echo $app->getBody();
exit;

