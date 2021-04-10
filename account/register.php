<?php 
	include("../assets/php/database.php");
	include("../assets/php/functions.php");
	include("../assets/php/session.php");
	include("../assets/php/pepper.php");
	include("../assets/php/acct/register.php");
	
	if(isset($_SESSION['user_id'])){
		header("Location: ../index.php");
	}
?>

	<?php include("../assets/php/_head.php"); ?>
	<body>
		<!-- Login Container -->
		<div id="login-container">
			<!-- Register Header -->
			<h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
				<strong><?php echo $_SESSION['site_name'];?></strong><br>
				<i class="fa fa-plus"></i> <strong>Create Account</strong>
			</h1>
			<?php if(isset($alert)){echo $alert;} ?>
			<!-- END Register Header -->

			<!-- Register Form -->
			<div class="block animation-fadeInQuickInv">
				<!-- Register Title -->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="login.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Back to login"><i class="fa fa-user"></i></a>
					</div>
					<h2>Register</h2>
				</div>
				<!-- END Register Title -->

				<!-- Register Form -->
				<form id="form-register" action="" method="post" class="form-horizontal">
					<div class="form-group">
						<div class="col-xs-12">
							<input type="text" id="register-username" name="register-username" class="form-control" placeholder="Username" pattern="[A-Za-z0-9_ `'\-]{8,}" title="Username must contain at least 8 alphanumeric characters and may include underscores." required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="text" id="register-firstname" name="register-firstname" class="form-control" placeholder="First Name" pattern="[A-Za-z0-9_ `'\-]{2,}" title="First name must contain at least 2 letters." required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="text" id="register-lastname" name="register-lastname" class="form-control" placeholder="Last Name" pattern="[A-Za-z0-9_ `'\-]{2,}" title="Last name must contain at least 2 letters and may contain hyphens." required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="email" id="register-email" name="register-email" class="form-control" placeholder="Email" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="password" id="register-password" name="register-password" class="form-control" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 10 or more characters" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="password" id="register-password-verify" name="register-password-verify" class="form-control" placeholder="Verify Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 10 or more characters" required>
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-xs-6">
							<label class="csscheckbox csscheckbox-primary" data-toggle="tooltip" title="Agree to the terms">
								<input type="checkbox" id="register-terms" name="register-terms" required>
								<span></span>
							</label>
							<a href="#modal-terms" data-toggle="modal">Terms</a>
						</div>
						<div class="col-xs-6 text-right">
							<button type="submit" class="btn btn-effect-ripple btn-primary" name="register"><i class="fa fa-plus"></i> Create Account</button>
						</div>
					</div>
				</form>
				<!-- END Register Form -->
			</div>
			<!-- END Register Block -->

			<!-- Footer -->
			<footer class="text-muted text-center animation-pullUp">
				<?php echo $_SESSION['site_footer']; ?>
			</footer>
			<!-- END Footer -->
		</div>
		<!-- END Login Container -->

		<!-- Modal Terms -->
		<div id="modal-terms" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title text-center"><strong>Terms and Conditions</strong></h3>
					</div>
					<div class="modal-body">
						<?php include("../assets/html/terms.html"); ?>
					</div>
					<div class="modal-footer">
						<div class="text-center">
							<button type="button" class="btn btn-effect-ripple btn-sm btn-primary" data-dismiss="modal">I've read them!</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END Modal Terms -->
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>