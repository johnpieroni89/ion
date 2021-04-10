<?php
	if(!empty($access['transactions'])){
		echo '{"status": [{"code": "success", "reason":"authenticated"}],';
		if(strpos($data, " AND ") == TRUE){
			$list = explode(" AND ", $data);
			$expanded_search = array();
			foreach($list as $item){
				array_push($expanded_search, "(buyer = '".trim($item)."' OR seller = '".trim($item)."' OR assets LIKE '%".trim($item)."%' OR source LIKE '%".trim($item)."%')");
			}
			$expanded_search = implode(" AND ", $expanded_search);
			$lookup = mysqli_query($db->connection, "SELECT * FROM transactions WHERE ".$expanded_search." ORDER BY timestamp DESC LIMIT 20");
		}else{
			$lookup = mysqli_query($db->connection, "SELECT * FROM transactions WHERE buyer = '".$data."' OR seller = '".$data."' OR assets LIKE '%".$data."%' OR source LIKE '%".$data."%' ORDER BY timestamp DESC LIMIT 20");
		}

		$count = mysqli_num_rows($lookup);
		if(mysqli_num_rows($lookup) != 0){
			echo '"response":[';
			while($row = mysqli_fetch_assoc($lookup)){
				$swc_time = swc_time($row['timestamp']);
				$time = "Y".$swc_time['year']." D".str_pad($swc_time['day'],3,"0",STR_PAD_LEFT);
				if($count == 1){
					echo '{"id": "'.$row['id'].'", "date":"'.$time.'", "buyer":"'.$row['buyer'].'", "seller":"'.$row['seller'].'", "price":"'.$row['price'].'", "assets":"'.$row['assets'].'", "source":"'.$row['source'].'"}';
				}else{
					echo '{"id": "'.$row['id'].'", "date":"'.$time.'", "buyer":"'.$row['buyer'].'", "seller":"'.$row['seller'].'", "price":"'.$row['price'].'", "assets":"'.$row['assets'].'", "source":"'.$row['source'].'"},';
				}
				$count--;
			}
			echo "]}";
		}else{
			echo '"response": [{"code": "failure", "reason":"no results"}]}';
		}
	}else{
		echo '{"status": [{"code": "failure", "reason":"access denied"}]}';
	}
?>