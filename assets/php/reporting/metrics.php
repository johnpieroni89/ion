<?php

	$db = new database;
	$db->connect();
	
	$total_information_needs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(need_id) as count FROM reporting_needs"))["count"];
	$total_information_needs_reports = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(id) as count FROM reporting_reports_coi"))["count"];
	$total_reports = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports"))["count"];
	$total_contributors = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(DISTINCT author) as count FROM reporting_reports"))["count"];
	$eco = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'ECO'"))["count"];
	$ind = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'IND'"))["count"];
	$ins = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'INS'"))["count"];
	$mil = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'MIL'"))["count"];
	$ops = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'OPS'"))["count"];
	$pol = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'POL'"))["count"];
	$soc = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'SOC'"))["count"];
	$tec = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'TEC'"))["count"];
	$nil = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(report_id) as count FROM reporting_reports WHERE focus = 'NIL'"))["count"];
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\"><center><h1><b>General Metrics</b></h1></center></div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"margin: auto;\">
				<center>
				<table class=\"table table-bordered table-striped table-hover\">
					<tr><td>Total Info Needs (INs):</td><td>".number_format($total_information_needs)."</td></tr>
					<tr><td>Total Reports:</td><td>".number_format($total_reports)."</td></tr>
					<tr><td>Reports Fulfilling INs:</td><td>".number_format($total_information_needs_reports)."</td></tr>
					<tr><td>Total Contributors:</td><td>".number_format($total_contributors)."</td></tr>
				</table>
				</center>
			</div>
		</div>
	";
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\"><center><h1><b>Topical Metrics</b></h1></center></div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"margin: auto;\">
				<center>
				<table class=\"table table-bordered table-striped table-hover\">
					<tr><td>Economy:</td><td>".number_format($eco)."</td></tr>
					<tr><td>Industry:</td><td>".number_format($ind)."</td></tr>
					<tr><td>Instability:</td><td>".number_format($ins)."</td></tr>
					<tr><td>Military:</td><td>".number_format($mil)."</td></tr>
					<tr><td>Operations:</td><td>".number_format($ops)."</td></tr>
					<tr><td>Politics:</td><td>".number_format($pol)."</td></tr>
					<tr><td>Social:</td><td>".number_format($soc)."</td></tr>
					<tr><td>Technology:</td><td>".number_format($tec)."</td></tr>
					<tr><td>None:</td><td>".number_format($nil)."</td></tr>
				</table>
				</center>
			</div>
		</div>
	";

?>