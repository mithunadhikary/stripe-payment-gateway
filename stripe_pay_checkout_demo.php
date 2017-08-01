<?php
/**
 * Stripe - Payment Gateway integration example (Stripe Checkout)
 * ==============================================================================
 * 
 * @version v1.0: stripe_pay_checkout_demo.php 2016/10/05
 * @copyright Copyright (c) 2016, http://www.ilovephp.net
 * @author Sagar Deshmukh <sagarsdeshmukh91@gmail.com>
 * You are free to use, distribute, and modify this software
 * ==============================================================================
 *
 */

// Stripe library
require 'stripe/Stripe.php';

$params = array(
	"testmode"   => "on",
	"private_live_key" => "sk_test_7UcC9DL82i35GhHkB1SLfqOd",
	"public_live_key"  => "pk_test_PiBgzPIL5p5qj7GP9qmvJoe9",
	"private_test_key" => "sk_test_7UcC9DL82i35GhHkB1SLfqOd",
	"public_test_key"  => "pk_test_PiBgzPIL5p5qj7GP9qmvJoe9"
);

if ($params['testmode'] == "on") {
	Stripe::setApiKey($params['private_test_key']);
	$pubkey = $params['public_test_key'];
} else {
	Stripe::setApiKey($params['private_live_key']);
	$pubkey = $params['public_live_key'];
}

if(isset($_POST['stripeToken']))
{
	$amount_cents = str_replace(".","","450.00");  // Chargeble amount
	$invoiceid = "14526321";                      // Invoice ID
	$description = "Invoice #" . $invoiceid . " - " . $invoiceid;

	try {
		$charge = Stripe_Charge::create(array(		 
			  "amount" => $amount_cents,
			  "currency" => "aud",
			  "source" => $_POST['stripeToken'],
			  "description" => $description)			  
		);

		if (isset($charge->card->address_zip_check) && $charge->card->address_zip_check == "fail") {
			throw new Exception("zip_check_invalid");
		} else if (isset($charge->card->address_line1_check) && $charge->card->address_line1_check == "fail") {
			throw new Exception("address_check_invalid");
		} else if (isset($charge->card->cvc_check) && $charge->card->cvc_check == "fail") {
			throw new Exception("cvc_check_invalid");
		}
		// Payment has succeeded, no exceptions were thrown or otherwise caught				

		$result = "success";

	} catch(Stripe_CardError $e) {			

	$error = $e->getMessage();
		$result = "declined";

	} catch (Stripe_InvalidRequestError $e) {
		$result = "declined";		  
	} catch (Stripe_AuthenticationError $e) {
		$result = "declined";
	} catch (Stripe_ApiConnectionError $e) {
		$result = "declined";
	} catch (Stripe_Error $e) {
		$result = "declined";
	} catch (Exception $e) {

		if ($e->getMessage() == "zip_check_invalid") {
			$result = "declined";
		} else if ($e->getMessage() == "address_check_invalid") {
			$result = "declined";
		} else if ($e->getMessage() == "cvc_check_invalid") {
			$result = "declined";
		} else {
			$result = "declined";
		}		  
	}
	
	echo "<BR>Stripe Payment Status : ".$result;
	
	echo "<BR>Stripe Response : ";
	
	print_r($charge); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stripe Pay Checkout Demo</title>
</head>
<body>
<h1 class="bt_title" align="center">Stripe Pay Checkout Demo</h1>
<div align="center">
  <form action="" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="<?php echo $params['public_test_key']; ?>"
    data-amount="333"
    data-name="Mithun"
    data-description="Adhikary"
    data-image=""
    data-locale="auto"
    data-zip-code="true">
  </script>
</form>
</div>
</body>
</html>