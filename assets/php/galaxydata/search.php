<?php

	if($results_count != 0){
		echo "<h2><center><b>Planet Search</b></center></h2>";
		$db = new database;
		$db->connect();
		$pages = new Paginator($results_count,9);
		echo '<div class="col-sm-12 text-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
		$limit = $pages->limit_start.','.$pages->limit_end;
		$query = mysqli_query($db->connection,$search_string." LIMIT ".$limit);
		echo "<div class=\"hidden-print\">Note: Click on Column headers for sorting</div>";
		echo "
			<table class=\"table table-bordered table-striped table-responsive table-hover sortable\">
			<tr>
				<th>Image</th>
				<th>Size</th>
				<th>Type</th>
				<th>Planet</th>
				<th>System</th>
				<th>Sector</th>
				<th>Owner</th>
				<th>Governor</th>
				<th>Magistrate</th>
				<th align=\"right\">Population</th>
				<th align=\"right\">Cities</th>
				<th align=\"right\">Civ Lvl</th>
				<th align=\"right\">Tax Lvl</th>
				<th class=\"hidden-print\">Deposits</th>
			</tr>
		";
		while($data = mysqli_fetch_assoc($query)){
			$deposits_exist = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(deposit_uid) as count FROM data_galaxydata_deposits WHERE planet_uid='".$data['uid']."'"))["count"];
			if($deposits_exist > 0){$deposits_link = "<a href=\"galaxydata.php?planet_uid=".$data['uid']."&type=deposits\" target=\"_blank\">Available</a>";}else{$deposits_link = "";}
			echo "
				<tr>
					<td><img style=\"max-height:20px;\" src=\"".$data['img_small']."\"></td>
					<td>".$data['size']."</td>
					<td>".$data['type']."</td>
					<td><a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$data['uid'])[1]."\">".$data['name']."</a></td>
					<td><a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&systemID=".explode(":",$data['system'])[1]."\">".$data['systemname']."</a></td>
					<td><a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&sectorID=".explode(":",$data['sector'])[1]."\">".$data['sectorname']."</a></td>
					<td>".$data['controlled_by']."</td>
					<td>".$data['governor']."</td>
					<td>".$data['magistrate']."</td>
					<td align=\"right\">".number_format($data['population'])."</td>
					<td align=\"right\">".number_format($data['cities'])."</td>
					<td align=\"right\">".$data['civilization']."</td>
					<td align=\"right\">".$data['tax']."</td>
					<td class=\"hidden-print\">".$deposits_link."</td>
				</tr>";
		}
		echo "</table>";
		
		echo '<div class="col-sm-12 text-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
	}else{
		echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
	}

?>