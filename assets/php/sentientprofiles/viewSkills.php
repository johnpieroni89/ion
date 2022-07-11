<?php
$data = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM characters_skills WHERE character_uid = '1:$uid'"));

if($data['strength'] == ""){ $strength = "?"; }else{ $strength = $data['strength']; }
if($data['dexterity'] == ""){ $dexterity = "?"; }else{ $dexterity = $data['dexterity']; }
if($data['speed'] == ""){ $speed = "?"; }else{ $speed = $data['speed']; }
if($data['dodge'] == ""){ $dodge = "?"; }else{ $dodge = $data['dodge']; }
if($data['projectiles'] == ""){ $projectiles = "?"; }else{ $projectiles = $data['projectiles']; }
if($data['nonprojectiles'] == ""){ $nonprojectiles = "?"; }else{ $nonprojectiles = $data['nonprojectiles']; }

if($data['medical'] == ""){ $medical = "?"; }else{ $medical = $data['medical']; }
if($data['diplomacy'] == ""){ $diplomacy = "?"; }else{ $diplomacy = $data['diplomacy']; }
if($data['crafting'] == ""){ $crafting = "?"; }else{ $crafting = $data['crafting']; }
if($data['management'] == ""){ $management = "?"; }else{ $management = $data['management']; }
if($data['perception'] == ""){ $perception = "?"; }else{ $perception = $data['perception']; }
if($data['stealth'] == ""){ $stealth = "?"; }else{ $stealth = $data['stealth']; }

if($data['metallurgy'] == ""){ $metallurgy = "?"; }else{ $metallurgy = $data['metallurgy']; }
if($data['electronics'] == ""){ $electronics = "?"; }else{ $electronics = $data['electronics']; }
if($data['engines'] == ""){ $engines = "?"; }else{ $engines = $data['engines']; }
if($data['weapons'] == ""){ $weapons = "?"; }else{ $weapons = $data['weapons']; }
if($data['repair'] == ""){ $repair = "?"; }else{ $repair = $data['repair']; }
if($data['computers'] == ""){ $computers = "?"; }else{ $computers = $data['computers']; }

if($data['fighter_p'] == ""){ $fighter_p = "?"; }else{ $fighter_p = $data['fighter_p']; }
if($data['fighter_c'] == ""){ $fighter_c = "?"; }else{ $fighter_c = $data['fighter_c']; }
if($data['capital_p'] == ""){ $capital_p = "?"; }else{ $capital_p = $data['capital_p']; }
if($data['capital_c'] == ""){ $capital_c = "?"; }else{ $capital_c = $data['capital_c']; }
if($data['space_command'] == ""){ $space_command = "?"; }else{ $space_command = $data['space_command']; }

if($data['vehicle_p'] == ""){ $vehicle_p = "?"; }else{ $vehicle_p = $data['vehicle_p']; }
if($data['vehicle_c'] == ""){ $vehicle_c = "?"; }else{ $vehicle_c = $data['vehicle_c']; }
if($data['infantry_command'] == ""){ $infantry_command = "?"; }else{ $infantry_command = $data['infantry_command']; }
if($data['vehicle_command'] == ""){ $vehicle_command = "?"; }else{ $vehicle_command = $data['vehicle_command']; }
if($data['heavy'] == ""){ $heavy = "?"; }else{ $heavy = $data['heavy']; }
?>

