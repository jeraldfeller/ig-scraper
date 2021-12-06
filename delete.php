<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();

if($_GET['pass'] == 'vehla1'){

    $model->deleteImportedData();
    echo 1;
}else{
    echo 0;
}
