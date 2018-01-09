<?php
namespace App\Api\View;
use \ArrayAccess;

Class Icons implements ArrayAccess{
	var $listnya=[
		1=>'fa-comment'
		,'fa-plus'
		,'fa-envelope'
		,'fa-shopping-cart'
		,'fa-eye'
	];
	public function offsetGet($offset)
	{
		if(isset($this->listnya[$offset]))return $this->listnya[$offset];
		return $this->listnya[1];
	}
	public function offsetSet($offset, $valu)
	{
		//
	}
	public function offsetExists($offset) {
		return isset($this->listnya[$offset]);
	}
	public function offsetUnset($offset) {
		//unset($this->container[$offset]);
	}
}