		<li class="dropdown">
			<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
				<img src="<?php echo $session->avatar;?>" alt="avatar">
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="dropdown-header">
					<strong><?php if($_SESSION['user_id']){echo $_SESSION['username'];}else{echo "Guest";} ?></strong>
				</li>
				<?php
					if($_SESSION['user_id']){
						if($site->app_mailbox == 1){
							//Mailbox Link
							if(glob("account/mailbox.php")){$link = "account/mailbox.php";}elseif(glob("../account/mailbox.php")){$link = "../account/mailbox.php";}else{$link = "mailbox.php";}
							echo '
							<li>
								<a href="'.$link.'">
									<i class=\"fas fa-envelope fa-fw pull-right\"></i>
									Mailbox
								</a>
							</li>
							';
						}
						if($site->app_usergroups == 1){
							//Usergroups Link
							if(glob("account/usergroups.php")){$link = "account/usergroups.php";}elseif(glob("../account/usergroups.php")){$link = "../account/usergroups.php";}else{$link = "usergroups.php";}
							echo '
							<li>
								<a href="'.$link.'">
									<i class=\"fas fa-users fa-fw pull-right\"></i>
									Usergroups
								</a>
							</li>
							';
						}
						if(isset($_SESSION['user_privs'])){
							if($_SESSION['user_privs']['admin'] > 0){
								//Admin Link
								if(glob("admin/panel.php")){$link = "admin/panel.php";}elseif(glob("../admin/panel.php")){$link = "../admin/panel.php";}else{$link = "panel.php";}
								echo '
								<li>
									<a href="'.$link.'">
										<i class=\"fas fa-toolbox fa-fw pull-right\"></i>
										Admin Panel
									</a>
								</li>
								';
							}
						}
						
						//Profile Link
						if(glob("account/profile.php")){$link = "account/profile.php";}elseif(glob("../account/profile.php")){$link = "../account/profile.php";}else{$link = "profile.php";}
						echo '
						<li class="divider"></li>
						<li>
							<a href="'.$link.'">
								<i class="fas fa-cog fa-fw pull-right"></i>
								Profile
							</a>
						</li>
						';
						
						//Logout Link
						if(glob("account/logout.php")){$link = "account/logout.php";}elseif(glob("../account/logout.php")){$link = "../account/logout.php";}else{$link = "logout.php";}
						echo '
						<li>
							<a href="'.$link.'">
								<i class="fas fa-sign-out-alt fa-fw pull-right"></i>
								Logout
							</a>
						</li>
						';
					}else{
						//Login Link
						if(glob("account/login.php")){$link = "account/login.php";}elseif(glob("../account/login.php")){$link = "../account/login.php";}else{$link = "login.php";}
						echo '
						<li>
							<a href="'.$link.'">
								<i class="fa fa-user fa-fw pull-right"></i>
								Login
							</a>
						</li>
						';
						
						//Register Link
						if(glob("account/register.php")){$link = "account/register.php";}elseif(glob("../account/register.php")){$link = "../account/register.php";}else{$link = "register.php";}
						echo '
						<li>
							<a href="'.$link.'">
								<i class="fa fa-plus fa-fw pull-right"></i>
								Register
							</a>
						</li>
						';
					}
				?>
			</ul>
		</li>