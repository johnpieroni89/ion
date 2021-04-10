<?php 
	include("../assets/php/database.php");
	include("../assets/php/functions.php");
	include("../assets/php/session.php");
	include("../assets/php/acct/login.php");
	
	if(isset($_SESSION['user_id'])){
		header("Location: ../index.php");
	}
	if(isset($_GET['logout'])){
		$alert = "<div class=\"alert alert-info\" style=\"font-size:14px;\"><strong>You have logged out.</strong></div>";
	}
?>

	<?php include("../assets/php/_head.php"); ?>
		<!-- Login Container -->
		<div id="login-container">
			<!-- Login Header -->
			<center><img class="img-responsive" style="max-height:200px; margin-bottom:50px;" src="../assets/img/logo.png"></center>
			<!--
			<h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
				<strong>Welcome to <?php echo $_SESSION['site_name']; ?></strong>
			</h1>
			-->
			<?php if(isset($alert)){echo $alert;} ?>

			<!-- Login Block -->
			<div class="block animation-fadeInQuickInv">
				<!-- Login Title -->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="recover.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Forgot your password?"><i class="fa fa-exclamation-circle"></i></a>
						<a href="register.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Create new account"><i class="fa fa-plus"></i></a>
					</div>
					<h2>Access Terminal</h2>
				</div>

				<!-- Login Form -->
				<form id="form-login" action="login.php" method="post" class="form-horizontal"><input type="hidden" name="login" value="1">
					<div class="form-group">
						<div class="col-xs-12">
							<input type="text" id="login-id" name="login-id" class="form-control" placeholder="Your email or username.." required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="password" id="login-password" name="login-password" class="form-control" placeholder="Your password.." title="Must contain at least one number and one uppercase and lowercase letter, and at least 10 or more characters" required>
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-xs-8">
							<label class="csscheckbox csscheckbox-primary">
								<input type="checkbox" id="login-remember-me" name="login-remember-me">
								<span></span>
							</label>
							Remember Me?
						</div>
						<div class="col-xs-4 text-right">
							<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary"><i class="fa fa-check"></i> Sign In</button>
						</div>
					</div>
				</form>
				<script>
					$("#form-login").validate();
				</script>
			</div>
			
			<!-- Footer -->
			<footer class="text-muted text-center animation-pullUp">
				<?php echo $_SESSION['site_footer']; ?>
			</footer>
			<!-- END Footer -->
			
		</div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>