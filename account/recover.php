<?php 
    include("../autoload.php");
	
	if(isset($_SESSION['user_id'])){
		header("Location: ../index.php");
	}
?>

	<?php require("../assets/php/_head.php"); ?>
	<body>
		<!-- Login Container -->
		<div id="login-container">
			<!-- Reminder Header -->
			<h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
				<strong><?php echo $site->name;?></strong><br>
				<i class="fa fa-history"></i> <strong>Password Recovery</strong>
			</h1>
			<?php if(isset($session->alert)){echo $session->alert;} ?>
			<!-- END Reminder Header -->

			<!-- Reminder Block -->
			<div class="block animation-fadeInQuickInv">
				<!-- Reminder Title -->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="login.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Back to login"><i class="fa fa-user"></i></a>
					</div>
					<h2>Recovery</h2>
				</div>
				<!-- END Reminder Title -->

				<!-- Reminder Form -->
				<form id="form-reminder" action="recover.php" method="post" class="form-horizontal"><input type="hidden" name="auth" value="recover">
					<div class="form-group">
						<div class="col-xs-12">
							<input type="email" id="reminder-email" name="reminder-email" class="form-control" placeholder="Enter your email.." required>
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary"><i class="fa fa-check"></i> Recover Password</button>
						</div>
					</div>
				</form>
				<!-- END Reminder Form -->
			</div>
			<!-- END Reminder Block -->

			<!-- Footer -->
			<footer class="text-muted text-center animation-pullUp">
				<?php echo $site->footer; ?>
			</footer>
			<!-- END Footer -->
		</div>
		<!-- END Login Container -->

		<?php require("../assets/php/_end.php"); ?>
	</body>
</html>