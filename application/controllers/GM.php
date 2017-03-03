<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GM extends CI_Controller {
	function __construct()  
    { 
        parent::__construct(); 
		
    }  
	function index()
	{
		$data['main_content'] = 'login_form';
		$this->load->helper('url');
		$this->load->view('includes/template', $data);		
	}
	public function SendEmail(){
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', "SendEmail".$data_post);
		$post_array = json_decode($data_post,true);
		$useridStr = $post_array['userid'];
		
	}
	public function getReward($str){
		$attachment = array();
		$rewards_list = explode("_",$str);
		foreach($rewards_list as $reward_key => $reward_value)
		{
			$reward_pair = explode("-",$reward_value);
			if(count($reward_pair) == 2){
 			}
		}
		return $attachment;
	}
	public function SendAllEmail(){
		$data_post = $this->input->post("data");
		$this->log->write_log($level = 'error', "SendAllEmail".$data_post);
		$post_array = json_decode($data_post,true);
		$titile = $post_array['title'];
		$detail = $post_array['detail']; 
		$attachment = $this->getReward($post_array['attachment']);		
		$userMailInfo = $this->Player->getAllPlayerMailInfo(array());  
		if($userMailInfo != null){
			$addMail_arr = array();
			$curDate = date("YmdHis");
			$mailId = $curDate . uniqid();			
			$newmail_array = array( "$mailId" => array(
												"id"=>$mailId,//邮件id
												"title"=>$titile,//标题
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

			$this->load->model("UserDB_model","Player");  			
			foreach($userMailInfo as $k =>$v){//针对每一个人 
				$this->Player->addMail($v,$addMail_arr); 
			}
		} 
	}
	
	function validate_credentials()
	{		
		$this->load->model('membership_model');
		$query = $this->membership_model->validate();
		
		if($query) // if the user's credentials validated...
		{
			$data = array(
				'username' => $this->input->post('username'),
				'is_logged_in' => true
			);
			$this->session->set_userdata($data);
			redirect('site/members_area');
		}
		else // incorrect username or password
		{
			$this->index();
		}
	}	
	
	function signup()
	{
		$data['main_content'] = 'signup_form';
		$this->load->view('includes/template', $data);
	}
	
	function create_member()
	{
		$this->load->library('form_validation');
		
		// field name, error message, validation rules
		$this->form_validation->set_rules('first_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
		
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('signup_form');
		}
		
		else
		{			
			$this->load->model('membership_model');			
			if($query = $this->membership_model->create_member())
			{
				$data['main_content'] = 'signup_successful';
				$this->load->view('includes/template', $data);
			}
			else
			{
				$this->load->view('signup_form');			
			}
		}
		
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		$this->index();
	}

}
