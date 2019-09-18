<?php 
class Contact{

	public static function createContact($subdomain, $name, $phone, $email, $id_phone, $id_email){

		$contacts['add'] = array(
			array(
				'name' => $name,
				'custom_fields' => array(
				    array(
				        'id' => $id_phone,
				        'values' => array(
				            array(
				                'value' => $phone,
				                'enum' => 'WORK',
				            ),
				        ),
				    ),
				    array(
				        'id' => $id_email,
				        'values' => array(
				            array(
				                'value' => $email,
				                'enum' => 'WORK',
				            ),
				        ),
				    ),
				),
			),
		);

		$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts';
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contacts));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$out = curl_exec($curl); 
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
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
		    $logInfo = 'New account was created with name: '.$name.', phone:'.$phone.', email: '.$email;
		}

		return($logInfo);
	}
}
?>