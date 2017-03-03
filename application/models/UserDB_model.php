<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once ('PlayerInfo.php');
require_once ('xmlConfig.php');
class UserDB_model extends CI_Model {
    public function getUserBlobInfo($feilds,$acountId)
	{
		$queryStr = "SELECT ";
		foreach($feilds as $k => $v)
		{
			$queryStr = $queryStr.$v.",";
		}
		$queryStr = substr($queryStr,0,strlen($queryStr)-1);
		$queryStr .= " FROM player where account='$acountId';";
		$query = $this->db->query($queryStr);
        if($query->num_rows() == 0){
			   $return_arr = array();
			   foreach($feilds as $row_k => $row_v){
				   $return_arr[$row_v] = array();
			   }
               return $return_arr;
        }else{    
            foreach ($query->result_array() as $row)
            {     
				return PlayerInfo::getArrayByBlob($row);
            }    
        } 
	}
	public function updateUserBlobInfo($feilds,$acountId)
	{
		$update_array = array();
		foreach($feilds as $k => $v)
		{
			$update_array[$k] = gzcompress(json_encode($v));
		} 
		$this->db->where('account', $acountId)->update('player', $update_array);
	}
	public function getUserIntInfo($feilds,$acountId)
	{
		$queryStr = "SELECT ";
		foreach($feilds as $k => $v)
		{
			$queryStr = $queryStr.$v.",";
		}
		$queryStr = substr($queryStr,0,strlen($queryStr)-1);
		$queryStr .= " FROM player where account='$acountId';";
		$query = $this->db->query($queryStr);
        if($query->num_rows() == 0){
			   $return_arr = array();
               return $return_arr;
        }else{    
            foreach ($query->result_array() as $row)
            {     
				return $row;
            }    
        } 
	}
	
	public function addItemToPlayer($array_itemList,$accountId)
	{
		$curInfo = $this->getUserBlobInfo(array("userinfo","usermaterial","userequip","userequip2"),$accountId);
		foreach($array_list as $item_key => $item_count)
		{
			$domain = strstr($item_key, "gold"); 
			if($domain != null){
				//gold
			}else{
				$domain = strstr($str, "gold"); 
			}
		}
		$this->updateUserBlobInfo($curInfo,$accountId);
	}
	
