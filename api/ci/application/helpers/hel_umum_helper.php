<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(! function_exists('hel_minify_html') ){
		
		function hel_minify_html($text) // 
		{
		// Set PCRE recursion limit to sane value = STACKSIZE / 500
		// ini_set("pcre.recursion_limit", "524"); // 256KB stack. Win32 Apache
		//ini_set("pcre.recursion_limit", "16777");  // 8MB stack. *nix
		
			$re = '%# Collapse whitespace everywhere but in blacklisted elements.
				(?>             # Match all whitespaces other than single space.
				  [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
				| \s{2,}        # or two or more consecutive-any-whitespace.
				) # Note: The remaining regex consumes no text at all...
				(?=             # Ensure we are not in a blacklist tag.
				  [^<]*+        # Either zero or more non-"<" {normal*}
				  (?:           # Begin {(special normal*)*} construct
					<           # or a < starting a non-blacklist tag.
					(?!/?(?:textarea|pre|script)\b)
					[^<]*+      # more non-"<" {normal*}
				  )*+           # Finish "unrolling-the-loop"
				  (?:           # Begin alternation group.
					<           # Either a blacklist start tag.
					(?>textarea|pre|script)\b
				  | \z          # or end of file.
				  )             # End alternation group.
				)  # If we made it here, we are not in a blacklist tag.
				%Six';
			$text = preg_replace($re, " ", $text);
			if ($text === null) exit("PCRE Error! File too big.\n");
			return $text;
		}
	}
if(! function_exists('hel_waktu') ){
	function hel_waktu($waktu){
		// ctt : waktu harus dari strtotime() atau time()
		$time = time() - $waktu;
		$token = array(
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);
		foreach($token as $unit => $text){
			if($time < $unit)continue;
			if($time > 31536000)return date("F d,Y ~ h:i a",$waktu);
			if($time > 172801){if(date("Y") != date("Y",$waktu) )return date("F d,Y ~ h:i a",$waktu); return date("F d ~ h:i a",$waktu); }
			$hasilnya = floor($time / $unit);
			return $hasilnya .' '.$text.( ($hasilnya > 1) ?  's' : '' ).' ago';
		}
		return '1 second';
	}
}
if(! function_exists('hel_timezone_offset_number') ){
	function hel_timezone_offset_number($timezone='America/Chicago'){
		$dtz = new DateTimeZone($timezone);
		$time_in_sofia = new DateTime('now', $dtz);
		$offset = $dtz->getOffset( $time_in_sofia ) / 3600;
		return ($offset < 0 ? $offset : "+".$offset);
	}
}
if(!function_exists('json_js_array')){
	function json_js_array($string=''){
		return str_replace(array('<','>',"'"),array('&lt;','&gt;','&#039;'),$string);
	}
}
if(!function_exists('validasi_array')){
	function validasi_array(&$data,$rules,$no_isi=true){
		$rule = explode('|',$rules);
		if(isset($rule[0]) && is_array($rule)){
			foreach($rule as $val){
				if(empty($data[$val]) && $no_isi )return false; else $data[$val]=empty($data[$val])? '' : $data[$val];
			}
			return true;
		}
		return false;
	}
}