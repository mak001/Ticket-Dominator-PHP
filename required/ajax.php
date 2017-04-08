<?php

require 'connect.php';
require 'methods.php';


if($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['action']) {
		$action = $_POST['action'];
		
		// update cart
		if ($action == 'cart' && $_POST['ticket'] && $_POST['amount']) {
			
			$ticketID = $_POST['ticket'];
			$amount = $_POST['amount'];
			if ($_POST['ticket'] && $_POST['amount']) {
				$sql = "UPDATE `Purchase` SET `Amount`=$amount WHERE `TicketID`=$ticketID AND `UserID`=$userID AND `OrderDate` IS NULL;";
				if (mysqli_query($conn, $sql)) {
					echo 'Success';
				} else {
					echo "ERROR: There was a problem while updating the number of items in the cart.";
				}
			} else {
				echo 'no amount or ticket';
			}
		
		// search
		} else if ($action == 'search') {
			$search = $_POST['search'];
			getSearchTickets($search);
		} else {
			echo 'Unknown action: ' . $action;
		}
		
	} else {
		echo 'ERROR: No action defined.';
	}
} else {
	echo 'ERROR: Not a post.';
}

function getSearchTickets($search) {
	global $conn;
	$sql = "SELECT * FROM `Ticket` WHERE 
		`Band` LIKE '%$search%' OR `Date` LIKE '%$search%' OR `Time` LIKE '%$search%' OR `Price` LIKE '%$search%' OR `Location` LIKE '%$search%'
		 ORDER BY CASE 
			WHEN Band LIKE '$search%' THEN 1 
			WHEN Band LIKE '%$search%' THEN 2 
			WHEN Location LIKE '$search%' THEN 3
			WHEN Location LIKE '%$search%' THEN 4
			ELSE 5 END
			LIMIT 5;";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) >= 1) {
		while($row = mysqli_fetch_assoc($result)) {
			generateSearchTicket($row['TicketID'], $row['Band'],  
				$row['Location'], $row['Date'], $row['Stock']);
		}
	} else {
		echo '<div class="no-tickets">No tickets found.</div>';
	}
}

function generateSearchTicket($id, $band, $location, $date, $stock) {
	?>
	<div class="search-ticket <?php	echo str_replace(' ', '_', $band);	?>">
		<a href="tickets.php?ticket=<?php	echo $id;	?>" class="search-ticket-info">
			<div class="band"><?php	echo $band;	?></div>
			<div class="location"><?php	echo $location;	?></div>
			<div class="date"><?php	echo $date;	?></div>
			<div class="stock">Tickets left: <?php	echo $stock;	?></div>
		</a>
	</div>
	<?php
}
?>