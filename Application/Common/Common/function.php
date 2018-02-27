<?php

function print_data($data, $var = false)
{
	echo '<pre>';
	if($var)
	{
		var_dump($data);
	}
	else
	{
		print_r($data);
	}
}

function full_url(&$url, $index = '')
{
	if(is_array($url))
	{
		if(empty($index) || ! isset($url[$index]))
		{
			foreach ($url as $key => $value) {
				full_url($value, $index);
				$url[$key] = $value;
			}
		}
		else
		{
			full_url($url[$index]);
		}
	}
	else
	{
		if( ! strpos($url, '://') && empty($index))
		{	
			$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $url;
		}
	}
}

function html_escape($var)
{
	if(is_array($var))
	{
		foreach ($var as $key => $value) {
			$var[$key] = html_escape($value);
		}
		return $var;
	}
	else if(is_string($var))
	{
		return htmlspecialchars($var);
	}
	else
		return $var;
}

function null_to_zero($var)
{
	if(is_array($var))
	{
		foreach ($var as $key => $value) {
			$var[$key] = null_to_zero($value);
		}
		return $var;
	}
	else
	{
		if(is_null($var))
		{
			return 0;
		}
		else
		{
			return $var;
		}
	}
}

function line_to_up($var)
{
	if(is_array($var))
	{
		foreach ($var as $key => $value) {
			$tmpKey = $key;
			if(is_string($key))
			{
				$key = explode('_', $key);
				for($i = 1, $len = count($key); $i < $len; $i++)
				{
					if( ! empty($key[$i]))
						$key[$i][0] = strtoupper($key[$i][0]);
				}
				$key = implode('', $key);
			}
			unset($var[$tmpKey]);
			$var[$key] = line_to_up($value);
		}
		return $var;
	}
	return $var;
}

function numeric_to_num($var)
{
	if(is_array($var))
	{
		foreach ($var as $key => $value) {
			$var[$key] = numeric_to_num($value);
		}
		return $var;
	}
	else
	{
		if(is_numeric($var))
			return (double)$var;
		return $var;
	}
}