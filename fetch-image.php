<?php
$path = $_POST['imageUrl'];

$images = [];
for($i = 0; $i < count($path); $i++){
    $type = pathinfo($path[$i], PATHINFO_EXTENSION);
    $data = file_get_contents($path[$i]);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $images[] = $base64;
}

echo json_encode($images);