<?php

require_once dirname(__FILE__) . '/vendors/spyc/spyc.php';


function yaml($input=null)
{
	return yaml_load($input);
}

function yaml_load($input=null)
{
	if(is_null($input)) return array();
	
	$input = yaml_include_contents($input);
	
	if(is_array($input)) return $input;
	
	if(function_exists('syck_load'))
	{
		$datas = syck_load($input);
		return is_array($datas) ? $datas : array();
	}
	else
	{
		return spyc_load($input);
	}
}

function yaml_dump($array)
{
	return Spyc::YAMLDump($input);
}

function yaml_include_contents($input)
{
	if(strpos($input, "\n") === false && is_file($input))
	{
		ob_start();
		$retval = include($input);
		$contents = ob_get_clean();
		return is_array($retval) ? $retval : $contents;
	}
	return $input;
}

function _yaml_require_spyc()
{
  static $loaded = false;
  if(!$loaded)
  {
    
    $loaded = true;
  }
}

?>