        <!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
		<?php
		$db = new database;
		$db->connect();
		$plugin_bootstrap3_js = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_bootstrap3_js'"));
		$plugin_chartjs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_chartjs'"));
		$plugin_jquery = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_jquery'"));
		$db->disconnect();
		unset($db);
		?>
		
        <script src="<?php echo $plugin_jquery['value']; ?>"></script>
        <script src="<?php echo $plugin_bootstrap3_js['value']; ?>"></script>
        <script src="<?php echo $plugin_chartjs['value']; ?>"></script>
        <script src="https://www.chartjs.org/samples/latest/utils.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/svg.js/3.0.16/svg.js" type="text/javascript"></script>
        <script src="<?php if(glob("assets/js/plugins.js")){ echo "assets/js/plugins.js";}else{echo "../assets/js/plugins.js";} ?>"></script>
        <script src="<?php if(glob("assets/js/app.js")){ echo "assets/js/app.js";}else{echo "../assets/js/app.js";} ?>"></script>
		
		<?php
			if(isset($_GET['uid']) && $_GET['view'] == 3){
				include("assets/php/sociograph.php");
			}
		?>