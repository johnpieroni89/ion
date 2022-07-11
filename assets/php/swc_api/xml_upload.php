<?php
global $db;
global $session;

$len = count($_FILES['file']['tmp_name']);

for($i = 0; $i < $len; $i++) {
	$file = $_FILES['file']['tmp_name'][$i];

	if ($_FILES["file"]["error"][$i] > 0){
		$notice = "<div class='error'>Error: ".$_FILES["file"]["error"][$i]."</div>";
	}else{
		if(($_FILES['file']['type'][$i] == "text/xml") || ($_FILES['file']['type'][$i] == "application/xml")){
			$xml_file = simplexml_load_string(str_replace("&","&amp;",file_get_contents($file)));
			if(preg_match("/Focus scan of/",$xml_file->channel->title)){
				include("xml_upload_focus.php");
			}elseif($xml_file{'version'} == "2.0"){
				include("xml_upload_inventory.php");
			}elseif($xml_file->channel->generator == "FeedCreator 1.7.2" && $xml_file->channel->description == "scanner output"){
				include("xml_upload_range.php");
			}elseif($xml_file->channel->generator == "FeedCreator 1.7.2" && $xml_file->channel->description == "facility income"){
				include("xml_upload_income.php");
			}elseif($xml_file{"type"} == "export"){
				include("xml_upload_export.php");
			}else{
				$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Incorrect xml format: Data must come from SWC.</div>";
			}
			mysqli_query($db->connection, "DELETE FROM data_tracking WHERE target = ''");
		}elseif($_FILES['file']['type'][$i] == "text/html"){
			$html = file_get_contents($file);
			if(preg_match("/<form id=\"inventory-form\" name=\"inventory\"/", $html)){
				if(preg_match("/<td\s*valign=\"top\">Docked in:.*<em>.*<\/em>\s*\(.*\)<br\s*\/>/", $html) || preg_match("/<td\s*valign=\"top\">Travelling/", $html)){
					$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - html data must not contain any records of travelling or docked entities, due to parsing errors.</div>";
				}else{
					include("html_parser.php");
				}
			}else{
				$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Incorrect html format: Data must come from SWC.</div>";
			}
		}else{
			$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Incorrect filetype: Only xml files are allowed.</div>";
		}
	}
}

?>