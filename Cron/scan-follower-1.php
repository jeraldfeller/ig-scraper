<?php
include "../Model/Init.php";
include "../Model/Model.php";
include "../Model/simple_html_dom.php";
$model = new Model();

$activeScanCount = $model->getActiveScanCount();

if($activeScanCount[0]['content'] == 5){
//    exit;
}
$following = $model->getFollowingToScan('ASC');

$model->setActiveScanCount('+1');
$i = 1;
foreach($following as $row){
    $id = $row['id'];
    $handle = $row['handle'];
    print_r("To scan: $handle\n");
    $userid = $row['userid'];
    $parentId = $row['followed_userid'];

    $rules = $model->getRules($parentId);
    $rules = json_decode($rules[0]['rules'], true);


    $url = "https://i.instagram.com/api/v1/users/$userid/info/";
    $rsp = $model->curlToIg($handle);
    $rsp = json_decode($rsp, true);
    if(!isset($rsp['graphql']['user'])){
        var_dump($rsp);
        continue;
    }else{
        $user = $rsp['graphql']['user'];
    }



    if(!isset($user['edge_followed_by']['count'])){
        continue;
    }else{
        $followerCount = $user['edge_followed_by']['count'];
        $followingCount = $user['edge_follow']['count'];
        $email = (isset($user['public_email']) ? $user['public_email'] : '');

        print_r($i . ". " . $userid . " - $handle - $followerCount \n");

        if($rules['number_followers']['operator'] == '>'){
            if($followerCount < $rules['number_followers']['max']){
                $model->deleteInfo('following_user', $id);
                continue;
            }
        }

        if($rules['number_followers']['operator'] == '<'){
            if($followerCount > $rules['number_followers']['max']){
                $model->deleteInfo('following_user', $id);
                continue;
            }
        }

        if($rules['number_followers']['operator'] == '<>'){
            if($followerCount > $rules['number_followers']['min'] && $followerCount < $rules['number_of_followers']['max']){
            }else{
                $model->deleteInfo('following_user', $id);
                continue;
            }
        }





        $model->updateInfo('following_user', $id, [
            'no_followers' => $followerCount,
            'no_following' => $followingCount,
            'email' => $email
        ]);

        $isExists = $model->isExists('user_metadata', $userid);
        if(!$isExists){
            $model->inserInfo('user_metadata', [
                'userid' => $userid,
                'data' => addslashes(json_encode($user))
            ]);
        }

    }

    $i++;

}

$model->setActiveScanCount('-1');