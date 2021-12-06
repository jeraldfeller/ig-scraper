<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();

if($_GET['pass'] == 'vehla1'){

    $model->deleteImportedData();
    $model->deleteImportedDataBlog();
    echo 1;
}else{
    echo 0;
}
