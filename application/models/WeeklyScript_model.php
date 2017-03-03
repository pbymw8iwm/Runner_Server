<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
CREATE TABLE `weekly_score` ( 
  `userid`  bigint(20) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;  

*/
    class WeeklyScript_model extends CI_Model {  
 
	public function getWeeklyScoreInfo()
    { 
	    $query = $this->db->query("SELECT * FROM weekly_score order by score desc;");
        if($query->num_rows() == 0){ 
               return null;
        }else{    
			$return_arr = array();
            foreach ($query->result_array() as $row)
            {   
               $return_arr[$row['userid']] = $row['score'] ;
            }  
			return $return_arr;
        }  
    }
	public function getWeeklyScoreUserInfo($user_arr)
    { 
	    $queryStr = "SELECT * FROM weekly_score where userid in (";
		foreach($user_arr as $k => $v)
		{
			$queryStr = $queryStr.$v.",";
		}
		$queryStr = substr($queryStr,0,strlen($queryStr)-1);
		$queryStr .= ") order by score desc;";
		$query = $this->db->query($queryStr);
        if($query->num_rows() == 0){ 
               return null;
        }else{    
			$return_arr = array();
            foreach ($query->result_array() as $row)
            {   
               $return_arr[$row['userid']] = $row['score'] ;
            }  
			return $return_arr;
        }  
    } 
	/*更新玩家积分排行*/
	public function updateUserScore($userId,$score){
		$query = $this->db->query("SELECT * FROM weekly_score where userid = $userId;");
        if($query->num_rows() == 0){ 
			$data = array('userid' => $userId, 'score' => $score);
			$this->db->insert('weekly_score', $data);
        }else{    
			$return_arr = array();
            foreach ($query->result_array() as $row)
            {   
                $return_arr[$userId] = $row['score'] ;
				break;
            }  
			$data = array('userid' => $userId, 'score' => $return_arr[$userId] + $score);
			$this->db->where('userid', $userId)->update('weekly_score', $data); 
        }  
	}
	public function clear(){
		$this->db->query("delete from weekly_score");
	}
}

	
?>
