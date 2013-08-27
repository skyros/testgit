<?php
include ('process.php');
if (!(isset($_SESSION['logged_in'])))
{
	header('Location:index.php');
}
$user_table = new process();
?>

<html>
<head>
	<title>Wall</title>
</head>
<body>
	<h3>Welcome <?= $_SESSION['logged_in']['first_name'].' '.$_SESSION['logged_in']['last_name']?></h3>
	<h4><?= $_SESSION['logged_in']['email'] ?></h4>
	<form action ="process.php" method="post">
		<input type="submit" name="logout" value="log out">
	</form>
	<br/>
	<h2><u>List of Friends</u></h2>
	<?php
		$user_table->friendtable();
	?>
	<br/>
	<h2><u>List of Users who subscribed to Friend Finder</u></h2>
	<?php
		$user_table->usertable();
	?>
</body>
</html>