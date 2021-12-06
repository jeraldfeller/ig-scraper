<?php
/**
 * Created by PhpStorm.
 * User: jeral
 * Date: 7/21/2019
 * Time: 11:47 AM
 */

class Model
{
    public $debug = TRUE;
    protected $db_pdo;

    public function getHandleToScan()
    {
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `followed_user` WHERE `scrape_running` = 0';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function getFollowingToScan($sort = 'DESC', $orderby = 'id')
    {
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `following_user` WHERE verified = 0 AND `scraped` = 0 AND exclude = 0 ORDER BY ' . $orderby . ' ' . $sort . ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->updateInfo('following_user', $row['id'], [
                'scraped' => 1
            ]);
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function getFollowerById($id)
    {
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `following_user` WHERE userid = ' . $id . ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function getRules($parentId)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT rules FROM `followed_user` WHERE id = $parentId LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function updateInfo($table, $id, $fields = [], $byUserid = false)
    {
        $pdo = $this->getPdo();

        $updateFields = '';
        $i = 0;
        foreach ($fields as $field => $value) {
            $f = $field;
            $v = $value;

            if ($i == count($fields) - 1) {
                $updateFields .= "$f = '$v'";
            } else {
                $updateFields .= "$f = '$v', ";
            }
            $i++;
        }

        if ($byUserid == true) {
            $sql = "UPDATE $table SET $updateFields WHERE userid = $id";
        } else {
            $sql = "UPDATE $table SET $updateFields WHERE id = $id";
        }


        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function inserInfo($table, $fields)
    {
        $pdo = $this->getPdo();

        $insertFields = '';
        $i = 0;
        foreach ($fields as $field => $value) {
            $f = $field;
            $v = $value;

            if ($i == count($fields) - 1) {
                $insertFields .= "$f = '$v'";
            } else {
                $insertFields .= "$f = '$v', ";
            }
            $i++;
        }

        $sql = "INSERT INTO $table SET $insertFields";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function isExists($table, $useird)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM $table WHERE `userid` = $useird";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return (count($result) > 0 ? true : false);
    }


    public function deleteInfo($table, $id)
    {
        $pdo = $this->getPdo();
        $sql = "DELETE FROM $table WHERE id = $id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }


    public function setActiveScanCount($count)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE options SET content = (content$count) WHERE title = 'active_scan_count'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function getActiveScanCount()
    {
        $pdo = $this->getPdo();
        $sql = "SELECT content FROM `options` WHERE title = 'active_scan_count'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function getCookie()
    {
        $pdo = $this->getPdo();
//        $sql = "SELECT id, cookie, user FROM `ig_cookies` WHERE active = 1 AND last_used = 0 ORDER BY RAND()";
        $sql = "SELECT id, cookie, user FROM `ig_cookies` WHERE active = 1  ORDER BY RAND()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result = $row;
        }
        $this->updateLastUsed(0, 0, true);
        $this->updateLastUsed($result['id'], 1);
        $pdo = null;

        return $result;
    }

    public function getCookieApp()
    {
        $pdo = $this->getPdo();
        $sql = "SELECT id, cookie, user FROM `ig_cookies_app` WHERE active = 1  ORDER BY RAND()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result = $row;
        }
        $this->updateLastUsedApp(0, 0, true);
        $this->updateLastUsedApp($result['id'], 1);
        $pdo = null;

        return $result;
    }

    public function updateCookie($id, $bol)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE ig_cookies SET active = $bol WHERE id = $id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function updateLastUsed($id, $bol, $reset = false)
    {
        $pdo = $this->getPdo();
        if ($reset == true) {
            $sql = "UPDATE ig_cookies SET last_used = 0";
        } else {
            $sql = "UPDATE ig_cookies SET last_used = $bol WHERE id = $id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function updateLastUsedApp($id, $bol, $reset = false)
    {
        $pdo = $this->getPdo();
        if ($reset == true) {
            $sql = "UPDATE ig_cookies_app SET last_used = 0";
        } else {
            $sql = "UPDATE ig_cookies_app SET last_used = $bol WHERE id = $id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    function getUserCurl($handle)
    {
        $rsp = $this->curlToProfile('https://www.instagram.com/' . $handle . '/');
        if ($rsp) {
            $html = str_get_html($rsp);
            $scripts = $html->find('script');
            foreach ($scripts as $script) {
                $scriptText = trim($script->innertext);
                if (strpos($scriptText, 'csrf_token')) {
                    $scriptText = str_replace('window._sharedData = ', '', $scriptText);
                    $scriptText = rtrim($scriptText, ";");
                    $arr = json_decode($scriptText, true);

                    $entryData = $arr['entry_data'];
                    $profilePage = $entryData['ProfilePage'];
                    $user = $profilePage[0]['graphql']['user'];
                    return $user;
                }
            }
        }

        return false;
    }


    public function deleteImportedData()
    {
        $pdo = $this->getPdo();
        $sql = "DELETE FROM imported_data";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function importData($data)
    {
        $pdo = $this->getPdo();
        $sql = "INSERT INTO imported_data SET 
                numbering = " . $data['numbering'] . ",
                vehlaApproved = '" . $data['vehlaApproved'] . "',
                igUsername = '" . $data['igUsername'] . "',
                approved = '" . $data['approved'] . "',
                userid = " . $data['userid'] . ",
                verified = '" . $data['veri'] . "',
                noFollowers = " . $data['noFollowers'] . ",
                noFollowed = " . $data['noFollowed'] . ",
                email = '" . $data['email'] . "',
                fullName = '" . $data['fullName'] . "',
                country = '" . $data['country'] . "',
                reason = '" . $data['reason'] . "',
                avatar = '" . $data['avatar'] . "',
                transferred = '" . $data['transferred'] . "'
               ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function hasImportData()
    {

    }

    public function getImportDataCount()
    {
        $pdo = $this->getPdo();
        $sql = "SELECT (SELECT count(id) FROM imported_data WHERE status = 'inbox') as inboxCount,
                       (SELECT count(id) FROM imported_data WHERE status = 'approved') as approvedCount,
                       (SELECT count(id) FROM imported_data WHERE status = 'rejected') as rejectedCount  
                FROM `imported_data`";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;

        return $result;
    }

    public function getImportData($offset, $status, $size = 20)
    {

        $limitQry = ($size != -1 ? "LIMIT $offset, $size" : '');
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM `imported_data` WHERE status = '$status' $limitQry";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }

    public function getImportDataToScrape()
    {
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM `imported_data` WHERE status = 'inbox' and scraped = 0 LIMIT 60";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
            $this->updateInfo('imported_data', $row['id'], [
                'scraped' => 1
            ]);
        }
        $pdo = null;

        return $result;
    }



    public function getImages($userid, $type){
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM `ig_blob` WHERE ig_id = $userid AND type = '$type'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $pdo = null;

        return $result;
    }


    public function markData($id, $mark)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE imported_data SET status = '$mark' WHERE id = $id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
    }

    public function curlTo($url)
    {
        $curl = curl_init();

        $cookieData = $this->getCookie();
        $cookie = $cookieData['cookie'];
        print_r($cookieData['user'] . "\n");
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
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
                'cookie: ' . $cookie,
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36',
                'Origin: https://www.instagram.com',
                'Referer: https://www.instagram.com',
                'sec-ch-ua-platform: Windows',
                'sec-fetch-site: same-site',
                'x-asbd-id: 198387',
                'x-ig-app-id: 936619743392459',
                'x-ig-www-claim: hmac.AR3_-bfLMOkVKHnnMdLu4f3oPm_RqO1PgBHmt9hdQQU8vPd3',
                'Keep-Alive: 9999999999'
            )
        ));

        $response = curl_exec($curl);


        curl_close($curl);

        $rsp = json_decode($response, true);
        if (isset($rsp['spam'])) {
            if ($rsp['spam'] == true) {
                $this->updateCookie($cookieData['id'], 0);
            }
        }
        return $response;
    }


    public function curlToIg($user)
    {
        $curl = curl_init();
        $cookieData = $this->getCookie();
        $cookie = $cookieData['cookie'];
        print_r($cookieData['user'] . "\n");
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.instagram.com/' . $user . '/?__a=1',
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
                'cookie: ' . $cookie,
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
        return $response;
    }

    public function curlToProfile($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.instagram.com/endlesslyloveclub/',
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
                'Cookie: csrftoken=oALdGcDZQ1hAvP4h3HTz8UF6Djx9JTvu; ds_user_id=30645581080; ig_did=50DB1E93-D351-4E84-8B4F-C22DB9C093EE; mid=YYRZrQAEAAG6iGCpnXAoNECvg8qt'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function getImage($path){
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    public function getPdo()
    {
        if (!$this->db_pdo) {
            if ($this->debug) {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            } else {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}


