<?php
$login_url = 'http://120.77.86.253/UserInfo/process';

$data = array("module" => "queryuser", "deviceUUID" => "gaoke-test-2",/*"channel"=>"deviceLogin",*/ "openID"=>"123334");
$data["channel"]="weiboLogin";
$data_string = json_encode($data);
$post_data = array ("data" => $data_string);

$cookie_file = tempnam("./temp","cookie");
function sendCURL($post_data,$cookie_file,$login_url){
	$ch = curl_init($login_url);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLINFO_HEADER_OUT, false);
	$res=curl_exec($ch);
	$result = json_decode($res,true);
	curl_close($ch);
	return $result;
}
$ret = sendCURL($post_data,$cookie_file,$login_url);
if($ret["errcode"] == 0)
{
	if($ret["role"] == true)
	{
		echo "login user================:";
		$data = array("module" => "loginuser","version"=>"0.0.1", "deviceUUID" => "gaoke-test-2","channel"=>"weiboLogin", "openID"=>"123334");
		$data_string = json_encode($data);
		$post_data = array ("data" => $data_string);
		$ret = sendCURL($post_data,$cookie_file,$login_url);
		var_dump($ret);
		
		$sessionid = $ret['sessionid'];	
		$account = $ret["account"];
		///获取邮件
		echo "getmaillist:\n";
                $data = array("module" =>"getmaillist", "session" =>$sessionid,"userid"=>$account);
                $data_string = json_encode($data);
                $post_data = array ("data" => $data_string);
                $ret = sendCURL($post_data,$cookie_file,$login_url);
                var_dump($ret);

		////////收邮件

		echo "getmailattachment:\n";
		foreach($ret['maillist']  as $k => $v){
			$data = array("module" =>"getmailattachment", "session" =>$sessionid,"userid"=>$account,"mailid"=>$k);
			$data_string = json_encode($data);
			$post_data = array ("data" => $data_string);
			$retgetmail = sendCURL($post_data,$cookie_file,$login_url);
			var_dump($retgetmail);
	//		break;
		}
		
		echo "deletemail\n";
		foreach($ret['maillist']  as $k => $v){
		$data = array("module" =>"deletemail", "session" =>$sessionid,"userid"=>$account,"mailid"=>$k);
		$data_string = json_encode($data);
		$post_data = array ("data" => $data_string);
		$ret = sendCURL($post_data,$cookie_file,$login_url);
		var_dump($ret);
		break;
		}


	
		echo "logout user---------------\n";
		$data = array("module" =>"logoutuser", "session" =>$sessionid,"userid"=>$account);
		$data_string = json_encode($data);
		$post_data = array ("data" => $data_string);
		$ret = sendCURL($post_data,$cookie_file,$login_url);
		var_dump($ret);
		
	}
	else{
		echo "create user++++++++++++++++:";
		$data = array("module" => "createuser","idfa"=>"idfa","version"=>"1.1.1","deviceType"=>"IOS", "deviceUUID" => "gaoke-test-2","channel"=>"weiboLogin", "openID"=>"123334",
				"nickName"=>"sa","gender"=>"male","icon"=>"sdfdf");
		$data_string = json_encode($data);
		$post_data = array ("data" => $data_string);
		$ret = sendCURL($post_data,$cookie_file,$login_url);
		var_dump($ret);
	}
}
else{
	echo "cannot find this msg";
	var_dump($ret["errcode"]);
}
?>
