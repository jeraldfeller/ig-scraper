<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();

$page = $_GET['page'];
$status = $_GET['status'];
$offset = ($page - 1) * 20;
$rsp = $model->getImportData($offset, $status);


echo json_encode([
    'data' => $rsp,
    'page' => $page + 1
]);