<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();
if(isset($_GET['export'])){
    $action = $_GET['action'];
    $data = $model->getImportData(0, $action, -1);
    $delimiter = ",";
    $filename = "$action" . date('Y-m-d-H-i-s') . ".csv";

// Create a file pointer
    $f = fopen('php://memory', 'w');


    $i = 1;
    $lineData = array('Numbering', 'VEHLA APPROVE?', 'IG USERNAME', 'Approved', 'USERID', 'VERIFIED', '# FOLLOWERS', '# FOLLWED', 'EMAIL', 'FULL NAME', 'Country', 'Reason', 'AVATAR' ,'Transferred');
    fputcsv($f, $lineData, $delimiter);
    foreach($data as $row){
        $lineData = array(
            $row['numbering'],
            $row['vehlaApproved'],
            $row['igUsername'],
            $row['approved'],
            $row['userid'],
            $row['verified'],
            $row['noFollowers'],
            $row['noFollowed'],
            $row['email'],
            $row['fullName'],
            $row['country'],
            $row['reason'],
            $row['avatar'],
            $row['transferred']
        );
        fputcsv($f, $lineData, $delimiter);
        $i++;
    }


// Move back to beginning of file
    fseek($f, 0);

// Set headers to download file rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
    fpassthru($f);

}else{
    $data = $_POST['data'];
    $action = $_POST['action'];
    $execute = $_POST['execute'];

    if($execute == 'save'){

        foreach($data as $row){
            $userid = $row['userid'];
            $mark = $row['mark'];
            for($i = 0; $i < count($_SESSION['data']); $i++){
                if($_SESSION['data'][$i]['userid'] == $userid){
                    $_SESSION['data'][$i]['mark'] = $mark;
                }
            }
        }
    }
}

