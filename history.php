<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

if (!$userID || $userAdmin) {
	header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - History</title>
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
			
			<?php
			getPurchases();
			?>
			
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>