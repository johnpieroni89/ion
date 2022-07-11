<?php

if(!empty($search_header)){$header_string = " for ".$search_header;}else{$header_string = "";}
if($results_count != 0){
	echo "<center><h1><b>Profiles$header_string</b></h1></center>";

	$pages = new Paginator($results_count,9);
	echo '<div class="col-sm-12 text-right pull-right hidden-print">';
	echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
	echo '</div>';
	echo '<div class="clearfix"></div><hr/>';
	$limit = $pages->limit_start.','.$pages->limit_end;

	if($query = mysqli_query($db->connection,$search_string." LIMIT ".$limit)){

		while($profile = mysqli_fetch_assoc($query)){
			$img = "http://custom.swcombine.com/static/1/".explode(":",$profile["uid"])[1]."-100-100.jpg";
			echo "
				<a target =\"blank\" href=\"profile.php?uid=".explode(":",$profile["uid"])[1]."\" style=\"color:black;text-decoration:none;:\">
				<div class=\"col-sm-4 col-md-4 col-lg-3 card\" style=\"margin-bottom:20px;\">
					<div style=\"border:1px solid black\">
						<div class=\"bg-primary\" style=\"text-align:center;\">
							<b>".$profile["handle"]."</b>
						</div>
						<div class=\"photo bg-secondary\" style=\"text-align:center;\">
							<img style=\"margin-top:4px;margin-bottom:4px;\" height=\"99\" width=\"100\" src=\"".$img."\">
						</div>
						<div class=\"bg-primary\" style=\"text-align:center;\">
							".$profile['faction']."
							<br/>
							ID#: ".explode(":",$profile["uid"])[1]."
						</div>
					</div>
				</div>
				</a>
			";
		}
		echo '<hr/><div class="col-sm-12 text-right pull-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
	}else{
		echo "Error";
	}
}else{
	echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
}

?>