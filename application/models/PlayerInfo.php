<?php	
class PlayerInfo{
	/*
	friends:
	{
		friends = array("11111"=>array("sendtime"=>"123123213",),
						 "22222"=>array("sendtime"=>"123123213",),
						 ...);
	}
		mails  = array(
						"1111111"=>array(
		id:"1111111",//邮件id
		title:"xxxxxxxxxxxxxxxxxxxxxx",//标题
		detail:"xxxxxxxxxxxxxxxxxxxxxx",//标题
		attachment[]:it->count
		getrewardflag:"true/false"
		senderid:"123"
		sendername:"gaoke"
		icon:"123123"
		sendtime:138122345
		mailtype:"0/1"   邮件类型 0系统 ),
						"2222222"=>array(
		id:"2222222",//邮件id
		title:"xxxxxxxxxxxxxxxxxxxxxx",//标题
		detail:"xxxxxxxxxxxxxxxxxxxxxx",//标题
		attachment[]:it->count
		getrewardflag:"true/false"
		senderid:"123"
		sendername:"gaoke"
		icon:"123123"
		sendtime:138122345
		mailtype:"0/1"   邮件类型 0系统 ),
		...)
		)
		signreward=array("month"=>1,"days"=>12,"signtime"=>20170102);			//当前签到到几月份，连续钱到了几天，上次签到年月日
		userinfo = array("gender"=>male,
						"icon"=>"http://url", 
						"module"=array(1,2,3,4,5,6,7,8,9,10),
						"diamond"=>0,//钻石
						"gold"=>0,//金币
						"point"=>0,//积分
						"zeroresettime"=>strtotime(date('Y-m-d',strtotime('+1 day'))),//下次刷新时间戳是明天凌晨0点
						); 
		dailymission = array(
						"dailyliveness" = "123",
						"dailyprocess"  = array(1,2,3);//哪些档位领取过了
						"tasklist" = array("1"=>array("taskid"=>1,"count"=>1,"status"=>0),   "2"=>array("taskid"=>1,"count"=>10,"status"=>1)  ,"3"=>array("taskid"=>1,"count"=>100,"status"=>2));
					);
	}
	*/
		public $account;
		public $sessionid;
		public $createtime;//'账号创建时间',
		public $logintime;//'账号登录时间',
		public $offlinetime;//'账号下线时间',
		public $resettime;//'每日重置时间',
		public $userinfo;//'玩家基本信息',
		public $usermaterial;//'玩家材料信息',
		public $usercharacter;//'玩家角色信息',
		public $userpets;//'玩家宠物信息',
		public $userequip;//'玩家装备信息',
		public $userequip2;//'玩家灵器信息',
		public $usegeneralskills;//'玩家通用技能信息',
		public $usegameinfo;//'玩家一般游戏信息',
		public $friends;//'玩家好友信息',
		public $recharge;//'充值信息',
		public $mainmission;//'主线任务',
		public $achievement;//'成就任务',
		public $dailymission;//'日常任务',
		public $activpointreward;//'活跃度奖励',
		public $signreward;//'签到奖励',
		public $mailinfo;//邮件系统
		public $dailyreset;//每日重置数据  
		public $username;//玩家姓名
		public $level;//玩家等级
		public $vip;//玩家vip
		public function __construct(){
				
		}
		/*
			将对象的数据转换成数据库数据
		*/
		public function getRole_DBData(){
			return array(
				"account" => $this->account,//'玩家id', 
				"sessionid" => $this->sessionid,//sessionid
				
				"username" => $this->username,//username
				"level" => $this->level,//level
				"vip" => $this->vip,//vip 
				
				"createtime" => $this->createtime,// '账号创建时间',
				"logintime" => $this->logintime,//'账号登录时间',
				"offlinetime" => $this->offlinetime,//'账号下线时间',
				"resettime" => $this->resettime,//'每日重置时间',
				"userinfo" => gzcompress(json_encode($this->userinfo)),//'玩家基本信息',
				"usermaterial" => (gzcompress(json_encode($this->usermaterial))),//'玩家材料信息',
				"usercharacter" => (gzcompress(json_encode($this->usercharacter))),//'玩家角色信息',
				"userpets" => (gzcompress(json_encode($this->userpets))),//'玩家宠物信息',
				"userequip" => (gzcompress(json_encode($this->userequip))),//'玩家装备信息',
				"userequip2" => (gzcompress(json_encode($this->userequip2))),//'玩家灵器信息',
				"usegeneralskills" => (gzcompress(json_encode($this->usegeneralskills))),//'玩家通用技能信息',
				"usegameinfo" => (gzcompress(json_encode($this->usegameinfo))),// '玩家一般游戏信息',
				"friends" => (gzcompress(json_encode($this->friends))),// '玩家好友信息',
				"recharge" => (gzcompress(json_encode($this->recharge))),//'充值信息',
				"mainmission" => (gzcompress(json_encode($this->mainmission))),// '主线任务',
				"achievement" => (gzcompress(json_encode($this->achievement))),// '成就任务',
				"dailymission" => (gzcompress(json_encode($this->dailymission))),//'日常任务',
				"activpointreward" => (gzcompress(json_encode($this->activpointreward))),// '活跃度奖励',
				"signreward" => (gzcompress(json_encode($this->signreward))),//'签到奖励',
				"mailinfo" => (gzcompress(json_encode($this->mailinfo))),//'邮件', 
				"dailyreset" => (gzcompress(json_encode($this->dailyreset))),//'每日重置数据记录', 
			);
		}
		/*
			将对象的数据转换成数组
		*/
		public function getRoleArray(){
			return array(
				"account" => $this->account,//'玩家id', 
				"sessionid" => $this->sessionid,//sessionid
				"username" => $this->username,// 'username',
				"level" => $this->level,// 'level',
				"vip" => $this->vip,// 'vip',
				
				"createtime" => $this->createtime,// '账号创建时间',
				"logintime" => $this->logintime,//'账号登录时间',
				"offlinetime" => $this->offlinetime,//'账号下线时间',
				"resettime" => $this->resettime,//'每日重置时间',
				"userinfo" =>  $this->userinfo ,//'玩家基本信息',
				"usermaterial" => $this->usermaterial ,//'玩家材料信息',
				"usercharacter" => $this->usercharacter ,//'玩家角色信息',
				"userpets" =>  $this->userpets ,//'玩家宠物信息',
				"userequip" =>  $this->userequip ,//'玩家装备信息',
				"userequip2" =>  $this->userequip2 ,//'玩家灵器信息',
				"usegeneralskills" =>  $this->usegeneralskills ,//'玩家通用技能信息',
				"usegameinfo" =>  $this->usegameinfo ,// '玩家一般游戏信息',
				"friends" =>  $this->friends ,// '玩家好友信息',
				"recharge" =>  $this->recharge ,//'充值信息',
				"mainmission" =>  $this->mainmission ,// '主线任务',
				"achievement" =>  $this->achievement ,// '成就任务',
				"dailymission" =>  $this->dailymission ,//'日常任务',
				"activpointreward" =>  $this->activpointreward ,// '活跃度奖励',
				"signreward" =>  $this->signreward ,//'签到奖励',
				"mailinfo" => $this->mailinfo,//'邮件', 
				"dailyreset" => $this->dailyreset,//'每日重置数据 
			);
		}
		/*
			将数据库的数据转换成数组
		*/
		public function ReadToArray($row){
			$this->account = $row["account"];
			$this->sessionid = $row["sessionid"]; 
			$this->username = $row["username"];
			$this->level = $row["level"];
			$this->vip = $row["vip"];
			$this->createtime = $row["createtime"];
			$this->logintime = $row["logintime"];
			$this->offlinetime = $row["offlinetime"];
			$this->resettime = $row["resettime"];
			if($row["userinfo"] == null){
				$this->userinfo = array();
			}else{
				$this->userinfo = json_decode(gzuncompress($row["userinfo"]),true);
			}
			if($row["usermaterial"] == null){
				$this->usermaterial = array();
			}else{
				$this->usermaterial = json_decode(gzuncompress($row["usermaterial"]),true);
			}
			if($row["usercharacter"] == null){
				$this->usercharacter = array();
			}else{
				$this->usercharacter = json_decode(gzuncompress($row["usercharacter"]),true);
			}
			if($row["userpets"] == null){
				$this->userpets = array();
			}else{
				$this->userpets = json_decode(gzuncompress($row["userpets"]),true);
			}
			if($row["userequip"] == null){
				$this->userequip = array();
			}else{
				$this->userequip = json_decode(gzuncompress($row["userequip"]),true);
			}
			if($row["userequip2"] == null){
				$this->userequip2 = array();
			}else{
				$this->userequip2 = json_decode(gzuncompress($row["userequip2"]),true);
			}
			if($row["usegeneralskills"] == null){
				$this->usegeneralskills = array();
			}else{
				$this->usegeneralskills = json_decode(gzuncompress($row["usegeneralskills"]),true);
			}
			if($row["usegameinfo"] == null){
				$this->usegameinfo = array();
			}else{
				$this->usegameinfo = json_decode(gzuncompress($row["usegameinfo"]),true);
			}
			if($row["friends"] == null){
				$this->friends = array();
			}else{
				$this->friends = json_decode(gzuncompress($row["friends"]),true);
			}
			if($row["recharge"] == null){
				$this->recharge = array();
			}else{
				$this->recharge = json_decode(gzuncompress($row["recharge"]),true);
			}
			if($row["mainmission"] == null){
				$this->mainmission = array();
			}else{
				$this->mainmission = json_decode(gzuncompress($row["mainmission"]),true);
			}
			if($row["achievement"] == null){
				$this->achievement = array();
			}else{
				$this->achievement = json_decode(gzuncompress($row["achievement"]),true);
			}
			if($row["dailymission"] == null){
				$this->dailymission = array();
			}else{
				$this->dailymission = json_decode(gzuncompress($row["dailymission"]),true);
			}
			if($row["activpointreward"] == null){
				$this->activpointreward = array();
			}else{
				$this->activpointreward = json_decode(gzuncompress($row["activpointreward"]),true);
			}
			if($row["signreward"] == null){
				$this->signreward = array();
			}else{
				$this->signreward = json_decode(gzuncompress($row["signreward"]),true); 
			}
			if($row["mailinfo"] == null){
				$this->mailinfo = array();
			}else{
				$this->mailinfo = json_decode(gzuncompress($row["mailinfo"]),true); //'邮件',
			}
			if($row["dailyreset"] == null){
				$this->dailyreset = array();
			}else{
				$this->dailyreset = json_decode(gzuncompress($row["dailyreset"]),true);  
			}
		}
		/*
			将数据库的blob数据转换成数组
		*/
		static public function getArrayByBlob($row){ 
			$return_arr = array();
			foreach($row as $k => $v)
			{
				if($v == null){
					$return_arr[$k] = array();
				}else{
					$return_arr[$k] = json_decode(gzuncompress($v),true);
				}
			}
			return $return_arr;
		}
		
		
		/*
			初始化玩家的结构类型
		*/
		public function initRole($account,$name,$userInfo,$cli_session){
			$this->account = 	$account;	
			$this->sessionid  = $cli_session;
			$this->createtime = time();
			$this->logintime = $this->createtime;
			$this->offlinetime = 0; 
			$this->resettime = strtotime(date('Y-m-d',strtotime('+1 day') - 7200)) + 7200;//下次刷新每日任务的时间戳是明天凌晨两点
			$this->username = $name;
			$this->level = 1;
			$this->vip = 0;
			$this->userinfo = $userInfo;
			//一些初始化操作    比如任务，宠物    
			 
			 
			return $this->getRoleArray();			
		}
		/*
			下线后
		*/
		public function offlineRole(){
			$this->sessionid  = 0;
			$this->offlinetime = time();
			//一些初始化操作    比如任务，宠物    
			 
			  			
		}
		/*
			加载配置文件
		*/
		/*public function LoadXMl(){
			$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/";
			$xml = @simplexml_load_file($xmlpath.'test.xml');
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}*/
		/*
			加载签到配置文件
		*/
		/*static public function LoadSignXMl(){
			$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/";
			$xml = @simplexml_load_file($xmlpath.'qiandao.xml');
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}*/
		/*
			加载敏感词汇
		*/
		/*static public function LoadForbiddenTxt(){
			$file_path = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/forbidden.txt"; 
			$keywords_arr = array();
			if(file_exists($file_path)){ 
				$file_arr = file($file_path); 
				for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容
					$var = str_replace(array("\n","\t","\r"," "), "", $file_arr[$i]);
					array_push($keywords_arr,str_replace(' ', '', $var));
				}
			}	
			return $keywords_arr;
		}*/
		/*
			检查关键的屏蔽词存在不存在
		*/
		/*static public function ForbiddenWordsExists($str){
			$keywords_arr = $this->LoadForbiddenTxt();
			foreach($keywords_arr as $var){ 
				$domain = strstr($str, $var); 
				if($domain != null){
					return true;//说明存在了
				}
			}
			return false;
		}*/
}
?>
