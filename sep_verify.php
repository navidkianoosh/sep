<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<LINK rel="address bar icon"  href="images/logo.ico" >
<meta http-equiv="Content-Language" content="fa">
<meta name="COPYRIGHT" content="shayany.com">
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
			//echo $response->getBody().'<br>';
			if ($response->getStatus() == 200) {			
				  $arr = json_decode($response->getBody(), true);
				  //echo $arr['ResultCode'].'<br>'.$arr['ResultDescription'];
				  if ($arr['ResultCode']==0 || $arr['ResultCode']==2){
					  //Success
					  //========================
					  require_once("assets/class/cls_reservation.php");
					  require_once("assets/class/dates.php");
					  $resultSet = array();
					  $obj=new reservation();
					  $resultSet= $obj->confirm_payment_via_pay_id($orderId,$TraceNo,"saman");
			  
					  echo('<center><br><br>');
					  echo('<br>آقا/خانم '.$resultSet[0]['firstName'].' '.$resultSet[0]['lastName']);
					  echo('<br><br>عملیات پرداخت شما با موفقیت انجام گردید.');
					  echo('<br><br>شماره پیگیری شما در وبگاه بانک ملت/سامان و وبگاه نوبت دهی اینترنتی '.$doc_name);
					  echo('<br><br>'.$TraceNo.'<br><br><br>');
					  echo('<br>نوبت شما: '.$resultSet[0]['rdate'].' ساعت '. substr($resultSet[0]['starttime'],0,5).'<br><br><br>');
					  echo('<br>مطب '.$doc_name.'<br>');
					  echo('خواهشمندیم این شماره را در روز نوبت به همراه داشته باشید. ');
					  //echo('شما می توانید نتیجه معاینه را پس از ثبت توسط آقای دکتر از قسمت پیگیری معاینه، مشاهده  نمایید. <Br>');
					  echo('با تشکر از شما');
					  echo('<br><br>');
					  echo('<a href="/blog/" class="button1 div_curve IRANSans_Medium font_12 " style="padding:10px 30px 10px 30px;">وبلاگ</a>');
					  echo('&nbsp;&nbsp;&nbsp;<a href="index.php" class="button1 div_curve IRANSans_Medium font_12 " style="padding:10px 30px 10px 30px;">صفحه اصلی</a>');
					  echo('</center><br><br>');				  
					  
					  $obj->delete_patient_resrve_time_temp_by_o_id($orderId);
					  $message='سلام نوبت شما '.$resultSet[0]['rdate'].' ساعت '.substr($resultSet[0]['starttime'],0,5).' '.$doc_name;
					  send_SMS($resultSet[0]['mobile'],$message,$sms_user,$sms_pass,$sms_line,$doc_name);
					  if ($secretary_mobile!=''){
						  $message='سلام نوبت '.$resultSet[0]['firstName'].' '.$resultSet[0]['lastName'].' در '.$resultSet[0]['rdate'].' ساعت '.substr($resultSet[0]['starttime'],0,5);
						  send_SMS($secretary_mobile,$message,$sms_user,$sms_pass,$sms_line,$doc_name);
					  }				  
				  }else{
					  //Un Success
					  echo('<br>فرآیند خرید شما ناقص می باشد');
					  echo('<br>وجه کسر شده از شما به حسابتان عودت می گردد');
					  echo('<br>خواهشمند است مجدد اقدام نمایید');
					  echo('<br><br><br><a href="reserve_list.php">نوبت دهی اینترنتی</a>');
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
		echo('<br><br><br><a href="reserve_list.php">نوبت دهی اینترنتی</a>');
	}
}
//exit(); 

function send_SMS($recipt,$message,$s_user,$s_pass,$s_line,$d_name){
    //convert persion number to latin number
    $persian_num = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $latin_num = range(0, 9);
    $recipt = str_replace($persian_num, $latin_num, $recipt);

    $recipt=floatval($recipt);
/*    $message='سلام
نوبت شما '.$reservedate.' ساعت '.$reservetime.'
ش پ '.$salerefno.' مطب '.$d_name;*/	
    $url = "https://ippanel.com/services.jspd";
    $rcpt_nm = array($recipt);
    $param = array
    (
        'uname'=>$s_user,
        'pass'=>$s_pass,
        'from'=>$s_line,
        'message'=>$message,
        'to'=>json_encode($rcpt_nm),
        'op'=>'send'
    );
    $handler = curl_init($url);
    curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    $response2 = curl_exec($handler);
    $response2 = json_decode($response2);
    $res_code = $response2[0];
    $res_data = $response2[1];
}
?>
</body>
</html>