<?php

namespace App\Api;
use \Exception;
/*
|------------------------------------------------------------------------------
|	Session dari Api system
|
|------------------------------------------------------------------------------
*/

Class AccesstokenCreate extends AbstractAccessToken{
	static $instance =false;
	public static function init()
	{
		self::$instance = (!self::$instance) ? new AccesstokenCreate : self::$instance ; 
		return self::$instance ;
	}
	function create( $userdata = array() )
	{
		try{
			$sessid = '';
			while (strlen($sessid) < 32)
			{
				$sessid .= mt_rand(0, mt_getrandmax());
			}

			// To make the session ID even more secure we'll combine it with the user's IP
			$sessid .= $this->CI->input->ip_address();

			$this->userdata = array(
				'session_id'	=> md5(uniqid($sessid, TRUE)),
				'ip_address'	=> $this->CI->input->ip_address(),
				'user_agent'	=> substr($this->CI->input->user_agent(), 0, 120),
				'last_activity'	=> $this->now,
				'at_generate'	=> $this->now
			); 
			$this->userdata = array_merge($userdata,$this->userdata);
			
			$custom_userdata = $userdata ;
			$cookie_userdata = array();

			// Before continuing, we need to determine if there is any custom data to deal with.
			// Let's determine this by removing the default indexes to see if there's anything left in the array
			// and set the session data while we're at it
			foreach (array('session_id','ip_address','user_agent','last_activity','at_generate') as $val)
			{
				unset($custom_userdata[$val]);
				$cookie_userdata[$val] = $this->userdata[$val];
			}
			if(! $this->sess_match_useragent ){
				unset($this->userdata['user_agent']);
			}

			// Did we find any custom data?  If not, we turn the empty array into a string
			// since there's no reason to serialize and store an empty array in the DB
			if (count($custom_userdata) === 0)
			{
				$custom_userdata = '';
			}
			else
			{
				// Serialize the custom data array so we can store it
				$custom_userdata = $this->_serialize($custom_userdata);
			}
			return $this->_set_cookie();
		}catch( Exception $e){
			throw new Exception( $e->getMessage() );
			return false;
		}
		return true;
	}
	function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		if ($this->sess_encrypt_cookie == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
			$cookie_data=base64_encode($cookie_data);
		}

		$cookie_data .= hash_hmac('sha1', $cookie_data, $this->encryption_key);
		return $cookie_data;
	}
}