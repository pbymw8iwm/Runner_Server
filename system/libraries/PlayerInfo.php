<?php	
class PlayerInfo{
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
		public function __construct(){
				
		}
		/*
			将对象的数据转换成数据库数据
		*/
		public function getRole_DBData(){
			return array(
				"account" => $this->account,//'玩家id', 
				"sessionid" => $this->sessionid,//sessionid
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
			);
		}
		/*
			将对象的数据转换成数组
		*/
		public function getRoleArray(){
			return array(
				"account" => $this->account,//'玩家id', 
				"sessionid" => $this->sessionid,//sessionid
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
			);
		}
		/*
			将数据库的数据转换成数组
		*/
		public function ReadToArray($row){
			$this->account = $row["account"];
			$this->sessionid = $row["sessionid"]; 
			$this->createtime = $row["createtime"];
			$this->logintime = $row["logintime"];
			$this->offlinetime = $row["offlinetime"];
			$this->resettime = $row["resettime"];
			$this->userinfo = json_decode(gzuncompress($row["userinfo"]));
			$this->usermaterial = json_decode(gzuncompress($row["usermaterial"]));
			$this->usercharacter = json_decode(gzuncompress($row["usercharacter"]));
			$this->userpets = json_decode(gzuncompress($row["userpets"]));
			$this->userequip = json_decode(gzuncompress($row["userequip"]));
			$this->userequip2 = json_decode(gzuncompress($row["userequip2"]));
			$this->usegeneralskills = json_decode(gzuncompress($row["usegeneralskills"]));
			$this->usegameinfo = json_decode(gzuncompress($row["usegameinfo"]));
			$this->friends = json_decode(gzuncompress($row["friends"]));
			$this->recharge = json_decode(gzuncompress($row["recharge"]));
			$this->mainmission = json_decode(gzuncompress($row["mainmission"]));
			$this->achievement = json_decode(gzuncompress($row["achievement"]));
			$this->dailymission = json_decode(gzuncompress($row["dailymission"]));
			$this->activpointreward = json_decode(gzuncompress($row["activpointreward"]));
			$this->signreward = json_decode(gzuncompress($row["signreward"])); 
		}
		/*
			返回数据库直接可以存贮的结构类型
		*/
		public function initRole($account,$cli_session){
			$this->account = 	$account;	
			$this->sessionid  = $cli_session;
			$this->createtime = time();
			$this->logintime = $this->createtime;
			$this->offlinetime = 0;
			$this->resettime = 0;
			$this->userinfo = array("uid"=>$account,
									"nickName"=>"",//呢称
									"gender"=>"male",//性别
									"icon"=>"",//头象 字符串还是数字待定
									"level"=>1,//等级
									"vipLevel"=>0,//VIP等级
									"diamond"=>0,//钻石
									"gold"=>0,//金币
									"point"=>0,//积分
									);
			//一些初始化操作    比如任务，宠物    
			 
			 
			return $this->getRoleArray();			
		}
		public function LoadXMl(){
			$xml = @simplexml_load_file('test.xml');
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}
	}
?>
