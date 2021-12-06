<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();

$userid = $_GET['userid'];



$avatar = $model->getImages($userid, 'avatar');

$timelines = $model->getImages($userid, 'timeline');

echo json_encode([
   'avatar' => ($avatar ? $avatar[0]['blob_data'] : 0),
    'timeline' => $timelines
]);
