<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();

$id = $_GET['id'];
$mark = $_GET['mark'];

$model->markData($id, $mark);


echo 1;