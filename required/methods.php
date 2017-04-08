<?php

//logs a user in
function login($username, $password) {
	global $conn;
	$sql = "SELECT * FROM `User` WHERE (`UserName`='$username' OR `Email`='$username') AND `Password`='$password';";
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) == 1) {
		while($row = mysqli_fetch_assoc($result)) {
			$user = $row;
			$_SESSION['UserID'] = $user['UserID'];
			$_SESSION['Admin'] = $user['IsAdmin'];
			$_SESSION['UserName'] = $user['UserName'];
			header('Location: tickets.php');
		}
	}
	return false;
}

//checks if a username or email is already registered
function isRegistered($username, $email) {
	global $conn;
	$sql = "SELECT * FROM `User` WHERE `UserName`='$username' OR `Email`='$email'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			if ($row['UserName'] == $username && $row['Email'] == $email) {
				return 'BOTH';
			} else if ($row['UserName'] == $username) {
				return 'USERNAME';
			} else if ($row['Email'] == $email) {
				return 'EMAIL';
			} else {
				return 'UNKNOWN';
			}
		}
	}
	return false;
}

//gets if the page should logout (included on every page)
if ($_GET['logout'] == 'true') {
	logout();
}

//logs a user out by deleting their session, then redirecting to the index
function logout() {
	session_destroy();
	header('Location: index.php?logout=false');
}

//Gets tickets from the table
function getTickets($search,  $limit) {
	
	//adds a limit if it is not specified
	if (!$limit) {
		$limit = 25;
	}
	global $conn;
	$sql = "";
	//if it has a search term it checks the table for any ticket with any feild like the term
	if ($search) { // good enough (for now)
		$sql = "SELECT * FROM `Ticket` WHERE 
		`Band` LIKE '%$search%' OR `Date` LIKE '%$search%' OR `Time` LIKE '%$search%' OR `Price` LIKE '%$search%' OR `Location` LIKE '%$search%'
		 ORDER BY CASE 
			WHEN Band LIKE '$search%' THEN 1 
			WHEN Band LIKE '%$search%' THEN 2 
			WHEN Location LIKE '$search%' THEN 3
			WHEN Location LIKE '%$search%' THEN 4
			ELSE 5 END
			LIMIT $limit;";
			
	//Selects all from the table
	} else {
		// orders by date then time
		$sql = "SELECT * FROM  `Ticket` ORDER BY Date, Time LIMIT $limit;";
	}
	
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			generateTicket($row['TicketID'], $row['Band'],  
				$row['Location'], $row['Date'], $row['Time'],
				$row['Price'], $row['Quantity'], $row['Stock']);
		}
	} else {
		echo '<div class="no-tickets">No tickets found.</div>';
	}
}

//used in the modify/admin page
function getTicket($id) {
	global $conn;
	$sql = "SELECT * FROM `Ticket` WHERE `TicketID`='$id';";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) == 1) {
		while($row = mysqli_fetch_assoc($result)) {
			return $row;
		}
	} 
	return false;
}

//Generates a ticket's html. Some minor changes are made if logged in, or logged in as admin
function generateTicket($id, $band, $location, $date, $time, $price, $quantity, $stock) {
	global $userID, $userAdmin;
	?>
	
	<div class="ticket-container">
		<div class="ticket <?php if ($quantity == 0) { echo ' sold-out'; } ?>" id="ticket-<?php echo $id; ?>">
		
			<div class="front <?php echo str_replace(' ', '_', $band); ?>">
				<div class="ticket-info">
					<div class="band"><?php echo $band; ?></div>
					<div class="location"><?php echo $location; ?></div>
					<div class="date"><?php echo $date; ?></div>
				</div>
			</div>
			
			<div class="back">
				<div class="ticket-info">
					<div class="band"><?php echo $band; ?></div>
					<div class="location"><?php echo $location; ?></div>
					<div class="date"><?php echo $date; ?></div>
					<div class="time"><?php echo $time; ?></div>
					<div class="price">
						<span class="description">Price: </span>
						<span class="info">$<?php echo $price; ?></span>
					</div>
					<div class="remaining">
						<span class="description">Tickets left: </span>
						<span class="info"><?php echo $stock; ?></span>
					</div>
				</div>
				<?php	if ($userID && !$userAdmin && $stock > 0) {	?>
					<a class="button add-to-cart" href="cart.php?add=<?php	echo $id;	?>">Add to cart</a>
				<?php	} else if ($userID && $userAdmin) {	?>
					<div class="button-container">
						<a class="button delete" 
							href="modify.php?action=delete&ticket=<?php	echo $id;	?>">Delete</a>
						<a class="button update" href="modify.php?action=update&ticket=<?php	echo $id;	?>">Update</a>
					</div>
				<?php	}	?>
			</div>
			
		</div>
	</div>
	
	<?php
}

//generates css for the band's images
function generateBandCss() {
	global $conn;
	$sql = "SELECT * FROM `Ticket` GROUP BY `Band`";
	
	echo '<style type="text/css">';
	
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			$bandName = str_replace(' ', '_', $row['Band']);
			echo '.' . $bandName . ' {';
			// band image, defaults to unknown-band if not found
			echo 'background-image: url(images/bands/' . $bandName . '.jpg), url(images/unknown-band.jpg);';
			echo '}';
		}
	}
	
	echo '</style>';
}

