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
	die;
}