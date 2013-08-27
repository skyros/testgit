<?php

//this is a comment

include('connection.php');
session_start();
//registration and login validator
class submitdata
{

	//registration
	function registration($first_name, $last_name, $email, $password)
	{
		$errors = array();

		if ($first_name == '')
		{
			$errors[] = "Please enter your first name.";
		}

		if ($last_name == '')
		{
			$errors[] = "Please enter your last name.";
		}

		if ($email == '')
		{
			$errors[] = 'Please enter your email.';
		}

		if ($password == '' or strlen($password) < 6)
		{
			$errors[] = 'Please enter valid password.';
		}

		if (count($errors) > 0)
		{
			return $errors;
		}
		else
		{
			$register = new logreg();
			$message[0] = $register->register($first_name, $last_name, $email, $password);
			return $message;
		}
	}

	//login
	function login($email, $password)
	{
		$errors = array();
		if ($email == '')
		{
			$errors[] = 'Please enter your email.';
		}

		if ($password == '')
		{
			$errors[] = 'Please enter your password.';
		}

		if (count($errors) > 0)
		{
			return $errors;
		}
		else
		{
			$login = new logreg();
			$message[0] = $login->login($email, $password);
			return $message;
		}
	}
}


//handles database for login and registration
class logreg
{

	//registration
	function register($first_name, $last_name, $email, $password)
	{
		$friends_db = new Database();
		$query = "SELECT * FROM users WHERE email = '".mysql_real_escape_string($email)."'";
		$emails = $friends_db->fetch_all($query);

		//checking for duplicate emails returns success failure
		if (count($emails) > 0)
		{
			$message = 'Duplicate email found. Please try again.';
		}

		else
		{
		$query = "INSERT INTO users (first_name, last_name, email, password, created_at) VALUES ('".mysql_real_escape_string($first_name)."', '".mysql_real_escape_string($last_name)."', '".mysql_real_escape_string($email)."', '".md5(mysql_real_escape_string($password))."', NOW())";
		mysql_query($query);
		$message = 'Registration successful.';
		}

		return $message;
	}

	//login
	function login($email, $password)
	{
		$friends_db = new Database();
		$query = "SELECT email, password FROM users WHERE email = '".mysql_real_escape_string($email)."' AND password = '".md5(mysql_real_escape_string($password))."'";
		$check = $friends_db->fetch_all($query); 

		//one last security check
		if ($email === $check[0]['email'] && md5($password) === $check[0]['password'] && count($check) == 1)
		{
			$message = 'Logged in.';
			session_destroy();
			session_start();
			$query = "SELECT id, first_name, last_name, email FROM users WHERE email = '".mysql_real_escape_string($email)."' AND password = '".md5(mysql_real_escape_string($password))."'";
			$_SESSION['logged_in'] = $friends_db->fetch_record($query);
		}

		else
		{
			$message = 'Something went wrong with the login.';
		}
		return $message;
	}
}

class process
{
	function regerrors($messages)
	{
		$html = NULL;
		foreach ($messages as $message)
		{
			$html .= '<p>'.$message.'</p>';
		}
			$data['html'] = $html;
			echo json_encode($data);
	}

	function logerrors($messages)
	{
		$html = NULL;
		foreach ($messages as $message)
		{
			$html .= '<p>'.$message.'</p>';
		}
			$_SESSION['html'] = $html;
	}

	function friendtable()
	{
		$friends_db = new Database();
		$query = "SELECT users.first_name, users.email FROM friends LEFT JOIN users ON users.id = friend_id WHERE friends.users_id = ".mysql_real_escape_string($_SESSION['logged_in']['id']);
		$users = $friends_db->fetch_all($query);
		$table = NULL;
		if (count($users) == 0)
		{
			$table = '<h3>None.</h3>';
		}
		else
		{
			$table = '
			<table>
				<tr>
					<th>First Name</th>
					<th>Email</th>
				</tr>';

			foreach($users as $user)
			{	
				$table .='
				<tr>
					<td>'.$user['first_name'].'</td>
					<td>'.$user['email'].'</td>
				</tr>';
			}

			$table .='
			</table>';
		}

		echo $table;
	}

	function usertable()
	{
		// include('connection.php');
		$friends_db = new Database();
		$query = "SELECT id, first_name, email FROM users;";
		$users = $friends_db->fetch_all($query);
		$table = NULL;
		$table = '
		<table>
			<tr>
				<th>First Name</th>
				<th>Email</th>
				<th>Action</th>
			</tr>';

		foreach($users as $user)
		{	
			$table .='
			<tr>
				<td>'.$user['first_name'].'</td>
				<td>'.$user['email'].'</td>
				<td>';
				$query = "SELECT users_id, friend_id FROM friends WHERE friend_id = ".$user['id']." AND users_id = ".$_SESSION['logged_in']['id']."";
				$friends = $friends_db->fetch_record($query);
				if (!(isset($friends['users_id'])))
				{
					$table .='
					<form class="friend'.$user['id'].'" action="process.php" method="post">
						<input type="hidden" name="user_id" value="'.$user['id'].'">
						<input type="submit" value="Add as Friend">
					</form>';
				}
				else
				{
					$table .='
					Friends';
				}
				$table .='
				</td>
			</tr>';
		}

		$table .='
		</table>';

		echo $table;
	}

	function add_friend($user_id, $friend_id)
	{
		$friends_db = new Database();
		$query = "INSERT INTO friends (users_id, friend_id) VALUES ({$user_id}, {$friend_id})";
		mysql_query($query);
	}

}

if ($_POST)
{
	if (isset($_POST['register']))
	{
		$register = new submitdata();
		$messages = $register->registration($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password']);
		$regerrors = new process();
		$regerrors->regerrors($messages);
	}

	else if (isset($_POST['login']))
	{
		$login = new submitdata();
		$messages = $login->login($_POST['email'], $_POST['password']);
		$logerrors = new process();
		$logerrors->logerrors($messages);
		header('Location:index.php');
	}

	else if (isset($_POST['user_id']))
	{
		$add_friend = new process();
		$add_friend->add_friend($_SESSION['logged_in']['id'], $_POST['user_id']);
		header('Location:wall.php');
	}

	else
	{
		session_destroy();
		header('Location:index.php');
	}
}
?>