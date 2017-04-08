<?php

//gets all the style sheets
function getStyleSheets() {
	?>
	<link rel="stylesheet" href="jquery-ui.min.css" type="text/css"/>
	<link rel="stylesheet" href="style.css" type="text/css"/>
	<?php
	generateBandCss();
}

//gets all the javascript files
function getJavaScript() {
	?>
	<script type="text/javascript" src="js/jquery-2.2.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.11.4.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/additional-methods.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.visible.min.js"></script>
	<script type="text/javascript" src="js/jquery.sticky.js"></script>
	<script type="text/javascript" src="js/bjqs-1.3.min.js"></script>
	
	<script type="text/javascript" src="js/script.js"></script>
	<?php
}

//generates the header
function getHeader() {
	global $userID, $userName, $userAdmin;
	?>
	<header>
		<a href="index.php">
			<hgroup>
				<h1>Ticket Dominator</h1>
				<h2>tag line.</h2>
			</hgroup>
		</a>
		<div id="header-sidebar" method="get" action="tickets.php">
			<form action="tickets.php" method="get" id="search-form">
				<input type="text" name="search" id="search" title="Enter search terms here"/><!--
				submit does not have a name so it does not get sent
			 --><input type="submit" id="search-submit" value="Search"/>
			</form>	
		<?php	if($userID || $userName) {	?>
			<div id="user-logout">
				Logged in as <span class="user-name"><?php	echo $userName;	?></span>
				<br>
				<a href="<?php echo($_SERVER["PHP_SELF"]);?>?logout=true" id="logout" class="button">Logout</a>
			</div>
			<?php
		}
	echo '</div></header>';
}

//generates the Nav bar
function getNav() {
	global $userID, $userAdmin;
	?>	
	<nav>
		<ul id="nav">
			<!-- Always visible -->
			<li><a href="index.php">Home</a></li>
			<li><a href="tickets.php">Tickets</a></li>	
			
			<?php	if ($userID && $userAdmin) {	?>
				<!-- only if admin -->
				<li><a href="modify.php?action=add">Add ticket</a></li>
				
				<?php	} else if ($userID) {	?>
				<!-- only if logged in -->
				<li><a href="cart.php">Cart</a></li>
				<li><a href="history.php">Past Orders</a></li>
				
				<?php	} else {	?>
					<!-- only visible when not logged in -->
					<li><a href="login.php">Login</a></li>
					<li><a href="register.php">Register</a></li>
				<?php			
			}
			?>
		</ul>
		<div id="nav-header">
			Menu
			<button class="hamburger">&#9776;</button>
			<button class="cross">&#735;</button>
		</div>
		<div id="mobile-nav">
		</div>
	</nav>
	<?php
}


// generates the footer
function getFooter() {
	?>	
	<footer>
		<div id="footer-container">
			<p>Copyright</p>
		</div>
	</footer>
	<?php
}
?>