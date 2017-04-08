var totalVisibility = false;

function menuVisibility(navVisibility, headerVisibility) {
	if ($(window).width() > 500) {
		$('#mobile-nav, .cross, #nav-header').hide();
		$('.hamburger').show();
	} else {
		//nothing in the top is visible / on screen
		if (!navVisibility && !headerVisibility) {
			//so hamburger re-appears when scrolling up with it open and then down (really fast and during its animation)
			$('#mobile-nav').finish();
			$('#nav-header').show();
		} else {
			// hides the hamburger menu if it is shown
			if ($('#mobile-nav').css('display') == 'block') {
				$('#mobile-nav').slideUp(250, 'linear', function() {
					$('.cross, .hamburger').toggle();
					$('#nav-header').hide();
				});
			} else {
				//so it doesnt delay hiding when hamburger isnt open
				$('#nav-header').hide();
			}
		}
	}
}

$(document).ready(function() {
	/* Hides the save button on the cart page */
	$('.updateDiv').hide();
	$('.amount-label').css({
		display: 'table-cell',
	});
	
	$('.amountDiv input[type="number"]').css({
		'text-align': 'right',
	});
	
	$("nav").sticky({
 		topSpacing: 0,
		responsiveWidth: true,
	});
	
	/* Sets the height because it may vary browser to browser */
	$('#nav-header').css({
		height: $('nav li:last-child').height() + 'px',
	});
	
	$('#mobile-nav').css({
		top: $('nav li:last-child').height() + 'px',
	});
	
	$('.cross').hide();
	
	old_visible = $('nav li:last-child').visible(totalVisibility);
	
	//runs the handler on scroll, resize and load (unsure if load actually works)
	$(window).on('DOMContentLoaded load resize scroll', function() {
			menuVisibility($('nav li:last-child').visible(totalVisibility), $('header').visible(true));
		}
	);
	
	if ($(window).width() <= 500) {
		//manually trigger it so if the page is loaded to a specific element it will show up without scrolling
		menuVisibility(old_visible, $('header').visible(true));
	}
	
	// from http://stackoverflow.com/a/12029259
	// adds a time rule (regex is slightly modified, original was HH:MM:SS)
	$.validator.addMethod("time24", function(value, element) { 
		return /([01]?[0-9]|2[0-3])(:[0-5][0-9])$/.test(value);
	}, "Invalid time format.");
	
	//from http://stackoverflow.com/a/6302576
	//adds minimum number rule
	$.validator.addMethod('minStrict', function (value, el, param) {
		return value > param;
	});
	
	//only trys to apply the slideshow when the element is present and there are more than one things in it
	if ($('#slideshow').length && $('#slideshow li').length > 1) {
		$('#slideshow').bjqs({
			'height' : 320,
			'width' : 320,
			'animspeed': 5000,
			'responsive' : true,
			'usecaptions' : true,
		});
	}
	
 });

 
 $(window).load(function(){
	
	/* Clones the nav so it can be used in the hamburger later */
	$('#nav').clone().attr('id', '').appendTo('#mobile-nav');
	
	// opens/closes the hamburger menu
	$('#nav-header').click(function() {
		$('#mobile-nav').slideToggle(
			500, 'linear', function() {
				$('.cross, .hamburger').toggle();
			}
		);
	});
	
	$('#login').validate({
		rules: {
			name: "required",
			password: "required",
		}, 
		messages: {
			name: "Username or Email is required",
			password: "Password is required",
		}
	});
	
	$('#register').validate({
		rules: {
			name: "required",
			email: {
				required: true,
				email: true,
			},
			password: "required",
			confirmPassword: {
				required: true,
				 equalTo: "#password",
			}
		}, 
		messages: {
			name: "Username is required",
			email: {
				required: "Email is required",
				email: "Must be a valid email",
			},
			password: "Password is required",
			confirmPassword: {
				required: "Password must be confirmed",
				equalTo: "Passwords do not match",
			},
		}
	});
	
	$('#add, #update').validate({
		rules: {
			band: "required",
			location: "required",
			date: {
				required: true,
				date: true,
			},
			time: {
				required: true,
				time24: true,
			},
			price: {
				required: true,
				number: true,
				minStrict: 0,
			},
			amount: {
				required: true,
				digits: true,
				minStrict: 0,
			}
		}, 
		messages: {
			band: "Band name is required",
			location: "Location is required",
			date: {
				required: "Date is required",
				date: "Must be a valid date",
			},
			time: {
				required: "Time is required",
				time24: "Must be a valid time",
			},
			price: {
				required: "Price is required",
				number: "Price must be a number",
				minStrict: "Price must be more than 0",
			},
			amount: {
				required: "Amount is required",
				digits: "Amount must be a whole number",
				minStrict: "Amount must be more than 0",
			}
		}
	});
	
	// Changes the stock amount without reloading
	$('#add #amount, #update #amount').change(function() {
		$(this).parents('form').find('#stock').val($(this).val());
	});
	
	// tooltips everything except search
	$('body input:not(#search)').tooltip();
	
	// On any change of any amount input
	$('.amountDiv input[type="number"]').change(function() {
		// the value of the input
		var value = $(this).val();
		// the input itself (needed because the success function can't access it using $(this))
		var input = $(this);
		$.post('required/ajax.php', {
			action: 'cart',
			ticket: $(this).parents('.cart-ticket').attr('id'),
			amount: value,
		}, function(data) {
			if (data == 'Success') {
				var priceInfo = input.parents('.price-info');
				var price = priceInfo.find('.price .data').text().replace('$', '');
				var subTotal = formatMoney(price * value);
				
				// Sets the sub total so the user can see it
				priceInfo.find('.sub-total .data').text('$' + subTotal);
				
				// Calculates out the total
				var total = 0;
				$('.sub-total .data').each(function() {
					total += Number($(this).text().replace(/[$,]/g, ''));
				});
				// Sets the grand total so the user can see it
				$('.total span').text('$' + formatMoney(total));
			}
		});
	});
	
	$('#search-form').after('<div id="live-search"></div>');
	
	// On change of search
	$('#search').keyup(function() {
		if ($(this).val() == '') {
			// clears the search
			$('#live-search').html('');
		} else {
			$.post('required/ajax.php', {
				action: 'search',
				search: $(this).val(),
			}, function(data) {
				console.log(data);
				$('#live-search').html(data);
			});
		}
	});
	
	//couldnt get it to hide properly
	/*$('#header-sidebar').focusout(function(evt) {
		console.log(evt.target.id);
		if (evt.target.id != '#live-search') {
			$('#live-search').hide();
		}
	}).focusin(function(evt) {
		console.log(evt.target.id);
		if ($('#live-search').is(':focus') || $('#search').is(':focus')){
			$('#live-search').show();
		}
	});*/
});

// from http://stackoverflow.com/a/14428340
function formatMoney(n) {
	n = Number(n);
	return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
}
