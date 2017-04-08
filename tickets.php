<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

$search;

if ($_GET['search']) {
	$search = $_GET['search'];
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - Tickets</title>
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
			<div id="ticket-box">
				<?php
				if ($_GET['ticket']) {
					$row = getTicket($_GET['ticket']);
					?>
					
					<div class="single-ticket" id="9">
						<div class="band-image <?php	echo str_replace(' ', '_', $row['Band']);	?>"></div>
						<div class="purchase-info">
							<div class="name"><?php	echo $row['Band'];	?></div>
							<div class="location"><?php	echo $row['Location'];	?></div>
							<div class="date"><?php	echo $row['Date'];	?></div>
							<div class="time"><?php	echo $row['Time'];	?></div>
							<div class="quantity">Tickets left: <?php	echo $row['Stock'];	?></div>
							
							<div class="price">
								<span class="label">Price: </span>
								<span class="data">$50.00</span>
							</div>
							<?php	if ($userID && !$userAdmin && $row['Stock'] > 0) {	?>
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
					<?php
				} else {
					getTickets($search);
				}
				?>
			</div>
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>