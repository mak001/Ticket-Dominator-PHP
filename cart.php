<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

if (!$userID || $userAdmin) {
	header('Location: index.php');
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$ticketID = -1;
	$amount = -1;
	
	// from http://stackoverflow.com/a/8825039
	foreach($_POST as $key => $value) {
		if (strpos($key, 'amount-') === 0) {
			// value starts with amount-
			$values = explode("-", $key);
			if (count($values) == 2) {
				$ticketID = $values[1];
				$amount = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			}
		}
	}
	
	if ($ticketID && isset($amount)) {
		$sql = "";
		if ($amount == 0) {
			$sql = "DELETE FROM `Purchase` WHERE `TicketID`=$ticketID AND `UserID`=$userID AND `OrderDate` IS NULL;";
		} else {
			$sql = "UPDATE `Purchase` SET `Amount`=$amount WHERE `TicketID`=$ticketID AND `UserID`=$userID AND `OrderDate` IS NULL;";
		}
		
		if (mysqli_query($conn, $sql)) {
			header('Location: cart.php');
		} else {
			$error = "There was a problem while updating the number of items in the cart.";
		}
	}
}

if ($_GET['purchase'] == 'true') {
	$sql = "UPDATE `Purchase` P, `Ticket` T 
		SET P.`OrderDate`=CURDATE(), T.`Stock`=T.`Stock` - P.`Amount`
		WHERE P.`OrderDate` IS NULL AND P.`UserID`='$userID' AND T.`TicketID`=P.`TicketID`;";
		
	if (mysqli_query($conn, $sql)) {
		header('Location: history.php');
	} else {
		$error = "There was a problem while trying to purchase an item.";
	}
}

$add = filter_var($_GET['add'], FILTER_SANITIZE_NUMBER_INT);
if ($add) {
	$sql = "SELECT * FROM `Purchase` WHERE `TicketID`=$add AND `UserID`=$userID AND `OrderDate` IS NULL;";
	
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			$sql2 = "UPDATE `Purchase` SET `Amount`=`Amount` + 1 WHERE `TicketID`=$add AND `UserID`=$userID AND `OrderDate` IS NULL;";
			if (mysqli_query($conn, $sql2)) {
				header('Location: cart.php#' . $add);
			} else {
				$error = "There was a problem while trying to add an item to the cart.";
			}
		}
	} else {
	
		$sql2 = "INSERT INTO `Purchase`(`UserID`, `TicketID`, `Amount`) 
					VALUES ('$userID', '$add', '1');";
	
		if (mysqli_query($conn, $sql2)) {
			header('Location: cart.php#' . $add);
		} else {
			$error = "There was a problem while trying to add an item to the cart.";
		}
	}
	
}

$remove = filter_var($_GET['remove'], FILTER_SANITIZE_NUMBER_INT);
if ($remove) {
	$sql = "DELETE FROM `Purchase` WHERE `TicketID`=$remove AND `UserID`=$userID;";
	if (mysqli_query($conn, $sql)) {
		header('Location: cart.php');
	} else {
		$error = "There was a problem while trying to remove an item from the cart.";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - Cart</title>
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
		<main class="cart">
			
			<?php
				if ($error) {
					?>
					<div class="error">
						<?php	echo $error;	?>
					</div>
					<?php
				} else {
					$total = getCart();
					if ($total > 0) {
						?>
						<div class="total">Total: <span>$<?php	echo $total;	?></span></div>
						<a class="button purchase" href="<?php	echo $_SERVER['PHP_SELF'];	?>?purchase=true">
							Purchase
						</a>
						<?php
					} else {
						?>
						<div class="no-cart">
							No items are in your cart. Would you like to browse all the <a href="tickets.php">tickets</a>?
						</div>
						<?php
					}
				}
			?>
			
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>