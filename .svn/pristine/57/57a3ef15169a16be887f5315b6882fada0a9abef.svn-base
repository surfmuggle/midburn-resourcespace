<?php

// ------------------------- LOG_CODE_ -------------------------

// codes used for log entries (including resource and activity logs)

define ('LOG_CODE_ACCESS_CHANGED',		'a');
define ('LOG_CODE_ALTERNATIVE_CREATED',	'b');
define ('LOG_CODE_CREATED',				'c');
define ('LOG_CODE_COPIED',				'C');
define ('LOG_CODE_DOWNLOADED',			'd');
define ('LOG_CODE_EDITED',				'e');
define ('LOG_CODE_EMAILED',				'E');
define ('LOG_CODE_LOGGED_IN',			'l');
define ('LOG_CODE_MULTI_EDITED',		'm');
define ('LOG_CODE_PAYED',				'p');
define ('LOG_CODE_REVERTED_REUPLOADED',	'r');
define ('LOG_CODE_REORDERED',			'R');
define ('LOG_CODE_STATUS_CHANGED',		's');
define ('LOG_CODE_SYSTEM',				'S');
define ('LOG_CODE_TRANSFORMED',			't');
define ('LOG_CODE_UPLOADED',			'u');
define ('LOG_CODE_UNSPECIFIED',			'U');
define ('LOG_CODE_VIEWED',				'v');
define ('LOG_CODE_DELETED',				'x');

// validates LOG_CODE is legal
function LOG_CODE_validate($log_code)
	{
	return in_array($log_code,LOG_CODE_get_all());
	}

// returns all allowable LOG_CODEs
function LOG_CODE_get_all()
	{
	return definitions_get_by_prefix('LOG_CODE');
	}

// used internally
function definitions_get_by_prefix($prefix)
	{
	$return_definitions = array();
	foreach (get_defined_constants() as $key=>$value)
		{
		if (preg_match('/^' . $prefix . '/', $key))
			{
			$return_definitions[$key]=$value;
			}
		}
	return $return_definitions;
	}