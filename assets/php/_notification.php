<?php
	if($_SESSION['app_notifications'] == 1){
		echo '
		<li>
			<a href="javascript:void(0)" onclick="App.sidebar(\'toggle-sidebar-alt\');this.blur();">
				<i class="far fa-bell" style="font-size:22px;"></i>
			</a>
		</li>
		';
	}
?>