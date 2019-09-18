<?php 	#mazitov979@gmail.com / 5d96f5f42a39f27b06f9e268cb83f02b18b0cb17
ini_set('log_errors', 'On');
ini_set('error_log', '/modules/log/errors.log');

include("auth.php");
include("create_contact.php");



$subdomain = 'mazitov979';
$login = 'mazitov979@gmail.com';
$hash_key = '5d96f5f42a39f27b06f9e268cb83f02b18b0cb17';
AmoAuth::authorize($subdomain, $login, $hash_key);

if (isset($_POST['queryData'])){
	$json = $_POST['queryData'];
	$data = json_decode($json, true);
	LogEdit::writeLogs("got json queryData");
	#$data["form"]["id"];
}
else{
	LogEdit::writeLogs("can't get json queryData");
}

$customer_name = $data["contact"]["name"];
$customer_phone = $data["contact"]["phone"];
$customer_email = $data["contact"]["email"];
$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts/?query='.substr($customer_phone, 2);
$link2 = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts/?query='.$customer_email;



$id_phone = '414507';
$id_email = '414509';

$Response = getContactsByQuery($link);
$Response2 = getContactsByQuery($link2);

if ($Response || $Response2){
	if (!findByPhone($Response, $customer_phone, $id_phone, false)){
		if (!findByEmail($Response2, $customer_email, $id_email, false)){
			$contact = new Contact();
			LogEdit::writeLogs(Contact::createContact($subdomain, $customer_name, $customer_phone, $customer_email, $id_phone, $id_email));
			$Response3 = getContactsByQuery($link);
			findByPhone($Response3, $customer_phone, $id_phone, true);
			LogEdit::writeLogs("-------------");
		}
	}
}
else{
	LogEdit::writeLogs(Contact::createContact($subdomain, $customer_name, $customer_phone, $customer_email, $id_phone, $id_email));
	$Response3 = getContactsByQuery($link);
	findByPhone($Response3, $customer_phone, $id_phone, true);
	LogEdit::writeLogs("-------------");
}


function findByPhone($arr, $phone, $id_phone, $isExist){
	foreach ($arr as $el) {
		foreach ($el['custom_fields'] as $cf) {
			if ($cf['id'] == $id_phone){
				foreach ($cf['values'] as $vs) {
					if( substr($vs['value'], 2) == substr($phone, 2)){
						printId($el['id'], "'Phone'", $isExist);
						return true;
					}
				}
			}
		}
	}
	return false;
}

function findByEmail($arr, $email, $id_email, $isExist){
	foreach ($arr as $el) {
		foreach ($el['custom_fields'] as $cf) {
			if ($cf['id'] == $id_email){
				foreach ($cf['values'] as $vs) {
					if($vs['value'] == $email){
						printId($el['id'], "'Email'", $isExist);
						return true;
					}
				}
			}
		}
	}
	return false;
}

function printId($id, $field_name, $isExist){
	if (!$isExist){
		LogEdit::writeLogs('User with same '.$field_name.' already exists and his ID: '.$id);
		LogEdit::writeLogs("-------------");
	}
	else{
		LogEdit::writeLogs('New user ID: '.$id);
		LogEdit::writeLogs("-------------");		
	}

}

function getContactsByQuery($link){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array());
	/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
	$code = (int) $code;

	curl_setopt($curl, CURLOPT_URL, $link2);
	$out2 = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
	$code2 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
	$code = (int) $code;

	$errors = array(
	    301 => 'Moved permanently',
	    400 => 'Bad request',
	    401 => 'Unauthorized',
	    403 => 'Forbidden',
	    404 => 'Not found',
	    500 => 'Internal server error',
	    502 => 'Bad gateway',
	    503 => 'Service unavailable',
	);

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

	$Response = json_decode($out, true);
	$Response = $Response['_embedded']['items'];
	return $Response;
}
?>