$( "#sendMsg" ).click(function() {
	$.ajax( "assets/php/ajax/ajax_mailbox.php" )
	.done(function() {
		$('#modalCompose').modal('toggle');
		$( "#dialog" ).dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
				Ok: function() {
				  $( this ).dialog( "close" );
				}
			  }
		});
	})
	.fail(function() {
		alert( "error" );
	})
});