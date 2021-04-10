<?php
if(!empty($_SESSION['user_privs']['transactions']) || !empty($_SESSION['user_privs']['admin'])){
	$query_transactions_cpm_purchases = mysqli_query($db->connection,"SELECT DISTINCT timestamp, order_id, buyer, price, source FROM transactions WHERE buyer = '$handle' AND source = 'CPM'");
	$query_transactions_cpm_sales = mysqli_query($db->connection,"SELECT DISTINCT timestamp, order_id, seller, price, source FROM transactions WHERE seller = '$handle' AND source = 'CPM'");
	$query_transactions_tfdot_purchases = mysqli_query($db->connection,"SELECT DISTINCT timestamp, order_id, buyer, price, source FROM transactions WHERE buyer = '$handle' AND source = 'TF DoT'");
	$query_transactions_tfdot_sales = mysqli_query($db->connection,"SELECT DISTINCT timestamp, order_id, seller, price, source FROM transactions WHERE seller = '$handle' AND source = 'TF DoT'");
	
	echo "
	<div class=\"block\" style=\"overflow:hidden;\">
		<div class=\"block-title bg-primary\">
			<h2>Market Transactions</h2>
		</div>
		<div class=\"block-content col-sm-12\" style=\"margin:0px;padding:0px;\">
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
				<tr><th align=\"center\">Market</th><th align=\"center\">Activity</th><th align=\"center\">Transactions</th><th align=\"center\">Total Value</th></tr>
	";
	
	if(mysqli_num_rows($query_transactions_cpm_purchases) != 0){
		$count = 0;
		$value = 0;
		while($row = mysqli_fetch_assoc($query_transactions_cpm_purchases)){
			$count++;
			$value = $value + str_replace(",","",$row['price']);
		}
		echo "<tr><td align=\"center\">CPM</td><td align=\"center\">Purchases</td><td align=\"center\">$count</td><td align=\"right\">".number_format($value)."</td></tr>";
	}else{
		echo "<tr><td align=\"center\">CPM</td><td align=\"center\">Purchases</td><td align=\"center\">0</td><td align=\"right\">0</td></tr>";
	}
	
	if(mysqli_num_rows($query_transactions_cpm_sales) != 0){
		$count = 0;
		$value = 0;
		while($row = mysqli_fetch_assoc($query_transactions_cpm_sales)){
			$count++;
			$value = $value + str_replace(",","",$row['price']);
		}
		echo "<tr><td align=\"center\">CPM</td><td align=\"center\">Sales</td><td align=\"center\">$count</td><td align=\"right\">".number_format($value)."</td></tr>";
	}else{
		echo "<tr><td align=\"center\">CPM</td><td align=\"center\">Sales</td><td align=\"center\">0</td><td align=\"right\">0</td></tr>";
	}
	
	if(mysqli_num_rows($query_transactions_tfdot_purchases) != 0){
		$count = 0;
		$value = 0;
		while($row = mysqli_fetch_assoc($query_transactions_tfdot_purchases)){
			$count++;
			$value = $value + str_replace(",","",$row['price']);
		}
		echo "<tr><td align=\"center\">TF DoT</td><td align=\"center\">Purchases</td><td align=\"center\">$count</td><td align=\"right\">".number_format($value)."</td></tr>";
	}else{
		echo "<tr><td align=\"center\">TF DoT</td><td align=\"center\">Purchases</td><td align=\"center\">0</td><td align=\"right\">0</td></tr>";
	}
	
	if(mysqli_num_rows($query_transactions_tfdot_sales) != 0){
		$count = 0;
		$value = 0;
		while($row = mysqli_fetch_assoc($query_transactions_tfdot_sales)){
			$count++;
			$value = $value + str_replace(",","",$row['price']);
		}
		echo "<tr><td align=\"center\">TF DoT</td><td align=\"center\">Sales</td><td align=\"center\">$count</td><td align=\"right\">".number_format($value)."</td></tr>";
	}else{
		echo "<tr><td align=\"center\">TF DoT</td><td align=\"center\">Sales</td><td align=\"center\">0</td><td align=\"right\">0</td></tr>";
	}
	
	
	echo "
			</table>
		</div>
	</div>
	";
} 
?>