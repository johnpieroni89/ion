<?php

if(isset($_POST['raceUpdate'])){
	$race_uid = mysqli_real_escape_string($db->connection, $_POST['raceUpdate']);
	mysqli_query($db->connection, "UPDATE characters SET race_uid = '".$race_uid."' WHERE uid = '1:$uid'");
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: User updated the race for <a target=\"blank\" href=\"../profile.php?uid=$uid\">$handle</a>.', '".swc_time(time(),TRUE)["timestamp"]."')");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The race for <a target=\"blank\" href=\"../profile.php?uid=$uid\">$handle</a> has been updated</div>";
}

if(isset($_POST['submitSkills'])){
	$strength = mysqli_real_escape_string($db->connection, $_POST['strength']);
	$dexterity = mysqli_real_escape_string($db->connection, $_POST['dexterity']);
	$speed = mysqli_real_escape_string($db->connection, $_POST['speed']);
	$dodge = mysqli_real_escape_string($db->connection, $_POST['dodge']);
	$projectiles = mysqli_real_escape_string($db->connection, $_POST['projectiles']);
	$nonprojectiles = mysqli_real_escape_string($db->connection, $_POST['nonprojectiles']);
	
	$medical = mysqli_real_escape_string($db->connection, $_POST['medical']);
	$diplomacy = mysqli_real_escape_string($db->connection, $_POST['diplomacy']);
	$crafting = mysqli_real_escape_string($db->connection, $_POST['crafting']);
	$management = mysqli_real_escape_string($db->connection, $_POST['management']);
	$perception = mysqli_real_escape_string($db->connection, $_POST['perception']);
	$stealth = mysqli_real_escape_string($db->connection, $_POST['stealth']);
	
	$metallurgy = mysqli_real_escape_string($db->connection, $_POST['metallurgy']);
	$electronics = mysqli_real_escape_string($db->connection, $_POST['electronics']);
	$engines = mysqli_real_escape_string($db->connection, $_POST['engines']);
	$weapons = mysqli_real_escape_string($db->connection, $_POST['weapons']);
	$repair = mysqli_real_escape_string($db->connection, $_POST['repair']);
	$computers = mysqli_real_escape_string($db->connection, $_POST['computers']);
	
	$fighter_p = mysqli_real_escape_string($db->connection, $_POST['fighter_p']);
	$fighter_c = mysqli_real_escape_string($db->connection, $_POST['fighter_c']);
	$capital_p = mysqli_real_escape_string($db->connection, $_POST['capital_p']);
	$capital_c = mysqli_real_escape_string($db->connection, $_POST['capital_c']);
	$space_command = mysqli_real_escape_string($db->connection, $_POST['space_command']);
	
	$vehicle_p = mysqli_real_escape_string($db->connection, $_POST['vehicle_p']);
	$vehicle_c = mysqli_real_escape_string($db->connection, $_POST['vehicle_c']);
	$infantry_command = mysqli_real_escape_string($db->connection, $_POST['infantry_command']);
	$vehicle_command = mysqli_real_escape_string($db->connection, $_POST['vehicle_command']);
	$heavy = mysqli_real_escape_string($db->connection, $_POST['heavy']);
	
	$query_skills = mysqli_query($db->connection, "SELECT * FROM characters_skills WHERE character_uid = '1:$uid'");
	if(empty(mysqli_num_rows($query_skills))){
		mysqli_query($db->connection, "INSERT INTO characters_skills VALUES ('1:$uid', '$strength', '$dexterity', '$speed', '$dodge', '$projectiles', '$nonprojectiles', '$medical', '$diplomacy', '$crafting', '$management', '$perception', '$stealth', '$metallurgy', '$electronics', '$engines', '$weapons', '$repair', '$computers', '$fighter_p', '$fighter_c', '$capital_p', '$capital_c', '$space_command', '$vehicle_p', '$vehicle_c', '$infantry_command', '$vehicle_command', '$heavy')");
	}else{
		mysqli_query($db->connection, "UPDATE characters_skills SET strength = '$strength', dexterity = '$dexterity', speed = '$speed', dodge = '$dodge', projectiles = '$projectiles', nonprojectiles = '$nonprojectiles', medical = '$medical', diplomacy = '$diplomacy', crafting = '$crafting', management = '$management', perception = '$perception', stealth = '$stealth', metallurgy = '$metallurgy', electronics = '$electronics', engines = '$engines', weapons = '$weapons', repair = '$repair', computers = '$computers', fighter_p = '$fighter_p', fighter_c = '$fighter_c', capital_p = '$capital_p', capital_c = '$capital_c', space_command = '$space_command', vehicle_p = '$vehicle_p', vehicle_c = '$vehicle_c', infantry_command = '$infantry_command', vehicle_command = '$vehicle_command', heavy = '$heavy' WHERE character_uid = '1:$uid'");
	}
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: Skill have been updated for <a target=\"blank\" href=\"../profile.php?uid=$uid\">$handle</a>.', '".swc_time(time(),TRUE)["timestamp"]."')");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Skills have been updated for <a target=\"blank\" href=\"../profile.php?uid=$uid\">$handle</a>.</div>";
}

