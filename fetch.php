<?php
$user = $_GET['user'];
$curl = curl_init();
$cookie = 'ig_did=5335B358-B5C1-4FE8-93D1-328DAC227A11; ig_nrcb=1; mid=YZrSAQALAAGWLaBo4Ry62TUepkow; csrftoken=MtA3xs96hYcWRVfSROh6IRhhCQia2IKf; ds_user_id=50350969932; sessionid=50350969932%3Ar6zuvTYk6NBfTl%3A24; rur="EAG\05450350969932\0541669072366:01f73532cbc42e12764977005597b1ffb612c4a13d07618325c0cb42d522bef205d80310"';
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