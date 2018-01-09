<?php

namespace App\Api;
use \Exception;
/*
|------------------------------------------------------------------------------
|	Session dari Api system
|
|------------------------------------------------------------------------------
*/

Class Accesstoken extends AbstractAccessToken{
	private static $instance =false;
	public static function init()
	{
		self::$instance = (!self::$instance) ? new Accesstoken : self::$instance ;
		return self::$instance ;
	}
	function read($accessToken='')
	{
		try{
			$this->access_token_string = !empty($accessToken) ?  $accessToken : $this->CI->input->post('accesstoken') ;
			$this->sess_read();
		}catch( Exception $e){
			throw new Exception( $e->getMessage() );
			return false;
		}
		return true;
	}
	
	function /* _readAccessToken */ sess_read()
	{
		$session = $this->access_token_string;
		if ( empty($session) )
		{
			throw new Exception(' Access Token is required for this request ');
		}
		$len = strlen($session) - 40;

		if ($len <= 0)
		{
			log_message('error', 'AccessToken: The access token string was not signed.');
			throw new Exception(' Access Token is not Valid ');
		}
		// Check cookie authentication
		$hmac = substr($session, $len);
		$session = substr($session, 0, $len);

		// Time-attack-safe comparison
		$hmac_check = hash_hmac('sha1', $session, $this->encryption_key);
		$diff = 0;

		for ($i = 0; $i < 40; $i++)
		{
			$xor = ord($hmac[$i]) ^ ord($hmac_check[$i]);
			$diff |= $xor;
		}

		if ($diff !== 0)
		{
			log_message('error', 'Access Token: HMAC mismatch. The access token string data did not match what was expected.');
			throw new Exception(' Access Token is not Valid ');
		}
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$session = base64_decode($session);
			$session = $this->CI->encrypt->decode($session);
		}

		// Unserialize the session array
		$session = $this->_unserialize($session);
		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR  ! isset($session['at_generate']) OR  ! isset($session['id_user']))
		{
			throw new Exception(' Access Token is not Valid ');
		}
		// Is the session current?
		//die(var_dump($this->api_time ));
		if (($session['at_generate'] + $this->api_time ) < $this->now )
		{
			throw new Exception(' Access Token is expired ');
		}
		if ( empty($session['id_user']) )
		{
			throw new Exception(' Access Token is not Valid ');
		}
		
		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address())
		{
			throw new Exception(' Access Token is not Valid ');
		}
		//
		if ($this->sess_match_useragent == TRUE && ! isset($session['user_agent']) )
		{
			throw new Exception(' Access Token is not Valid ');
		}elseif($this->sess_match_useragent == FALSE && isset($session['user_agent']) ){
			throw new Exception(' Access Token is not Valid ');
		}
		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 120)))
		{
			throw new Exception(' Access Token is not Valid ');
		}
		// Session is valid!
		$this->userdata = $session;
	}
}