<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<form class="form-horizontal" role="form" method="post" action="">
	<div class="block-title bg-primary">
		<h2>Skills</h2> <input class="btn btn-info pull-right hidden-print" type="submit" name="submitSkills" value="Update">
	</div>
	<div class="modal-body">
		<div class="col-sm-4">
			<table class="table table-bordered table-striped table-responsive table-hover table-condensed">
				<?php
					echo "<tr><th>General Skills</th><th>Proficiency</th></tr>";
					echo "<tr><td>Strength</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"strength\" value=\"".$strength."\"></td></tr>";
					echo "<tr><td>Dexterity</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"dexterity\" value=\"".$dexterity."\"></td></tr>";
					echo "<tr><td>Speed</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"speed\" value=\"".$speed."\"></td></tr>";
					echo "<tr><td>Dodge</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"dodge\" value=\"".$dodge."\"></td></tr>";
					echo "<tr><td>Projectiles</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"projectiles\" value=\"".$projectiles."\"></td></tr>";
					echo "<tr><td>Non-Projectiles</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"nonprojectiles\" value=\"".$nonprojectiles."\"></td></tr>";
				?>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-bordered table-striped table-responsive table-hover table-condensed">
				<?php					
					echo "<tr><th>Social Skills</th><th>Proficiency</th></tr>";
					echo "<tr><td>Medical Treatment</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"medical\" value=\"".$medical."\"></td></tr>";
					echo "<tr><td>Diplomacy/Trading</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"diplomacy\" value=\"".$diplomacy."\"></td></tr>";
					echo "<tr><td>Crafting/Slicing</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"crafting\" value=\"".$crafting."\"></td></tr>";
					echo "<tr><td>Management</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"management\" value=\"".$management."\"></td></tr>";
					echo "<tr><td>Perception</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"perception\" value=\"".$perception."\"></td></tr>";
					echo "<tr><td>Stealth</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"stealth\" value=\"".$stealth."\"></td></tr>";
				?>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-bordered table-striped table-responsive table-hover table-condensed">
				<?php					
					echo "<tr><th>Science Skills</th><th>Proficiency</th></tr>";
					echo "<tr><td>R&D Metallurgy</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"metallurgy\" value=\"".$metallurgy."\"></td></tr>";
					echo "<tr><td>R&D Electronics</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"electronics\" value=\"".$electronics."\"></td></tr>";
					echo "<tr><td>R&D Engines</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"engines\" value=\"".$engines."\"></td></tr>";
					echo "<tr><td>R&D Weapons</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"weapons\" value=\"".$weapons."\"></td></tr>";
					echo "<tr><td>Repair</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"repair\" value=\"".$repair."\"></td></tr>";
					echo "<tr><td>Computer Operations</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"computers\" value=\"".$computers."\"></td></tr>";
				?>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-bordered table-striped table-responsive table-hover table-condensed">
				<?php					
					echo "<tr><th>Space Skills</th><th>Proficiency</th></tr>";
					echo "<tr><td>Fighter/Freighter Piloting</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"fighter_p\" value=\"".$fighter_p."\"></td></tr>";
					echo "<tr><td>Fighter/Freighter Combat</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"fighter_c\" value=\"".$fighter_c."\"></td></tr>";
					echo "<tr><td>Capital Ship Piloting</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"capital_p\" value=\"".$capital_p."\"></td></tr>";
					echo "<tr><td>Capital Ship Combat</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"capital_c\" value=\"".$capital_c."\"></td></tr>";
					echo "<tr><td>Space Command</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"space_command\" value=\"".$space_command."\"></td></tr>";
				?>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-bordered table-striped table-responsive table-hover table-condensed">
				<?php					
					echo "<tr><th>Ground Skills</th><th>Proficiency</th></tr>";
					echo "<tr><td>Vehicle Piloting</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"vehicle_p\" value=\"".$vehicle_p."\"></td></tr>";
					echo "<tr><td>Vehicle Combat</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"vehicle_c\" value=\"".$vehicle_c."\"></td></tr>";
					echo "<tr><td>Infantry Command</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"infantry_command\" value=\"".$infantry_command."\"></td></tr>";
					echo "<tr><td>Vehicle Command</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"vehicle_command\" value=\"".$vehicle_command."\"></td></tr>";
					echo "<tr><td>Heavy Weapons</td><td align=\"center\"><input style=\"text-align:center;\" type=\"text\" name=\"heavy\" value=\"".$heavy."\"></td></tr>";
				?>
			</table>
		</div>
	</div>
	</form>
</div>