	public function getAccountInfo($post_array,$cli_ip)
    {
		$deviceUUID = $post_array['deviceUUID'];
		$channel = $post_array['channel'];
		if($channel == "deviceLogin"){			
			$query = $this->db->query("SELECT account FROM account where uuid='$deviceUUID'  and thirdid='$channel';");
			if($query->num_rows() == 0){//没有任何角色信息，需要重新创建一个角色 
				$data = array('thirdid' => $channel, 'uuid' => $deviceUUID, 'idfa'=>$post_array['idfa'], 'devicetype'=>$post_array['deviceType'],
				'version' => $post_array['version'],'clientip' => $cli_ip);
				$str = $this->db->insert('account', $data); 
				$query = $this->db->query("SELECT account FROM account where uuid='$deviceUUID'   and thirdid='$channel';");
				if($query->num_rows() != 0){
					foreach ($query->result() as $row)
					{
						return $row->account;
					}
				}else{
						return -1;
				}
			}else{           
				foreach ($query->result() as $row)
				{
					//更新下数据
					$data = array(  
					'uuid' => $deviceUUID, 
					'idfa'=>$post_array['idfa'], 
					'devicetype'=>$post_array['deviceType'],
					'version' => $post_array['version'],
					'clientip' => $cli_ip);
					$this->db->where('account', $row->account)->update('account', $data); 
					return $row->account;
				}       
			} 
		}
		else if($channel == "qqLogin" || $channel == "smsLogin" || $channel == "weiboLogin"){	
			$userid = $post_array['openID'];
			$query = $this->db->query("SELECT account FROM account where userid='$userid'  and thirdid='$channel';");
			if($query->num_rows() == 0){//没有任何角色信息，需要重新创建一个角色 
				$data = array('thirdid' => $channel, 'userid' => $userid, 'uuid' => $deviceUUID, 'idfa'=>$post_array['idfa'], 'devicetype'=>$post_array['deviceType'],
				'version' => $post_array['version'],'clientip' => $cli_ip);
				$str = $this->db->insert('account', $data); 
				$query = $this->db->query("SELECT account FROM account where userid='$userid'   and thirdid='$channel';");
				if($query->num_rows() != 0){
					foreach ($query->result() as $row)
					{
						return $row->account;
					}
				}else{
						return -1;
				}
			}else{           
				foreach ($query->result() as $row)
				{
					return $row->account;
				}       
			} 
		}
    }
	/*
		玩家下线
	*/
	 public function getPlayerInistence($accountId)
    { 
	    $query = $this->db->query("SELECT * FROM player where account='$accountId';");
        if($query->num_rows() == 0){ 
               return null;
        }else{    
            foreach ($query->result_array() as $row)
            {   
                $playerInstense = new PlayerInfo(); 			
                $playerInstense->ReadToArray($row);//从数据库里解析出来的json格式数据
				return $playerInstense;
            }    
        }  
    }
	/*
		通过登录方式获得玩家有没有注册信息
	*/
	public function selectAccountInfo($post_array)
    {
		$deviceUUID = $post_array['deviceUUID'];
		$channel = $post_array['channel'];
		if($channel == "deviceLogin"){//设备登录方式查询有没有角色			
			$query = $this->db->query("SELECT account FROM account where uuid='$deviceUUID'  and thirdid='$channel';");
			if($query->num_rows() == 0){//没有任何角色信息 
				return 0;
			}else{        
				foreach ($query->result() as $row)
				{ 
					return $row->account;
				}       
			} 
		}
		else if(($channel == "qqLogin" || $channel == "smsLogin" || $channel == "weiboLogin") && isset($post_array['openID'])){	
			$userid = $post_array['openID'];
			$query = $this->db->query("SELECT account FROM account where userid='$userid'  and thirdid='$channel';");
			if($query->num_rows() == 0){//没有任何角色信息
				return 0;
			}else{           
				foreach ($query->result() as $row)
				{
					return $row->account;
				}       
			} 
		}
		else{
			return -1;
		}
    }
	/*
		通过玩家account 获得玩家信息
	*/
	public function selectPlayerInfo($accountId)
	{
		$query = $this->db->query("SELECT account,userinfo FROM player where account='$accountId';");
        if($query->num_rows() == 0){ 
                //没有任何角色信息 
				return null;
        }else{    
            foreach ($query->result_array() as $row)
            {   
				return $row;//返回角色信息
            }    
        }  
	}
	/*
		增加一条account信息
	*/
	public function addAccountInfo($post_array,$cli_ip)
	{
		$deviceUUID = $post_array['deviceUUID'];
		$channel = $post_array['channel'];
		
		if($channel == "qqLogin" || $channel == "smsLogin" || $channel == "weiboLogin"){	
			$userid = $post_array['openID'];
			$data = array('thirdid' => $channel, 'userid' => $userid, 'uuid' => $deviceUUID, 'idfa'=>$post_array['idfa'], 'devicetype'=>$post_array['deviceType'],
		'version' => $post_array['version'],'clientip' => $cli_ip);
			$str = $this->db->insert('account', $data); 
			$query = $this->db->query("SELECT account FROM account where userid='$userid'   and thirdid='$channel';");
			if($query->num_rows() != 0){
				foreach ($query->result() as $row)
				{
					return $row->account;
				}
			}else{
				return -1;
			}
		}else{
			$data = array('thirdid' => $channel, 'uuid' => $deviceUUID, 'idfa'=>$post_array['idfa'], 'devicetype'=>$post_array['deviceType'],
				'version' => $post_array['version'],'clientip' => $cli_ip);
			$str = $this->db->insert('account', $data); 	
			$query = $this->db->query("SELECT account FROM account where uuid='$deviceUUID'   and thirdid='$channel';");
			if($query->num_rows() != 0){
				foreach ($query->result() as $row)
				{
					return $row->account;
				}
			}else{
					return -1;
			}
		}
	}
	/*
		绑定玩家角色信息
	*/
	public function bindAccountInfo($post_array){ 
		$deviceUUID = $post_array['deviceUUID'];
		$channel = $post_array['channel'];
		$visitor = $post_array['visitor'];
		if($channel == "qqLogin" || $channel == "smsLogin" || $channel == "weiboLogin"){	
			$userid = $post_array['openID']; 
			$query = $this->db->query("SELECT account FROM account where userid='$userid'   and thirdid='$channel';");
			if($query->num_rows() == 0){//说明被绑定的第三方没有角色信息
				$query = $this->db->query("SELECT account FROM account where uuid='$deviceUUID'  and thirdid='deviceLogin and account = $visitor; ");
				if($query->num_rows() == 0){//说明没有角色信息
					return -3;
				}else{ 
					$update_array = array("thirdid"=>$channel,"userid"=>$userid);
					$this->db->where('account',  $visitor)->update('account', $update_array);  
				}
			}else{
				return -2;
			}
		}else{
			return -1;
		}
	}
	/*
		update玩家角色信息
	"module":"loginuser", 			//请求更新锦标塞信息
	"deviceUUID":"deviceToken",//设备号
	"channel":"deviceLogin",//登录方式 qqLogin smsLogin weiboLogin
	"deviceType":"IOS",//设备类型信息 android 
	"openID":"123123"//第三方登录平台返回的id，如果是设备登录不需要这个参数了或者为空
	"version":"1.1.1" //客户端版本
	*/
	public function updateAccountInfo($account,$post_array,$cli_ip){  
		$channel = $post_array['channel']; 
		if($channel == "qqLogin" || $channel == "smsLogin" || $channel == "weiboLogin"){ 
			$data = array('version' => $post_array['version'],'clientip' => $cli_ip);
			$ret = $this->db->where('account',  $account)->update('account', $data);   
		}
	}
	/*
		增加一个玩家信息
	*/
	public function addPlayerInfo($accountId,$post_array,$cli_session)
	{
		$deviceUUID = $post_array['deviceUUID'];
		$channel = $post_array['channel'];
		$playerInstense = new PlayerInfo();
		$username = "";
		$userinfo = array("uid"=>$accountId, 
									"gender"=>"male",//性别
									"icon"=>"",//头象 字符串还是数字待定 
									"diamond"=>0,//钻石
									"gold"=>0,//金币
									"point"=>0,//积分
									"zeroresettime"=>strtotime(date('Y-m-d',strtotime('+1 day'))),//下次刷新时间戳是明天凌晨0点
									);
		if($channel == "qqLogin" || $channel == "weiboLogin"){
			$username = mysql_real_escape_string($post_array["nickName"]);
			$userinfo["gender"] = $post_array["gender"];
			$userinfo["icon"] = $post_array["icon"];
		}else if($channel == "smsLogin"){
            //检查一下玩家的姓名是不是屏蔽词
			if(xmlConfig::ForbiddenWordsExists($post_array["nickName"]) == true){
				return null;				
			} 	 
			$username = mysql_real_escape_string($post_array["nickName"]);
			$userinfo["gender"] = $post_array["gender"];
			$userinfo["icon"] = $post_array["icon"];
		}else{
			$subName = 10000 + $accountId;
			$username = "游客".$subName;  
		}	
		
        $playerData = $playerInstense->initRole($accountId,$username,$userinfo,$cli_session); 
        $str = $this->db->insert('player', $playerInstense->getRole_DBData()); 
        return $playerData;//返回多维数组
	}
	/*
		玩家登录
	*/
	 public function loginPlayer($accountId,$cli_session)
    { 
	    $query = $this->db->query("SELECT * FROM player where account='$accountId';");
        if($query->num_rows() == 0){ 
               return null;
        }else{    
            foreach ($query->result_array() as $row)
            {   
                $playerInstense = new PlayerInfo();  
				$row["sessionid"] = $cli_session;				
                $playerInstense->ReadToArray($row);//从数据库里解析出来的json格式数据
				//登录后更新下登录时间，任务或者需要重置的东西
				//
				$playerInstense->logintime = time();
				$playerInstense->sessionid =  $cli_session;
				$array_dailyTask = $this->checkUpdateDailyTask($accountId,$playerInstense->resettime);
				if($array_dailyTask != null){
					$playerInstense->resettime = $array_dailyTask['resettime'];
					$playerInstense->dailymission = $array_dailyTask['dailymission'];
				}
				//检查要不要刷新每日重新结算东西
				$reset_daily = $this->checkResetDailyData($accountId,$playerInstense->resettime);
				if($reset_daily != null){
					$playerInstense->dailyreset = $reset_daily;
				}
				//$new_mail = $this->addMail($accountId,array());//检查下邮件是不是已经过期了
				//if($new_mail != null)
				//{
				//	$playerInstense->mailinfo = $new_mail;
				//}
				//检查下爱心值
				$this->log->write_log($level = 'error', "loginUser:".$playerInstense->sessionid);
				//....
				$this->db->where('account', $accountId)->update('player', $playerInstense->getRole_DBData()); 
				return $playerInstense->getRoleArray();
            }    
        }  
    }
	/*
		玩家下线
	*/
	 public function logoutPlayer($accountId)
    { 
		$playerInstense = new PlayerInfo();
	    $playerInstense = $this->getPlayerInistence($accountId); 
        if($playerInstense != null){  //登录后更新下登录时间，任务或者需要重置的东西
				$playerInstense->offlineRole(); 
				$this->db->where('account', $accountId)->update('player', $playerInstense->getRole_DBData()); 
        }  
    }
	
