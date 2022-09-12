<?php 

namespace App\Http\Controllers\Exceptions;
use Exception;

/*
	* CheckSam will Mismatched
	* Return Object
*/

class InvalidCheckSum extends Exception
{
	protected $message = "";
	protected $errorCode = "";

	function __construct($errorCode)
	{
		$this->message = $this->getErrorMessage($errorCode);
		$this->code = $errorCode;
	}

	private function getErrorMessage($errorCode)
	{
		switch ($errorCode) {
			case '485-412':
				return __('Security token was expired, please try again!');
				break;
			
			case '485-500': 
				return __('Invalid contact details or checksum');
				break;

			case '485-404': 
				return __('Invalid security token. Please try again!');
				break;
			default:
				# code...
				break;
		}
	}
}