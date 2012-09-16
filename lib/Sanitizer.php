<?php
class Sanitizer
{

	const MIN_POSTAL_CODE = 0;
	const MAX_POSTAL_CODE = 99999;

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @return mixed
	 */
	public static function string($data = NULL)
	{
		/*
		 FILTER_FLAG_NO_ENCODE_QUOTES
		FILTER_FLAG_STRIP_LOW
		FILTER_FLAG_STRIP_HIGH
		FILTER_FLAG_ENCODE_LOW
		FILTER_FLAG_ENCODE_HIGH
		FILTER_FLAG_ENCODE_AMP
		*/
		return filter_var(trim($data), FILTER_SANITIZE_STRING);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @return mixed
	 */
	public static function postalCode($data = NULL)
	{
		return filter_var($data,
		FILTER_VALIDATE_INT,
		array('options' =>
		array(
							      	   'min_range'=> self::MIN_POSTAL_CODE, 
									   'max_range'=> self::MAX_POSTAL_CODE
		)
		)
		);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @return mixed
	 */
	public static function integer($data = NULL)
	{
		return filter_var($data, FILTER_SANITIZE_INT);
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function isFloat($data = NULL)
	{
		return filter_var($data, FILTER_VALIDATE_FLOAT);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function isEmail($data = NULL)
	{
		return filter_var($data, FILTER_VALIDATE_EMAIL);
	}

	public static function email($data = NULL)
	{
		return filter_var($data, FILTER_SANITIZE_EMAIL);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function isURL($data = NULL)
	{
		/*
		 FILTER_FLAG_SCHEME_REQUIRED
		FILTER_FLAG_HOST_REQUIRED
		FILTER_FLAG_PATH_REQUIRED
		FILTER_FLAG_QUERY_REQUIRED
		*/
		return filter_var($data, FILTER_VALIDATE_URL);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function isIp($data = NULL)
	{
		# FILTER_FLAG_IPV6
		# FILTER_FLAG_NO_PRIV_RANGE
		# FILTER_FLAG_NO_RES_RANGE
		return filter_var($data, FILTER_VALIDATE_IP);
	}

}