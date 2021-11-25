<?php
$user = $_GET['user'];
$curl = curl_init();
$cookie = [
  'ig_did=17297759-4AF2-4F21-9F7C-40F534683042; ig_nrcb=1; mid=YZ7hdwALAAEF-JLu8ETAmt9udiRN; csrftoken=oVloZ4DZsRtWgsl72SeFKn7J7DLE7FLD; ds_user_id=50340100186; sessionid=50340100186%3AAHpt6wva9Vlj2M%3A26; rur="FRC\05450340100186\0541669338613:01f7fd4ab9cab5b5cf22bf8e3fa76039dd8150b794595787cdff0a216d1aac4f8be60551"',
  'ig_did=7A58808F-1FC8-413D-A82A-4A3FDDD29D53; ig_nrcb=1; mid=YZ7miQALAAFW72J3hbIcLTdIPumb; csrftoken=7knUBk3wT5yzoEbhzAIyuEK1LCJ4u54R; ds_user_id=50517850211; sessionid=50517850211%3A4utnY8iL05IqYG%3A0; rur="ATN\05450517850211\0541669340211:01f72197fc618f2432111858c503a51183838dff757026868aa12ed14eb651e87246c804"'
];

$cookie = $cookie[rand(0, 1)];
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