<?php

	echo "<center><h2><b>Facility Income by ".ucwords($focus)."</b></h2></center>";
	$results_count = mysqli_num_rows($queryIncome);
	if($results_count != 0){
		echo "
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
			<tr><th>".ucwords($focus)."</th><th>Facility Income</th></tr>
		";
		
		while($data = mysqli_fetch_assoc($queryIncome)){
			echo "<tr><td>".$data['focus']."</td><td>".number_format($data['facilityincome'])."</td></tr>";
		}
		echo "</table>";
	}else{
		echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
	}

?>