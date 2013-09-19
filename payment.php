<!DOCTYPE html>
<html>
<body>

<?php 
//Check to see if request is a post.  If not display the form.  If so process the form and execute the API call
if($_SERVER['REQUEST_METHOD'] == 'POST'):

	//Start building array of parameters for CURL API call
	$params = array(
		
		//These are required parameters and must be included in the call.
		'PARTNER' => 'PayPal',  //Payflow Partner.  This should always be PayPal
		'VENDOR' => '', //Put your manager.paypal.com vendor login here
		'USER' => '', //Put your manager.paypal.com user login here
		'PWD' => '', //Put your manager.paypal.com vendor password here
		'TRXTYPE' => 'S', 			//This is the transaction type.  S is for sale, is an Authorization
		'CREATESECURETOKEN' => 'Y',	//Tells the payflow server to create a secure token for you
		'SECURETOKENID' =>	md5(uniqid(rand(), true)),  //This needs to be a pseudo random string up to 36 characters.  I am just md5 hashing a pseudo random number
		'AMT' => '75.00',  //Set the Amount of the order.  Needs to be calculated but I am just hardcoding the example.  This needs to be accurate because the token returned is only good for this amount
		'RETURNURL' => 'http://localhost/PayPalPaymentsAdvancedLayoutC/success.php',  //Setup your return url.  This is where paypal will return when complete.  See my success.php page
		'ERRORURL' => 'http://localhost/PayPalPaymentsAdvancedLayoutC/error.php',  //Setup your error url.  This is where paypal will return when an error occurs.  See my error.php page
		'CANCELURL' => 'http://localhost/PayPalPaymentsAdvancedLayoutC/cancel.php',  //Setup your cancel url.  This is where paypal will return when the user cancels an order.  See my cancel.php page
			
		//Optional variables from form post
		'BILLTOFIRSTNAME' => $_POST['BILLTOFIRSTNAME'],
		'BILLTOLASTNAME' => $_POST['BILLTOLASTNAME'],
		'BILLTOSTREET' => $_POST['BILLTOSTREET'],
		'BILLTOCITY' => $_POST['BILLTOCITY'],
		'BILLTOSTATE' => $_POST['BILLTOSTATE'],
		'BILLTOZIP' => $_POST['BILLTOZIP'],
			
		//Now you can place any other parameters from https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/payflowgateway_guide.pdf
		//For this example I am going to set up two items
		'L_NAME0' => 'Item 1',
		'L_DESC0' => 'Item 1 Description',
		'L_COST0' => '50.00',
		'L_QTY0'  => 1,
		'L_NAME1' => 'Item 2',
		'L_DESC1' => 'Item 2 Description',
		'L_COST1' => '25.00',
		'L_QTY1'  => 1,	
			
		
	);

	//In PHP CURL will only accept a string for parameters so I am going to turn my array into a query string
	//DO NOT use http_build_query or any sort of URL encoding on the parameters.
	//Just take the key and value
	$querystring = '';
	foreach($params as $key => $value)
		$querystring .= $key . '=' . $value . '&';
	
	//Setup URLS
	$url = 'https://payflowpro.paypal.com';
	$url = 'https://pilot-payflowpro.paypal.com'; //COMMENT THIS LINE OUT FOR a LIVE TRANSACTION
	
	//Setup CURL CALL to get a secure token for the transaction
	$ch = curl_init ();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt ($ch, CURLOPT_POST, true);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $querystring);  //Set My query string
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);		//Execute the API Call
	//print_r($response);	//Uncomment to view the raw response
	
	//The response is sent back as a string so we need to decode it into an array to use
	$responsedata = array();
	$key = explode('&',$response);
	foreach($key as $temp)
	{
		$keyval = explode('=',$temp);
		if(isset($keyval[1]))
			$responsedata[$keyval[0]] = $keyval[1];
	}
	//print_r($responsedata);	//Uncomment to view the decoded response
	
	//Setup LInk URLS
	$url = 'https://payflowlink.paypal.com';
	$url = 'https://pilot-payflowlink.paypal.com'; //Comment this line for live transaction

	//Output a small confirmation message and payment iframe
?>
	<h3>Confirmation</h3>
	<p>Make sure everything is correct.  I would show them the Name address etc that they put in the form as well.  Also the item information</p>
	
	Now we can use the secure token in the response to display the iframe for the customer.
	<iframe src="<?php echo $url ?>?SECURETOKEN=<?php echo $responsedata['SECURETOKEN'] ?>&SECURETOKENID=<?php echo $responsedata['SECURETOKENID'] ?>&MODE=TEST" width="490" height="565" border="0" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';
	

<?php else :?>

<form action="" method="post">

	<label for="BILLTOFIRSTNAME">First Name</label><input type="text" name="BILLTOFIRSTNAME" /><br/>
	<label for="BILLTOLASTNAME">Last Name</label><input type="text" name="BILLTOLASTNAME" /><br/>
	<label for="BILLTOSTREET">Street</label><input type="text" name="BILLTOSTREET" /><br/>
	<label for="BILLTOCITY">City</label><input type="text" name="BILLTOCITY" /><br/>
	<label for="BILLTOSTATE">State</label><input type="text" name="BILLTOSTATE" /><br/>
	<label for="BILLTOZIP">Zip</label><input type="text" name="BILLTOZIP" /><br/>
	<input type="submit" name="Process Payment" >

</form>

<?php endif;?>
</body>
</html>