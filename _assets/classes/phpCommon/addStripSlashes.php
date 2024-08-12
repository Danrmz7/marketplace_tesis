<?php

/*==========================================================

stripslashes_array($input_arr):

	- Strip slashes 

	- Return: Array;

============================================================*/

function stripslashes_array($input_arr){

    if(is_array($input_arr)){

        $tmp = array();

        foreach ($input_arr as $key1 => $val){

            $tmp[$key1] = stripslashes_array($val);

        }

        return $tmp;

    }else{

        return stripslashes(trim($input_arr));

    }

}

/*==========================================================

tripslashes_decode_html_array($input_arr):

	- Strip slashes & decode htmlspecialchars

	- Return: Array;

============================================================*/

function stripslashes_decode_html_array($input_arr){

    if(is_array($input_arr)){

        $tmp = array();

        foreach ($input_arr as $key1 => $val){

            $tmp[$key1] = stripslashes_array(_htmlEntityDecode($val));

        }

        return $tmp;

    }else{

        return stripslashes(trim(_htmlEntityDecode($input_arr)));

    }

}

/*==========================================================

stripsDecodeInjection($input_arr):

	- Strip slashes, decode htmlspecialchars, & prevent SQL 

	  injection

	- Return: Array;

============================================================*/

function stripsDecodeInjection($input_arr){

	if(is_array($input_arr)){

        $tmp = array();

        foreach ($input_arr as $key1 => $val){

			//Don't allow "1=1" or "1=0" 

			if(preg_match("/1=1/i", $val) || preg_match("/1 = 1/i", $val) || preg_match("/1 =1/i", $val) || preg_match("/1= 1/i", $val))

				$tmp[$key1] ="";

			elseif(preg_match("/1=0/i", $val) || preg_match("/1 = 0/i", $val) || preg_match("/1 =0/i", $val) || preg_match("/1= 0/i", $val))

				$tmp[$key1] ="";

			elseif(is_array($val))

				$tmp[$key1] = stripsDecodeInjection($val);

			else

				$tmp[$key1] = mysql_real_escape_string(stripslashes_array(_htmlEntityDecode($val)));

        }

        return $tmp;

    }else{

		//Don't allow "1=1" or "1=0" 

		if(preg_match("/1=1/i", $input_arr) || preg_match("/1 = 1/i", $input_arr) || preg_match("/1 =1/i", $input_arr) || preg_match("/1= 1/i", $input_arr))

			return "";

		elseif(preg_match("/1=0/i", $input_arr) || preg_match("/1 = 0/i", $input_arr) || preg_match("/1 =0/i", $input_arr) || preg_match("/1= 0/i", $input_arr))

			return "";

		else

			return mysql_real_escape_string(stripslashes(trim(_htmlEntityDecode($input_arr))));

    }

}

/*==========================================================



============================================================*/

function addslashes_array($input_arr){

    if(is_array($input_arr)){

        $tmp = array();

        foreach ($input_arr as $key1 => $val){

            $tmp[$key1] = addslashes_array($val);

        }

        return $tmp;

    }else{

        return addslashes(trim($input_arr));

    }

}



/*==========================================================

stripAndHtml($input_arr):

	- Use this to strip slash and convert some predefined characters to HTML entities

	- Return: Array;

============================================================*/

function stripAndHtml($input_arr){

	if(is_array($input_arr)){

        $tmp = array();

        foreach ($input_arr as $key1 => $val){

            $tmp[$key1] = stripAndHtml($val);

        }

        return $tmp;

    }else{

        return htmlspecialchars(stripslashes(trim($input_arr)), ENT_QUOTES);

    }

}

/*==========================================================

_htmlEntityDecode($str):

	- Decode htmlspecialchars

	- Return: String;

============================================================*/

function _htmlEntityDecode($str){

	$search = array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;");

	$replacement = array("&", '"', "'", "<", ">");

	return str_replace($search, $replacement, $str);

}

?>