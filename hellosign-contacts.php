<?php
require_once __DIR__.'/settings.php';


file_put_contents(__DIR__.'/hellosign.log', date('Y-m-d H:i:s')."\n".var_export($_REQUEST, true)."\n\n", FILE_APPEND);


if ( isset($_REQUEST['json'])) {
	$hellosign_data = json_decode($_REQUEST['json'], true);

	if ($hellosign_data['event']['event_type'] == 'signature_request_signed') {

		$signer_data = $hellosign_data['signature_request']['signatures'][0];
		$email = $signer_data['signer_email_address'];
		list($first_name, $last_name) = explode(' ', $signer_data['signer_name'], 2);

		$custom_fields = $hellosign_data['signature_request']['custom_fields'];
		$response_data = $hellosign_data['signature_request']['response_data'];

		// API doc: https://legacydocs.hubspot.com/docs/methods/contacts/create_contact
		$arr = array(
	        'properties' => array(
	            array(
	                'property' => 'email',
	                'value' => $email
	            ),
	            array(
	                'property' => 'firstname',
	                'value' => $first_name
	            ),
	            array(
	                'property' => 'lastname',
	                'value' => $last_name
	            ),
	        )
	    );
	    $post_json = json_encode($arr);

	    $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $hubspotApiKey;
	    $ch = @curl_init();
	    @curl_setopt($ch, CURLOPT_POST, true);
	    @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
	    @curl_setopt($ch, CURLOPT_URL, $endpoint);
	    @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = @curl_exec($ch);
	    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    $curl_errors = curl_error($ch);
	    @curl_close($ch);


		$logstr =  "curl Errors: " . $curl_errors
			. "\nStatus code: " . $status_code
	    	. "\nResponse: " . $response;
		file_put_contents(__DIR__.'/hubspot.log', date('Y-m-d H:i:s')."\n".$logstr."\n\n", FILE_APPEND);
	    
	}
}

echo 'Hello API Event Received';


	
