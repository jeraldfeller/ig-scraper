<?php
$user = $_GET['user'];
$curl = curl_init();
$cookie = 'ig_did=3562EA9D-BD31-4B37-9D5E-E0C5261787B5; ig_nrcb=1; mid=YZtUJQALAAH5ZwRrkCHUX94G2lHB; csrftoken=3J8BVpA4GQQTqjIQRnE66x9kfGfTbx39; ds_user_id=50707888929; sessionid=50707888929%3AnUfHdN42cs30ob%3A29; rur="NAO\05450707888929\0541669105679:01f7eb5344afa9af032399fd960fb7ba016fde784f5a7d4f7da677a3020a1ab56ec87fe7"';
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

curl_close($curl);
echo $response;