if(isset($_POST['submitAssociation'])){
	$associate = mysqli_real_escape_string($db->connection, $_POST['inputHandle']);
	$associate_uid = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid FROM characters WHERE handle = '$associate'"))['uid'];
	$type = mysqli_real_escape_string($db->connection, $_POST['inputType']);
	$check = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(handle) AS count FROM characters_associations WHERE character_uid = '1:$uid' AND handle = '$associate'"))['count'];
	if($check == 1){
		mysqli_query($db->connection, "UPDATE characters_associations SET association = '$type', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '1:$uid' AND handle = '$associate'");
		if($type == "Adversary" || $type == "Family" || $type == "Friend" || $type == "Partner" || $type == "Peer"){
			mysqli_query($db->connection, "UPDATE characters_associations SET association = '$type', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '$associate_uid' AND handle = '$handle'");
		}elseif($type == "Employee"){
			mysqli_query($db->connection, "UPDATE characters_associations SET association = 'Employer', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '$associate_uid' AND handle = '$handle'");
		}elseif($type == "Employer"){
			mysqli_query($db->connection, "UPDATE characters_associations SET association = 'Employee', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '$associate_uid' AND handle = '$handle'");
		}elseif($type == "Subordinate"){
			mysqli_query($db->connection, "UPDATE characters_associations SET association = 'Supervisor', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '$associate_uid' AND handle = '$handle'");
		}elseif($type == "Supervisor"){
			mysqli_query($db->connection, "UPDATE characters_associations SET association = 'Subordinate', user_id = '".$_SESSION['user_id']."' WHERE character_uid = '$associate_uid' AND handle = '$handle'");
		}
	}else{
		mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '1:$uid', '$associate', '$type')");
		if($type == "Adversary" || $type == "Family" || $type == "Friend" || $type == "Partner" || $type == "Peer"){
			mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '$associate_uid', '$handle', '$type')");
		}elseif($type == "Employee"){
			mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '$associate_uid', '$handle', 'Employer')");
		}elseif($type == "Employer"){
			mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '$associate_uid', '$handle', 'Employee')");
		}elseif($type == "Subordinate"){
			mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '$associate_uid', '$handle', 'Supervisor')");
		}elseif($type == "Supervisor"){
			mysqli_query($db->connection, "INSERT INTO characters_associations (user_id, character_uid, handle, association) VALUES ('".$_SESSION['user_id']."', '$associate_uid', '$handle', 'Subordinate')");
		}
	}
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">An association for $handle has been updated</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: An association for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=4\">$handle</a> has been updated', '".swc_time(time(),TRUE)["timestamp"]."')");
}

if(isset($_POST['submitFaction'])){
	$newFaction = mysqli_real_escape_string($db->connection, $_POST['factionUpdate']);
	$check = mysqli_query($db->connection, "SELECT * FROM characters_faction WHERE character_uid = '1:$uid'");
	if(mysqli_num_rows($check) == 0){
		mysqli_query($db->connection, "INSERT INTO characters_faction (character_uid, faction) VALUES ('1:$uid', '$newFaction')");
	}else{
		mysqli_query($db->connection, "UPDATE characters_faction SET faction = '$newFaction' WHERE character_uid = '1:$uid'");
	}
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The current faction for $handle has been updated</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: Current faction for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=4\">$handle</a> has been updated', '".swc_time(time(),TRUE)["timestamp"]."')");
}

