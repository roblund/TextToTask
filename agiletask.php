<?
    /* roblund - 9/25/10
    ** A quick and dirty Twilio -> AgileTask integration
    **
    ** Note: you will need a Twilio account, and an AgileTask account for this integration
    ** to work correctly. Host this script with any hosting provider that supports PHP,
    ** and cURL.
    */

    //null check request parms
    if($_REQUEST['From'] == null || $_REQUEST['Body'] == null)
    {
        exit("Missing Parms");
    }

    //put your cell phone number here (when using the dev sandbox remove the +1)
    $my_cell = '+1XXXXXXXXXX';

    //make sure the text is coming from my cell phone
    if($my_cell != $_REQUEST['From'])
    {
        exit("Wrong Cell Number");
    }

    //put your api key here
    $agiletask_api_key = 'XXXXXXXXXXXXXXXXXXXX';
    $agiletask_url = sprintf('http://agiletask.me/tasks.json?api_key=%s', $agiletask_api_key);

    //request headers
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
    );


    //put message body into an appropriate json data structure
    $json_data = json_encode(array(
        'task' => array(
            'name' => $_REQUEST['Body']
        )
    ));

    //setup cURL options
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $agiletask_url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $json_data);

    //run cURL request
    $response = curl_exec($handle);

    //pull http status code
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    //check the status code
    if($code == 200)
    {
        $response_message = 'Task added, thanks.';
    }
    else
    {
        //DEBUG - $response_message = 'Failed - ' . $code;
        $response_message = 'Task was NOT added.';
    }

    //NOTE: you can remove the response below if would like to cut your Twilio bill in 
    //    half. Just comment out the two lines below, and remove the response XML at 
    //    the bottom.
    
    //set up response to twilio and your cell phone
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
	<Sms><?= $response_message ?></Sms>
</Response>
