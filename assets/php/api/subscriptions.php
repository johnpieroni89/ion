<?php
	echo '{"status": [{"code": "success", "reason":"open access"}],';

	$lookup = mysqli_query($db->connection, "SELECT * FROM bot_subscriptions WHERE discord_id = '".$data."'");
	if(mysqli_num_rows($lookup) != 0){
		$lookup = mysqli_fetch_assoc($lookup);
		if($lookup['subscription'] > 0){
			echo '"response": [{"code": "success", "reason":"'.$lookup['subscription'].'"}]}';
		}else{
			echo '"response": [{"code": "failure", "reason":"subscription expired"}]}';
		}
	}else{
		echo '"response": [{"code": "failure", "reason":"no results"}]}';
	}
?>