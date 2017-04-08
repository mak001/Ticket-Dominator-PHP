<?php
require 'required/connect.php';
require 'required/layout.php';
require 'required/methods.php';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ticket Dominator - Home</title>
		<?php
		getStyleSheets();
		getJavaScript();
		
		//gets if the page should show logout message
		if ($_GET['logout'] == 'false') {
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$('main').append('<div class="overlay">' +
						'<div class="overlay-content">' +
							'<h2>You have successfully logged out.</h2>' +
							'<div class="button close">Close</div>' +
						'</div>' +
					'</div>');
					
					//.click() wont work because its generated
					$('main').on('click', '.overlay', function() {
						$(this).hide();
						//redirects so any page reloading doesn't show it again
						window.location = "index.php";
					});
				});
			</script>
			<?php
		}
		?>
		
	</head>
	<body>

		<?php

		getHeader();
		getNav();
		?>
		<!-- Content -->
		<main>
			<div id="slideshow">
				<ul class="bjqs">
					<?php
		
					$list = glob('images/bands/*.{jpg, jpeg,gif,png}', GLOB_BRACE);
					//if count > 5 return 5, else return count
					$number = count($list) > 5 ? 5 : count($list);
					for ($i = 0; $i < $number; $i++) {
						preg_match('/.*\/(.*).(png|jpg|jpeg|gif)/', $list[$i], $matches);
						$bandName = str_replace('_', ' ', $matches[1]);;
						?>
						<li>
							<img src="<?php	echo $list[$i];	?>" alt="<?php	echo $bandName;	?>" title="<?php	echo $bandName;	?>"/>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			
		</main>
		<?php
		getFooter();
		?>
	</body>
</html>