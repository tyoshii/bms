<?php

class Controller_Register extends Controller
{
	public function action_index(){
		$view = View::forge('register.twig');

		$input_name			= Input::post('input_name');
		$input_password = Input::post('input_password');
		$input_confirm  = Input::post('input_confirm_password');
		$input_mail			= Input::post('input_mail');

		if( isset($input_name) && isset($input_password) && isset($input_mail) && isset($input_confirm) ){
			if($input_password === $input_confirm){
				try{
					$result =Auth::create_user(
							$input_name,
							$input_password,
							$input_mail,
							1
							);
					if($result === false){
						throw new Exception('Failed');
					}
					$view->ret_message = "Create Account:$input_name Succeed."; 
				}catch(Exception $e){
					$view->ret_message = "Create Account:$input_name Failed. " . $e->getMessage();
				}
			}else{
				$view->ret_message = "Password is not matched. Please Retry.";
			}
		}
		$view->input_name = $input_name;
		$view->input_mail = $input_mail;
	
		return Response::forge( $view );
	}
}
