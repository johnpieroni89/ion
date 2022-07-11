<?php

// set values, rename file to "config.php", and add this file to git ignore

// MySQL Connection
define('MYSQL_DB_HOST', 'localhost');
define('MYSQL_DB_PORT', '3306');
define('MYSQL_DB_DATABASE', '');
define('MYSQL_DB_USERNAME', '');
define('MYSQL_DB_PASSWORD', '');

// SWC API
define('SWC_API_CLIENT_ID', '');
define('SWC_API_CLIENT_SECRET', '');
define('SWC_API_ENDPOINT_HANDLES', 'https://www.swcombine.com/ws/v2.0/character/handlecheck/');
define('SWC_API_ENDPOINT_FACTIONS', 'https://www.swcombine.com/ws/v2.0/factions/');
define('SWC_API_ENDPOINT_ENTITY_TYPES', 'https://www.swcombine.com/ws/v2.0/types/');
define('SWC_API_ENDPOINT_ENTITY_TYPES_CLASSES', 'https://www.swcombine.com/ws/v2.0/types/classes/');
define('SWC_API_ENDPOINT_ENTITY_RACES', 'https://www.swcombine.com/ws/v2.0/types/races/');
define('SWC_API_ENDPOINT_FLASHNEWS', 'http://www.swcombine.com/community/news/flashfeed.php');
define('SWC_API_ENDPOINT_GALAXY_PLANETS', 'https://www.swcombine.com/ws/v2.0/galaxy/planets/');
define('SWC_API_ENDPOINT_GALAXY_PLANETS_LIMIT_PER_SCAN', 250);
define('SWC_API_ENDPOINT_GALAXY_SECTORS', 'https://www.swcombine.com/ws/v2.0/galaxy/sectors/');
define('SWC_API_ENDPOINT_GALAXY_SYSTEMS', 'https://www.swcombine.com/ws/v2.0/galaxy/systems/');
define('SWC_API_ENDPOINT_GALAXY_STATIONS', 'https://www.swcombine.com/ws/v2.0/galaxy/stations/');
define('SWC_API_ENDPOINT_GALAXY_STATIONS_LIMIT_PER_SCAN', 200);

// User Accounts
define('PASSWORD_PEPPER', '');

// CPM Connection for Scraping Order Data - Requires an Account Login
define('CPM_ENDPOINT', 'http://market.centrepointstation.com/details.php?lid=');
define('CPM_USERNAME', '');
define('CPM_PASSWORD', '');

// TFM Connection for Scraping Order Data
define('TFM_ENDPOINT', 'https://market.swc-tf.com/view/recentlysold.php');

?>