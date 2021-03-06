<!DOCTYPE html>
<html>
<head>
	<title>Registration Screen</title>
</head>
<body>
	<div id="reg-mid">
		<div id="reg-div" class="k-header">
			<h2>Registration Form</h2>
			<div id="regForm-div">
				<form id="regForm" method="post" action="main.php">
					<table align="center">
						<tr>
							<th align="left">Email:</th>
							<th>
								<input id="regEmail" name="regEmail" class="k-textbox" type="email" autofocus placeholder="myname@example.net" required/>*
							</th>
						</tr>
						<tr>
							<th align="left">Password:</th>
							<th>
								<input id="regPass1" name="regPass1" class="k-textbox" type="password" required/>*
							</th>
						</tr>
						<tr>
							<th align="left">Confirm Password:</th>
							<th>
								<input id="regPass2" name="regPass2" class="k-textbox" type="password" onkeyup="checkPass(); return false;" required/>*
							</th>
						</tr>
						<tr>
							<th align="left">First Name:</th>
							<th>
								<input id="fname" name="fname" class="k-textbox" type="text"/>&nbsp
							</th>
						</tr>
						<tr>
							<th align="left">Last Name:</th>
							<th>
								<input id="lname" name="lname" class="k-textbox" type="text"/>&nbsp
							</th>
						</tr>
						<tr>
							<th align="left">Purpose:</th>
							<th>
								<input id="purpose" name="purpose"/>
							</th>
						</tr>
						<tr>
							<th align="left">Capcha:</th>
							<th>
							<?php 
								$_POST['captcha'] = NULL;
								include_once("/captcha-code-generator/index.php");
							?>
						</tr>
					</table>
				</form>
				<p align="center">
					<button class="k-button" id="reg-btn">Register</button>
				</p>
			</div>
			<div id="regInfo-div">
				Please complete the fields on the right to register a new account.
				<br>Note that fields marked with * are mandatory.
			</div>
		</div>
	</div>
	<script>
		var popupNotification = $("#popupNotification").kendoNotification().data("kendoNotification");

		$("#purpose").kendoDropDownList({
			dataTextField: "type",
			dataValueField: "id",
			dataSource: purposeData,
			index: 0
		});
		
		function checkPass()
		{
			//Store the password field objects into variables ...
			var pass1 = document.getElementById('regPass1');
			var pass2 = document.getElementById('regPass2');
			//Set the colors we will be using ...
			var goodColor = "#66cc66";
			var badColor = "#ff6666";
			//Compare the values in the password field
			//and the confirmation field
			if(pass1.value == pass2.value){
				//The passwords match.
				//Set the color to the good color and inform
				//the user that they have entered the correct password
				pass2.style.backgroundColor = goodColor;
				return true;
			}else{
				//The passwords do not match.
				//Set the color to the bad color and
				//notify the user.
				pass2.style.backgroundColor = badColor;
				return false;
			}
		}  
		
		$("#reg-btn").click(function() {
			if ( $("#regEmail").val()=="" || $("#regPass1").val()=="" || $("#regPass2").val()=="" )
			{
				popupNotification.show("Please fill in the fields with *", "warning");
				return false;
			}
			
			if (!checkPass()) {
				popupNotification.show("Password confirmation incorrect.", "warning");
				return false;
			}
			
			$("#loading-img").fadeIn("slow");
			$("#regForm-div").css('visibility','hidden');
				
			$.ajax({
				type: "POST",
				url: "calls.php",
				dataType: 'json',
				data: {restcall: 'registration', arguments: [$("#regEmail").val(), $("#regPass1").val(), $("#fname").val() , $("#lname").val(), $("#purpose").val()]},
				success: function(data) {
					$("#loading-img").fadeOut("slow");
					popupNotification.show("Registration Successful", "success");
					$("#regForm").submit();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					popupNotification.show("Error: " + jqXHR.responseText, "error");
					$("#loading-img").fadeOut("slow");
					$("#regForm-div").css('visibility','visible');
				}
			});
		});
    </script>
	<style>
		html {
		}
		
		html,body {
			height:100%;
			width: 100%;
			margin: 0;
			padding: 0;
		}
		
		#reg-mid {
			font-size: 75%;
			margin: 0;
			height: 100%;
			background: url("images/wallpaper.jpg") no-repeat center center fixed;
			background-size: cover;
			text-align:center;
		}
		
		#reg-div {
			border-radius: 10px 10px 10px 10px;
			border-style: solid;
			border-width: 1px;
			overflow: visible;
			width: 600px;
			margin: auto;
			padding: 10px 20px;
			opacity: 0.85;
			position: relative;
			top: 20%;
			overflow: auto;
		}
		
		#regInfo-div {
			float: right;
			width: 50%;
			text-align: left;
		}
		
		#regForm-div {
			float: left;
		}

		input {
			width: 100%;
		}
		
		#reg-btn {
			width: 100px;
			height: 30px;
			font-size: 14px;
		}
	</style>
</body>
</html>
