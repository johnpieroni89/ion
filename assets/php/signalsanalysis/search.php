<?php

	echo "<center><h2><b>".ucfirst(urldecode($sessionType))."</b></h2></center>";
	if($results_count != 0){
		if($sessionType == "Search Details" || $sessionType == "activity"){
			$pages = new Paginator($results_count,9);
			if($_SESSION['user_privs']['signalsanalysis_export'] > 0 || $_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
				echo "<form method=\"post\" action=\"$form_link\" id=\"dataForm\">";
				echo "
					<button title=\"Select All\" type=\"button\" class=\"btn btn-primary hidden-print\" id=\"checkAll\">
						<span class=\"far fa-check-square\"></span> Select All
					</button>
					<button title=\"Unselect All\" type=\"button\" class=\"btn btn-primary hidden-print\" id=\"uncheckAll\">
						<span class=\"far fa-square\"></span> Unselect All
					</button>
				";
				
				if(($_SESSION['user_privs']['signalsanalysis_export'] > 0 || $_SESSION['user_privs']['admin'] > 0) && isset($search_string)){
					echo "<input type=\"submit\" class=\"btn btn-info fa fa-input hidden-print\" style=\"height:34px;margin:1px;\" name=\"Export\" onclick=\"$('form#dataForm').attr('target', '_blank');$('form#dataForm').attr('action', 'export.php');\" value=\"&#xf019 Export\">";
				}
				if(($_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0) && isset($search_string)){
					echo "<input type=\"submit\" class=\"btn btn-warning fa fa-input hidden-print\" style=\"height:34px;margin:1px;\" name=\"Delete\" value=\"&#xf1f8 Delete\" onclick=\"return confirm('Do you really want to delete the selected records?');\">";
				}
			}
			echo '<div class="col-sm-6 text-right pull-right hidden-print">';
			echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
			echo '</div>';
			echo '<div class="clearfix"></div>';
			$limit = $pages->limit_start.','.$pages->limit_end;
			$query = mysqli_query($db->connection,$search_string." LIMIT ".$limit);
			
			echo "
				<table class=\"table table-bordered table-striped table-responsive table-hover sortable\">
				<tr>
					<th class=\"hidden-print sorttable_nosort\" style=\"position:sticky; top:0;\">#</th>
					<th class=\"sorttable_nosort\" style='max-width:70px;'>Image</th>
					<th style='width:220px;'>Entity</th>
					<th style='width:200px;'>Name</th>
					<th>Location</th>
					<th>Assignment</th>
					<th>Cargo</th>
					<th>Collection Metadata</th>
				</tr>
			";

			$count = 1 + $pages->limit_start;
			while($data = mysqli_fetch_assoc($query)){
				$time = swc_time($data["timestamp"]);
				if(isset($data['custom_image'])){ $img = $data['custom_image'];}else{ $img = $data['img_small'];}
				if(isset($data['ent_class'])){ $class = "".$data['ent_class'];}else{ $class = "";}
				if(isset($data['loc_sec'])){ $loc_sec = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&sectorID=".explode(":",$data['sector'])[1]."\">".$data['loc_sec']."</a>";}else{ $loc_sec = "Deepspace";}
				if(isset($data['loc_sys'])){ $loc_sys = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&systemID=".explode(":",$data['system'])[1]."\">".$data['loc_sys']."</a>";}else{ $loc_sys = "Deepspace";}
				if(isset($data['loc_planet'])){ $loc_planet = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$data['planet'])[1]."\">".$data['loc_planet']."</a>";}else{ $loc_planet = "Space ";}
				if(isset($data['atmox'])){ $atmosphere = "Atmosphere Position: (".$data['atmox'].", ".$data['atmoy'].")";}else{ $atmosphere = "";}
				if(isset($data['surfx'])){ $surface = "Surface Position: (".$data['surfx'].", ".$data['surfy'].")";}else{ $surface = "";}
				if(isset($data['manager']) && ($_SESSION['user_privs']['signalsanalysis_search'] == 2 || $_SESSION['user_privs']['admin'] > 0)){ $manager = "<br/>Manager: ".$data['manager'];}else{ $manager = "";}
				if(isset($data['operator']) && ($_SESSION['user_privs']['signalsanalysis_search'] == 2 || $_SESSION['user_privs']['admin'] > 0)){ $operator = "<br/>Operator: ".$data['operator'];}else{ $operator = "";}
				if(isset($data['passengers'])){ $passengers = "Passengers: ".$data['passengers'];}else{ $passengers = "";}
				if(isset($data['ships'])){ $ships = "Ships: ".$data['ships'];}else{ $ships = "";}
				if(isset($data['vehicles'])){ $vehicles = "Vehicles: ".$data['vehicles'];}else{ $vehicles = "";}
				if(isset($data['income']) && ($_SESSION['user_privs']['signalsanalysis_search'] == 2 || $_SESSION['user_privs']['admin'] > 0)){ $income = "<br/>Income: ".$data['income'];}else{ $income = "";}
				if(isset($data['activity_id'])){ $activity = "Activity: <a href=\"signalsanalysis.php?activity=".$data['activity_id']."\" target=\"_blank\">".$data['activity_id']."</a>";}else{ $activity = "";}
				if($_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
					$checkbox = "<br/><br/><input type=\"checkbox\" name=\"entities[]\" value=\"".$data['uid'].";".$data["timestamp"]."\">";
				}else{$delete_checkbox = "";}
				
				echo "
					<tr>
						<td class=\"hidden-print\">".$count.$checkbox."</td>
						<td align=\"center\"><img height=\"70\" width=\"70\" src=\"".$img."\"></td>
						<td>#".explode(":",$data['uid'])[1]."<br/>".$data['type_name']."<br/>".$class."</td>
						<td>".utf8_decode($data['name'])."</td>
						<td>".$loc_sec."<br/>".$loc_sys." (".$data['galx'].", ".$data['galy'].")<br/>".$loc_planet." (".$data['sysx'].", ".$data['sysy'].")<br/>".$atmosphere."<br/>".$surface."</td>
						<td>Owner: ".$data['owner'].$manager.$operator.$income."</td>
						<td>".$passengers."<br>".$ships."<br>".$vehicles."</td>
						<td>Year ".$time['year']." Day ".$time['day']." ".$time['hour'].":".$time['minute'].":".$time['second']."<br/>".$activity."</td>
					</tr>";
				$count++;
			}
			echo "</table>";
			if($_SESSION['user_privs']['signalsanalysis_export'] > 0 || $_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
				echo "
					<button title=\"Select All\" type=\"button\" class=\"btn btn-primary hidden-print\" id=\"checkAll2\">
						<span class=\"far fa-check-square\"></span> Select All
					</button>
					<button title=\"Select All\" type=\"button\" class=\"btn btn-primary hidden-print\" id=\"uncheckAll2\">
						<span class=\"far fa-square\"></span> Unselect All
					</button>
				";
				if(($_SESSION['user_privs']['signalsanalysis_export'] > 0 || $_SESSION['user_privs']['admin'] > 0) && isset($search_string)){
					echo "<input type=\"submit\" class=\"btn btn-info fa fa-input hidden-print\" style=\"height:34px;margin:1px;\" id=\"export\" name=\"Export\" onclick=\"$('form#dataForm').attr('target', '_blank');$('form#dataForm').attr('action', 'export.php');\" value=\"&#xf019 Export\">";
				}
				if(($_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0) && isset($search_string)){
					echo "<input type=\"submit\" class=\"btn btn-warning fa fa-input hidden-print\" style=\"height:34px;margin:1px;\" name=\"Delete\" value=\"&#xf1f8 Delete\" onclick=\"return confirm('Do you really want to delete the selected records?');\">";
				}
				echo "</form>";
			}
			
			echo '<div class="col-sm-6 text-right pull-right hidden-print">';
			echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}elseif($sessionType == "Search Summary"){
			
			$query = mysqli_query($db->connection,$search_string_types);
			echo "
				<div class=\"col-sm-12 col-md-8 col-md-offset-2\" style=\"font-size:14px;\">
				<table class=\"table table-bordered table-striped table-hover sortable\">
				<tr>
					<th width=\"400\">Entity Types</th>
					<th width=\"200\">Count</th>
				</tr>
			";
			while($results = mysqli_fetch_assoc($query)){
				echo "
					<tr>
						<td style=\"padding:2px;\">".$results['type_name']."</td>
						<td style=\"padding:2px;\">".$results['count']."</td>
					</tr>
				";
			}
			echo "</table></div>";
			
			$query = mysqli_query($db->connection,$search_string_owners);
			echo "
				<div class=\"col-sm-12 col-md-8 col-md-offset-2\" style=\"font-size:14px;\">
				<table class=\"table table-bordered table-striped table-hover sortable\">
				<tr>
					<th width=\"400\">Owners</th>
					<th width=\"200\">Entity Count</th>
				</tr>
			";
			while($results = mysqli_fetch_assoc($query)){
				echo "
					<tr>
						<td style=\"padding:2px;\">".$results['owner']."</td>
						<td style=\"padding:2px;\">".$results['count']."</td>
					</tr>
				";
			}
			echo "</table></div>";
			
			$query = mysqli_query($db->connection,$search_string_classes);
			echo "
				<div class=\"col-sm-12 col-md-8 col-md-offset-2\" style=\"font-size:14px;\">
				<table class=\"table table-bordered table-striped table-hover sortable\">
				<tr>
					<th width=\"400\">Entity Classes</th>
					<th width=\"200\">Count</th>
				</tr>
			";
			while($results = mysqli_fetch_assoc($query)){
				$class = $results['ent_class'];
				if($class == ""){$class = "Stations";}
				echo "
					<tr>
						<td style=\"padding:2px;\">".$class."</td>
						<td style=\"padding:2px;\">".$results['count']."</td>
					</tr>
				";
			}
			echo "</table></div>";
		}
	}else{
		echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
	}

?>