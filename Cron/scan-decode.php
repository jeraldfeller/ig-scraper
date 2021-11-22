<?php
$data = file_get_contents('php://input');
include "../Model/Init.php";
include "../Model/Model.php";
include "../Model/simple_html_dom.php";
$model = new Model();

$rsp = json_decode($data, true);

if(!isset($rsp['graphql']['user'])){
    var_dump($rsp);
    exit;
}else{
    $user = $rsp['graphql']['user'];
}



if(!isset($user['edge_followed_by']['count'])){
    exit;
}else {

    $followerCount = $user['edge_followed_by']['count'];
    $followingCount = $user['edge_follow']['count'];
    $email = (isset($user['public_email']) ? $user['public_email'] : '');

    $userid = $user['id'];
    $userInfo = $model->getFollowerById($userid);
    $id = $userInfo[0]['id'];
    $handle = $userInfo[0]['handle'];

    $parentId = $userInfo[0]['followed_userid'];

    $rules = $model->getRules($parentId);
    $rules = json_decode($rules[0]['rules'], true);

    print_r($userid . " - $handle - $followerCount \n");

    if ($rules['number_followers']['operator'] == '>') {
        if ($followerCount < $rules['number_followers']['max']) {
            $model->deleteInfo('following_user', $id);
            exit;
        }
    }

    if ($rules['number_followers']['operator'] == '<') {
        if ($followerCount > $rules['number_followers']['max']) {
            $model->deleteInfo('following_user', $id);
            exit;
        }
    }

    if ($rules['number_followers']['operator'] == '<>') {
        if ($followerCount > $rules['number_followers']['min'] && $followerCount < $rules['number_of_followers']['max']) {
        } else {
            $model->deleteInfo('following_user', $id);
            exit;
        }
    }


    $model->updateInfo('following_user', $id, [
        'no_followers' => $followerCount,
        'no_following' => $followingCount,
        'email' => $email
    ]);

    $isExists = $model->isExists('user_metadata', $userid);
    if (!$isExists) {
        $model->inserInfo('user_metadata', [
            'userid' => $userid,
            'data' => addslashes(json_encode($user))
        ]);
    }
}