<?php 
	include("../assets/php/database.php");
	include("../assets/php/functions.php");
	include("../assets/php/session.php");
	include("../assets/php/acct/check.php");
?>

	<?php include("../assets/php/_head.php"); ?>
	<style>
		a.modItem:hover{color:red;}
	</style>
    <body>
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading">
            <?php include("../assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                
                <?php include("../assets/php/_sidebar-alt.php"); ?>
                <?php include("../assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container">
                    <?php include("../assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Mailbox</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li><a href="mailbox.php">Mailbox</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>
                        <!-- Example Block -->
						<hr>
						<div class="container">
							<div class="row">
								<div class="list-group col-md-2 pad-right-0">
									<div class="md-form md-outline" style="padding-bottom:5px;"><input class="form-control form-control-lg" type="text" placeholder="Search" aria-label="Search"></div>
									<a class="list-group-item active" href="#"><i class="fa fa-inbox" aria-hidden="true"></i>&nbsp; Inbox <span class="badge badge-pill badge-danger pull-right">1 new</span><span class="badge pull-right">38</span></a>
									<a class="list-group-item" href="#"><i class="fas fa-archive" aria-hidden="true"></i>&nbsp; Archive</a>
									<a class="list-group-item" href="#"><i class="fas fa-share" aria-hidden="true"></i>&nbsp; Sent</a>
									<a class="list-group-item" href="#"><i class="fas fa-pencil-alt" aria-hidden="true"></i>&nbsp; Drafts <span class="badge pull-right">3</span></a>
									<a class="list-group-item" href="#"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp; Trash</a>
								</div>
								<div class="col-md-10">
									<!--inbox toolbar-->
									<div class="row">
										<div class="col-xs-12">
											<button title="Compose New" class="btn btn-primary btn-lg" data-target="#modalCompose" data-toggle="modal">
												<span class="fa fa-edit fa-lg"></span> Compose
											</button>
											<button title="Refresh Inbox" class="btn btn-info btn-lg" id="refreshInbox">
												<span class="fas fa-sync fa-lg"></span> Refresh
											</button>
											<button title="Archive Selected" class="btn btn-info btn-lg" id="archiveSelected">
												<span class="fas fa-archive fa-lg"></span> Archive
											</button>
											<button title="Delete Selected" class="btn btn-warning btn-lg" id="deleteSelected">
												<span class="fa fa-trash fa-lg"></span> Delete
											</button>
											<div class="pull-right">
												<!--<span class="text-muted"><b>{{(itemsPerPage * currentPage) + 1}}</b>–<b>{{(itemsPerPage * currentPage) + pagedItems[currentPage].length}}</b> of <b>{{items.length}}</b></span>-->
												<span class="text-muted"><b>1</b>–<b>20</b> of <b>38</b></span>
												<div class="btn-group btn-group">
													<button class="btn btn-default btn-lg" type="button">
														<span class="glyphicon glyphicon-chevron-left"></span>
													</button>
													<button class="btn btn-default btn-lg" type="button">
														<span class="glyphicon glyphicon-chevron-right"></span>
													</button>
												</div>
											</div>
										</div><!--/col-->
										<div class="col-xs-12 spacer5"></div>
									</div><!--/row-->
									<!--/inbox toolbar-->
									<div class="panel panel-default inbox" id="inboxPanel">
										<!--message list-->
										<div class="table-responsive">
											<table class="table table-striped table-hover refresh-container pull-down">
												<thead class="hidden-xs"><tr>
													<td class="col-sm-1"><input title="Mark all" type="checkbox"></td>
													<td class="col-sm-2"><a href="javascript:;"><strong>Date</strong></a></td>
													<td class="col-sm-1" align="center">Action</td>
													<td class="col-sm-3"><a href="javascript:;"><strong>From</strong></a></td>
													<td class="col-sm-4"><a href="javascript:;"><strong>Subject</strong></a></td>
													<td class="col-sm-1"></td>
												</tr></thead>
												<tbody><tr>
													<td class="col-sm-1 col-xs-4"><input title="Mark this item here" type="checkbox"></td>
													<td class="col-sm-2 col-xs-4" data-target="#modalRead" data-toggle="modal" style="cursor: pointer;"><span>29-Jun-2019 13:02:13</span></td>
													<td class="col-sm-1 col-xs-4" align="center"><a class="modItem" title="Delete" style="margin-right:6px;" href="#"><i class="fa fa-trash"></i></a> <a class="modItem" title="Archive" href="#"><i class="fas fa-archive"></i></a></td>
													<td class="col-sm-3 col-xs-4" data-target="#modalRead" data-toggle="modal" style="cursor: pointer;"><span>John Doeberman</span></td>
													<td class="col-sm-4 col-xs-6" data-target="#modalRead" data-toggle="modal" style="cursor: pointer;"><span>This is the subject</span></td>
													<td class="col-sm-1 col-sm-2" data-target="#modalRead" data-toggle="modal" style="cursor: pointer;"><span class="glyphicon glyphicon-paperclip pull-right"></span> <!--<span class="pull-right glyphicon glyphicon-warning-sign text-danger"></span>--></td>
												</tr></tbody>
											</table>
										</div>
									</div><!--/inbox panel-->
								</div><!--/col-9-->
								<!-- /.modal compose message -->
								<div class="modal fade" id="modalCompose">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
												<h4 class="modal-title">Compose Message</h4>
											</div>
											<div class="modal-body">
												<form class="form-horizontal" role="form">
													<div class="form-group">
														<label class="col-sm-2" for="inputTo">To</label>
														<div class="col-sm-10"><input class="form-control" id="inputTo" type="email" placeholder="comma separated list of recipients"></div>
													</div>
													<div class="form-group">
														<label class="col-sm-2" for="inputSubject">Subject</label>
														<div class="col-sm-10"><input class="form-control" id="inputSubject" type="text" placeholder="subject"></div>
													</div>
													<div class="form-group">
														<label class="col-sm-12" for="inputBody">Message</label>
														<div class="col-sm-12"><textarea class="form-control" id="inputBody" rows="12"></textarea></div>
													</div>
												</form>
											</div>
											<div class="modal-footer">
												<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button> 
												<button class="btn btn-warning pull-left" type="button">Save Draft</button>
												<button id="sendMsg" class="btn btn-primary " type="button" onclick="sendMsg()">Send <i class="fa fa-arrow-circle-right fa-lg"></i></button>
											</div>
										</div>
									</div>
								</div>
								<!-- /.modal read message -->
								<div class="modal fade" id="modalRead">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
												<h4 class="modal-title">Read Message</h4>
											</div>
											<div class="modal-body">
												<form class="form-horizontal" role="form">
													<div class="form-group">
														<div class="col-sm-2" style="padding:5px;"><b>From</b></div>
														<div class="col-sm-10" style="padding:5px;">{username}</div>
													</div>
													<div class="form-group">
														<div class="col-sm-2" style="padding:5px;"><b>Subject</b></div>
														<div class="col-sm-10" style="padding:5px;">{subject}</div>
													</div>
													<div class="form-group">
														<div class="col-sm-12" style="padding:5px;"><b>Message</b></div>
														<div class="col-sm-12" style="padding:5px;">{body}</div>
													</div>
													<div class="col-sm-12">
														<span class="glyphicon glyphicon-paperclip"></span>&emsp;<a title="Download attachment" href="javascript:;">filename.type</a>
													</div>
												</form>
											</div>
											<div class="modal-footer">
												<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Close</button> 
												<button class="btn btn-warning " type="button">Delete <i class="fa fa-trash fa-lg"></i></button>
												<button class="btn btn-primary " type="button">Reply <i class="fa fa-arrow-circle-right fa-lg"></i></button>
											</div>
										</div>
									</div>
								</div>
								<div id="dialog" style="display:none;" title="Message Sent!">
									<p>This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
		<script src="../assets/js/mailbox.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	</body>
</html>