<?php
include('../../../config.php');
include('../classes/Database.php');
include('../classes/Utility.php');
include('../classes/SwcApiProcessor.php');
include('../classes/Analytics.php');
$db = new Database();
$db->connect();

if(isset($_GET['script'])) {
    if($_GET['script'] == 'market_cpm') {
        SwcApiProcessor::scan_cpm_orders();
    } elseif($_GET['script'] == 'market_tfm') {
        SwcApiProcessor::scan_tfm_orders();
    } elseif($_GET['script'] == 'entity_classes') {
        SwcApiProcessor::scan_entity_classes();
    } elseif($_GET['script'] == 'entity_races') {
        SwcApiProcessor::scan_entity_races();
    } elseif($_GET['script'] == 'entity_types') {
        SwcApiProcessor::scan_entity_types();
    } elseif($_GET['script'] == 'factions') {
        SwcApiProcessor::scan_factions();
    } elseif($_GET['script'] == 'flashnews') {
        SwcApiProcessor::scan_flashnews();
    } elseif($_GET['script'] == 'planets') {
        SwcApiProcessor::scan_galaxy_planets();
    } elseif($_GET['script'] == 'sectors') {
        SwcApiProcessor::scan_galaxy_sectors();
    } elseif($_GET['script'] == 'systems') {
        SwcApiProcessor::scan_galaxy_systems();
    } elseif($_GET['script'] == 'stations') {
        SwcApiProcessor::scan_galaxy_stations();
    } elseif($_GET['script'] == 'galaxydata_planets') {
        Analytics::galaxy_planets();
    }
} else {
    echo '
    <html style="width:100%; height:100%;">
    <body style="width:100%; height:100%;">
    <div style="width:100%; height:100%;">
        <table border="1" style="margin:auto;">
            <thead>
                <tr>
                    <td colspan="2" style="text-align:center; font-weight:bold; text-decoration:underline;">SWC API for ION 3.0</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 25%;">Method:</td>
                    <td style="width: 75%;">Get</td>
                </tr>
                <tr>
                    <td style="width: 25%;">Query:</td>
                    <td style="width: 75%;">script</td>
                </tr>
                <tr>
                    <td style="width: 25%;">Values:</td>
                    <td style="width: 75%;">entity_classes | entity_races| entity_types | factions | flashnews | market_cpm | <br/> market_tfm | planets | sectors | systems | stations | galaxydata_planets</td>
                </tr>
                <tr>
                    <td style="width: 25%;">Example:</td>
                    <td style="width: 75%;">{root directory}/assets/php/swc_api/index.php?script=factions</td>
                </tr>
            </tbody>
        </table>
    </div><body></html>
    ';
}

?>