//returns 0 if nothing is in the cart, otherwise it is the total
function getCart() {
	global $userID, $conn;
	$cartTotal = 0;
	
	//Date IS NULL
	$sql = "SELECT P.`PurchaseID`, P.`UserID`, P.`TicketID`, P.`Amount`,
		T.`Band`, T.`Date`, T.`Time`, T.`Price`, T.`Quantity`, T.`Location`, T.`Stock`
		FROM `Purchase` P, `Ticket` T  
		WHERE P.`OrderDate` IS NULL AND T.`TicketID`=P.`TicketID` 
		AND P.`UserID`='$userID';";
	
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) >= 1) {
		$currentRow = 0;
		
		while($row = mysqli_fetch_assoc($result)) {
			getCartItem($row);
			$cartTotal += $row['Price'] * $row['Amount'];
		}
		return number_format($cartTotal, 2);
	} else {
		return 0;
	}
}

//Date IS NOT NULL
//gets all past purchases
function getPurchases() {
	global $userID, $conn;
	$sql = "SELECT P.`PurchaseID`, P.`UserID`, P.`TicketID`, P.`Amount`, P.`OrderDate`,
		T.`Band`, T.`Date`, T.`Time`, T.`Price`, T.`Quantity`, T.`Stock`, T.`Location`
		FROM `Purchase` P, `Ticket` T  
		WHERE P.`OrderDate` IS NOT NULL AND T.`TicketID`=P.`TicketID` 
		AND P.`UserID`='$userID' 
		ORDER BY P.`OrderDate` DESC, P.`PurchaseID` ASC;";
		
	$result = mysqli_query($conn, $sql);
	
	$date = 0;
	$orderTotal = 0;
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			
			//checks if the order date is equal to what it was last
			if ($date != $row['OrderDate']) {
				//sets the order date right away (otherwise it would mess up and put 0 on the first one)
				if ($date == 0) {	
					$date = $row['OrderDate'];
					?>
					
					<div class="order">
						<div class="order-details">
							<div class="order-date"><?php	echo $date;	?></div>
						</div>
						<div class="order-items">
					
				<?php	} else {	
							
							$date = $row['OrderDate'];
						?>
								<div class="order-total">Order total: <span>$<?php	echo number_format($orderTotal, 2);	?></span></div>
							</div>
						</div>
					<div class="order">
						<div class="order-details">
							<div class="order-date"><?php	echo $date;	?></div>
						</div>
						<div class="order-items">
					
				<?php	
					$orderTotal = 0;
				}
			}
			//adds to the total
			$orderTotal += $row['Price'] * $row['Amount'];
			getHistoryItem($row);
		}
		?>
			</div>
			<div class="order-total">Order total: <span>$<?php	echo number_format($orderTotal, 2);	?></span></div>
		</div>
		<?php
	} else {
		// no history found
		?>
			<div class="no-history">
				No past purchases found. Would you like to purchase what is in your <a href="cart.php">Cart</a>?
			</div>
		<?php
	}
}

//Generates the item's HTML
function getCartItem($row) {
	?>
	
	<div class="cart-ticket" id="<?php	echo $row['TicketID'];	?>">
		<div class="band-image <?php	echo str_replace(' ', '_', $row['Band']);	?>"></div>
		<div class="purchase-info">
			<div class="name"><?php	echo $row['Band'];	?></div>
			<div class="location"><?php	echo $row['Location'];	?></div>
			<div class="date"><?php	echo $row['Date'];	?></div>
			<div class="time"><?php	echo $row['Time'];	?></div>
			<div class="quantity">Tickets left: <?php	echo $row['Stock'];	?></div>
			
			<div class="price-info">
				<div class="price">
					<span class="label">Price: </span>
					<span class="data">$<?php	echo $row['Price'];	?></span>
				</div>
				<form class="amountDiv" method="post" action="<?php	echo $_SERVER["PHP_SELF"];	?>">
					<div class="amount-label">
						<label for="amount-<?php	echo $row['TicketID'];	?>">Amount: </label>
					</div>
					<div>
						<input type="number" name="amount-<?php	echo $row['TicketID'];	?>" value="<?php	echo $row['Amount'];	?>"
							min="0" max="<?php	echo $row['Stock'];	?>"/>
					</div>
					<div class="updateDiv">
						<input type="submit"  value="Update" class="button update"/>
					</div>
				</form>
				
				<div class="sub-total">
					<span class="label">Subtotal: </span>
					<span class="data">$<?php	echo number_format($row['Price'] * $row['Amount'], 2);	?></span>
				</div>
			</div>
			<a class="button remove" href="cart.php?remove=<?php	echo $row['TicketID'];	?>">Remove</a>
		</div>
	</div>
	
	<?php
}

//Generates the item's HTML
function getHistoryItem($row) {
	?>
	
	<div class="history-ticket" id="<?php	echo $row['TicketID'];	?>">
		<div class="band-image <?php	echo str_replace(' ', '_', $row['Band']);	?>"></div>
		<div class="purchase-info">
			<div class="name"><?php	echo $row['Band'];	?></div>
			<div class="location"><?php	echo $row['Location'];	?></div>
			<div class="date"><?php	echo $row['Date'];	?></div>
			<div class="time"><?php	echo $row['Time'];	?></div>
			<div class="price-info">
				<div class="price">
					<span class="label">Price: </span>
					<span class="data">$<?php	echo $row['Price'];	?></span>
				</div>
				<div class="amount">
					<span class="label">Amount: </span>
					<span class="data"><?php	echo $row['Amount'];	?></span>
				</div>
				<div class="sub-total">
					<span class="label">Subtotal: </span>
					<span class="data">$<?php	echo number_format($row['Price'] * $row['Amount'], 2);	?></span>
				</div>
			</div>
		</div>
	</div>
	
	<?php
}

?>