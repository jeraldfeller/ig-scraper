<?php

include "../Model/Init.php";
include "../Model/Model.php";
include "../Model/simple_html_dom.php";
$model = new Model();

$activeScanCount = $model->getActiveScanCount();

if($activeScanCount[0]['content'] == 5){
//    exit;
}
$following = $model->getFollowingToScan();

echo '<a id="handle" href="https://www.instagram.com/'.$following[0]['handle'].'/?__a=1">'.$following[0]['handle'].'</a>';