    public function loginUser($accountId,$cli_session)
    { 
	    $query = $this->db->query("SELECT * FROM player where account='$accountId';");
		$this->log->write_log($level = 'error', "SELECT * FROM player where account='$accountId';");
        if($query->num_rows() == 0){ 
                //没有任何角色信息，需要重新创建一个角色 
                //$COMPRESS_CONTENT = bin2hex(gzcompress($CONTENT)); //压缩二进制字段 addslashes 防止sql注入  $username = addslashes($username);
                $playerInstense = new PlayerInfo();
				$userinfo = array("uid"=>$accountId, 
									"gender"=>"male",//性别
									"icon"=>"",//头象 字符串还是数字待定
									"level"=>1,//等级
									"vipLevel"=>0,//VIP等级
									"diamond"=>0,//钻石
									"gold"=>0,//金币
									"point"=>0,//积分
									);
                $playerData = $playerInstense->initRole($accountId,"",$userinfo,$cli_session); 
                $str = $this->db->insert('player', $playerInstense->getRole_DBData()); 
                return $playerData;//返回多维数组
        }else{    
            foreach ($query->result_array() as $row)
            {   
                $playerInstense = new PlayerInfo();  
				$row["sessionid"] = $cli_session;				
                $playerInstense->ReadToArray($row);//从数据库里解析出来的json格式数据
				//登录后更新下登录时间，任务或者需要重置的东西
				//
				$playerInstense->logintime = time();
				$playerInstense->sessionid =  $cli_session;
				$this->log->write_log($level = 'error', "loginUser:".$playerInstense->sessionid);
				//....
				$this->db->where('account', $accountId)->update('player', $playerInstense->getRole_DBData()); 
				return $playerInstense->getRoleArray();
            }    
        }  
    }
	/*
		检查玩家在不在线或者当前发送过来seesion的是不是存在玩家
	*/
	public function checkUserInfo($accountId,$cli_session){
		$query = $this->db->query("SELECT * FROM player where account='$accountId' and sessionid='$cli_session';");
        if($query->num_rows() == 0){ 
                return null;
        }else{    
            foreach ($query->result_array() as $row)
            {   
                $playerInstense = new PlayerInfo();
                $playerInstense->ReadToArray($row);
				return $playerInstense;
            }    
        }  
	}
	/*
		检查玩家在不在线或者当前发送过来seesion的是不是存在玩家
	*/
	public function checkUserAccount($accountId,$cli_session){
		$query = $this->db->query("SELECT account,resettime FROM player where account='$accountId' and sessionid='$cli_session';");
        if($query->num_rows() == 0){ 
            return false;
        }else{    
			//检查要不要刷新每日重置的数据
			foreach ($query->result_array() as $row)
            {   
                $resettime = $row['resettime'];
				$array_dailyTask = $this->checkUpdateDailyTask($accountId,$row['resettime']);//检查要不要更新每日任务
				if($array_dailyTask != null){
					$this->updateUserBlobInfo($array_dailyTask,$accountId); 
				}
				//检查要不要刷新每日重新结算东西
				$reset_daily = $this->checkResetDailyData($accountId,$playerInstense->resettime);
				if($reset_daily != null){
					$playerInstense->dailyreset = $reset_daily;
				}
				break;
            }  
            return true;  
        }  
	}
	/*
	    玩家重新命名
	*/	
    public function renamePlayer($userid,$name)
    {
		$return_data = array();
        //检查空，屏蔽词
		if(xmlConfig::ForbiddenWordsExists($post_array["nickName"]) == true || empty($name)){
			return array("ts"=> time(), "errcode" => -1, "errmsg" => "Character contains special characters"); 
		}  
		//要不要扣除资源
    }
	/*
	    玩家完成主线任务
	*/	
    public function finshMainQuest($userid,$questid)
	{
		$return_data = array();
        require_once ('PlayerInfo.php');
		//要不要扣除资源
    }
 
