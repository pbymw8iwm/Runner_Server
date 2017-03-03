<?php	
class xmlConfig{ 
		static public function getReward($str){
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
		/*
			加载签到配置文件
		*/
		static public function LoadSignXMl(){
			$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/";
			$xml = @simplexml_load_file($xmlpath.'qiandao.xml');
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}
		/*
			加载每日任务配置文件
		*/
		static public function LoadDailyTaskXMl(){
			$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/";
			$xml = @simplexml_load_file($xmlpath.'qiandao.xml');
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}
		/*
			加载通用配置文件
		*/
		static public function LoadXmlData($filename)
		{
			$xmlpath = rtrim($_SERVER['DOCUMENT_ROOT'],'/')."/config/".$filename;
			$xml = @simplexml_load_file($xmlpath);
			$ret = json_decode(json_encode($xml),true);		
			return $ret;
		}
		/*
			加载敏感词汇
		*/
		static public function LoadForbiddenTxt(){
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
		}
		/*
			检查关键的屏蔽词存在不存在
		*/
		static public function ForbiddenWordsExists($str){
			$keywords_arr = $this->LoadForbiddenTxt();
			foreach($keywords_arr as $var){ 
				$domain = strstr($str, $var); 
				if($domain != null){
					return true;//说明存在了
				}
			}
			return false;
		}
}
?>
