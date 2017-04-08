<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

$usernameError;
$passwordError;

//redirects if already logged in
if ($userID) {
	header('Location: index.php');
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	
	if ($username && $password) {
		if(!login($username, $password)) {
			$error = "No username and password match.";
		}
	} else {
		if (!$username) {
			$usernameError = "Username or Email is required";
		}
		
		if (!$password) {
			$passwordError = "Password is required";
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - Login</title>
		<?php
		getStyleSheets();
		getJavaScript();
		?>
	</head>
	<body>

		<?php

		getHeader();
		getNav();
		?>
		<!-- Content -->
		<main>
			<?php echo $error; ?>
			<form id="login" method="post" action="<?php echo($_SERVER["PHP_SELF"]);?>">
				<div class="form-feilds">
					<div id="nameDiv">
						<label for="name">Username/Email: </label>
						<input type="text" name="name" id="name" title="Your username or email" <?php if ($username) { echo 'value="' . $username . '"'; } ?>/>
						<?php
						if ($usernameError) {
							echo '<label for="name" class="error">' . $usernameError . '</label>';
						}
						?>
					</div>
					<div id="passwordDiv">
						<label for="password">Password: </label>
						<input type="password" name="password" id="password" title="Your password" <?php if ($password) { echo 'value="' . $password . '"'; } ?>/>
						<?php
						if ($passwordError) {
							echo '<label for="password" class="error">' . $passwordError . '</label>';
						}
						?>
					</div>
				</div>
				<!-- will use javascript to insert a checkbox to make password visible -->
				<input type="submit" name="submit" value="Login"/>
				
			</form>
			
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>