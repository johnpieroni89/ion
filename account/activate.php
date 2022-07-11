<?php 
    include("../autoload.php");
	
	if(isset($_SESSION['user_id'])){
		header("Location: ../index.php");
	}
	if(isset($_POST['activate'])){
	    $account->activate();
	}
?>

	<?php include("../assets/php/_head.php"); ?>
	<body>
		<!-- Activate Container -->
		<div id="login-container">
			<!-- Activate Header -->
			<h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
				<strong><?php echo $site->name;?></strong><br>
				<i class="fas fa-clipboard-check"></i> <strong>Account Activation</strong>
			</h1>
			<?php if(isset($session->alert)){echo $session->alert;} ?>
			<!-- END Activate Header -->

			<!-- Activate Block -->
			<div class="block animation-fadeInQuickInv">
				<!-- Activate Title -->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="login.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Back to Login"><i class="fa fa-user"></i></a>
					</div>
					<h2>Activation</h2>
				</div>
				<!-- END Activate Title -->

				<!-- Activate Form -->
				<form id="form-activate" action="activate.php?code=<?php if(isset($_SESSION['activation_code'])){ echo $_SESSION['activation_code'];}elseif(isset($_GET['code'])){echo $_GET['code'];} ?>" method="post" class="form-horizontal"><input type="hidden" name="activate" value="1">
					<div class="form-group">
						<div class="col-xs-12">
							<input type="text" id="activate-code" name="activatecode" class="form-control" placeholder="Enter your activation code.." value="<?php if(isset($_SESSION['activation_code'])){ echo $_SESSION['activation_code'];	}elseif(isset($_GET['code'])){echo $_GET['code'];} ?>" required>
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary"><i class="fa fa-check"></i> Activate Account</button>
						</div>
					</div>
				</form>
				<!-- END Activate Form -->
			</div>
			<!-- END Activate Block -->

			<!-- Footer -->
			<footer class="text-muted text-center animation-pullUp">
				<?php echo $site->footer; ?>
			</footer>
			<!-- END Footer -->
		</div>
		<!-- END Activate Container -->

		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>