<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class WeeklyScript extends CI_Controller {
	function __construct()  
        { 
            parent::__construct();
			$this->load->model("WeeklyScript_model","WeeklyScript"); 
			$this->load->model("UserDB_model","Player"); 
			$this->calcReward();//结算世界排名奖励
			$this->clearWeeklyScore();
        } 
	public function index(){
		
	}
	public function clearWeeklyScore(){
		$userScore_arr = $this->WeeklyScript->clear();
	}
	public function getReward($str){
		$attachment = array();
		$rewards_list = explode("_",$str);
		foreach($rewards_list as $reward_key => $reward_value)
		{
			$reward_pair = explode("-",$reward_value);
			if(count($reward_pair) == 2){
				$attachment[$reward_pair[0]] = $reward_pair[1];
			}
		}
		return $attachment;
	}
	public function calcReward(){   
		//检查设备登录的数据库信息 
		$userScore_arr = $this->WeeklyScript->getWeeklyScoreInfo();
		if($userScore_arr != null)
		{
			$xmlData = $this->LoadXMl();
			$myOrder = 0;
			$curDate = date("YmdHis");
			foreach($userScore_arr as $userid => $score)
			{
				$myOrder++;
				$addMail_arr = array();
				
				//世界排名奖励
				$world_reward_item = null;
				foreach($xmlData['activity_wordranks']['wordrank'] as $rank_key => $rank_value){
                    foreach($rank_value as $item_key => $item_value){
                        $this->log->write_log($level = 'error', "items key:$item_key value:".json_encode($item_value));
                        $split_rank = explode("-",$item_value['wordrank_id']);
                        if(count($split_rank) == 1){
                                if($myOrder == $split_rank[0]){
                                        $world_reward_item = $item_value;
                                        break;
                                }
                        }else if(count($split_rank) == 2){
                                if($myOrder >= $split_rank[0] && $myOrder <= $split_rank[1]){
                                        $world_reward_item = $item_value;
                                        break;
                                }
                        }
                    }
                }

				if($world_reward_item != null)
				{
					//发送奖励 
					$mailId = $curDate . uniqid();
					$detail = sprintf($world_reward_item['wordrank_mailcontent'],$myOrder);
					$attachment = $this->getReward($world_reward_item['wordrank_reward']); 
					$newmail_array = array( "$mailId" => array(
												"id"=>$mailId,//邮件id
												"title"=>$world_reward_item['wordrank_mailtitle'],//标题
												"detail"=>$detail,//
												"attachment"=>$attachment,
												"getrewardflag"=>false,
												"senderid"=>"0",
												"sendername"=>"",
												"icon"=>"",
												"sendtime"=>time(0),
												"mailtype"=>0,
											));
					array_push($addMail_arr,$newmail_array);				
				}
				
				//发送好友邮件
				//找到自己的好友信息
				$friendInfo = $this->Player->getUserBlobInfo(array("friends"),$userid);  
				if($friendInfo == null){
					if(count($addMail_arr)){
						$this->Player->addMail($userid,$addMail_arr);
					}
					continue;
				} 
				if(count($friendInfo) < 10){//好友数目超过10个才算
					if(count($addMail_arr)){
						$this->Player->addMail($userid,$addMail_arr);
					}
					continue;
				}
				$calcUser_arr = array();//保存需要查询的好友积分，把自己也放进去
				foreach($friendInfo as $key_id => $value_id){
					array_push($calcUser_arr,$key_id);
				}
				array_push($calcUser_arr,$userid);
				$Score_arr = $this->WeeklyScript->getWeeklyScoreUserInfo($calcUser_arr);//找出这些人的积分信息
				$order = 0;
				foreach($Score_arr as $friend_user => $friend_score)
				{
					$order = $order + 1;
					if($friend_user == $userid)
					{  
						$friend_reward_item = null;
						foreach($xmlData['activity_friendranks']['friendrank'] as $rank_key => $rank_value){
							$split_rank = explode("-",$rank_value['friendrank_id']);
							if($rank_value['friendrank_id'] == $order){ 
								$friend_reward_item = $rank_value;
								break;
							} 
						}
						if($friend_reward_item != null)
						{
							$mailId = $curDate . uniqid();
							$detail = sprintf($friend_reward_item['friendrank_mailcontent'],$myOrder);
							$attachment = $this->getReward($friend_reward_item['friendrank_reward']); 
							$newmail_array = array( "$mailId" => array(
														"id"=>$mailId,//邮件id
														"title"=>$friend_reward_item['friendrank_mailtitle'],//标题
														"detail"=>$detail,
														"attachment"=>$attachment,
														"getrewardflag"=>false,
														"senderid"=>"0",
														"sendername"=>"",
														"icon"=>"",
														"sendtime"=>time(0),
														"mailtype"=>0,
													));
							array_push($addMail_arr,$newmail_array);
						}							
						break;
					}
				}	
				$this->Player->addMail($userid,$addMail_arr);				
			}
		}
	} 
    /*
		加载mail配置文件
	*/
	public function LoadXMl(){
		$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/";
		$xml = @simplexml_load_file($xmlpath.'rank.xml');
		$ret = json_decode(json_encode($xml),true);		
		return $ret;
	}	
}
