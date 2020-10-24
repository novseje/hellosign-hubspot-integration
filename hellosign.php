<?php
require_once __DIR__.'/settings.php';

file_put_contents(__DIR__.'/hellosign.log', date('Y-m-d H:i:s')."\n".var_export($_REQUEST, true)."\n\n", FILE_APPEND);

//$_REQUEST['json'] = file_get_contents(__DIR__.'/test.json');

if (isset($_REQUEST['json'])) {
	$hellosign_data = json_decode($_REQUEST['json'], true);

	// $hellosign_data['event']['event_type'] == 'signature_request_signed' ||
	if ($hellosign_data['event']['event_type'] == 'signature_request_all_signed') {

		$signer_data = $hellosign_data['signature_request']['signatures'][0];
		$email = $signer_data['signer_email_address'];

		$custom_fields = $hellosign_data['signature_request']['custom_fields'];
		$response_data = $hellosign_data['signature_request']['response_data'];

		$fields = [];
		foreach ($response_data as $field) {
		    if ($field['type'] == 'text') {
		        if ( ! empty($field['value'])) {
                    $fields[] = [
                        "name" => $field['name'],
                        "value" => $field['value'],
                    ];
                }
            } elseif ($field['type'] == 'checkbox') {
		        if ($field['value'] == true) {
                    list($name, $value) = explode(':', $field['name'], 2);
                    if ($value != NULL) {
                        $fields[] = [
                            "name" => $name,
                            "value" => $value,
                        ];
                    }
                }
            }
        }

		// API doc: https://legacydocs.hubspot.com/docs/methods/forms/submit_form
		$hdata = [
            "fields" => $fields,
            "legalConsentOptions" => [
                "consent" => [
                    "consentToProcess" => true,
                    "text" => "I agree to allow Example Company to store and process my personal data.",
                    "communications" => [
                        [
                            "value" => true,
                            "subscriptionTypeId" => 999,
                            "text" => "I agree to receive marketing communications from Example Company."
                        ]
                    ]
                ]
            ]
        ];
        $post_json = json_encode($hdata);

	    $endpoint = "https://api.hsforms.com/submissions/v3/integration/submit/{$hubspotPortalId}/{$hubspotFormId}";
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

	    $logstr = var_export($fields, true)."\n\n";

		$logstr .=  "curl Errors: " . $curl_errors
			. "\nStatus code: " . $status_code
	    	. "\nResponse: " . $response;
		file_put_contents(__DIR__.'/hubspot.log', date('Y-m-d H:i:s')."\n".$logstr."\n\n", FILE_APPEND);
	    
	}
}

echo 'Hello API Event Received';


	
