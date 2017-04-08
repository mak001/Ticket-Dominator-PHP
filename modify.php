<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

// If the user is not an admin it redirects without doing any proccessing
if (!$userAdmin) {
	header('Location: index.php');
}

//gets the action to perform, can be add, update or delete
if ($_GET['action']) {
	$action = $_GET['action'];
}

$buttonValue = "Submit";
$id = $band = $location = $date = $time = $price = $amount = $stock = $newAmount = "";
$error = $bandError = $locationError = $dateError = $timeError = $priceError = $amountError = "";
$success = "";

switch($action) {
	case 'add':
		$buttonValue = "Add";
		break;
	case 'update':
		$buttonValue = "Update";
		$id = $_GET['ticket'];
		$ticket = getTicket($id);
		if ($ticket) { 
			$band = $ticket['Band'];
			$location = $ticket['Location'];
			$date = $ticket['Date'];
			$time = $ticket['Time'];
			$price = $ticket['Price'];
			$amount = $ticket['Quantity'];
			$stock = $ticket['Stock'];
		} else {
			$buttonValue = "Error";
		}
		
		break;
	case 'delete':
		$buttonValue = "Delete";
		$id = $_GET['ticket'];
		$ticket = getTicket($id);
		if ($ticket) { 
			$band = $ticket['Band'];
			$location = $ticket['Location'];
			$date = $ticket['Date'];
			$time = $ticket['Time'];
			$price = $ticket['Price'];
			$amount = $ticket['Quantity'];
			$stock = $ticket['Stock'];
		}
		break;
	
	//default case, shouldn't happen, but users
	default:
		$buttonValue = "unknown";
		break;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$id = $_POST['ticket'];
	$band = $_POST['band'];
	$location = $_POST['location'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$price = $_POST['price'];
	$newAmount = $_POST['amount'];
	
	if (ableToModify()) {
		$sql = "";
		if ($action == 'delete') {
			if ($stock != $amount) {
				$sql = "";
				$error = "Cannot delete a ticket with any sold.";
				/* Reverts all feild values on failure */
				$band = $ticket['Band'];
				$location = $ticket['Location'];
				$date = $ticket['Date'];
				$time = $ticket['Time'];
				$price = $ticket['Price'];
				$amount = $ticket['Quantity'];
				$stock = $ticket['Stock'];
			} else {
				$sql = "DELETE FROM `Ticket` WHERE `TicketID`='$id';";
			}
		} else if ($action == 'update') {
			
			//recalculates stock
			$newStock = $newAmount - ($amount - $stock);

			$sql = "UPDATE `Ticket` SET Band='$band', Location='$location', Date='$date', 
				Time='$time', Price='$price', Quantity='$newAmount', Stock='$newStock' 
				WHERE `TicketID`='$id';";
		} else if ($action == 'add') {
			$sql = "INSERT INTO `Ticket`(`Band`, `Location`, `Date`, `Time`, `Price`, `Quantity`, `Stock`) 
				VALUES ('$band', '$location', '$date', '$time', '$price', '$newAmount', '$newAmount');";
		}
		
		/* Only attempts when there is an sql statement */
		if ($sql != "") {
			if (mysqli_query($conn, $sql)) {
				$success = "Successfully " . $action . 'ed the ticket';
			} else {
				$error = "An error occured while updating the database. " . mysqli_error($conn);
			}
		}
		
	} else {
		
		// produces the error text
		if (!$band) {
			$bandError = "Band name is required";
		}
		
		if (!$location) {
			$locationError = "Location is required";
		}
		
		if (!$date) {
			$dateError = "Date is required";
		}
		
		if (!$time) {
			$timeError = "Time is required";
		}
		
		if (!$price) {
			$priceError = "Price is required";
		}
		
		if (!$newAmount && !$amount) {
			$amountError = "Amount is required";
		}
	}
	
}

//Checks if it is able to run a query with data provided. Each action has different requirements
function ableToModify() {
	global $id, $band, $location, $date, $time, $price, $amount, $newAmount, $action;
	if ($action =='add') {
		return $band && $location && $date && $time && $price && $newAmount;
	} else if ($action == 'delete') {
		return $id;
	} else if ($action == 'update') {
		return $id && $band && $location && $date && $time && $price && $amount && $newAmount;
	}
	return false;
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - <?php echo ucfirst($action); ?></title>
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
			<?php	if ($buttonValue =='unknown') {	?>
				<div class="modify-error">
					Unknown action <?php	echo $action;	?>
				</div>
				
			<?php	} else {	?>
				
				<?php	if ($error) {	?>
					<div class="modify-error">
						<?php	echo $error;	?>
					</div>
				<?php	} else if ($success) {	?>
						<div class="modify-success">
							<?php	echo $success;	?>
						</div>
				<?php	}	?>
			
			<!--	has the id of $action so it can be validated differently	-->
			<form class="modify-form" id="<?php	echo $action;	?>" method="post" action="<?php 
				echo $_SERVER["PHP_SELF"] . '?action=' . $action;
				if ($action == 'delete' || $action =='update') {
					echo '&ticket=' . $id;
				}
			?>">
			
				<input type="hidden" name="ticket" value="<?php	echo $id;	?>"/>
				<div class="form-feilds">
					<div id="bandDiv">
						<label for="band">Band: </label>
						<input type="text" name="band" id="band" title="The band name" 
							<?php
							if ($band) {
								echo ' value="' . $band . '"';
							}
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($bandError) {	?>
							<label for="band" class="error"><?php	echo $bandError;	?></label>
						<?php	}	?>
					</div>
					
					<div id="locationDiv">
						<label for="location">Location: </label>
						<input type="text" name="location" id="location" title="The location of the event" 
							<?php
							if ($location) {
								echo ' value="' . $location . '"';
							}
							
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($locationError) {	?>
							<label for="location" class="error"><?php	echo $locationError;	?></label>
						<?php	}	?>
					</div>
					
					<div id="dateDiv">
						<label for="date">Date: </label>
						<input type="date" name="date" id="date" title="The date of the event" 
							<?php
							if ($date) {
								echo ' value="' . $date . '"';
							}
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($dateError) {	?>
							<label for="date" class="error"><?php	echo $dateError;	?></label>
						<?php	}	?>
					</div>
					
					<div id="timeDiv">
						<label for="time">Time: </label>
						<input type="time" name="time" id="time" title="The time of the event" 
							<?php
							if ($time) {
								echo ' value="' . $time . '"';
							}
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($timeError) {	?>
							<label for="time" class="error"><?php	echo $timeError;	?></label>
						<?php	}	?>
					</div>
					
					<div id="priceDiv">
						<label for="price">Price: </label>
						<input type="text" name="price" id="price" title="The pirce of an idividual ticket" 
							<?php
							if ($price) {
								echo ' value="' . $price . '"';
							}
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($priceError) {	?>
							<label for="price" class="error"><?php	echo $priceError;	?></label>
						<?php	}	?>
					</div>
					
					<div id="amountDiv">
						<label for="amount">Amount: </label>
						<input type="text" name="amount" id="amount" title="The total amount of tickets (sold + stock)" 
							<?php
							if ($newAmount) {
								echo 'value="' . $newAmount . '"';
							} else if ($amount) {
								echo ' value="' . $amount . '"';
							}
							if ($action == 'delete') {
								echo ' disabled="disabled"';
							}
							?>
						/>
						<?php if ($amountError) {	?>
							<label for="amount" class="error"><?php	echo $amountError;	?></label>
						<?php	}	?>
					</div>
					
					<div class="stockDiv">
						<label for="stock">Stock left: </label>
						<input type="text" name="stock" id="stock" title="The amount of tickets in stock" 
							<?php
							if ($stock) {
								echo 'value="' . $stock . '"';
							} else if ($newAmount) {
								echo 'value="' . $newAmount . '"';
							} else if ($amount) {
								echo 'value="' . $amount . '"';
							}
							?>
						disabled="disabled"/>
						
					</div>
				</div>
				<input type="submit" id="submit" value="<?php	echo $buttonValue;	?>">
			</form>
			
			<?php	}	?>
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>