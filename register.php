<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

//redirects if already logged in
if ($userID) {
	header('Location: index.php');
}

$usernameError = $emailError = $passwordError = $confirmPasswordError = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	$confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
	
	
	if ($username && $email && $password && $confirmPassword) {
		if ($password === $confirmPassword) {
			$registered = isRegistered($username, $email);
			if ($registered == 'BOTH') {
				$usernameError = "Username is already registered";
				$emailError = "Email is already registered";
			} else if ($registered == 'USERNAME') {
				$usernameError = "Username is already registered";
			} else if ($registered == 'EMAIL') {
				$emailError = "Email is already registered";
			} else if ($register == 'UNKNOWN') {
				$error = "An unknown error occured. Please try again";
			} else {
				$sql = "INSERT INTO `User`(`UserName`, `Password`, `Email`) 
					VALUES ('$username', '$password', '$email')";
				$result = mysqli_query($conn, $sql);
				
				login($username, $password);
			}
		} else {
			$confirmPasswordError = "Passwords do not match";
		}
	} else {
		if (!$username) {
			$usernameError = "Username or Email is required";
		}
		
		if (!$email) {
			$emailError = "Email is required";
		}
		
		if (!$password) {
			$passwordError = "Password is required";
		}
		
		if (!$confirmPassword) {
			$confirmPasswordError = "Password needs to be enetered again";
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - Register</title>
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
			<form id="register" method="post" action="<?php echo($_SERVER["PHP_SELF"]);?>">
				<div class="form-feilds">
					<div id="nameDiv">
						<label for="name">Username: </label>
						<input type="text" name="name" id="name" title="The username you want to log in with" <?php if ($username) { echo 'value="' . $username . '"'; } ?>/>
						<?php
						if ($usernameError) {
							echo '<label for="name" class="error">' . $usernameError . '</label>';
						}
						?>
					</div>
					
					<div id="emailDiv">
						<label for="email">Email: </label>
						<input type="text" name="email" id="email" title="The email you want to log in with" <?php if ($email) { echo 'value="' . $email . '"'; } ?>/>
						<?php
						if ($emailError) {
							echo '<label for="name" class="error">' . $emailError . '</label>';
						}
						?>
					</div>
					
					<div id="passwordDiv">
					
						<div id="firstPassword">
							<label for="password">Password: </label>
							<input type="password" name="password" id="password" title="The passwrod you want to use" <?php if ($password) { echo 'value="' . $password . '"'; } ?>/>
							<?php
							if ($passwordError) {
								echo '<label for="password" class="error">' . $passwordError . '</label>';
							}
							?>
						</div>
						
						<div id="confirmPassword">
							<label for="confirmPassword">Password: </label>
							<input type="password" name="confirmPassword" id="confirmPassword" title="The same password as above" <?php if ($confirmPassword) { echo 'value="' . $confirmPassword . '"'; } ?>/>
							<?php
							if ($confirmPasswordError) {
								echo '<label for="confirmPassword" class="error">' . $confirmPasswordError . '</label>';
							}
							?>
						</div>
						<!-- will use javascript to insert a checkbox to make password visible -->
					</div>
				</div>
				
				<input type="submit" name="submit" value="Register"/>
				
			</form>
			
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>