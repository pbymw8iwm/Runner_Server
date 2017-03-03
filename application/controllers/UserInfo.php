<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserInfo extends CI_Controller {
	function __construct()  
        { 
            parent::__construct();  
        }  
	//保存cookie  例如 saveCookie($userinfo['JSESSIONID'],86400);
	public function saveCookie($info,$time){
            $this->load->helper('cookie');
            set_cookie("userInfo",$info,$time);//userInfo：cookie名称。$info:要保存的cookie 。$time 设置保存期，即过期时间
        }
	//获取cookie:
	public  function getCookie($info){
	     //$info实际就是形成，调用这个方法的时候，需要获取哪个cookie名称就在调用的时候输入cookie名称
	     $this->load->helper("cookie");
	     return get_cookie($info);
	}
	//删除cookie
	public function deleteCookie($info){
	     $this->load->helper("cookie");
	     delete_cookie($info); 
	} 
	public function GetClientIp(){
		$this->load->helper('captcha');
		return $this->input->ip_address();
	}
	public function SendErrorMsg($errCode, $errMsg){ 
		$main_Arr = array("status" => "0", "ts"=> time(), "errcode" => $errCode, "errmsg" => $errMsg); 
		$return_Data = array("main" => $main_Arr); 
		echo json_encode($return_Data); 
	}
	public function checkDeviceVersion($cliVersion){
		$unixtime = time();		
		$base_path = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/hotfix/"; 
		$ini_array = parse_ini_file($base_path."version.lua");//解析版本号配置文件 
		if(empty($ini_array))
		{
			$main_Arr = array("status" => "0", "ts"=> $unixtime, "errcode" => "-2", "errmsg" => "get version error");
			$version_Arr = array("serverVersion" => "0", "hotUpdateVersion"=> "0");
			$return_Data = array("main" => $main_Arr, "version"=>$version_Arr); 
			return json_encode($return_Data); 
		}
		$main_Arr = array("status" => "1", "ts"=> "$unixtime", "errcode" => "0", "errmsg" => "");
		$version_Arr = array("serverVersion" => $ini_array["serverVersion"], "hotUpdateVersion"=> $ini_array["hotUpdateVersion"]);
		$return_Data = array("main" => $main_Arr, "version"=>$version_Arr); 
		return json_encode($return_Data); 
	} 
	public function checkDefaultVersion($cliVersion){
		$unixtime = time();
		$main_Arr = array("status" => "0", "ts"=> $unixtime, "errcode" => "-1", "errmsg" => "Bad device type information");
		$version_Arr = array("serverVersion" => "0", "hotUpdateVersion"=> "0");
		$return_Data = array("main" => $main_Arr, "version"=>$version_Arr); 
		return json_encode($return_Data); 
	}
	/*
		设备查询版本号信息
	*/
	public function queryVersion(){
		//$data_post = $this->input->post(NULL, TRUE);
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', "clientIp:".$this->GetClientIp()."data:".$data_post);
		$post_array = json_decode($data_post,true);
		if($post_array["deviceType"] == "IOS" || $post_array["deviceType"] == "android" || $post_array["deviceType"] == "ANDROID"){ 
			echo $this->checkDeviceVersion($post_array["version"]);
		}else{
			echo $this->checkDefaultVersion($post_array["version"]);
		}
	}
	/*
		查询玩家
C->S	
{ 
	"module":"queryuser", 			//请求更新锦标塞信息
	"deviceUUID":"deviceToken",//设备号
	"channel":"deviceLogin",//登录方式 qqLogin smsLogin weiboLogin  
	"openID":"123123" //第三方登录平台返回的id，如果是设备登录不需要这个参数了或者为空 
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":""
	"role" :"true"//有没有角色 true false
}	
	*/
	public function getAccount($post_array){  
		if(isset($post_array['deviceUUID']) && isset($post_array['channel']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$accountId = $this->Player->selectAccountInfo($post_array);
			if($accountId == -1){//Parameter error
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Parameter error", "role" =>false); 
				echo json_encode($return_Data); 
			}
			else if($accountId == 0){//没有任何角色
				$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "role" =>false);  
				echo json_encode($return_Data); 
			}else{
				//找到account之后从玩家表里查询			 
				$playerInfo = $this->Player->selectPlayerInfo($accountId);
				if($playerInfo == null){//没有任何角色
					$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "role" =>false); 
					echo json_encode($return_Data); 
				}else{
					$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "role" =>true); 
					echo json_encode($return_Data); 
				}
			}
		}else{
			$return_Data = array( "ts"=> time(), "errcode" => -1, "errmsg" => "Parameter error", "role" =>false); 
			echo json_encode($return_Data); 
		}
	}
	/*
		创建玩家
C->S	
{ 
	"module":"createuser", 			//请求更新锦标塞信息
	"deviceUUID":"deviceToken",//设备号
	"channel":"deviceLogin",//登录方式 qqLogin smsLogin weiboLogin
	"deviceType":"IOS",//设备类型信息 android
	"idfa":"xxxxxxxxxxx"
	"openID":"123123"//第三方登录平台返回的id，如果是设备登录不需要这个参数了或者为空
	"version":"1.1.1" //客户端版本
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":""
	"userInfo"：
	...
}	
	*/
	public function createAccount($post_array){ 
		$unixtime = time(); 
		if(isset($post_array['deviceUUID']) && isset($post_array['channel'])&& isset($post_array['deviceType']) && isset($post_array['idfa']) && isset($post_array['version']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$accountId = $this->Player->selectAccountInfo($post_array);
			if($accountId == -1){
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Parameter error");  
				echo json_encode($return_Data); 					
			}
			if($accountId == 0){//没有任何角色
				//增加一个account，然后增加一个player信息
				$accountId = $this->Player->addAccountInfo($post_array,$this->GetClientIp());
				if($accountId != -1){
					$cli_session = md5($accountId.$post_array['deviceUUID']."fuck!###".$unixtime); 
					$playerInfoArr = $this->Player->addPlayerInfo($accountId,$post_array,$cli_session);
					if($playerInfoArr == null){
						$return_Data = array( "ts"=> time(), "errcode" => -1, "errmsg" => "Character contains special characters");  
						echo json_encode($return_Data); 						
					}else{
						//等待完善
						
						//...
						echo  json_encode($playerInfoArr); //根据实际情况返回json
					}
				}
			}else{
				//找到account之后从玩家表里查询			 
				$playerInfo = $this->Player->selectPlayerInfo($accountId);
				if($playerInfo == null){//没有任何角色			
					$cli_session = md5($accountId.$post_array['deviceUUID']."fuck!###".$unixtime);
					$playerInfoArr = $this->Player->addPlayerInfo($accountId,$post_array,$cli_session);
					if($playerInfoArr == null){
						$return_Data = array( "ts"=> time(), "errcode" => -1, "errmsg" => "Character contains special characters");   
						echo json_encode($return_Data); 						
					}else{
						//等待完善
						
						//...
						echo  json_encode($playerInfoArr); //根据实际情况返回json
					}
				}else{
					//已经存在了对应的account信息
					$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Role already exists"); 
					echo json_encode($return_Data); 
				}
			}			 
		}else{
			$return_Data = array( "ts"=> time(), "errcode" => -3, "errmsg" => "Parameter error");  
			echo json_encode($return_Data); 		
		}
	}
	/*
		登录玩家
C->S	
{ 
	"module":"loginuser", 			//请求更新锦标塞信息
	"deviceUUID":"deviceToken",//设备号
	"channel":"deviceLogin",//登录方式 qqLogin smsLogin weiboLogin
	"deviceType":"IOS",//设备类型信息 android 
	"openID":"123123"//第三方登录平台返回的id，如果是设备登录不需要这个参数了或者为空
	"version":"1.1.1" //客户端版本
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":""
	"userInfo"：
	...
}	
	*/
	public function loginAccount($post_array){ 
		$unixtime = time();
		if(isset($post_array['deviceUUID']) && isset($post_array['channel']) && isset($post_array['version']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$accountId = $this->Player->selectAccountInfo($post_array);
			if($accountId == -1){
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Parameter error");  
				echo json_encode($return_Data); 					
			}
			if($accountId == 0){//没有任何角色 
				$return_Data = array( "ts"=> time(), "errcode" => -1, "errmsg" => "Role does not exist"); 
				echo  json_encode($return_Data); //根据实际情况返回json 
			}else{
				//找到account之后从玩家表里查询	
				$cli_session = md5($accountId.$post_array['deviceUUID']."fuck!###".$unixtime);	
				$this->Player->updateAccountInfo($accountId,$post_array,$this->GetClientIp());				
				$playerInfo = $this->Player->loginPlayer($accountId,$cli_session);
				if($playerInfo == null){//没有任何角色			
					$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Role does not exist");  
					echo  json_encode($return_Data); //根据实际情况返回json 
				}else{
					//找到account之后从玩家表里查询角色的全部信息 
					//等待完善
					echo  json_encode($playerInfo);
				}
			}			 
		}
	}
	/*
		绑定玩家 如果当前要绑定的账号下有角色了，绑定失败
		C->S	
{ 
	"module":"binduser", 			//请求更新锦标塞信息
	"deviceUUID":"deviceToken",//设备号
	"channel":"qqLogin",//绑定账号的方式 qqLogin smsLogin weiboLogin  
	"openID":"123123"   //第三方登录平台返回的id 
	"visitor":"12" //游客账号也就是对应的userinfo里的玩家id
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":"" 
}	
	*/
	public function bindAccount($post_array){ 
		$unixtime = time(); 
		if(isset($post_array['deviceUUID']) && isset($post_array['channel']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$ret = $this->Player->bindAccountInfo($post_array);
			if($ret != 0){ 
				$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "绑定角色失败");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}else{
				$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}			 
		}
	}
	/*
		玩家下线
C->S	
{ 
	"module":"logoutuser", 			//请求更新锦标塞信息
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":"" 
}
	*/
	public function logoutAccount($post_array){
		if(isset($post_array['session']) && isset($post_array['userid']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$cli_session =  $post_array['session'];//签名实际上就是服务器的session
			$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id
			$ret = $this->Player->checkUserAccount($accountId,$cli_session);
			if($ret == false){
				$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}else{ //找到这个玩家，那么处理下线的时间等等
				$this->Player->logoutPlayer($accountId);
				$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}			
		}else{
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Parameter error");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		} 
	}
	/*
		玩家重命名
C->S	
{ 
	"module":"rename", 			//请求更新锦标塞信息
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id
	"name":"fuck"
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳
	"errcode":"0"
	"errmsg":"" 
}
	*/
	public function renameUser($post_array)
	{
		if(isset($post_array['session']) && isset($post_array['userid'])&& isset($post_array['name']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$cli_session =  $post_array['session'];//签名实际上就是服务器的session
			$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id
			$name = $post_array['name'];
			$ret = $this->Player->checkUserAccount($accountId,$cli_session);
			if($ret == false){
				$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}else{ //找到这个玩家，那么处理下线的时间等等
				$return_Data = $this->Player->renamePlayer($accountId,$name);
				echo json_encode($return_Data); 
			}			
		}else{
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Parameter error");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		} 
	}
	/*
		完成主线任务 
		finshMainQuest
C->S	
{ 
	"module":"finishmainquest",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id
	"questid":"1"//完成的任务id
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳       
	"errcode":"0"
	"errmsg":"" 
	"reward":{
		"itemid": "1122"
		"count" : "10"
	}
}
	*/
	public function finshMainQuest($post_array)
	{
		if(isset($post_array['session']) && isset($post_array['userid'])&& isset($post_array['questid']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$cli_session =  $post_array['session'];//签名实际上就是服务器的session
			$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id
			$questid = $post_array['questid'];
			$ret = $this->Player->checkUserAccount($accountId,$cli_session);
			if($ret == false){
				$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
				echo  json_encode($return_Data); //根据实际情况返回json 
			}else{ //找到这个玩家，那么处理下线的时间等等
				$return_Data = $this->Player->finshMainQuest($accountId,$questid);
				echo json_encode($return_Data); 
			}			
		}else{
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Parameter error");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		} 
	}
	/*
		添加好友 addFriend 
C->S	
{ 
	"module":"addfriend",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id
	"adduserid":"123"//完成的任务id
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳       
	"errcode":"0"
	"errmsg":""  
}
	*/
	public function addFriend($post_array)
	{
		$userid = $post_array['adduserid'];
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{			
			//检查这个玩家在不在
			$playerInfo = $this->Player->selectPlayerInfo($userid);
			if($playerInfo == null){//没有任何角色
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Role does not exists"); 
				echo json_encode($return_Data); 
			}else{
				//找到自己的好友信息
				$friendInfo = $this->Player->getUserBlobInfo(array("friends"),$userid);
				if(isset($friendInfo["friends"][$userid])){//判断当前有没有这个好友信息
					$return_Data = array( "ts"=> time(), "errcode" => -3, "errmsg" => "Role does not exists"); 
					echo json_encode($return_Data);  
				}else{
					$friendInfo["friends"][$userid] = array();
					$this->Player->updateUserBlobInfo(array("friends"=>$friendInfo),$userid);
					$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "role" =>true); 
					echo json_encode($return_Data); 
				}
			}
		}		
	}
		/*
		赠送爱心 sendlovegift 
C->S	
{ 
	"module":"sendlovegift",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id
	"friendid":"123"//完成的任务id
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳       
	"errcode":"0"
	"errmsg":""  
}
	*/
	public function sendLovegift($post_array)
	{
		$friendid = $post_array['friendid'];
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{			
			//找到自己的好友信息
			$friendInfo = $this->Player->getUserBlobInfo(array("friends"),$accountId);
			if(count($friendInfo["friends"]) == 0)
			{
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "You have no Friends"); 
				echo json_encode($return_Data);  
			}
			else{
				if(isset($friendInfo["friends"][$friendid])){//判断当前有没有这个好友信息
					$return_Data = array( "ts"=> time(), "errcode" => -3, "errmsg" => "Role does not exists"); 
					echo json_encode($return_Data);  
				}else{
					$serverTime = time();
					//检查下CD时间
					$cdtime = $serverTime - $friendInfo["friends"][$friendid]['sendtime'];
					if($cdtime < 2 * 3600){
						$return_Data = array( "ts"=> time(), "errcode" => -4, "errmsg" => "CD time Error"); 
						echo json_encode($return_Data);  
					}else{
						//检查下有没有红花
						//等待完善
						//...
						//检查下自己发出去的有没有达到上限
						//检查下 对方接受的有没有达到上限
						$friendInfo["friends"][$friendid]['sendtime'] = $serverTime;
						//增加自己的发送次数
						//增加对方的接受次数
						$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");  
						echo  json_encode($return_Data); //根据实际情况返回json
					}
				}
			}
		}		
	}
	/*
		获取邮件列表信息 getmaillist 
C->S	
{ 
	"module":"getmaillist",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id 
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":"" 
	"maillist":
		{
			id:"1111111111111111111111111",//邮件id
			title:"xxxxxxxxxxxxxxxxxxxxxx",//标题
			detail:"xxxxxxxxxxxxxxxxxxxxxx",//标题
			attachment[]:it->count
			getrewardflag:"true/false"
			senderid:"123"
			sendername:"gaoke"
			icon:"123123"
			sendtime:138122345
			type:"0" 邮件类型
		}
		{...}
}
	*/	
	public function getMailList($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			//找到自己的好友信息
			$mailInfo = $this->Player->getMailList($accountId);  
			$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "maillist" =>$mailInfo); 
			echo json_encode($return_Data);  
		}		
	}
/*
		获取邮件附件 getMailAttachment 
C->S	
{ 
	"module":"getmailattachment",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id 
	"mailid":"123213"
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
}
	*/
	public function getMailAttachment($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$mailId =  $post_array['mailid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			//找到自己的好友信息
			$ret = $this->Player->getMailAttachment($accountId,$mailId);  
			if($ret == 0){
				$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "maillist" =>$mailId); 
			}else{
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Get Attachment Failed"); 
			}
			echo json_encode($return_Data);  
		}		
	}
	/*
		删除邮件 deleteMail 
C->S	
{ 
	"module":"deletemail",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id 
	"mailid":"123213"
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
}
	*/
	public function deleteMail($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$mailId =  $post_array['mailid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			//找到自己的好友信息
			$ret = $this->Player->deleteMail($accountId,$mailId);  
			if($ret == 0){
				$return_Data = array( "ts"=> time(), "errcode" => 0, "errmsg" => "", "maillist" =>$mailId); 
			}else{
				$return_Data = array( "ts"=> time(), "errcode" => -2, "errmsg" => "Delete E-Mail Failed"); 
			}
			echo json_encode($return_Data);  
		}		
	}
		/*
		获取签到列表 getsignlist 
C->S	
{ 
	"module":"getsignlist",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id  
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function getSignlist($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->getSignList($accountId);  
			echo json_encode($return_Data); 
		}		
	} 
/*
    获取签到列表 signtoday 
C->S	
{ 
	"module":"signtoday",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id  
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function signToday($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->signToday($accountId);   
			echo json_encode($return_Data);  
		}		
	} 
	/*
    获取每日任务 getdailytask 
C->S	
{ 
	"module":"getdailytask",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id  
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function getDailytask($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->getDailytask($accountId);   
			echo json_encode($return_Data);  
		}		
	}
	
	
	/*
    获取每日任务奖励 getdailytaskreward 
C->S	
{ 
	"module":"getdailytaskreward",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id  
	"taskindex":"1"任务索引
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function getDailytaskReward($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->getDailytaskReward($accountId,$post_array['taskindex']);   
			echo json_encode($return_Data);  
		}		
	}
/*
    获取每日活跃值奖励 getdailyliveness 
C->S	
{ 
	"module":"getdailyliveness",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id  
	"livenessindex":"1" 任务索引
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function getDailyliveness($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->getDailyliveness($accountId,$post_array['livenessindex']);   
			echo json_encode($return_Data);  
		}		
	}
/*
    刷新每日任务 refreshdailytask 
C->S	
{ 
	"module":"refreshdailytask",  
	"session":"1111111111111",//sessionid用来校验的
	"userid":"1",//玩家id   
}	
S->C
{ 
	"ts":"132323232323"//服务器时间戳      
	"errcode":"0"
	"errmsg":""  
	"signlist":{
		{}
	}
}
	*/
	public function refreshDailytask($post_array)
	{ 
		$cli_session =  $post_array['session'];//签名实际上就是服务器的session
		$accountId =  $post_array['userid'];//玩家的session其实就是玩家的id 
		$this->load->model("UserDB_model","Player");
		$ret = $this->Player->checkUserAccount($accountId,$cli_session);
		if($ret == false){
			$return_Data = array("ts"=> time(), "errcode" => -1, "errmsg" => "Logout");  
			echo  json_encode($return_Data); //根据实际情况返回json 
		}else{	
			$return_Data = $this->Player->refreshDailytask($accountId);   
			echo json_encode($return_Data);  
		}		
	}	
 
	/*
		所有的协议处理入口
	*/
	public function process(){ 
		$unixtime = time();	
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', $data_post);
		$post_array = json_decode($data_post,true);
		if(isset($post_array['module']))
		{ 
			$cmd_model = $post_array['module'];//处理的协议名
			if($cmd_model == "queryuser"){
				$this->getAccount($post_array);
			}else if($cmd_model == "createuser"){
				$this->createAccount($post_array);
			}else if($cmd_model == "loginuser"){
				$this->loginAccount($post_array);
			}else if($cmd_model == "binduser"){
				$this->bindAccount($post_array);
			}else if($cmd_model == "smsverify"){
				$this->smsVerify($post_array);
			}else if($cmd_model == "logoutuser"){
				$this->logoutAccount($post_array);
			}else if($cmd_model == "rename"){
				$this->renameUser($post_array);
			}else if($cmd_model == "rechargecallback"){
				//$this->rechargeCallback($post_array);
			}else if($cmd_model == "finshmainquest"){
				$this->finshMainQuest($post_array);
			}else if($cmd_model == "addfriend"){
				$this->addFriend($post_array);
			}else if($cmd_model == "getmaillist"){
				$this->getMailList($post_array);
			}else if($cmd_model == "getmailattachment"){
				$this->getMailAttachment($post_array);
			}else if($cmd_model == "deletemail"){
				$this->deleteMail($post_array);
			}else if($cmd_model == "getsignlist"){
				$this->getSignlist($post_array);
			}else if($cmd_model == "signtoday"){
				$this->signToday($post_array);
			}else if($cmd_model == "getdailytask"){
				$this->getDailytask($post_array);
			}else if($cmd_model == "getdailytaskreward"){
				$this->getDailytaskReward($post_array);
			}else if($cmd_model == "getdailyliveness"){
				$this->getDailyliveness($post_array);
			}else if($cmd_model == "refreshdailytask"){
				$this->refreshDailytask($post_array);
			} 
			//检查设备登录的数据库信息
			/*$this->load->model("UserDB_model","Player");
			$cli_session =  $post_array['sign'];//签名实际上就是服务器的session
			$accountId =  $post_array['sessionid'];//玩家的session其实就是玩家的id
			$ret = $this->Player->checkUserAccount($accountId,$cli_session);
			if($ret == false){
				$this->SendErrorMsg("-1","请重新登录游戏");  
			}else{
				$module = $post_array['module'];
				if($module == "rename"){
					$this->Player->procRename($post_array);
				}else if($module == "finishtask"){
					$this->Player->procFinishTask($post_array);
				}
			}*/			
		}
		/*if(isset($post_array['module']) && isset($post_array['sessionid']) && isset($post_array['sign']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$cli_session =  $post_array['sign'];//签名实际上就是服务器的session
			$accountId =  $post_array['sessionid'];//玩家的session其实就是玩家的id
			$ret = $this->Player->checkUserAccount($accountId,$cli_session);
			if($ret == false){
				$this->SendErrorMsg("-1","请重新登录游戏");  
			}else{
				$module = $post_array['module'];
				if($module == "rename"){
					$this->Player->procRename($post_array);
				}else if($module == "finishtask"){
					$this->Player->procFinishTask($post_array);
				}
			}			
		}*/
	}
	
	public function gm(){
		
	}
	 
	public function getversion(){
	
	}
	/*
		设备登录游戏
	*/
	public function login(){ 
		$unixtime = time();	
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', $data_post);
		$post_array = json_decode($data_post,true);
		if($post_array['module'] == "login" && isset($post_array['deviceUUID']))
		{ 
			//检查设备登录的数据库信息
			$this->load->model("UserDB_model","Player");
			$accountId = $this->Player->getAccountInfo($post_array,$this->GetClientIp());
			if($accountId == -1){
				$this->SendErrorMsg("-1","获取角色信息失败"); 
				return;
			}
			//找到account之后从玩家表里查询角色的全部信息
			$cli_session = md5($accountId.$post_array['deviceUUID']."fuck!###".$unixtime);
			$playerInfo = $this->Player->loginUser($accountId,$cli_session);
			echo  json_encode($playerInfo); 
		}
	}
}
