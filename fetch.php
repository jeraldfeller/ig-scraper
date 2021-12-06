<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();
$curl = curl_init();

$data = $model->getImportDataToScrape();
foreach($data as $row){
    $cookieData = $model->getCookieApp();
    $cookie = $cookieData['cookie'];
    $user = $row['igUsername'];
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.instagram.com/'.$user.'/?__a=1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER => array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'accept-encoding: gzip, deflate, br',
            'cookie: '.$cookie,
            'sec-ch-ua: Google Chrome";v="95", "Chromium";v="95", ";Not A Brand";v="99',
            'sec-ch-ua-platform: Windows',
            'sec-fetch-dest: document',
            'sec-fetch-mode: navigate',
            'sec-fetch-site: none',
            'sec-fetch-user: ?1',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36',
            'Keep-Alive: 9999999999'
        ),
    ));

    $response = curl_exec($curl);

    $rsp = json_decode($response, true);

    $userData = $rsp['graphql']['user'];
    $profilePic = $userData['profile_pic_url'];

    $model->inserInfo('ig_blob', [
        'ig_id' => $row['id'],
        'blob_data' => $model->getImage($profilePic),
        'type' => 'avatar'
    ]);

    $userPosts =  array_slice($userData['edge_owner_to_timeline_media']['edges'], 0, 9);

    $latestPostImages = [];
    for($i = 0; $i < count($userPosts); $i++){
        $model->inserInfo('ig_blob', [
            'ig_id' => $row['id'],
            'blob_data' => $model->getImage($userPosts[$i]['node']['display_url']),
            'type' => 'timeline'
        ]);
    }
}
curl_close($curl);

exit;
