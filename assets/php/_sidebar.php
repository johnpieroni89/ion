<!-- Main Sidebar -->
<div id="sidebar">
	<!-- Sidebar Brand -->
	<div id="sidebar-brand" class="themed-background">
		<a href="<?php if(glob("index.php")){echo "index.php";}else{echo "../index.php";} ?>" class="sidebar-title">
			<!--<i class="fa fa-cube"></i>--><img style="margin-right:10px;" src="<?php if(glob("assets/img/icon48.ico")){ echo "assets/img/icon48.ico";}else{echo "../assets/img/icon48.ico";} ?>"> <span class="sidebar-nav-mini-hide"><?php echo $_SESSION['site_name']; ?></strong></span>
		</a>
	</div>

	<!-- Wrapper for scrolling functionality -->
	<div id="sidebar-scroll">
		<!-- Sidebar Content -->
		<div class="sidebar-content">
			<!-- Sidebar Navigation -->							
			<ul class="sidebar-nav">
				<li>
					<a href="<?php if(glob("index.php")){echo "index.php";}else{echo "../index.php";} ?>" <?php if(basename($_SERVER['PHP_SELF']) == "index.php"){echo "class=\"active\"";} ?>><i class="fas fa-tachometer-alt sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Dashboard</span></a>
				</li>
				<li>
					<center><a href="#"><?php $swc_date = swc_time(time(),TRUE); echo "Year ".$swc_date["year"]." Day ".$swc_date["day"]." ".$swc_date["hour"].":".$swc_date["minute"].":".$swc_date["second"];?></a></center>
				</li>
				<li class="sidebar-separator">
					<i class="fa fa-ellipsis-h"></i>
				</li>
				<?php
					if(isset($_SESSION['user_privs'])){
						if(glob("sentientprofiles.php")) {
							$link = "sentientprofiles.php";
						} else {
							$link = "../sentientprofiles.php";
						}
						
						if($_SESSION['user_privs']['sentientprofiles_general'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "sentientprofiles.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\"title=\"Sentient Database for querying all-source intelligence about individuals\" ".$active."><i class=\"fas fa-male sidebar-nav-icon\"></i>Sentient Profiles</a></li>";
						}
						
						if(glob("galaxydata.php")) {
							$link = "galaxydata.php";
						} else {
							$link = "../galaxydata.php";
						}

						if($_SESSION['user_privs']['galaxydata_search'] != 0 || $_SESSION['user_privs']['galaxydata_analytics'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "galaxydata.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Galaxy Database for querying planets/territories\" ".$active."><i class=\"fas fa-globe sidebar-nav-icon\"></i>Galaxy Data</a></li>";
						}
						
						if(glob("geolocation.php")) {
							$link = "geolocation.php";
						} else {
							$link = "../geolocation.php";
						}

						if($_SESSION['user_privs']['geolocation'] != 0 ||  $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "geolocation.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Geolocation Data for analyzing target location data\" ".$active."><i class=\"fas fa-map-marked-alt sidebar-nav-icon\"></i>Geolocation</a></li>";
						}
						
						if(glob("signalsanalysis.php")) {
							$link = "signalsanalysis.php";
						} else {
							$link = "../signalsanalysis.php";
						}

						if($_SESSION['user_privs']['signalsanalysis_search'] != 0 || $_SESSION['user_privs']['signalsanalysis_upload'] != 0 || $_SESSION['user_privs']['signalsanalysis_analytics'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "signalsanalysis.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Entity database for querying signals intelligence data\" ".$active."><i class=\"fas fa-broadcast-tower sidebar-nav-icon\"></i>Signals Analysis</a></li>";
						}

						if(glob("factioncatalog.php")) {
							$link = "factioncatalog.php";
						} else {
							$link = "../factioncatalog.php";
						}

						if($_SESSION['user_privs']['factioncatalog_general'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "factioncatalog.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Faction database for querying all-source intelligence about organizations\" ".$active."><i class=\"fas fa-sitemap sidebar-nav-icon\"></i>Faction Catalog</a></li>";
						}
						
						if(glob("flashnews.php")) {
							$link = "flashnews.php";
						} else {
							$link = "../flashnews.php";
						}
						
						if($_SESSION['user_privs']['flashnews'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "flashnews.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Galactic flashnews for historical research and trend analysis\" ".$active."><i class=\"fas fa-rss-square sidebar-nav-icon\"></i>Flashnews</a></li>";
						}
						
						if(glob("reporting.php")) {
							$link = "reporting.php";
						} else {
							$link = "../reporting.php";
						}
						
						if($_SESSION['user_privs']['reporting_publish'] != 0 || $_SESSION['user_privs']['reporting_view'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "reporting.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Reporting module for intelligence and analysis\" ".$active."><i class=\"fas fa-book sidebar-nav-icon\"></i>Reporting</a></li>";
						}
						
						if(glob("transactions.php")) {
							$link = "transactions.php";
						} else {
							$link = "../transactions.php";
						}
						
						if($_SESSION['user_privs']['transactions'] != 0 || $_SESSION['user_privs']['admin'] != 0) {
							if(basename($_SERVER['PHP_SELF']) == "transactions.php"){$active = "class=\"active\"";}else{$active = "";}
							echo "<li><a href=\"".$link."\" title=\"Market transactions for financial and social analysis\" ".$active."><i class=\"fas fa-search-dollar sidebar-nav-icon\"></i>Transactions</a></li>";
						}
						
						if(glob("utilities.php")) {
							$link = "utilities.php";
						} else {
							$link = "../utilities.php";
						}
						
						if(basename($_SERVER['PHP_SELF']) == "utilities.php"){$active = "class=\"active\"";}else{$active = "";}
						echo "<li><a href=\"".$link."\" title=\"Utilities and tools\" ".$active."><i class=\"fas fa-tools sidebar-nav-icon\"></i>Utilities</a></li>";
					}
				?>
			</ul>
		</div>
	</div>

	<!-- Sidebar Extra Info -->
	<div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
		<?php
			$db = new database;
			$db->connect();
			if($_SESSION['app_subscription'] == 1 && $_SESSION['user_id']){
				$subscription = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users_subscription WHERE user_id = '".$_SESSION['user_id']."'"));
				if($subscription == TRUE){
					echo '
						<div class="push-bit" style="text-align:center;">
							<span class="pull-right">
								<a target="_blank" href="https://www.swcombine.com/members/credits/?receiver=Tomas+O`Cuinn&amount=40,000,000&communication=ION+Direct+Subscription" title="Extend your subscription!" class="text-muted"><i class="fa fa-plus"></i></a>
							</span>
							<small><strong>Subscription: '.$subscription['days'].' days remain</strong></small>
						</div>
						<div class="progress progress-mini push-bit">
							<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="'.$subscription['days'].'" aria-valuemin="0" aria-valuemax="30" style="width: '.(($subscription['days']/30)*100).'%"></div>
						</div>		
					';
				}
				/*
				echo '
					<div class="push-bit" style="text-align:center;">
						<span class="pull-right">
							<a href="account/subscription.php" title="Manage your subscription!" class="text-muted"><i class="fa fa-plus"></i></a>
						</span>
						<small><strong>'.$_SESSION['user_subscription_current'].' '.$_SESSION['app_subscription_metric_abbr'].'</strong> / '.$_SESSION['user_subscription_max'].' '.$_SESSION['app_subscription_metric_abbr'].'</small>
					</div>
					<div class="progress progress-mini push-bit">
						<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="'.$_SESSION['user_subscription_current'].'" aria-valuemin="0" aria-valuemax="'.$_SESSION['user_subscription_max'].'" style="width: '.(($_SESSION['user_subscription_current']/$_SESSION['user_subscription_max'])*100).'%"></div>
					</div>		
				';
				*/
			}
		?>
		<div class="text-center">
			<?php echo $_SESSION['site_footer']; ?>
		</div>
	</div>
</div>