<?php
	if(!empty($access['signalsanalysis_search'])){
		echo '{"status": [{"code": "success", "reason":"authenticated"}],';
		if($query == "ship_id"){
			$category = "2";
		}elseif($query == "station_id"){
			$category = "5";
		}elseif($query == "facility_id"){
			$category = "4";
		}elseif($query == "vehicle_id"){
			$category = "3";
		}
		
		$lookup = mysqli_query($db->connection, "SELECT data_signalsanalysis.*, entities.name AS type_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name, galaxy_planets.img_small, custom_image FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_customs.uid = data_signalsanalysis.uid WHERE data_signalsanalysis.uid = '".$category.":".$data."'");
		if(mysqli_num_rows($lookup) == 0){
			$lookup = mysqli_query($db->connection, "SELECT data_signalsanalysis.*, entities.name AS type_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name, galaxy_planets.img_small, custom_image FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_customs.uid = data_signalsanalysis.uid WHERE data_signalsanalysis.name = '".$data."'");
		}
		
		if(mysqli_num_rows($lookup) != 0){
			$lookup = mysqli_fetch_assoc($lookup);
			$gal = "(".$lookup['galx'].", ".$lookup['galy'].")";
			$sys = "(".$lookup['sysx'].", ".$lookup['sysy'].")";
			$timestamp = swc_time($lookup['timestamp'])['date'];
			if($lookup['custom_image'] == ""){
				if($query == "ship_id"){
					$entImg = "https://img.swcombine.com//ships/".substr($lookup['type'], 2)."/main.jpg";
				}elseif($query == "station_id"){
					$entImg = "https://img.swcombine.com//stations/".substr($lookup['type'], 2)."/main.jpg";
				}elseif($query == "facility_id"){
					$entImg = "https://img.swcombine.com//facilities/".substr($lookup['type'], 2)."/main_1.jpg";
				}elseif($query == "vehicle_id"){
					$entImg = "https://img.swcombine.com//vehicles/".substr($lookup['type'], 2)."/main.jpg";
				}
			}else{
				$entImg = $lookup['custom_image'];
			}
			echo '"response": [{"id": "'.$data.'", "customImg": "'.$entImg.'", "type": "'.$lookup['type_name'].'", "owner": "'.$lookup['owner'].'", "name":"'.utf8_decode($lookup['name']).'", "sector":"'.$lookup['sector_name'].'", "system":"'.$lookup['system_name'].'", "planet":"'.$lookup['planet_name'].'", "planetImage": "'.$data['img_small'].'", "gal": "'.$gal.'", "sys": "'.$sys.'", "timestamp":"'.$timestamp.'"}]}';
		}else{
			echo '"response": [{"code": "failure", "reason":"no results"}]}';
		}
	}else{
		echo '{"status": [{"code": "failure", "reason":"access denied"}]}';
	}
?>