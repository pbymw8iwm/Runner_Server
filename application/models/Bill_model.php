<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once ('PlayerInfo.php'); 
/*
  `bill_id` varchar(32) NOT NULL,
  `userid` varchar(32) NOT NULL,
  `paytype` tinyint(3) NOT NULL,
  `itemid` varchar(32) NOT NULL DEFAULT ''  COMMENT '支付的档位',
  `orderid` varchar(32) NOT NULL DEFAULT '' COMMENT '第三方支付平台的订单号',
  `ordermoney` int(11) NOT NULL DEFAULT '0',
  `gold` int(11) NOT NULL DEFAULT '0',
  `billstatus` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1 业务方请求 2 业务完成',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
*/
class Bill_model extends CI_Model {
	public function createBillInfo($userid, $paytype, $itemId)
    {
		$OutArray = array();
        if(trim($userid) == '')
        {
            $OutArray['ret'] = -1001;
            return $OutArray;
        } 
        $curDate = date("YmdHis");
        $billNo = $curDate . uniqid(); 
		$query = $this->db->query("SELECT bill_id FROM `bill_info` where bill_id = '$billNo';");
		if($query->num_rows() > 0){ 
			$OutArray['ret'] = -1002;
            return $OutArray;
		} 
 	
		$data = array('bill_id' => $billNo, 'userid' => $userid, 'paytype' => $paytype, 'itemid'=>$itemId, 'billstatus'=>1);
		$str = $this->db->insert('bill_info', $data);  
        if ($str > 0)
        {
                $OutArray['ret'] = 0;
                $OutArray['billno'] = $billNo;
        }
        else
        {
                $OutArray['ret'] = -1003;
        }
        return $OutArray;
    }
	public function getBillInfo($billNo)
    {   
		$query = $this->db->query("SELECT * FROM `bill_info` where `bill_id` = '$billNo'");
		if($query->num_rows() == 0){ 
			return null;
		}else{
			foreach ($query->result_array() as $row)
            {   
				return $row;
            }   			
		} 
    }
	public function updateBillSucc($billNo,$array_info)
    {   
		$ret = $this->db->where('bill_id', $billNo)->update('bill_info', $array_info); 
		return $ret;
    }
}

	
?>
