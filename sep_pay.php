<?php
if (isset($_POST['orderid'])){
	$terminal_Id=0;
	$amount=$_POST['amount']; // Order Price
	$ResNum=$_POST['orderid']; // Order Id
	$RedirectUrl="http://".$_SERVER['SERVER_NAME']."/sep_verify.php";
	$CellNumber="";
	require_once 'HTTP/Request2.php';
	$request = new HTTP_Request2();
	//your token
	$token="";
	$request->setUrl('https://sep.shaparak.ir/onlinepg/onlinepg');
	$request->setMethod(HTTP_Request2::METHOD_POST);
	$request->setConfig(array(
	  'follow_redirects' => TRUE
	));
	$request->setHeader(array(
	  'Content-Type' => 'application/json'
	));
	$request->setBody('{
		"action":"token",
		"TerminalId":'.$terminal_Id.',
		"Amount":'.$amount.',
		"ResNum":"'.$ResNum.'",
		"RedirectUrl":"'.$RedirectUrl.'",
		"CellNumber":"'.$CellNumber.'"
	}');
	try {
		$response = $request->send();
		if ($response->getStatus() == 200) {
			  $arr = json_decode($response->getBody(), true);
			  $token=$arr['token'];
		}
		else {
			echo $response->getBody();
		}
	}
	catch(HTTP_Request2_Exception $e) {
	    echo $e->getMessage();
	}
}
?>	
<form id="sep_form" onload="document.forms['forms'].submit()" action="https://sep.shaparak.ir/OnlinePG/OnlinePG" method="post">
 <input type="text" name="Token" value="<?php echo $token; ?>" />
 <input name="GetMethod" type="text" value=""> <!--true | false | empty string | null-->
</form>
<script type="text/javascript">
  document.getElementById('sep_form').submit();
</script>