if(isset($_POST['submitAffiliation'])){
	$affiliation = mysqli_real_escape_string($db->connection, $_POST['inputFaction']);
	$date = mysqli_real_escape_string($db->connection, $_POST['inputDate']);
	$note = mysqli_real_escape_string($db->connection, $_POST['inputNote']);
	$check = mysqli_query($db->connection, "SELECT * FROM characters_faction WHERE character_uid = '1:$uid'");
	mysqli_query($db->connection, "INSERT INTO characters_affiliations (character_uid, faction, date, note) VALUES ('1:$uid', '$affiliation', '$date', '$note')");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">A new affiliation for $handle has been added</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: A new affiliation for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=4\">$handle</a> has been added', '".swc_time(time(),TRUE)["timestamp"]."')");
}

if(isset($_GET['deleteAssoc']) && (!empty($_SESSION['user_privs']['admin']) || !empty($_SESSION['user_privs']['sentientprofiles_delete']))){
	$associate = mysqli_real_escape_string($db->connection, $_GET['deleteAssoc']);
	$associate_uid = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid FROM characters WHERE handle = '$associate'"))['uid'];
	mysqli_query($db->connection, "DELETE FROM characters_associations WHERE character_uid = '1:$uid' AND handle = '$associate'");
	mysqli_query($db->connection, "DELETE FROM characters_associations WHERE character_uid = '$associate_uid' AND handle = '$handle'");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">An association for $handle has been deleted</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: An association for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=4\">$handle</a> has been deleted', '".swc_time(time(),TRUE)["timestamp"]."')");
}

if(isset($_GET['deleteAffil']) && (!empty($_SESSION['user_privs']['admin']) || !empty($_SESSION['user_privs']['sentientprofiles_delete']))){
	$id = mysqli_real_escape_string($db->connection, $_GET['deleteAffil']);
	mysqli_query($db->connection, "DELETE FROM characters_affiliations WHERE character_uid = '1:$uid' AND id = '$id'");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">An affiliation for $handle has been deleted</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: An affiliation for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=4\">$handle</a> has been deleted', '".swc_time(time(),TRUE)["timestamp"]."')");
}

if(isset($_POST['submitNote'])){
	$details = mysqli_real_escape_string($db->connection, $_POST['inputDetails']);
	if($details != ""){
		mysqli_query($db->connection, "INSERT INTO characters_notes (character_uid, timestamp, author, note_details) VALUES ('1:$uid', '".swc_time(time(),TRUE)["timestamp"]."', '".$_SESSION['user_id']."', '$details')");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">A note has been added.</div>";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: A new note for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=7\">$handle</a> has been added', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		$session->alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">You cannot submit a blank note!</div>";
	}
}

if(isset($_POST['submitVerify'])){
	$comment = mysqli_real_escape_string($db->connection, $_POST['inputComment']);
	if($comment != ""){
		mysqli_query($db->connection, "UPDATE characters_notes SET verified = '1', verifier = '".$_SESSION['user_id']."', verify_note = '$comment'");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The note has been verified.</div>";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: A note for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=7\">$handle</a> has been verified', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		$session->alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">You cannot submit a blank comment!</div>";
	}
}

if(isset($_GET['deleteNote']) && (!empty($_SESSION['user_privs']['admin']) || !empty($_SESSION['user_privs']['sentientprofiles_delete']))){
	$id = mysqli_real_escape_string($db->connection, $_GET['deleteNote']);
	mysqli_query($db->connection, "DELETE FROM characters_notes WHERE note_id = '$id'");
	$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">A note for $handle has been deleted</div>";
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'UPDATE: A note for <a target=\"blank\" href=\"../profile.php?uid=$uid&view=7\">$handle</a> has been deleted', '".swc_time(time(),TRUE)["timestamp"]."')");
}

?>