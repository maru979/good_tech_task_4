<?php 
include ("save_log.php");

class AmoAuth{

    public static function authorize($subdomain, $login, $hash){
        $user = array(
            'USER_LOGIN' => $login, 
            'USER_HASH' => $hash);
        $link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        curl_close($curl); 

        $errors = [301 => 'Moved permanently', 400 => 'Bad request', 401 => 'Unauthorized',
            403 => 'Forbidden', 404 => 'Not found', 500 => 'Internal server error', 
            502 => 'Bad gateway', 503 => 'Service unavailable'];
        $code = (int) $code;

        if ($code != 200 && $code != 204) {
            if($errors[$code]){
                $logInfo = date('Y-m-d H:i:s').' Error '.$code.': '.$errors[$code]; 
            }
            else{
                $logInfo = date('Y-m-d H:i:s').' Error '.$code.': Unknown error.';
            } 
        }
        else {
            $logInfo = 'Successful authorization.';
        }

        LogEdit::writeLogs($logInfo);
    }
}