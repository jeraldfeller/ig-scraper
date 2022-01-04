<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();
$fileHandle = fopen($_FILES['file']['tmp_name'], "r");
$i = 0;
$flag = true;

$lists = [];

while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
    if ($flag) {
        $flag = false;
        continue;
    }


    $data[] = [
        'igUsername' => $data[0],
        'veri' => $data[1],
        'noFollowers' => $data[2],
        'noFollowed' => $data[3],
        'email' => $data[4],
        'fullName' => $data[5],
        'avatar' => $data[7],
    ];

    $model->importData([
        'igUsername' => $data[0],
        'veri' => $data[1],
        'noFollowers' => $data[2],
        'noFollowed' => $data[3],
        'email' => $data[4],
        'fullName' => $data[5],
        'avatar' => $data[7],
    ]);

}

$_SESSION['data'] = $lists;

echo json_encode($lists);