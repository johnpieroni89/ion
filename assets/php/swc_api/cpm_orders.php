<?php
		include("database.php");
		include("functions.php");
		
		
		define('USERNAME', 'Cedron Tryonel'); //The username or email address of the account.
		define('PASSWORD', '12391110'); //The password of the account.
		define('USER_AGENT', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36'); //Set a user agent. This basically tells the server that we are using Chrome ;)
		define('COOKIE_FILE', 'cookie.txt'); //Where our cookie information will be stored (needed for authentication).
		define('LOGIN_FORM_URL', 'http://market.centrepointstation.com/index.php'); //URL of the login form.
		define('LOGIN_ACTION_URL', 'http://market.centrepointstation.com/login.php'); //Login action URL. Sometimes, this is the same URL as the login form.
		
		$postValues = array(
			'handle' => USERNAME,
			'password' => PASSWORD
		);
		 
		$curl = curl_init(); //Initiate cURL.
		 
		curl_setopt($curl, CURLOPT_URL, LOGIN_ACTION_URL); //Set the URL that we want to send our POST request to. In this case, it's the action URL of the login form.
		curl_setopt($curl, CURLOPT_POST, true); //Tell cURL that we want to carry out a POST request.
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues)); //Set our post fields / date (from the array above).
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //We don't want any HTTPS errors.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //We don't want any HTTPS errors.
		curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE); //Where our cookie details are saved. This is typically required for authentication, as the session ID is usually saved in the cookie file.
		curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT); //Sets the user agent. Some websites will attempt to block bot user agents. Hence the reason I gave it a Chrome user agent.
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //Tells cURL to return the output once the request has been executed.
		curl_setopt($curl, CURLOPT_REFERER, LOGIN_FORM_URL); //Allows us to set the referer header. In this particular case, we are fooling the server into thinking that we were referred by the login form.
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); //Do we want to follow any redirects?
		curl_exec($curl); //Execute the login request.
		if(curl_errno($curl)){ throw new Exception(curl_error($curl)); } //Check for errors!
		
		$db = new database;
		$db->connect();
		$current_order = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'last_update_cpm'"))["value"];
		$current_order = $current_order - 300;
		$count_process = 0;
		while($count_blanks <= 50 && $count_process < 5000){
			if(mysqli_num_rows(mysqli_query($db->connection,"SELECT order_id, source FROM transactions WHERE order_id = '$current_order' AND source = 'CPM'")) == 0){
				
				
				curl_setopt($curl, CURLOPT_URL, 'http://market.centrepointstation.com/details.php?lid='.$current_order); //We should be logged in by now. Let's attempt to access a password protected page
				curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE); //Use the same cookie file.
				curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT); //Use the same user agent, just in case it is used by the server for session validation.
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //We don't want any HTTPS / SSL errors.
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //We don't want any HTTPS / SSL errors.
				$data = curl_exec($curl); //Execute the GET request and store the result.

				preg_match_all("#\(\#([0-9]{1,10})\)#", $data, $matches_order);
				$order_number = $matches_order[1][0];
				if($order_number != ""){
					preg_match_all("#From:<\/td><td><a href=\"\/profile\.php\?user=c[0-9]{1,8}\" onclick=\"javascript:userStats\('[cf][0-9]{1,8}', '.*'\);\">(.*)<\/a>#", $data, $matches_buyer);
					preg_match_all("#Sold At:<\/b> <\/td><td>Y&nbsp;([0-9]{1,2})&nbsp;D&nbsp;([0-9]{1,3}),&nbsp#", $data, $matches_date);
					preg_match_all("#Seller:<\/b> <\/td><td><a href=\"\/profile\.php\?user=[cf][0-9]{1,8}\">([A-Za-z0-9`'\/\- \(\)]*)<\/a>#", $data, $matches_seller);
					preg_match_all("#Sale Price:<\/b> <\/td><td>(.*) <img#", $data, $matches_price);
					preg_match_all("#Name: <\/b><\/td><td><a href=\"\/browse\.php\?type=[0-9]{1,3}&amp;class=[0-9]{1,3}&amp;id=[0-9]{1,5}\">(.*)<\/a>#", $data, $matches_items);
					preg_match_all("#Quantity: <\/b><\/td><td>([0-9,]{1,10})<\/td><\/tr>#", $data, $matches_quantity);
					$count = 0;
					$assets = array();
					
					foreach($matches_items[1] as $layer){
						array_push($assets, $matches_quantity[1][$count]."x ".$layer);
						$count++;
					}
					
					$asset_list = "";
					foreach($assets as $asset){
						$asset_list = $asset_list.$asset."; ";
					}
					
					$timestamp = swc_time(real_time($matches_date[1][0], $matches_date[2][0]), FALSE)["timestamp"];
					$seller = $matches_seller[1][0];
					if($seller != $matches_buyer[1][0]){ $buyer = $matches_buyer[1][0]; }else{ $buyer = $matches_buyer[1][1]; }
					$price = $matches_price[1][0];
					
					if($buyer != ""){
						mysqli_query($db->connection,"INSERT INTO transactions (timestamp, order_id, buyer, seller, price, assets, source) VALUES ('$timestamp', '$order_number', '$buyer', '$seller', '$price', '$asset_list', 'CPM')");
					}
					mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_cpm'");
					$current_order++;
					$count_blanks = 0;
				}else{
					$count_blanks++;
					$current_order++;
				}
			}else{
				$current_order++;
			}
			$count_process++;
		}

		if($count_blanks >= 50){
			$db = new database;
			$db->connect();
			$current_order = $current_order - $count_blanks;
			mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_cpm'");
		}
?>