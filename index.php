<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once('session.php');




//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html>
<head>
    <title>homologyScanner - Web Tool Main</title>
	<link rel="shortcut icon" href="images/ZEMuIcon.ico" type="image/x-icon">
	<link rel="icon" href="/images/ZEMuIcon.ico" type="image/x-icon">

    <link rel="stylesheet" href="styles/kendo.common.min.css" />
    <link rel="stylesheet" href="styles/kendo.moonlightMod.css" />

    <script src="js/jquery.min.js"></script>
	<script src="JSmol.min.js"></script>
    <script src="js/kendo.ui.core.min.js"></script>
</head>
<body>
	<div hidden="hidden" id="s_userDiv" data-sUser=<?php echo $_SESSION['ZEMuser']?>></div>
	<div hidden="hidden" id="s_idDiv" data-sID=<?php echo session_id()?>></div>
	<div id="menu">
		<ul>
			<li>
				<div>
					<img alt"ZEMu Logo" class="logo" src="images/ZEMuLogoWhite.png">
				</div>
			</li>
			<li>Home</li>
			<li>Tool</li>
			<li>Submit</li>
			<li>About</li>
			<div id="loginForm-div">
				<form id="loginForm" method="post" action="session_start.php">
					<input id="email" name="email" class="k-textbox" type="email" placeholder="Email" required/>
					<input id="pswrd" name="pswrd" class="k-textbox" type="password" placeholder="Password" required/>
					<button class="k-button" id="login-btn" type="button">Login</button>
				</form>
				<a id="register" href="">Register a new Account</a>
			</div>
			<form id="logoutForm" method="post" action="session_end.php">
				<button class="k-button" id="accInfo-btn" type="button">My Account</button>
				<button class="k-button" id="logout-btn" type="button">Logout</button>
			</form>
		</ul>
	</div>
	<div id="content">
	</div>
	<img id="loading-img" src="styles/Moonlight/loading-image.gif"/>
	<span id="popupNotification"></span>
	<script>
		$("#loading-img").hide();
		
		var user = document.getElementById("s_userDiv").getAttribute("data-sUser");
		var sID = document.getElementById("s_idDiv").getAttribute("data-sID");
		var purposeData;
		var win = null;
		
		var popupNotification = $("#popupNotification").kendoNotification().data("kendoNotification");
		popupNotification.setOptions({
			show: onShow
		});
		
		function onShow(e) {
			e.element.parent().css({
				zIndex: 999999
			});
		}
		
		popupNotification.show("Welcome " + user);
		
		if (user != "Guest") {
			$("#loginForm-div").hide();
		}else {
			$("#logoutForm").hide();
		}
		
		$("#content").load("home.html");
		
		$.ajax({
			type: "POST",
			url: "calls.php",
			dataType: "json",
			data: {restcall: 'getPurposes', arguments: [0]},
			success: function(data) {
				purposeData = data;
			},
			error: function(jqXHR, textStatus, errorThrown) {
				popupNotification.show("Error loading purposeDataSource: " + jqXHR.responseText, "error");
			}
		});
		
		var evalData = [
			{ status: "Rejected", id: "1" },
			{ status: "Uncertain", id: "2" },
			{ status: "Accepted", id: "3" }
		];

		$("#register").click(function(event){
			event.preventDefault();
			$("#content").load("registration.html");
		});
		
		$("#pswrd").keyup(function(event){
           	if(event.keyCode == 13){
              	$("#login-btn").click();
           	}
        });
		
		$("#login-btn").click(function() {
			if ( $("#email").val()=="" || $("#pswrd").val()=="" ) {
				popupNotification.show("Please fill in your Credentials...", "warning");
				return false;
			}
			
			$("#loading-img").fadeIn("slow");
			$("#loginForm-div").css('visibility','hidden');
			
			$.ajax({
				type: "POST",
				url: "calls.php",
				dataType: 'json',
				data: {restcall: 'userLogin', arguments: [$("#email").val(), $("#pswrd").val()]},
				success: function(data) {
					$("#loading-img").fadeOut("slow");
					$("#loginForm").submit();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					if (jqXHR.responseText.length > 0) {
						popupNotification.show("Error: " + jqXHR.responseText, "error");
					}else {
						popupNotification.show("Invalid Credentials.", "error");
					}
					$("#loading-img").fadeOut("slow");
					$("#loginForm-div").css('visibility','visible');
				}
			});
		});
		
		$("#logout-btn").click(function() {
			$("#logoutForm").submit();
		});
		
		$("#accInfo-btn").click(function() {
			if (win != null) {
				if (!(win.data("kendoWindow").element.is(":hidden"))) {
					win.data("kendoWindow").close();
				}
			}
			$("#content").load("account.html");
		});
		
		function onSelectMenu(e) {
			
			if (win != null) {
				if (!(win.data("kendoWindow").element.is(":hidden"))) {
					win.data("kendoWindow").close();
				}
			}
			
			if ($(e.item).index() == 1) {
				$("#content").load("home.html");
			}else if ($(e.item).index() == 2) {
				$("#content").load("tool2.html");
			}else if ($(e.item).index() == 3) {
				$("#content").load("submitNewJob.html");
			}else if ($(e.item).index() == 4) {
				$("#content").load("about.html");
			}
		}
		
		$(document).ready(function() {
			$("#menu ul").kendoMenu({
				select: onSelectMenu,
				navigatable : false
			});
		});
	</script>
	<style>
		html {
			min-width: 800px;
			min-height: 600px;
			font-family: Arial, Helvetica, sans-serif;
		}
		
		html,body {
			height: 100%;
			width: 100%;
			margin: 0;
			padding: 0;
		}
		
		h1,h2,h3 {
			color: #F4AF03;
		}
		
		p.text {
			text-align: justify;
			margin-bottom: 35px;
		}
		
		.k-state-focused {
			box-shadow: none;
		}
		
		#menu ul {
			margin: 0;
			padding: 0;
			height: 46px;
		}
		
		#menu ul li {
			list-style: none;
			float: left;
			text-align: center;
			border-right: 0;
			border-left: 0;
			margin-top: 6px;
			margin-right: 8px;
			font-weight: bold;
		}
		
		#menu ul li:first-of-type {
			margin: 3px 8px;
		}
		
		.logo {
			width:100px;
			height: 41px;
			display: block;
		}
		
		#loginForm-div {
			width: 360px;
			float: right;
			margin-top: 3px;
			font-size: 75%;
		}
		
		#logoutForm {
			float: right;
			font-size: 75%;
			margin: 10px;
		}
		
		#email, #pswrd, #login-btn {
			height: 25px;
		}
		
		#register {
			float: right;
			color: white;
			margin-right: 13px;
			margin-top: 3px;
			font-size: 80%;
		}
		
		#content {
			height: calc(100% - 48px);
			border: none;
		}
		
		#loading-img {
			position: absolute;
			top: 45%;
			left: 45%;
			height: 70px;
			z-index: 99999;
		}
	</style>
</body>
</html>
