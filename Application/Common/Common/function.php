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