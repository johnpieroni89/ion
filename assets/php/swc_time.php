<?php
$year = date("y", time() - 912149988) - 70;
$day = date("z", time() - 912149988);
$hour = date("H", time() - 912149988);
$minute = date("i", time() - 912149988);
$second = date("s", time() - 912149988);

$date = "Y".$year." D".$day;
$time = $hour.":".$minute.":".$second;

$timestamp = strtotime(date("U", time() - 912149988));

function eventTimestamp($time,$part){
	return strtotime(date($part, $time - 912149988));
}
?>