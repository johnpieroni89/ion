<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">

        <title><?php echo $_SESSION['site_title']; ?></title>

        <meta name="description" content="<?php echo $_SESSION['site_description']; ?>">
        <meta name="author" content="<?php echo $_SESSION['site_author']; ?>">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="<?php if(glob("assets/img/favicon.svg")){ echo "assets/img/favicon.svg";}else{echo "../assets/img/favicon.svg";} ?>">
        <link rel="shortcut icon" href="<?php if(glob("assets/img/favicon.ico")){ echo "assets/img/favicon.ico";}else{echo "../assets/img/favicon.ico";} ?>">
        <link rel="apple-touch-icon" href="<?php if(glob("assets/img/favicon.svg")){ echo "assets/img/favicon.svg";}else{echo "../assets/img/favicon.svg";} ?>" sizes="180x180">
        <!-- END Icons -->

		<?php
		$db = new database;
		$db->connect();
		$plugin_bootstrap3_css = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_bootstrap3_css'"));
		$plugin_fontawesome = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_fontawesome'"));
		$plugin_modernizr = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_modernizr'"));
		$db->disconnect();
		unset($db);
		?>

        <!-- Stylesheets -->
        <link rel="stylesheet" type="text/css" href="<?php echo $plugin_bootstrap3_css['value']; ?>"> <!-- Bootstrap is included in its original form, unaltered -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $plugin_fontawesome['value']; ?>"> <!-- Fontawesome -->
        <link rel="stylesheet" type="text/css" href="<?php if(glob("assets/css/plugins.css")){ echo "assets/css/plugins.css";}else{echo "../assets/css/plugins.css";} ?>"> <!-- Related styles of various icon packs and plugins-->
        <link rel="stylesheet" type="text/css" href="<?php if(glob("assets/css/main.css")){ echo "assets/css/main.css";}else{echo "../assets/css/main.css";} ?>"> <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" type="text/css" href="<?php if(glob("assets/css/themes/".$_SESSION['site_style'].".css")){ echo "assets/css/themes/".$_SESSION['site_style'].".css";}else{echo "../assets/css/themes/".$_SESSION['site_style'].".css";} ?>">
        <!-- END Stylesheets -->
		
		<style>
			nav > .nav.nav-tabs{
				border: none;
				color:#fff;
				background:#272e38;
				border-radius:0;
			}
			nav > div a.nav-item.nav-link,
			nav > div a.nav-item.nav-link.active
			{
			  border: none;
				padding: 18px 25px;
				color:#fff;
				background:#272e38;
				border-radius:0;
			}

			nav > div a.nav-item.nav-link.active:after
			 {
			  position: relative;
			  border: 15px solid transparent;
			  border-top-color: #e74c3c ;
			}
			.tab-content{
				background: #fdfdfd;
				border: 1px solid #ddd;
				border-top:5px solid #e74c3c;
				border-bottom:5px solid #e74c3c;
				padding:30px 25px;
			}
			nav > div a.nav-item.nav-link:hover,
			nav > div a.nav-item.nav-link:focus
			{
			  border: none;
				background: #e74c3c;
				color:#fff;
				border-radius:0;
				transition:background 0.20s linear;
			}
			@media print {
				a[href]:after {
					content: none !important;
				}
			}
		</style>
		
		<script type="text/javascript" src="<?php if(glob("assets/js/sorttable.js")){ echo "assets/js/sorttable.js";}else{echo "../assets/js/sorttable.js";} ?>"></script>
        <script src="<?php echo $plugin_modernizr['value']; ?>"></script> <!-- Modernizr (browser feature detection library) -->
    </head>