<?php
	if(!empty($access['signalsanalysis_search'])){
		echo '{"status": [{"code": "success", "reason":"authenticated"}],';
		if(strpos($data, " AND ") == TRUE){
			$list = explode(" AND ", $data);
			$expanded_search = array();
			foreach($list as $item){
				array_push($expanded_search, "(data_signalsanalysis.uid LIKE '%:".trim($item)."' OR data_signalsanalysis.name LIKE '%".trim($item)."%' OR data_signalsanalysis.owner LIKE '%".trim($item)."%' OR entities.name LIKE '%".trim($item)."%' OR galaxy_sectors.name LIKE '%".trim($item)."%' OR galaxy_systems.name LIKE '%".trim($item)."%' OR galaxy_planets.name LIKE '%".trim($item)."%')");
			}
			$expanded_search = implode(" AND ", $expanded_search);
			$lookup = mysqli_query($db->connection, "SELECT data_signalsanalysis.*, entities.name AS type_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name, galaxy_planets.img_small, custom_image FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_customs.uid = data_signalsanalysis.uid WHERE ".$expanded_search." ORDER BY timestamp DESC LIMIT 20");
		}else{
			$lookup = mysqli_query($db->connection, "SELECT data_signalsanalysis.*, entities.name AS type_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name, galaxy_planets.img_small, custom_image FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_customs.uid = data_signalsanalysis.uid WHERE data_signalsanalysis.uid LIKE '%:".$data."' OR data_signalsanalysis.name LIKE '%".$data."%' OR data_signalsanalysis.owner LIKE '%".$data."%' OR entities.name LIKE '%".$data."%' OR galaxy_sectors.name LIKE '%".$data."%' OR galaxy_systems.name LIKE '%".$data."%' OR galaxy_planets.name LIKE '%".$data."%' ORDER BY timestamp DESC LIMIT 20");
		}
		$count = mysqli_num_rows($lookup);
		if(mysqli_num_rows($lookup) != 0){
			echo '"response":[';
			while($row = mysqli_fetch_assoc($lookup)){
				$gal = "(".$row['galx'].", ".$row['galy'].")";
				$sys = "(".$row['sysx'].", ".$row['sysy'].")";
				if(!empty($row['atmox'])){ $atmo = "(".$row['atmox'].", ".$row['atmoy'].")"; }else{ $atmo = ""; }
				if(!empty($row['surfx'])){ $surf = "(".$row['surfx'].", ".$row['surfy'].")"; }else{ $surf = ""; }
				if($count == 1){
					echo '{"uid": "'.substr($row['uid'],2).'", "timestamp":"'.swc_time($row['timestamp'])['date'].'", "name":"'.utf8_decode($row['name']).'", "type":"'.$row['type_name'].'", "owner":"'.$row['owner'].'", "sector":"'.$row['sector_name'].'", "system":"'.$row['system_name'].'", "planet":"'.$row['planet_name'].'", "gal": "'.$gal.'", "sys": "'.$sys.'", "atmo": "'.$atmo.'", "surf": "'.$surf.'"}';
				}else{
					echo '{"uid": "'.substr($row['uid'],2).'", "timestamp":"'.swc_time($row['timestamp'])['date'].'", "name":"'.utf8_decode($row['name']).'", "type":"'.$row['type_name'].'", "owner":"'.$row['owner'].'", "sector":"'.$row['sector_name'].'", "system":"'.$row['system_name'].'", "planet":"'.$row['planet_name'].'", "gal": "'.$gal.'", "sys": "'.$sys.'", "atmo": "'.$atmo.'", "surf": "'.$surf.'"},';
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