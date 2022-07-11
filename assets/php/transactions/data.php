<?php	
	if(isset($_POST['search'])){
		$search = $_POST['search'];
		$query = "SELECT DISTINCT timestamp, order_id, buyer, seller, price, assets, source FROM transactions WHERE order_id LIKE '%$search%' OR buyer LIKE '%$search%' OR seller LIKE '%$search%' OR price LIKE '%$search%' OR assets LIKE '%$search%' OR source LIKE '%$search%' ORDER BY timestamp DESC, order_id DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '5', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}elseif(isset($_GET['search'])){
		$search = $_GET['search'];
		$query = "SELECT DISTINCT timestamp, order_id, buyer, seller, price, assets, source FROM transactions WHERE order_id LIKE '%$search%' OR buyer LIKE '%$search%' OR seller LIKE '%$search%' OR price LIKE '%$search%' OR assets LIKE '%$search%' OR source LIKE '%$search%' ORDER BY timestamp DESC, order_id DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '5', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		$query = "SELECT DISTINCT timestamp, order_id, buyer, seller, price, assets, source FROM transactions ORDER BY timestamp DESC, order_id DESC";
	}
	
	$results_count = mysqli_num_rows(mysqli_query($db->connection,$query));
	echo "<center><h2><b>Transactions Log</b></h2></center>";
	
	if($results_count != 0){
		
		$pages = new Paginator($results_count,9);
		
		echo "<div class=\"col-sm-6 text-left pull-left\" style=\"font-size:18px;\"><span class=\"form-inline\"><b>Results: ".number_format($results_count)."</b></span></div>";

		echo '<div class="col-sm-6 text-right pull-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
		
		$limit = $pages->limit_start.','.$pages->limit_end;
		$query = mysqli_query($db->connection,$query." LIMIT ".$limit);
		
		echo "
			<table class=\"table table-bordered table-striped table-responsive table-hover sortable\" style=\"font-size:14px;\">
			<tr>
				<th width=\"100\">Timestamp</th>
				<th width=\"100\">Order #</th>
				<th width=\"190\">Buyer</th>
				<th width=\"190\">Seller</th>
				<th width=\"100\">Price</th>
				<th style=\"max-width:400px;\">Assets</th>
				<th>Source</th>
			</tr>
		";

		$count = 1 + $pages->limit_start;
		while($data = mysqli_fetch_assoc($query)){
			$time = swc_time($data["timestamp"]);
			if($data['source'] == "TF DoT"){
				$link = "<a target=\"_blank\" href=\"http://dot.swc-tf.com/view/view_order_sidepanel.php?order=".$data['order_id']."\">";
			}else{
				$link = "<a target=\"_blank\" href=\"http://market.centrepointstation.com/details.php?lid=".$data['order_id']."\">";
			}
			
			echo "
				<tr>
					<td align=\"center\">Y".str_pad($time['year'],2,"0",STR_PAD_LEFT)." D".str_pad($time['day'],3,"0",STR_PAD_LEFT)."</td>
					<td align=\"center\">".$link.$data['order_id']."</a></td>
					<td>".$data['buyer']."</td>
					<td>".$data['seller']."</td>
					<td align=\"right\">".$data['price']."</td>
					<td>".$data['assets']."</td>
					<td align=\"center\">".$data['source']."</td>
				</tr>";
			$count++;
		}
		echo "</table>";
		
		echo '<div class="col-sm-6 text-right pull-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
		
	}else{
		echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
	}
	
?>