<?php
    include("autoload.php");
    global $db;
	
	if(isset($_GET['api_key'])){
		$api_key = mysqli_real_escape_string($db->connection, $_GET['api_key']);
		$query = mysqli_real_escape_string($db->connection, $_GET['query']);
		if($query != "logdata"){
			$data = mysqli_real_escape_string($db->connection, $_GET['data']);
			$access = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT users_api.api_key, users_privs.* FROM users_api LEFT JOIN users_privs ON users_api.user_id = users_privs.user_id WHERE api_key = '$api_key'"));
			if(!empty($access['api_key'])){
				if(!empty($data)){
					if($query == "ship_id" || $query == "station_id" || $query == "facility_id" || $query == "vehicle_id"){
						require("assets/php/api/entity_id.php");
					}elseif($query == "sentient"){
						require("assets/php/api/sentient.php");
					}elseif($query == "commerce"){
						require("assets/php/api/commerce.php");
					}elseif($query == "assets"){
						require("assets/php/api/assets.php");
					}elseif($query == "subscription"){
						require("assets/php/api/subscriptions.php");
					}else{
						echo '{"status": [{"code": "failure", "reason":"invalid query"}]}';
					}
				}else{
					echo '{"status": [{"code": "failure", "reason":"invalid data"}]}';
				}
			}else{
				echo '{"status": [{"code": "failure", "reason":"invalid key"}]}';
			}
		}else{
			if(!empty($discord_id)){
				$discord_id = mysqli_real_escape_string($db->connection, $_GET['discord_id']);
				$type = mysqli_real_escape_string($db->connection, $_GET['type']);
				$search = mysqli_real_escape_string($db->connection, $_GET['search']);
				mysqli_query($db->connection, "INSERT INTO logs_discord (timestamp, discord_id, type, keywords) VALUES ('".swc_time(time(), TRUE)['timestamp']."', '".$discord_id."', '".$type."', '".$search."')");
				echo '{"status": [{"code": "success", "reason":"authenticated"}], "response": [{"code": "success", "reason":"activity recorded"}]}';
			}else{
				$user = mysqli_real_escape_string($db->connection, $_GET['user']);
				$type = mysqli_real_escape_string($db->connection, $_GET['type']);
				if($type == "ship_id" || $type == "station_id" || $type == "facility_id" || $type == "vehicle_id" || $type == "assets"){ $type = "1";}elseif($type == "commerce"){ $type = "5";}elseif($type == "sentient"){ $type = "0";}
				$search = mysqli_real_escape_string($db->connection, $_GET['search']);
				mysqli_query($db->connection, "INSERT INTO logs (user_id, log_type, details, timestamp) VALUES ('0', '".$type."', 'API: ".$user." queried for ".$search."', '".swc_time(time(), TRUE)['timestamp']."')");
				echo '{"status": [{"code": "success", "reason":"authenticated"}], "response": [{"code": "success", "reason":"activity recorded"}]}';
			}
		}
	}else{
		echo "
			<h1>Automated Programming Interface</h1><hr/>
			<ul>
				<li>Data Search
					<ul>
						<li>Asset ID Query:
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: ship_id OR station_id OR facility_id OR vehicle_id</li>
								<li>data: SWC entity ID number</li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=ship_id&data=69</li>
							</ul>
						</li>
						<li>Sentient Profile Overview:
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: sentient</li>
								<li>data: character handle with url formatting</li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=sentient&data=cedron+tryonel</li>
							</ul>
						</li>
						<li>Commerce Data:
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: commerce</li>
								<li>data: buyer handle, seller handle, asset entity name, market source (url formatted) </li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=commerce&data=Security+Control+Panel</li>
							</ul>
						</li>
						<li>Assets Data:
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: assets</li>
								<li>data: entity id, entity name, entity owner handle, entity type, sector, system, planet (url formatted) </li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=assets&data=Sector+5</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Data Entry
					<ul>
						<li>Log Entry:
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: logdata</li>
								<li>user: name of user</li>
								<li>type: search type, derived from Data Search \"query\" field values</li>
								<li>search: search parameters that user entered</li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=logdata&user=Cedron+Tryonel&type=ship_id&search=123</li>
							</ul>
						</li>
						<li>Log Entry (Discord User):
							<ul>
								<li>api_key: {user's API key}</li>
								<li>query: logdata</li>
								<li>discord_id: discord id of user</li>
								<li>type: search type, derived from Data Search \"query\" field values</li>
								<li>search: search parameters that user entered</li>
								<li>example: ../api.php?api_key=841e6a352920cfff665e4ded08e432b2&query=logdata&discord_id=Cedron3386&type=ship_id&search=123</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		";
	}
?>