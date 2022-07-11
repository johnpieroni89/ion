<?php
if(!isset($_GET['uid'])){ header("Location: sentientprofiles.php"); }
$uid = mysqli_real_escape_string($db->connection, $_GET['uid']);
$img = "http://custom.swcombine.com/static/1/$uid-100-100.jpg";
$query = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT characters.*, faction, race FROM characters LEFT JOIN characters_faction ON characters.uid = characters_faction.character_uid LEFT JOIN entities_races ON characters.race_uid = entities_races.uid WHERE characters.uid = '1:$uid'"));
$handle = mysqli_real_escape_string($db->connection,$query['handle']);

?>

<table class="table table-bordered table-striped table-responsive table-hover">
	<tr>
		<td style="vertical-align: middle;"><center><img src="<?php echo $img;?>"></center></td>
		<td style="vertical-align: middle;"><center><h1><b><?php echo $handle;?></b></h1></center></td>
		<td style="vertical-align: middle;"><center><h3><b>ID: <?php echo $uid;?></b></h3></center></td>
	</tr>
	<tr>
		<td style="vertical-align: middle;"><center><h4><b>Race:
			<form method="post" action="" style="display:inline;">
				<select name="raceUpdate">
					<option value="">--Unknown--</option> 
					<?php
						$races = mysqli_query($db->connection,"SELECT * FROM entities_races ORDER BY race");
						while($race_list = mysqli_fetch_assoc($races)){
							if($query['race'] == $race_list['race']){
								echo "<option value=\"".$race_list['uid']."\" selected>".$race_list['race']."</option>";
							}else{
								echo "<option value=\"".$race_list['uid']."\">".$race_list['race']."</option>";
							}
						}
					?>
				</select>
				<input type="submit" class="btn btn-primary hidden-print" name="submitRace" value="Save">
			</form>
		</h4></b></center></td>
		<td style="vertical-align: middle;">
			<center><h4><b>Current Faction:
			<form method="post" action="" style="display:inline;">
				<select name="factionUpdate">
					<?php if(!empty($query['faction'])){echo "<option value=\"\">--Unknown--</option>";}else{echo "<option value=\"\">--Unknown--</option>";} ?> 
					<?php
						$factions = mysqli_query($db->connection,"SELECT * FROM factions ORDER BY name");
						while($faction_list = mysqli_fetch_assoc($factions)){
							if($query['faction'] == $faction_list['name']){
								echo "<option value=\"".$faction_list['name']."\" selected>".$faction_list['name']."</option>";
							}else{
								echo "<option value=\"".$faction_list['name']."\">".$faction_list['name']."</option>";
							}
						}
					?>
				</select>
				<input type="submit" class="btn btn-primary hidden-print" name="submitFaction" value="Save">
			</form>
		</b></h4></center></td>
		<td style="vertical-align: middle;"><center><h4><b><a target="_blank" href="https://www.swcombine.com/profiles/index.php?c=1&tHand=<?php echo urlencode($handle);?>">[SWC Profile]</a><br/><br/><a target="_blank" href="https://www.swcombine.com/members/messages/msgframe.php?mode=send&receiver=<?php echo urlencode($handle);?>">[Send DM]</a></b></h4></center></td>
	</tr>
</table>