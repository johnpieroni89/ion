<?php 

class SwcApiProcessor {
    static function scan_cpm_orders() {
        global $db;
        error_reporting(0);
        define('USER_AGENT', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36'); //Set a user agent. This basically tells the server that we are using Chrome ;)
        define('COOKIE_FILE', 'cookie.txt'); //Where our cookie information will be stored (needed for authentication).
        define('LOGIN_FORM_URL', 'http://market.centrepointstation.com/index.php'); //URL of the login form.
        define('LOGIN_ACTION_URL', 'http://market.centrepointstation.com/login.php'); //Login action URL. Sometimes, this is the same URL as the login form.
        
        $postValues = array(
            'handle' => CPM_USERNAME,
            'password' => CPM_PASSWORD
        );
        
        $curl = curl_init(); //Initiate cURL.
        
        curl_setopt($curl, CURLOPT_URL, LOGIN_ACTION_URL); //Set the URL that we want to send our POST request to. In this case, it's the action URL of the login form.
        curl_setopt($curl, CURLOPT_POST, true); //Tell cURL that we want to carry out a POST request.
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues)); //Set our post fields / date (from the array above).
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //We don't want any HTTPS errors.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //We don't want any HTTPS errors.
        curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE); //Where our cookie details are saved. This is typically required for authentication, as the session ID is usually saved in the cookie file.
        curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT); //Sets the user agent. Some websites will attempt to block bot user agents. Hence the reason I gave it a Chrome user agent.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //Tells cURL to return the output once the request has been executed.
        curl_setopt($curl, CURLOPT_REFERER, LOGIN_FORM_URL); //Allows us to set the referer header. In this particular case, we are fooling the server into thinking that we were referred by the login form.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); //Do we want to follow any redirects?
        curl_exec($curl); //Execute the login request.
        if(curl_errno($curl)){ throw new Exception(curl_error($curl)); } //Check for errors!
        
        $current_order = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'last_update_cpm'"))["value"];
        $current_order = $current_order - 300;
        $count_process = 0;
        $count_valid = 0;
        $count_blanks = 0;
        while($count_blanks <= 50 && $count_process < 5000){
            if(mysqli_num_rows(mysqli_query($db->connection,"SELECT order_id, source FROM transactions WHERE order_id = '$current_order' AND source = 'CPM'")) == 0){
                
                
                curl_setopt($curl, CURLOPT_URL, CPM_ENDPOINT.$current_order); //We should be logged in by now. Let's attempt to access a password protected page
                curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE); //Use the same cookie file.
                curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT); //Use the same user agent, just in case it is used by the server for session validation.
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //We don't want any HTTPS / SSL errors.
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //We don't want any HTTPS / SSL errors.
                $data = curl_exec($curl); //Execute the GET request and store the result.
                
                preg_match_all("#\(\#([0-9]{1,10})\)#", $data, $matches_order);
                $order_number = $matches_order[1][0];
                if($order_number != ""){
                    preg_match_all("#From:<\/td><td><a href=\"\/profile\.php\?user=c[0-9]{1,8}\" onclick=\"javascript:userStats\('[cf][0-9]{1,8}', '.*'\);\">(.*)<\/a>#", $data, $matches_buyer);
                    preg_match_all("#Sold At:<\/b> <\/td><td>Y&nbsp;([0-9]{1,2})&nbsp;D&nbsp;([0-9]{1,3}),&nbsp#", $data, $matches_date);
                    preg_match_all("#Seller:<\/b> <\/td><td><a href=\"\/profile\.php\?user=[cf][0-9]{1,8}\">([A-Za-z0-9`'\/\- \(\)]*)<\/a>#", $data, $matches_seller);
                    preg_match_all("#Sale Price:<\/b> <\/td><td>(.*) <img#", $data, $matches_price);
                    preg_match_all("#Name: <\/b><\/td><td><a href=\"\/browse\.php\?type=[0-9]{1,3}&amp;class=[0-9]{1,3}&amp;id=[0-9]{1,5}\">(.*)<\/a>#", $data, $matches_items);
                    preg_match_all("#Quantity: <\/b><\/td><td>([0-9,]{1,10})<\/td><\/tr>#", $data, $matches_quantity);
                    $count = 0;
                    $assets = array();
                    
                    foreach($matches_items[1] as $layer){
                        array_push($assets, $matches_quantity[1][$count]."x ".$layer);
                        $count++;
                    }
                    
                    $asset_list = "";
                    foreach($assets as $asset){
                        $asset_list = $asset_list.$asset."; ";
                    }
                    
                    $timestamp = Utility::swc_time(Utility::real_time($matches_date[1][0], $matches_date[2][0]), FALSE)["timestamp"];
                    $seller = $matches_seller[1][0];
                    if($seller != $matches_buyer[1][0]){ $buyer = $matches_buyer[1][0]; }else{ $buyer = $matches_buyer[1][1]; }
                    $price = $matches_price[1][0];
                    
                    if($buyer != ""){
                        mysqli_query($db->connection,"INSERT INTO transactions (timestamp, order_id, buyer, seller, price, assets, source) VALUES ('$timestamp', '$order_number', '$buyer', '$seller', '$price', '$asset_list', 'CPM')");
                        $count_valid++;
                    }
                    mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_cpm'");
                    $current_order++;
                }else{
                    $count_blanks++;
                    $current_order++;
                }
            }else{
                $current_order++;
            }
            $count_process++;
        }
        
        if($count_blanks >= 50){
            $current_order = $current_order - $count_blanks;
            mysqli_query($db->connection,"UPDATE site_settings SET value = '$current_order' WHERE field = 'last_update_cpm'");
        }
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.CPM_ENDPOINT.'{order_number}</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$count_valid.' CPM Orders were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_tfm_orders() {
        global $db;
        
        $data = file_get_contents(TFM_ENDPOINT);
        $count_orders = 0;
        $count_new = 0;
        $total_orders = preg_match_all("#Order No\. ([0-9]{1,10}):#", $data, $matches_order);
        
        while($count_orders < 100){
            preg_match_all('/<b>Placed on:<\/b> Y([0-9]{1,3}) D([0-9]{1,3}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/', $data, $matches_order_time);
            preg_match_all('/<b>Purchased by: <\/b> (.*)/', $data, $matches_order_buyer);
            preg_match_all('/<b>Purchased from:<\/b> (.*)/', $data, $matches_order_seller);
            preg_match_all('/<b>Order total:<\/b> (.*) <img/', $data, $matches_order_price);
            preg_match_all('/<div class=\'col\-6 col\-md\-5\'>(.*)<\/div>/', $data, $matches_order_wares);
            
            $order_number = $matches_order[1][$count_orders];
            
            if(!mysqli_num_rows(mysqli_query($db->connection, "SELECT order_id FROM transactions WHERE order_id = '$order_number'"))) {
                $year = $matches_order_time[1][$count_orders];
                $day = $matches_order_time[2][$count_orders];
                $hour = $matches_order_time[3][$count_orders];
                $minute = $matches_order_time[4][$count_orders];
                $second = $matches_order_time[5][$count_orders];
                $timestamp = Utility::swc_timestamp($year, $day, $hour, $minute, $second);
                
                $buyer = $matches_order_buyer[1][$count_orders];
                $seller = str_replace('<br>', '', trim(explode('(', $matches_order_seller[1][$count_orders])[0]));
                $price = $matches_order_price[1][$count_orders];
                $asset_list = trim(str_replace('<br>', '; ', $matches_order_wares[1][$count_orders]));
                
                mysqli_query($db->connection,"INSERT INTO transactions (timestamp, order_id, buyer, seller, price, assets, source) VALUES ('$timestamp', '$order_number', '$buyer', '$seller', '$price', '$asset_list', 'TF DoT')");
                
                $count_new++;
                $count_orders++;
            }
            $count_orders++;
        }
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.TFM_ENDPOINT.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$count_new.' of '.$count_orders.' TF DoT Orders were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_factions() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_FACTIONS;
        mysqli_query($db->connection,"TRUNCATE TABLE factions");
        
        $xml_data = SwcApiProcessor::parse_data($endpoint, 1, 50);
        $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
        
        while((int)$xml_data->factions['start'] <= (int)$xml_data->factions['total']){
            
            foreach($xml_data->factions->faction as $faction){
                $uid = mysqli_real_escape_string($db->connection,$faction['uid']);
                $name = mysqli_real_escape_string($db->connection,$faction);
                mysqli_query($db->connection,"INSERT INTO factions (uid, name) VALUES ('$uid','$name') ON DUPLICATE KEY UPDATE name='$name'");
            }
            
            $iter = $xml_data->factions['start'] + 50;
            $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_factions'");
        $update_log = mysqli_real_escape_string($db->connection, "FACTIONS UPDATE: '".SWC_API_ENDPOINT_FACTIONS."' has been scanned; ".$xml_data->factions['total']." factions exist.");
        if(isset($_SESSION['user_id'])){
            mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '3', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
        }
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$xml_data->factions['total'].' factions were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_flashnews() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_FLASHNEWS;
        
        $xml_data = SwcApiProcessor::parse_data($endpoint);
        $total_count = 0;
        $total_new = 0;
        
        foreach($xml_data->channel->item as $item) {
            $total_count += 1;
            $check = mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM flashnews WHERE guid = '".$item->guid."'"));
            if($check == 0){
                $total_new += 1;
                $title = mysqli_real_escape_string($db->connection, $item->title);
                $description = mysqli_real_escape_string($db->connection, $item->description);
                mysqli_query($db->connection,"INSERT INTO flashnews (guid, title, description, timestamp) VALUES ('".$item->guid."', '".$title."', '".$description."', '".strtotime($item->pubDate)."')");
                if(strpos($item->title, "renamed to")){
                    preg_match("#(.*) renamed to (.*)#", $item->title, $match);
                    $former = mysqli_real_escape_string($db->connection, $match[1]);
                    $total = mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM data_signalsanalysis WHERE owner = '$former'"));
                    $current = mysqli_real_escape_string($db->connection, $match[2]);
                    mysqli_query($db->connection, "UPDATE data_signalsanalysis SET owner = '$current' WHERE owner = '$former'");
                    mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('NULL', '7', \"$total records have been changed from being owned by $former to $current\", '".swc_time(time(),TRUE)["timestamp"]."')");
                }
            }
        }
        
        echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$total_new.' of '.$total_count.' GNS articles were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
    }
    
    static function scan_entity_types() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_ENTITY_TYPES;
        mysqli_query($db->connection,"TRUNCATE TABLE entities");
        
        $types = [
            'facilities' => [
                'type' => 'facilities',
                'object' => 'facility'
            ],
            'ships' => [
                'type' => 'ships',
                'object' => 'ship'
            ],
            'stations' => [
                'type' => 'stations',
                'object' => 'station'
            ],
            'vehicles' => [
                'type' => 'vehicles',
                'object' => 'vehicle'
            ]
        ];
        
        $total_types = 0;
        
        foreach($types as $type) {
            $xml_data = SwcApiProcessor::parse_data($endpoint, 1, 50, $type['type']);
            $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
            $arrayStringPlural = $type['object'].'types';
            $arrayStringSingular = $type['object'].'type';
            
            $total_types += (int)$xml_data->$arrayStringPlural['total'];
            
            while((int)$xml_data->$arrayStringPlural['start'] <= (int)$xml_data->$arrayStringPlural['total']){
                
                foreach($xml_data->$arrayStringPlural->$arrayStringSingular as $entity){
                    
                    $uid = mysqli_real_escape_string($db->connection,$entity['uid']);
                    $name = mysqli_real_escape_string($db->connection,$entity);
                    $link = $entity['href'];
                    
                    $entity_data = SwcApiProcessor::fetch_entity($link);
                    
                    $class = mysqli_real_escape_string($db->connection,$entity_data->class['uid']);
                    $price = mysqli_real_escape_string($db->connection,$entity_data->price->credits);
                    if($type['type'] == "facilities"){
                        $image_small = mysqli_real_escape_string($db->connection,$entity_data->imagesets->small);
                    }else{
                        $image_small = mysqli_real_escape_string($db->connection,$entity_data->images->small);
                    }
                    
                    mysqli_query($db->connection,"INSERT INTO entities (uid, name, class, price, img_small) VALUES ('$uid','$name','$class','$price','$image_small')");
                }
                
                $xml_data = SwcApiProcessor::parse_data($endpoint, $xml_data->$arrayStringPlural['start'] + 50, 50, $type['type']);   
            }
            
            $process = $endpoint.$type['type'];
            $update_log = mysqli_real_escape_string($db->connection, "ENTITIES UPDATE: '".$process."' has been scanned");

            if(isset($_SESSION['user_id'])){
                mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
            }else{
                mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '1', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
            }
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_entities'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$total_types.' entity types were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_entity_classes() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_ENTITY_TYPES_CLASSES;
        
        $types = [
            'facilities' => [
                'type' => 'facilities',
                'object' => 'facility'
            ],
            'ships' => [
                'type' => 'ships',
                'object' => 'ship'
            ],
            'vehicles' => [
                'type' => 'vehicles',
                'object' => 'vehicle'
            ]
        ];
        
        mysqli_query($db->connection,"TRUNCATE TABLE entities_classes");
        
        $total_classes = 0;
        
        foreach($types as $type) {
            $xml_data = SwcApiProcessor::parse_data($endpoint, 1, 50, $type['type']);
            $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
            $arrayStringPlural = $type['type'];
            
            $total_classes += $xml_data->classes->$arrayStringPlural['count'];
            
            foreach($xml_data->classes->$arrayStringPlural->class as $class){
                
                $uid = mysqli_real_escape_string($db->connection,$class['uid']);
                $name = mysqli_real_escape_string($db->connection,$class);
                
                mysqli_query($db->connection,"INSERT INTO entities_classes (uid, class) VALUES ('$uid','$name')");
            }
            
            $update_log = mysqli_real_escape_string($db->connection, "ENTITIES CLASSES UPDATE: '".$endpoint."' has been scanned");
            if(isset($_SESSION['user_id'])){
                mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
            }else{
                mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '2', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
            }
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_classes'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$total_classes.' entity classes were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_entity_races() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_ENTITY_RACES;
        
        mysqli_query($db->connection,"TRUNCATE TABLE entities_races");
        
        $xml_data = SwcApiProcessor::parse_data($endpoint, 1, 50);
        $total_races = $xml_data->races['total'];
        $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
        
        while((int)$xml_data->races['start'] <= (int)$xml_data->races['total']) {
            foreach($xml_data->races->race as $race){
                $uid = mysqli_real_escape_string($db->connection,$race['uid']);
                $name = mysqli_real_escape_string($db->connection,$race);
                
                mysqli_query($db->connection,"INSERT INTO entities_races (uid, race) VALUES ('$uid','$name')");
            }
            
            $xml_data = SwcApiProcessor::parse_data($endpoint, $xml_data->races['start'] + 50, 50);
        }
        
        $update_log = mysqli_real_escape_string($db->connection, "ENTITIES RACES UPDATE: '".$endpoint."' has been scanned");
        if(isset($_SESSION['user_id'])){
            mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
        }else{
            mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '2', '$update_log', '".Utility::swc_time(time(),TRUE)["timestamp"]."')");
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_races'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$total_races.' races were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_galaxy_planets() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_GALAXY_PLANETS;
        
        $iter = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_planets_count'"))["value"];
        $planets_processed = 0;
        $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        $pages_to_process = SWC_API_ENDPOINT_GALAXY_PLANETS_LIMIT_PER_SCAN / 50;
        $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
        
        while($pages_to_process != 0) {
            
            foreach($xml_data->planets->planet as $planet){
                
                $uid = mysqli_real_escape_string($db->connection,$planet['uid']);
                $name = mysqli_real_escape_string($db->connection,$planet['name']);
                $link = $planet['href'];
                $controlled_by = mysqli_real_escape_string($db->connection,$planet->controlledby);
                $population = mysqli_real_escape_string($db->connection,$planet->population);
                $cities = mysqli_real_escape_string($db->connection,$planet->cities);
                
                $planet_data = SwcApiProcessor::fetch_entity($link);
                $planet_data = $planet_data->planet;
                
                $size = mysqli_real_escape_string($db->connection,$planet_data->size);
                $governor = mysqli_real_escape_string($db->connection,$planet_data->governor);
                $magistrate = mysqli_real_escape_string($db->connection,$planet_data->magistrate);
                $sector = mysqli_real_escape_string($db->connection,$planet_data->location->sector['uid']);
                $system = mysqli_real_escape_string($db->connection,$planet_data->location->system['uid']);
                $galx = mysqli_real_escape_string($db->connection,$planet_data->location->coordinates->galaxy['x']);
                $galy = mysqli_real_escape_string($db->connection,$planet_data->location->coordinates->galaxy['y']);
                $sysx = mysqli_real_escape_string($db->connection,$planet_data->location->coordinates->system['x']);
                $sysy = mysqli_real_escape_string($db->connection,$planet_data->location->coordinates->system['y']);
                $img_small = mysqli_real_escape_string($db->connection,$planet_data->images->small);
                $type = mysqli_real_escape_string($db->connection, $planet_data->type);
                $civilization = mysqli_real_escape_string($db->connection,$planet_data->civilisationlevel);
                $tax = mysqli_real_escape_string($db->connection,$planet_data->taxlevel);
                $terrain = mysqli_real_escape_string($db->connection,$planet_data->terrainmap);
                
                $planets_processed++;
                mysqli_query($db->connection, "INSERT INTO galaxy_planets (uid, name, controlled_by, population, governor, magistrate, sector, `system`, galx, galy, sysx, sysy, img_small, size, type, terrain, cities, civilization, tax) VALUES ('$uid','$name','$controlled_by','$population', '$governor', '$magistrate', '$sector', '$system', '$galx', '$galy', '$sysx', '$sysy', '$img_small', '$size', '$type', '$terrain', '$cities', '$civilization', '$tax') ON DUPLICATE KEY UPDATE name='$name', controlled_by='$controlled_by', population='$population', governor='$governor', magistrate='$magistrate', sector='$sector', `system`='$system', galx='$galx', galy='$galy', sysx='$sysx', sysy='$sysy', img_small='$img_small', size='$size', type='$type', terrain='$terrain', cities='$cities', civilization='$civilization', tax='$tax'");
            }
            
            $pages_to_process -= 1;

            if($iter > $xml_data->planets['total']){
                mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_planets_count'");
            }else{
                mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_planets_count'");
            }
            
            $iter += 50;
            $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        }
        
        mysqli_query($db->connection, "UPDATE site_settings SET value=".$iter." WHERE field='update_planets_count'");
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_planets'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$planets_processed.' planets were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_galaxy_sectors() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_GALAXY_SECTORS;
        
        $iter = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_sectors_count'"))["value"];
        $sectors_processed = 0;
        $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
        
        while($iter - 50 < $xml_data->sectors['total']) {
            
            foreach($xml_data->sectors->sector as $sector){
                
                $uid = mysqli_real_escape_string($db->connection,$sector['uid']);
                $name = mysqli_real_escape_string($db->connection,$sector['name']);
                $controlled_by = mysqli_real_escape_string($db->connection,$sector->controlledby);
                $population = mysqli_real_escape_string($db->connection,$sector->population);
                
                $sectors_processed++;
                mysqli_query($db->connection, "INSERT INTO galaxy_planets (uid, name, controlled_by, population) VALUES ('$uid','$name','$controlled_by','$population') ON DUPLICATE KEY UPDATE name='$name', controlled_by='$controlled_by', population='$population'");
            }
            
            if($iter > $xml_data->sectors['total']){
                mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_sectors_count'");
            }else{
                mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_sectors_count'");
            }
            
            $iter += 50;
            $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_sectors'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.($sectors_processed - 1).' sectors were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_galaxy_systems() {
        error_reporting(0);
        global $db;
        $endpoint = SWC_API_ENDPOINT_GALAXY_SYSTEMS;
        
        $iter = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_systems_count'"))["value"];
        $systems_processed = 0;
        $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        $timestamp = Utility::swc_time($xml_data['timestamp'], true)['timestamp'];
        
        while($iter - 50 < $xml_data->systems['total']) {
            foreach($xml_data->systems->system as $system){
                
                $uid = mysqli_real_escape_string($db->connection,$system['uid']);
                $name = mysqli_real_escape_string($db->connection,$system['name']);
                $controlled_by = mysqli_real_escape_string($db->connection,$system->controlledby);
                $population = mysqli_real_escape_string($db->connection,$system->population);
                $sector = mysqli_real_escape_string($db->connection,$system->location->sector['uid']);
                $x = mysqli_real_escape_string($db->connection,$system->location->coordinates->galaxy['x']);
                $y = mysqli_real_escape_string($db->connection,$system->location->coordinates->galaxy['y']);
                
                $systems_processed++;
                mysqli_query($db->connection,"INSERT INTO galaxy_systems (uid, name, controlled_by, population, sector, galx, galy) VALUES ('$uid','$name','$controlled_by','$population', '$sector', '$x', '$y') ON DUPLICATE KEY UPDATE name='$name', controlled_by='$controlled_by', population='$population', sector='$sector', galx='$x', galy='$y'");
            }
            
            if($iter > $xml_data->systems['total']){
                mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_systems_count'");
            }else{
                mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_systems_count'");
            }
            
            $iter += 50;
            $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        }
        
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_systems'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.($systems_processed - 1).' systems were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function scan_galaxy_stations() {
        global $db;
        $endpoint = SWC_API_ENDPOINT_GALAXY_STATIONS;
        
        $iter = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_stations_count'"))["value"];
        $stations_processed = 0;
        $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        $pages_to_process = SWC_API_ENDPOINT_GALAXY_STATIONS_LIMIT_PER_SCAN / 50;
        $timestamp = Utility::swc_time($xml_data['timestamp'])['timestamp'];
        
        while($pages_to_process != 0) {
            
            foreach($xml_data->stations->station as $station){
                
                $uid = mysqli_real_escape_string($db->connection,$station['uid']);
                $name = mysqli_real_escape_string($db->connection,$station['name']);
                $link = str_replace('%3A', ':', $station['href']);
                
                $station_data = SwcApiProcessor::fetch_entity($link);
                $station_data = $station_data->station;
                
                $type = mysqli_real_escape_string($db->connection,str_replace("&amp;", "&", $station_data->type));
                $type = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM entities WHERE name='$type'"))["uid"];
                $owner = mysqli_real_escape_string($db->connection,$station_data->owner);
                $sector = mysqli_real_escape_string($db->connection,$station_data->location->sector['uid']);
                $system = mysqli_real_escape_string($db->connection,$station_data->location->container['uid']);
                $galx = mysqli_real_escape_string($db->connection,$station_data->location->coordinates->galaxy['x']);
                $galy = mysqli_real_escape_string($db->connection,$station_data->location->coordinates->galaxy['y']);
                $sysx = mysqli_real_escape_string($db->connection,$station_data->location->coordinates->system['x']);
                $sysy = mysqli_real_escape_string($db->connection,$station_data->location->coordinates->system['y']);
                
                $query_planet = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM galaxy_planets WHERE galx = '".$galx."' AND galy = '".$galy."' AND sysx = '".$sysx."' AND sysy = '".$sysy."'"));
                if(!empty($query_planet['uid'])){
                    $planet_ins = "'".$query_planet['uid']."'";
                    $planet_upd = "planet = '".$query_planet['uid']."', ";
                }else{
                    $planet_ins = "NULL";
                    $planet_upd = "";
                }
                
                $stations_processed++;
                mysqli_query($db->connection, "INSERT INTO data_signalsanalysis (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, timestamp) VALUES ('$uid','$name','$type','$owner', '$sector', '$galx', '$galy', '$system', '$sysx', '$sysy', ".$planet_ins.", '$timestamp') ON DUPLICATE KEY UPDATE name='$name', type='$type', owner='$owner', sector='$sector', galx='$galx', galy='$galy', system='$system', sysx='$sysx', sysy='$sysy', ".$planet_upd."timestamp='$timestamp'");
            }
            
            $pages_to_process -= 1;
            
            if($iter > $xml_data->planets['total']){
                mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_stations_count'");
            }else{
                mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_stations_count'");
            }
            
            $iter += 50;
            $xml_data = SwcApiProcessor::parse_data($endpoint, $iter, 50);
        }
        
        mysqli_query($db->connection, "UPDATE site_settings SET value=".$iter." WHERE field='update_stations_count'");
        mysqli_query($db->connection,"UPDATE site_settings SET value=".$timestamp." WHERE field='last_update_stations'");
        
        if(isset($_GET['report'])) {
            echo '
            <div style="width:100%;height:100%">
                <table border="1" style="margin:auto;">
                    <thead><tr><td style="font-weight:bold; text-decoration:underline; text-align:center;" colspan="2">SWC API Process</td></tr></thead>
                    <tbody>
                        <tr><td>Endpoint:</td><td>'.$endpoint.'</td></tr>
                        <tr><td>Status:</td><td>Complete</td></tr>
                        <tr><td>Details:</td><td>'.$stations_processed.' stations were processed!</td></tr>
                    </tbody>
                </table>
            </div>';
        }
    }
    
    static function parse_data($endpoint, $start_index = '', $item_count = '', $type = '') {
        $file = $endpoint.$type;;
        if($start_index && $item_count) {
            $file = $endpoint.$type."?start_index=".$start_index."&item_count=".$item_count;
        }
        
        $xml_file = file_get_contents($file);
        $xml = simplexml_load_string($xml_file);
        return $xml;
    }
    
    static function fetch_entity($link){
        $xml_file = file_get_contents($link);
        $xml = simplexml_load_string($xml_file);
        return $xml;
    }
    
}

?>