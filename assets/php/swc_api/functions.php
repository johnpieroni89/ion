<?php

function createPepper(){
	$f=fopen($site_path.'assets/php/pepper.php','w');
	fwrite($f,'<?php $password_pepper = "'.randgen(32).'";?>');
	fclose($f);
}

function randgen($charLen){
	$setLower = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
	$setUpper = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	$setNumber = ['0','1','2','3','4','5','6','7','8','9'];
	$setSpecial = ['!','@','#','$','%','^','&','*','?','-','+','='];
	$genString = "";
	
	if($charLen >= 8 && is_int($charLen)){
		//Get 2 lowercase chars
		$arrSize = sizeof($setLower);
		$genString = $genString."".$setLower[rand(0,$arrSize - 1)];
		$genString = $genString."".$setLower[rand(0,$arrSize - 1)];

		//Get 2 uppercase chars
		$arrSize = sizeof($setUpper);
		$genString = $genString."".$setUpper[rand(0,$arrSize - 1)];
		$genString = $genString."".$setUpper[rand(0,$arrSize - 1)];

		//Get 2 numbers
		$arrSize = sizeof($setNumber);
		$genString = $genString."".$setNumber[rand(0,$arrSize - 1)];
		$genString = $genString."".$setNumber[rand(0,$arrSize - 1)];

		//Get 2 special chars
		$arrSize = sizeof($setSpecial);
		$genString = $genString."".$setSpecial[rand(0,$arrSize - 1)];
		$genString = $genString."".$setSpecial[rand(0,$arrSize - 1)];
		
		//Generate the rest of the string
		$remainder = $charLen - 8;
		$arrAll = array_merge($setLower, $setUpper, $setNumber, $setSpecial);
		$arrSize = sizeof($arrAll);
		while($remainder != 0){
			$genString = $genString."".$arrAll[rand(0,$arrSize - 1)];
			$remainder = $remainder - 1;
		}
	}else{
		echo "randgen() requires at least 8 characters in length to function properly.";
	}
	str_shuffle($genString);
	str_shuffle($genString);
	str_shuffle($genString);
	return $genString;
}

function geolocate($ip){
	$db = new database;
	$db->connect();
	$geolocate_api_key = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='geolocate_api_key'"))['value'];
	$obj = json_decode(file_get_contents("http://api.ipapi.com/api/".$ip."?access_key=".$geolocate_api_key.""));
	$country = $obj->{'country_name'};
	$db->disconnect();
	unset($db);
	return $country;
}

function add_character($handle){
	$db = new database;
	$db->connect();
	$handle = urlencode(str_replace(" ","+",$handle));
	$xml = simplexml_load_string(file_get_contents("http://www.swcombine.com/ws/v1.0/handle/".$handle));
	$uid = mysqli_real_escape_string($db->connection, $xml->uid);
	$handle = mysqli_real_escape_string($db->connection, $xml->handle);
	if(explode(":",$uid)[0] == 1){
		if(mysqli_query($db->connection,"INSERT INTO characters (uid, handle) VALUES ('$uid', '$handle')")){
			return $uid;
		}else{
			return FALSE;
		}
	}else{
		return FALSE;
	}
	$db->disconnect();
	unset($db);
}

function add_character_affiliation($character_uid,$faction,$time,$source){
	$db = new database;
	$db->connect();
	mysqli_query($db->connection,"INSERT INTO characters_faction_affiliations (character_uid, faction, timestamp, source) VALUES ('$character_uid', '$faction', '$time', '$source')");
	$db->disconnect();
	unset($db);
}

function swc_time($time, $offset = FALSE){ // TRUE to convert real time() to swc_time. FALSE to convert swc timestamps to swc_time.
	if($offset == TRUE){$time = $time - 912668323;}
	$array = array();
	$array["year"] = floor(($time / 86400) / 365);
	$array["day"] = ($time / 3600 / 24) % 365 + 1;
	$array["hour"] = str_pad(($time / 3600) % 24,2,0,STR_PAD_LEFT);
	$array["minute"] = str_pad(($time / 60) % 60,2,0,STR_PAD_LEFT);
	$array["second"] = str_pad(($time) % 60,2,0,STR_PAD_LEFT);
	$array["timestamp"] = $time;
	$array["date"] = "Year ".$array["year"]." Day ".$array["day"]." ".$array["hour"].":".$array["minute"];
	return $array;
}

function real_time($year, $day, $hour = 0, $minute = 0, $second = 0){
	$years = $year * 31536000;
	$days = $day * 86400;
	$hours = $hour * 3600;
	$minutes = $minute * 60;
	$seconds = $second;
	return $years + $days + $hours + $minutes + $seconds - 86400;
}

function checkRemoteFile($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if($result !== FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

?>