<!-- Header -->
<header class="navbar navbar-inverse navbar-fixed-top">
	<!-- Left Header Navigation -->
	
	<ul class="nav navbar-nav-custom">
		<li>
			<a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');this.blur();">
				<i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
				<i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>
			</a>
		</li>

		<li class="hidden-xs animation-fadeInQuick">
			<a href="<?php if(glob("index.php")){echo "index.php";}else{echo "../index.php";} ?>"><strong>DASHBOARD</strong></a>
		</li>
	</ul>
	
	
	<!-- Right Header Navigation -->
	<ul class="nav navbar-nav-custom pull-right">
		<!-- Search Form -->
		<li>
			<form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];?>" method="post" class="navbar-form-custom" role="search">
				<input type="text" id="top-search" name="search" class="form-control" placeholder="Search..">
			</form>
		</li>

		<!-- Notification Dropdown -->
		<?php if($_SESSION['site_account_required'] == 0){ include("_notification.php");} ?>
		
		<!-- User Dropdown -->
		<?php //if($_SESSION['site_account_required'] == 1){ include("_userpanel.php");}
		include("_userpanel.php");
		?> 
		
		
	</ul>
</header>