	/*
		发送邮件 必须是key=>value的格式  key是邮件id  value是邮件内容  
		mail:{
			id:"1111111111111111111111111",//邮件id
			title:"xxxxxxxxxxxxxxxxxxxxxx",//标题
			detail:"xxxxxxxxxxxxxxxxxxxxxx",//标题
			attachment[]:it->count
			getrewardflag:"true/false"
			senderid:"123"
			sendername:"gaoke"
			icon:"123123"
			sendtime:138122345
			mailtype:"0/1"   邮件类型 0系统 
		}
	*/
	public function addMail($accountId,$newmail_array){
		$curMailInfo = $this->getUserBlobInfo(array("mailinfo"),$accountId);
		$this->log->write_log($level = 'error', "newmail========:".json_encode($newmail_array));

		$changed = false;//是否有数据库更新 
		foreach($newmail_array as $k => $v)
		{ 
			foreach($v as $mail_k => $mail_v){
				$curMailInfo['mailinfo'][$mail_k]=$mail_v;
				$changed = true;				
			}			
		} 
		$this->log->write_log($level = 'error', "current mail========:".json_encode($curMailInfo['mailinfo']));
		//然后处理过期的邮件
		$time_now = time();
		$new_mail = array();
		$add_attachment = array();//处理过期邮件或者删除多余邮件 增加的奖励
		if(count($curMailInfo['mailinfo'])){
			
			foreach($curMailInfo['mailinfo'] as $k => $v)			{
				if($v['sendtime'] + 15 * 86400 <= $time_now )//需要删除的邮件 
				{					 
					//查一下有没有领取过邮件附件，如果没有领取，直接加在身上
					if($v['getrewardflag'] == false && count($v['attachment']) > 0){
						foreach($v['attachment'] as $attach_key => $attach_value)
						{
							$add_attachment[$attach_key] = $add_attachment[$attach_key] + $attach_value;
						}						 
					}
					$changed = true;
				}else{					
					$new_mail[$k]=$v;
				} 
			}
		}
		//处理多余的邮件		
		$max_mail = count($new_mail);
		if($max_mail > 30)
		{		
			$curMailInfo = $new_mail;
			$new_mail = array();	
			$mail_count = 0;			
			foreach($curMailInfo as $k => $v)
			{
				if($mail_count < $max_mail - 30)//需要删除的邮件 
				{					 
					//查一下有没有领取过邮件附件，如果没有领取，直接加在身上
					if($v['getrewardflag'] == false && count($v['attachment']) > 0){
						foreach($v['attachment'] as $attach_key => $attach_value)
						{
							$add_attachment[$attach_key] = $add_attachment[$attach_key] + $attach_value;
						}						 
					}
				}else{					
					$new_mail[$k]=$v;
				}
				$mail_count = $mail_count + 1;
			}
			$this->updateUserBlobInfo(array("mailinfo"=>$new_mail),$accountId); 
            //需要完善   增加道具 $this->getPlayerInistence($accountId);
			$this->addItemToPlayer($add_attachment,$accountId);
			return $new_mail;
		} else{
			if($changed == true){
				$this->updateUserBlobInfo(array("mailinfo"=>$new_mail),$accountId); 
				$this->addItemToPlayer($add_attachment,$accountId);
			}
			return $curMailInfo["mailinfo"];
		}
	} 
	/*
		拉取邮件列表
	*/
	public function getMailList($accountId){
		return $this->addMail($accountId,array());
	}
	/*
		领取邮件奖励
	*/
	public function getMailAttachment($accountId,$mailId){
		$curMailInfo = $this->getUserBlobInfo(array("mailinfo"),$accountId);
		if(count($curMailInfo['mailinfo']))
		{
			if(array_key_exists($mailId,$curMailInfo['mailinfo']))
			{
				$add_attachment = array();
				//查一下有没有领取过邮件附件，如果没有领取，直接加在身上
				if($curMailInfo['mailinfo'][$mailId]['getrewardflag'] == false && count($curMailInfo['mailinfo'][$mailId]['attachment']) > 0){
					//增加道具并且修改领取状态
					$this->log->write_log($level = 'error', "getreward:".json_encode($curMailInfo['mailinfo'][$mailId]['attachment']));
					$curMailInfo['mailinfo'][$mailId]['getrewardflag'] = true;
					$this->updateUserBlobInfo(array("mailinfo"=>$curMailInfo['mailinfo']),$accountId);
					$this->addItemToPlayer($curMailInfo['mailinfo'][$mailId]['attachment'],$accountId); 
					return 0;
				}else{
					return -2;//奖励不存在
				}				
			}else{
				return -1;//邮件不存在
			}
		}else{
			return -1;//到时候看看返回啥
		}
	}
	/*
		删除邮件
	*/
	public function deleteMail($accountId,$mailId){
		$curMailInfo = $this->getUserBlobInfo(array("mailinfo"),$accountId);
		if(count($curMailInfo['mailinfo']))
		{
			if(array_key_exists($mailId,$curMailInfo['mailinfo']))
			{ 
				unset($curMailInfo['mailinfo'][$mailId]); 
				$this->updateUserBlobInfo(array("mailinfo"=>$curMailInfo['mailinfo']),$accountId);
				//$this->db->where('account', $accountId)->update('player', array("mailinfo"=>gzcompress(json_encode($curMailInfo['mailinfo'])))); 
				return 0;
			}else{
				return -1;//邮件不存在
			}
		}else{
			return -1;//到时候看看返回啥
		}
	}
	/*
		获取签到列表
	*/
	public function getSignList($accountId)
	{
		$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");  
		$year = date("Y");
        $month = date("m");
		$day =  date("d");
        $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);//当前月份一共有多少天
		$time_now = time();
		$lastsignmonth = 0;
		$lastsigndays = 0;
		$yearmonthday = date("Ymd");//当前年月日
		$curSignInfo = $this->getUserBlobInfo(array("signreward"),$accountId);
		if(count($curSignInfo['signreward']))
		{
			if(array_key_exists("month",$curSignInfo['signreward']))
			{ 
				$lastsignmonth = $curSignInfo['signreward']["month"];
				$lastsigndays = $curSignInfo['signreward']["days"];
			}
		}else{
			$curSignInfo['signreward'] = array();
		}
		if($month != $lastsignmonth){//重新开始
			$curSignInfo['signreward']["month"] = $month;
			$curSignInfo['signreward']["days"] = 0;
			$curSignInfo['signreward']["signtime"] = 0;	
			$this->updateUserBlobInfo(array("signreward"=>$curSignInfo['signreward']),$accountId);			
			//$this->db->where('account', $accountId)->update('player', array("signreward"=>gzcompress(json_encode($curSignInfo['signreward']))));  			
		} 
		
