<?php
	if(!empty($access['sentientprofiles_general'])){
		include("../functions.php");
		add_character($data);
		$db = new database;
		$db->connect();
		$charCheck = mysqli_query($db->connection, "SELECT characters.*, entities_races.race, characters_faction.faction FROM characters LEFT JOIN characters_faction ON characters.uid = characters_faction.character_uid LEFT JOIN entities_races ON entities_races.uid = characters.race_uid WHERE handle = '$data'");
		echo '{"status": [{"code": "success", "reason":"authenticated"}],';
		
		if(mysqli_num_rows($charCheck) != 0){
			//OVERVIEW
			$charCheck = mysqli_fetch_assoc($charCheck);
			if(!empty($charCheck['race'])){ $race = ", a ".$charCheck['race'].", ";}else{ $race = "";}
			if(!empty($charCheck['faction'])){ $overviewResponse = "".ucwords($data)."".$race."is a member of ".$charCheck['faction'].".";}else{ $overviewResponse = "It is unknown which organization ".ucwords($data)."".$race."is a member of.";}
			
			//ASSETS
			$ships = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis WHERE data_signalsanalysis.owner = '".$data."' AND data_signalsanalysis.uid LIKE '2:%' GROUP BY owner"));
			$stations = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis WHERE data_signalsanalysis.owner = '".$data."' AND data_signalsanalysis.uid LIKE '5:%' GROUP BY owner"));
			$facilities = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis WHERE data_signalsanalysis.owner = '".$data."' AND data_signalsanalysis.uid LIKE '4:%' GROUP BY owner"));
			$vehicles = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis WHERE data_signalsanalysis.owner = '".$data."' AND data_signalsanalysis.uid LIKE '3:%' GROUP BY owner"));
			$all = mysqli_query($db->connection, "SELECT COUNT(data_signalsanalysis.uid) AS count, galaxy_sectors.name AS sector_name FROM data_signalsanalysis LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid WHERE data_signalsanalysis.owner = '".$data."' GROUP BY sector_name ORDER BY count DESC");
			if(mysqli_num_rows($all) != 0){
				//COUNT ASSETS
				$counts = array();
				if($ships['count'] != 0){ array_push($counts, "".$ships['count']." ships"); }
				if($stations['count'] != 0){ array_push($counts, "".$stations['count']." stations"); }
				if($facilities['count'] != 0){ array_push($counts, "".$facilities['count']." facilities"); }
				if($vehicles['count'] != 0){ array_push($counts, "".$vehicles['count']." vehicles"); }
				
				//LOCATE MAJORITY OF ASSETS
				$assetsSector = mysqli_fetch_assoc($all)['sector_name'];
				if($assetsSector == ""){ $assetsSector = "Deepspace";}else{ $assetsSector = "the ".$assetsSector." sector";}
				
				//CREATE ASSET STATEMENT
				$assetResponse = "This person of interest has at least ".implode(", ", $counts).". Most of these known assets are located in ".$assetsSector.".";
			}else{
				$assetResponse = "no results";
			}
			
			//PLANETS
			$planGov = mysqli_query($db->connection, "SELECT * FROM galaxy_planets WHERE governor = '".$data."'");
			$numGov = mysqli_num_rows($planGov);
			$planMag = mysqli_query($db->connection, "SELECT * FROM galaxy_planets WHERE magistrate = '".$data."'");
			$numMag = mysqli_num_rows($planMag);
			
			if($numGov == 0 && $numMag == 0){
				$planetsResponse = "";
			}else{				
				if($numGov != 0){ 
					$planetsResponse = "They are governor of ".$numGov." planets, ";
					$govOwner = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(galaxy_planets.uid) AS count, galaxy_planets.controlled_by FROM galaxy_planets WHERE governor = '".$data."' GROUP BY galaxy_planets.controlled_by ORDER BY count DESC"));
					$govSector = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(galaxy_planets.uid) AS count, galaxy_sectors.name AS sector_name FROM galaxy_planets LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE governor = '".$data."' GROUP BY galaxy_sectors.name ORDER BY count DESC"));
					$planetsResponse = $planetsResponse."the majority are controlled by ".$govOwner['controlled_by']." and the majority are located in the ".$govSector['sector_name']." sector.";
				}
				
				if($numMag != 0){ 
					$planetsResponse = "They are magistrate of ".$numMag." planets, ";
					$magOwner = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(galaxy_planets.uid) AS count, galaxy_planets.controlled_by FROM galaxy_planets WHERE magistrate = '".$data."' GROUP BY galaxy_planets.controlled_by ORDER BY count DESC"));
					$magSector = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(galaxy_planets.uid) AS count, galaxy_sectors.name AS sector_name FROM galaxy_planets LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE magistrate = '".$data."' GROUP BY galaxy_sectors.name ORDER BY count DESC"));
					$planetsResponse = $planetsResponse."the majority are controlled by ".$magOwner['controlled_by']." and the majority are located in the ".$magSector['sector_name']." sector.";
				}
			}
			
			//COMMERCE
			$trades = mysqli_query($db->connection, "SELECT SUM(REPLACE(price, ',', '')) AS value, COUNT(id) AS trades FROM transactions WHERE buyer = '".$data."' OR seller = '".$data."'");
			$source = mysqli_query($db->connection, "SELECT COUNT(id) AS count, source FROM transactions WHERE buyer = '".$data."' OR seller = '".$data."' GROUP BY source ORDER BY count DESC");
			if(mysqli_num_rows($trades) != 0){
				$trades = mysqli_fetch_assoc($trades);
				$source = mysqli_fetch_assoc($source);
				$commerceResponse = "This person of interest has a trading record of ".number_format($trades['value'],0)." credits with ".number_format($trades['trades'],0)." total transactions, primarily on ".$source['source'].".";
			}else{
				$commerceResponse = "";
			}
			
			//RESPONSE
			echo '"response": [{"overview": "'.$overviewResponse.'", "assets": "'.$assetResponse.'", "planets": "'.$planetsResponse.'", "commerce":"'.$commerceResponse.'"}]}';
		}else{
			echo '"response": [{"code": "failure", "reason":"no results"}]}';
		}
		
		/*
		if(mysqli_num_rows($lookup) != 0){
			
		}*/
	}else{
		echo '{"status": [{"code": "failure", "reason":"access denied"}]}';
	}
?>