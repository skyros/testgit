<?php
session_start();
if (isset($_SESSION['logged_in']))
{
	header('Location:wall.php');
}
?>

<html>
<head>
	<title>Login</title>
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script>
		$(document).ready(function(){
			

			console.log('js running');

			$('#reg').submit(function()
			{

				$.post
				(
					$(this).attr('action'),
					$(this).serialize(),
					function(data){
						console.log('data');
						$('#regmessages').html(data.html);
						
					},
					'json'
				);
				return false;

			});

			// $('#log').submit(function()
			// {

			// 	$.post
			// 	(
			// 		$(this).attr('action'),
			// 		$(this).serialize(),
			// 		function(data){
			// 			$('#logmessages').html(data.html);
			// 		},
			// 		'json'
			// 	);
			// 	// return false;

			// });
		});
	</script>
</head>
<body>
	<div style="display:inline-block; vertical-align:top">
		<h2>Register</h2>
		<form id='reg' action="process.php" method="post">
			<input type="hidden" name="register" value="set">
			<label for="first_name">First Name</label>
			<input type="text" name="first_name"><br/>
			<label for="last_name">Last Name</label>
			<input type="text" name="last_name"><br/>
			<label for="email">Email</label>
			<input type="email" name="email"><br/>
			<label for="password">Password</label>
			<input type="password" name="password"><br/>
			<input type="submit" value="Register">
		</form>
		<div id="regmessages"></div>
	</div>
	<div style="display:inline-block; vertical-align:top">
		<h2>Login</h2>
		<form id='log' action="process.php" method="post">
			<input type="hidden" name="login" value="set">
			<label for="email">Email</label>
			<input type="email" name="email"><br/>
			<label for="password">Password</label>
			<input type="password" name="password"><br/>
			<input type="submit" value="Login">
		</form>
		<div id="logmessages"> 
			<?php
			if (isset($_SESSION['html']))
			{
			echo $_SESSION['html'];
			unset($_SESSION['html']);
			}
			?>
		</div>
	</div>
</body>
</html>