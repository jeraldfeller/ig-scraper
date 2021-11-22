<?php
include "../Model/Init.php";
include "../Model/Model.php";
include "../Model/simple_html_dom.php";
$model = new Model();

$handles = $model->getHandleToScan();

foreach($handles as $row){
    $handle = $row['handle'];
    $scrapeRunning = $row['scrape_running'];
    $userid = $row['userid'];
    $id = $row['id'];
    $nextMaxId = $row['next_max_id'];
    $follower_scan_count = $row['follower_scan_count'];
    $rules = json_decode($row['rules'], true);

    print_r("SCAN: " . $handle . "\n");

    $model->updateInfo('followed_user', $id, [
        'scrape_running' => 1
    ]);

    if(!$userid){
        $user = $model->getUserCurl($handle);
        $model->updateInfo('followed_user', $id, [
            'userid' => $user['id'],
            'full_name' => $user['full_name'],
            'profile_pic_url' => $user['profile_pic_url_hd']
        ]);


        $isExists = $model->isExists('user_metadata', $user['id']);
        if(!$isExists){
            $model->inserInfo('user_metadata', [
                'userid' => $user['id'],
                'data' => addslashes(json_encode($user))
            ]);
        }
    }

    // we have now userid, next scan followers
    $needVerified = $rules['verified'];

    if($nextMaxId){
        $curlUrl = "https://i.instagram.com/api/v1/friendships/$userid/followers/?count=10000&max_id=$nextMaxId&search_surface=follow_list_page";
    }else{
        $curlUrl = "https://i.instagram.com/api/v1/friendships/$userid/followers/?count=10000&search_surface=follow_list_page";
    }



    $rsp = $model->curlTo($curlUrl);
    $rsp = json_decode($rsp, true);


    if(!isset($rsp['next_max_id'])){
        if($rsp == null){
            $model->updateInfo('followed_user', $id, [
            'scrape_running' => 0
        ]);
        }elseif(isset($rsp['spam'])){
            if($rsp['spam'] == true){
                $model->updateInfo('followed_user', $id, [
                    'scrape_running' => 0
                ]);
            }
        }else{
            var_dump($rsp);
//        $model->updateInfo('followed_user', $id, [
//            'scrape_running' => 0
//        ]);
            break;
        }

    }
    $nextMaxId = $rsp['next_max_id'];
    $c = 1;
    if($nextMaxId != ''){
        $model->updateInfo('followed_user', $id, [
            'next_max_id' => $nextMaxId
        ]);


        foreach($rsp['users'] as $r){
            $isVerified = $r['is_verified'];
            $fUserid = $r['pk'];
            $fUsername = $r['username'];
            $fFullName = $r['full_name'];
            $fProfilePicUrl = $r['profile_pic_url'];


//        echo $c . '. '. $fUsername . ' - ' . $isVerified . "\n";
            $c++;
            if($needVerified == true){
                if($isVerified == false){
                    continue;
                }
            }


//        echo $fUsername . "\n";
            $model->inserInfo('following_user', [
                'followed_userid' => $id,
                'handle' => addslashes($fUsername),
                'userid' => $fUserid,
                'verified' => ($isVerified == true ? 1 : 0),
                'full_name' => addslashes($fFullName),
                'profile_pic_url' => urlencode($fProfilePicUrl),
                'account_badges' => json_encode($r['account_badges'])
            ]);


        }

        $model->updateInfo('followed_user', $id, [
            'follower_scan_count' => $c + $follower_scan_count
        ]);
    }




    print 'Total: ' . $c . "\n";
    print "Next ID: " . $nextMaxId . "\n";
    print "----------------------------\n";

    $model->updateInfo('followed_user', $id, [
        'scrape_running' => 0
    ]);
}


