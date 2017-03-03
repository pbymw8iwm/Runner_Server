<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Billsys extends CI_Controller {
	function __construct()  
    { 
        parent::__construct();  
		$this->load->model("Bill_model","Bill"); 
    }  
	public function GetClientIp(){
		$this->load->helper('captcha');
		return $this->input->ip_address();
	}

	/*
		设备查询版本号信息
		返回格式
		{
			"ret":"-1000" //0是成功
			"billno":"423424"//订单号
		}
	*/
	public function getbillno(){
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', "clientIp:".$this->GetClientIp()."data:".$data_post);
		$post_array = json_decode($data_post,true); 
		$userid  = $post_array['userid'];
		$paytype = $post_array['paytype'];
		$itemId  = $post_array['itemid']; 
		
		$billInfo = $this->Bill->createBillInfo($userid, $paytype, $itemId);
		echo json_encode($billInfo);
	} 
	public function yijie()
	{
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', "clientIp:".$this->GetClientIp()."data:".$data_post);
		$post_array = json_decode($data_post,true);  
        $st = $post_array['st'];
        $billNo =  $post_array['cbi'];
        $ConsumeStreamId = $post_array['tcd'];
        $OriginalMoney = $post_array['fee']/100;//fen wei dan wei    
		
		$billInfo = $this->Bill->getBillInfo($billNo); 
        if($billInfo == null)
        {
                return "1003";
        }
        $fetch = mysql_fetch_array($result); 
        $itemId = $billInfo['itemid'];
        $Uin = $billInfo['userid']; 
        if(intval($billInfo['billstatus']) == 2)
        {
                return "SUCCESS";
        }
        $gold = 0; 
		$num = $this->Bill->updateBillSucc($billNo, $array_info); 
        if ($num > 0)
        {
			//然后更新玩家数据库表
			$this->load->model("UserDB_model","Player");
			$accountId = $this->Player->updateRecharge($Uin,$post_array);
			return "SUCCESS";
        }
        else
        {
            return "1004";
        } 
        return "SUCCESS"; 
	}
}
