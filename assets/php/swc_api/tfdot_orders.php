<?php
		include("database.php");
		include("functions.php");
		
		$db = new database;
		$db->connect();
		$current_order = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'last_update_tfdot'"))["value"];
		
		while($count_blanks <= 30){
			if(mysqli_num_rows(mysqli_query($db->connection,"SELECT order_id, source FROM transactions WHERE order_id = '$current_order' AND source = 'TF DoT'")) == 0){
				$data = file_get_contents("http://dot.swc-tf.com/view/view_order_sidepanel.php?order=".$current_order);
				preg_match_all("#Order No\. ([0-9]{1,10})#", $data, $matches_order);
				$order_number = $matches_order[1][0];
				if($order_number != ""){
					preg_match_all("#: \[(.*)\]-#", $data, $matches_buyer);
					preg_match_all("#\]-\[(.*)\]#", $data, $matches_date);
					preg_match_all("#From: <\/b><Br>(.*) <i>#", $data, $matches_seller);
					if($matches_seller[1][0] == ""){
						preg_match_all("#From: <\/b><Br>(.*)<br><Br><\/td><\/tr><tr><Td><b#", $data, $matches_seller);
					}
					preg_match_all("#Purchase Total: <\/b>(.*)&n#", $data, $matches_price);
					preg_match_all("#<table border=0><tr><Td><font size=-1 class=standard(>.*)<\/tr><\/table><\/td>#", $data, $matches_items);
					preg_match_all("#align=right>([0-9,]*)<br>#", $data, $matches_quantity);
					$count = 0;
					$assets = array();
					foreach($matches_items[0] as $layer){
						$quantity = $matches_quantity[1][$count];
						preg_match_all("#>([0-9]*x [A-Za-z0-9-:'`\/\(\)\" ]*)<\/td#", $layer, $matches_items_2);
						foreach($matches_items_2[0] as $item){
							preg_match_all("#([0-9]*)x ([A-Za-z0-9-:'`\/\(\)\" ]*)#", $item, $matched_item);
							$quantity_items = $matched_item[1][0] * $quantity; //Batch_quantity
							$asset = $matched_item[2][0]; //entity
							array_push($assets, $quantity_items."x ".$asset);
							$asset_list = $asset_list + $quantity_items."x ".$asset."; ";
						}
						$count++;
					}
					
					$asset_list = "";
					foreach($assets as $asset){
						$asset_list = $asset_list.$asset."; ";
					}
					
					$buyer = $matches_buyer[1][0];
					$date = $matches_date[1][0];
					$timestamp = swc_time(real_time(substr($date,1,2), substr($date,5)), FALSE)["timestamp"];
					$seller = $matches_seller[1][0];
					$price = $matches_price[1][0];
						
					mysqli_query($db->connection,"INSERT INTO transactions (timestamp, order_id, buyer, seller, price, assets, source) VALUES ('$timestamp', '$order_number', '$buyer', '$seller', '$price', '$asset_list', 'TF DoT')");
					mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_tfdot'");
					$current_order++;
					$count_blanks = 0;
				}else{
					$count_blanks++;
					$current_order++;
				}
			}else{
				$current_order++;
			}
		}

		if($count_blanks >= 3){
			$db = new database;
			$db->connect();
			$current_order = $current_order - $count_blanks;
			mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_tfdot'");
		}
?>