		if($curSignInfo['signreward']["signtime"] == $yearmonthday)
		{
			$current_signday = $curSignInfo['signreward']["days"];
			$current_signstatus = true;//已经签到
		}else{
			$current_signday = $curSignInfo['signreward']["days"] + 1;
			$current_signstatus = false;//未签到
		}	
		
		//打印输出当前已经签到到哪里 
		$return_Data["data"] = array("totaldays"=>$days,"currentday"=>$current_signday, "signstatus"=> $current_signstatus);
		return $return_Data;
	}
	
	/*
		签到奖励领取
	*/
	public function signToday($accountId)
	{
		$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");  
		$year = date("Y");
        $month = date("m");
		$day =  date("d");
        $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);//当前月份一共有多少天
		$time_now = time();
		$lastsignmonth = 0;
		$lastsigndays = 0;
		$yearmonthday = date("Ymd");//当前年月日
		$curSignInfo = $this->getUserBlobInfo(array("signreward"),$accountId);
		if(count($curSignInfo['signreward']))
		{
			if(array_key_exists("month",$curSignInfo['signreward']))
			{ 
				$lastsignmonth = $curSignInfo['signreward']["month"];
				$lastsigndays = $curSignInfo['signreward']["days"];
			}
		}else{
			$curSignInfo['signreward'] = array();
		}
		if($month != $lastsignmonth){//重新开始
			$curSignInfo['signreward']["month"] = $month;
			$curSignInfo['signreward']["days"] = 0;
			$curSignInfo['signreward']["signtime"] = 0;					
		} 
		
		if($curSignInfo['signreward']["signtime"] == $yearmonthday)
		{
			$current_signday = $curSignInfo['signreward']["days"];
			$current_signstatus = true;//已经签到
			return array("ts"=> time(), "errcode" => -1, "errmsg" => "Sign Error");
		}else{
			$current_signday = $curSignInfo['signreward']["days"] + 1;
			$current_signstatus = true;//未签到
			$curSignInfo['signreward']["days"] = $current_signday;
			$curSignInfo['signreward']["signtime"] = $yearmonthday;
			$this->updateUserBlobInfo(array("signreward"=>$curSignInfo['signreward']),$accountId);	
			//领取签到奖励
			$retSignConf = xmlConfig::LoadSignXMl();
			$str_sheets = "qiandao_qiandao_".$days."s";
			$str_items = "qiandao_".$days;
			foreach($retSignConf[$str_sheets][$str_items] as $sheeet_key => $sheet_value){
				foreach($sheet_value as $item_key => $value){
					if($value['id'] == $curSignInfo['signreward']["days"])
					{
						$attachment = xmlConfig::getReward($value['reward']);
						$double = $value['double'];
						//找一下玩家是多少Vip等级的
						$vipInfo = $this->getUserIntInfo(array("vip"),$accountId);
						if(count($vipInfo) && $vipInfo["vip"] >= $double){//翻倍
							foreach($attachment as $attach_key => $attach_value)
							{
								$attachment[$attach_key] =  $attach_value*2;
							}
						}
						////增加奖励给玩家 
						$this->addItemToPlayer($attachment,$accountId);
						break;
					}
				}
			}
		}	
		
		//打印输出当前已经签到到哪里 
		$return_Data["data"] = array("totaldays"=>$days,"currentday"=>$current_signday, "signstatus"=> $current_signstatus);
		return $return_Data;
	}
	/*
		获取当天每日任务
	*/
	public function getDailytask($accountId)
	{
		$return_Data = array("ts"=> time(), "errcode" => 0, "errmsg" => "");
		$curMissionInfo = $this->getUserBlobInfo(array("dailymission"),$accountId);
		if(count($curMissionInfo['dailymission']))
		{
			$return_Data['data'] = $curMissionInfo['dailymission'];
		}else{
			$this->log->write_log($level = 'error', "cannot find getDailytask data in db ");
			$daily_data = array();			
		}
		return $return_Data;
	}
	/*
		领取每日任务奖励
	*/
	public function getDailytaskReward($accountId,$taskid){ 
		$curMissionInfo = $this->getUserBlobInfo(array("dailymission"),$accountId);
		if(count($curMissionInfo['dailymission']))
		{
			$flag = false;
			foreach($curMissionInfo['dailymission']['tasklist'] as $task_key => $task_value){
				if($task_key == $taskid){//找到这个任务
					if($task_value['status'] == 2){//已经领取过奖励了
						return array("ts"=> time(), "errcode" => -1, "errmsg" => "Rewards has been Got");
					}
					if($task_value['status'] == 0){//已经领取过奖励了
						return array("ts"=> time(), "errcode" => -2, "errmsg" => "Condition Error");
					}
					//发送奖励，并且更改状态 等待完善
					//加载配置
					$curMissionInfo['dailymission']['tasklist'][$task_key]['status'] = 2;
					//$this->addItemToPlayer($attachment,$accountId);
					$this->updateUserBlobInfo(array("dailymission"=>$curMissionInfo['dailymission']),$accountId);	
					$flag = true;
					break;
				}
			}
			if($flag == false){
				return array("ts"=> time(), "errcode" => -3, "errmsg" => "Task does not exist");  
			}
		}else{
			return array("ts"=> time(), "errcode" => -4, "errmsg" => "Get DailyReward Failed"); 
		} 
	}
	/*
		领取每日活跃值奖励
	*/
	public function getDailyliveness($accountId,$liveness){ 
		$curMissionInfo = $this->getUserBlobInfo(array("dailymission"),$accountId);
		if(count($curMissionInfo['dailymission']))
		{
			$flag = false;
			//先检查下当前活跃值够不够领取这个档位的奖励
			//等待完善
			if(array_key_exists($liveness,$curMissionInfo['dailymission']['dailyprocess'])){ 
				return array("ts"=> time(), "errcode" => -1, "errmsg" => "Rewards has been Got"); 
			}
			//如果没有领取过，加进来
			array_push($curMissionInfo['dailymission']['dailyprocess'],$liveness);
			$this->updateUserBlobInfo(array("dailymission"=>$curMissionInfo['dailymission']),$accountId);	
		}else{
			return array("ts"=> time(), "errcode" => -4, "errmsg" => "Get DailyReward Failed"); 
		} 
	}
	/*
		刷新每日未完成的任务
	*/
	public function refreshDailytask($accountId,$liveness){ 
		$curMissionInfo = $this->getUserBlobInfo(array("dailymission"),$accountId);
		if(count($curMissionInfo['dailymission']))
		{
			$flag = false;
			//先检查下当前资源够不够 
			//等待完善
			foreach($curMissionInfo['dailymission']['tasklist'] as $task_key => $task_value){ 
				if($task_value['status'] == 2 || $task_value['status'] == 1){//已经完成的
					continue;
				}
				//刷新任务等待完善
				//...
			}
			$this->updateUserBlobInfo(array("dailymission"=>$curMissionInfo['dailymission']),$accountId); 
		}else{
			return array("ts"=> time(), "errcode" => -4, "errmsg" => "Get DailyReward Failed"); 
		} 
	}
	/*
		更新每日任务的接口
		$type:  1
				2
				3
				4
				5
				6
				7
				8
				9
	*/
	public function updateDailyTask($type,$count){
		$curMissionInfo = $this->getUserBlobInfo(array("dailymission"),$accountId);
		if(count($curMissionInfo['dailymission']) == 0)
		{
			$curMissionInfo['dailymission']['tasklist'] = array();
		}
		$xmlData = xmlConfig::LoadDailyTaskXMl();
		switch($type)
		{
			case 1:
			case 2:
				//先找一下有么有这个任务
				if(array_key_exists($type,$curMissionInfo['dailymission']['tasklist'])){
					//判断下当前任务的次数是不是已经达到可以领取了，增加活跃值,等待完善
				}
				break;
			case 3:
			case 4:
			
			case 5:
			case 6:
		}
	}
	/*
		检测要不要刷新每日任务
	*/
	public function checkUpdateDailyTask($account,$resettime){
		$time_now = time(0); 
		if($time_now >= $resettime)//需要刷新
		{
			$resettime = strtotime(date('Y-m-d',strtotime('+1 day') - 7200)) + 7200;//下次刷新每日任务的时间戳是明天凌晨两点 
			$curMissionInfo = array("dailyliveness" => 0,"dailyprocess"=> array());
			//生成任务，等待完善		
			/*
				dailymission = array(
						"dailyliveness" = "123",
						"dailyprocess"  = array(1,2,3);//哪些档位领取过了
						"tasklist" = array("1"=>array("taskid"=>1,"count"=>1,"status"=>0),   "2"=>array("taskid"=>1,"count"=>10,"status"=>1)  ,"3"=>array("taskid"=>1,"count"=>100,"status"=>2));
					);
			*/
			return array("dailymission"=>$curMissionInfo,"resettime"=>$resettime);	 
		}else{
			return null;
		}
	}
	/*
		检测要不要刷新每日重置的数据
	*/
	public function checkResetDailyData($account,$resettime){
		$time_now = time(0); 
		if($time_now >= $resettime)//需要刷新
		{
			return array();	 
		}else{
			return null;
		}
	}
	/*
		获得所有玩家的邮件信息
	*/
	public function getAllPlayerMailInfo($user_arr){
		if(count($user_arr) == 0){
			$queryStr = "SELECT account FROM player;";			
		}else{
			$queryStr = "SELECT account FROM player where account in (";
			foreach($user_arr as $k => $v)
			{
				$queryStr = $queryStr.$v.",";
			}
			$queryStr = substr($queryStr,0,strlen($queryStr)-1);
			$queryStr .= ");";
		}
		$query = $this->db->query($queryStr);
		if($query->num_rows() == 0){ 
			return null;	
		}else{    
			$return_arr = array();
			foreach ($query->result_array() as $row)
			{   
				array_push($return_arr,$row['account']);
			}    
			return $return_arr;
		} 
	}
	 
}

	
?>
