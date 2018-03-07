</div>
</div>

<script>
	
function submit() {
	var data = new Object();
	data.email = $.trim($('#email').val());
	data.password = $.trim($('#password').val());
	$.post('/web/processing.php', {'login':data}, function(data) {
		data = $.parseJSON( data );
		if (data.success == 1) {
			window.location.replace("/releases/yours");
		} else {
			alert(data.error);
		}
	});
};

function register() {
	var data = new Object();
	data.email = $.trim($('#reg_email').val());
	data.password = $.trim($('#reg_password').val());
	$.post('/web/processing.php', {'register':data}, function(data) {
		data = $.parseJSON( data );
		if (data.success == 1) {
			window.location.replace("/releases/yours");
		} else {
			alert(data.error);
		}
	});
};

$(function() {	
	
	$("#login_submit").click(function(){
		submit();
	});
	
	$("#reg_submit").click(function(){
		register();
	});
	
	$("#public_name_button").click(function(){
		var username = $.trim($('#public_name').val());
		$.post('/web/processing.php', {'set_username':username}, function(data) {
			data = $.parseJSON( data );
			if (data.success == 1) {
				//alert("Success");
				$("#public_name_button").html("✓ Saved");
			} else {
				alert(data.error);
			}
		});
	});

	$(".filter_button").each(function() {
		$(this).click(function(){
			var type = $(this).attr("type");
			var that = this;
			if ($(this).hasClass("shown")) {
				$.post('/web/processing.php', {'filter_off':type}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("shown");
						$(that).addClass("hidden");
						$(that).html("Hidden");
					} else {
						alert(data.error);
					}
				});
			} else {
				$.post('/web/processing.php', {'filter_on':type}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("hidden");
						$(that).addClass("shown");
						$(that).html("Shown");
					} else {
						alert(data.error);
					}
				});
			}
		});
	});
	
	$(".listen_marker").each(function(){
		$(this).click(function(){
			var release_id = $(this).attr("release_id");
			var that = this;
			if ($(this).hasClass("unread")) {
				$.post('/web/processing.php', {'listened':release_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("unread");
						$(that).addClass("read");
						$(that).prop("title","Listened");
					} else {
						alert(data.error);
					}
				});
			} else {
				$.post('/web/processing.php', {'unlistened':release_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("read");
						$(that).addClass("unread");
						$(that).prop("title","Unlistened");
					} else {
						alert(data.error);
					}
				});
			}
			
		});
	});
	
	$("#listen_button").each(function(){
		$(this).click(function(){
			var release_id = $(this).attr("release_id");
			var that = this;
			if ($(this).hasClass("unlistened")) {
				$.post('/web/processing.php', {'listened':release_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("unlistened");
						$(that).addClass("listened");
						$(that).html("✓ Listened");
					} else {
						alert(data.error);
					}
				});
			} else {
				$.post('/web/processing.php', {'unlistened':release_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("listened");
						$(that).addClass("unlistened");
						$(that).html("Mark Listened");
					} else {
						alert(data.error);
					}
				});
			}
			
		});
	});
	
	$(".follow_marker").each(function(){
		$(this).click(function(){
			var artist_id = $(this).attr("artist_id");
			var that = this;
			if ($(this).hasClass("following")) {
				$.post('/web/processing.php', {'unfollow':artist_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("following");
						$(that).addClass("follow");
						$(that).prop("title","Not Following");
					} else {
						alert(data.error);
					}
				});
			} else {
				$.post('/web/processing.php', {'follow':artist_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("follow");
						$(that).addClass("following");
						$(that).prop("title","Following");
					} else {
						alert(data.error);
					}
				});
			}
			
		});
	});
	
	$("#follow_button").each(function(){
		$(this).click(function(){
			var artist_id = $(this).attr("artist_id");
			var that = this;
			if ($(this).hasClass("following")) {
				$.post('/web/processing.php', {'unfollow':artist_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("following");
						$(that).addClass("follow");
						$(that).html("Follow Artist");
					} else {
						alert(data.error);
					}
				});
			} else {
				$.post('/web/processing.php', {'follow':artist_id}, function(data) {
					data = $.parseJSON( data );
					if (data.success == 1) {
						$(that).removeClass("follow");
						$(that).addClass("following");
						$(that).html("✓ Following");
					} else {
						alert(data.error);
					}
				});
			}
			
		});
	});

	
});
	
</script>

</body>
</html>
