<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Language" content="fa">
<meta name="AUTHOR" content="Navid Kianooshmoghaddam">
<meta name="RATING" content="General">
</head>
<body>
<?php
if (isset ($_POST['RefNum'])){
	$RefNum=$_POST['RefNum'];
	$State=$_POST['State'];
	$orderId=$_POST['ResNum'];
	$TerminalId=$_POST['TerminalId'];
	$TraceNo=$_POST['TraceNo'];
	if($State=='OK')
	{
		require_once 'HTTP/Request2.php';
		$request = new HTTP_Request2();
		$request->setUrl('https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/VerifyTransaction');
		$request->setMethod(HTTP_Request2::METHOD_POST);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		$request->setHeader(array(
		  'Content-Type' => 'application/json'
		));
		$request->setBody('{		
			"RefNum":"'.$RefNum.'",
			"TerminalNumber":'.$TerminalId.'
		}');
		try {
			$response = $request->send();
			if ($response->getStatus() == 200) {
				  $arr = json_decode($response->getBody(), true);
				  if ($arr['ResultCode']==0 || $arr['ResultCode']==2){
					  //Success
					  //=== Update Your Order
				  }else{
					  //Un Success
					  echo('<br>فرآیند خرید شما ناقص می باشد');
					  echo('<br>وجه کسر شده از شما به حسابتان عودت می گردد');
					  echo('<br>خواهشمند است مجدد اقدام نمایید');
				  }
			}
			else {
				echo $response->getBody();
			}
		}
		catch(HTTP_Request2_Exception $e) {
			echo $e->getMessage();
		}
	}else{
		echo('<br>فرآیند خرید شما ناقص می باشد');
		echo('<br>وجه کسر شده از شما به حسابتان عودت می گردد');
		echo('<br>خواهشمند است مجدد اقدام نمایید');
	}
}
?>
</body>
</html>