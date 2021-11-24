<?php
ob_start();
session_start();

$fileHandle = fopen($_FILES['file']['tmp_name'], "r");
$i = 0;
$flag = true;

$lists = [];

$curl = curl_init();
$cookie = 'ig_did=A270B776-25FA-492E-BCA1-77D57AB9F23D; ig_nrcb=1; mid=YZxAwwALAAFeLy-beZIpqMYjf7oy; csrftoken=WhJWMNDR478Z8W5JdsSXr7t1YN4wXzw3; ds_user_id=50693370883; sessionid=50693370883%3AnEeNEy7PN862uW%3A16; rur="VLL\05450693370883\0541669166750:01f74af431acfe8f9e9b5a79148d1af84bdbaf781a3e0db951509116cf6cab02c95cb307"';
while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
    if ($flag) {
        $flag = false;
        continue;
    }


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.instagram.com/'.$data[2].'/?__a=1',
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

    $response = json_decode(curl_exec($curl), true);

    $user = $response['graphql']['user'];


    $profilePicUrl = getImage($user['profile_pic_url']);



    $timelines = array_slice($user['edge_owner_to_timeline_media']['edges'], 0, 9);

    $timelinesImg = [];
    for($i = 0; $i < count($timelines); $i++){
        $timelinesImg[] = getImage($timelines[$i]['node']['thumbnail_src']);
    }


    $lists[] = [
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
        'transferred' => $data[13],
        'timelines' => $timelinesImg,
        'profileImg' => $profilePicUrl
    ];
}

curl_close($curl);

function getImage($path){
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    return $base64;
}

echo json_encode($lists);