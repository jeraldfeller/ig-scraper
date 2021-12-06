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
        'numbering' => $data[0],
        'vehlaApproved' => $data[1],
        'igUsername' => $data[2],
        'approved' => $data[3],
        'userid' => $data[4],
        'veri' => $data[5],
        'noFollowers' => $data[6],
        'noFollowed' => $data[7],
        'email' => $data[8],
        'fullName' => $data[9],
        'country' => $data[10],
        'reason' => $data[11],
        'avatar' => $data[12],
        'transferred' => $data[13]
    ];

    $model->importData([
        'numbering' => $data[0],
        'vehlaApproved' => $data[1],
        'igUsername' => $data[2],
        'approved' => $data[3],
        'userid' => $data[4],
        'veri' => $data[5],
        'noFollowers' => $data[6],
        'noFollowed' => $data[7],
        'email' => $data[8],
        'fullName' => $data[9],
        'country' => $data[10],
        'reason' => $data[11],
        'avatar' => $data[12],
        'transferred' => $data[13]
    ]);

}

$_SESSION['data'] = $lists;

echo json_encode($lists);