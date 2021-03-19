/*
* Author:      Marco Kuiper (http://www.marcofolio.net/)
*/
import '../css/style.css';

$(document).ready(function()
{
	const animationTime = 500;
	// Variable to store the colours
	const colours = ["#ff0707", "#f88446", "#f9d218", "#a0da72", "#259225"];
	const badge_classes = ['rate-low', 'rate-poor', 'rate-medium', 'rate-positive', 'rate-very-positive'];

	$('form span.sv-field-suffix ul#rating').each(function() {
		const $self = $(this);
		const initIndex = $self.closest('div.form-group').find('input').val();
		let selectedIndex = initIndex - 1;
		let selectedText = '';

		var ratingInfobox = $("<div />")
			.attr("class", "ratinginfo")
			.insertAfter($(this));

		// Function to colorize the right ratings
		var colourizeRatings = function(nrOfRatings) {
			$self.find("li a").each(function() {
				if($(this).parent().index() <= nrOfRatings) {
					//$(this).stop().animate({ backgroundColor : "#" + colours[nrOfRatings] } , animationTime);
					$(this).stop().css({'background-color': colours[nrOfRatings], 'border-color': colours[nrOfRatings]});
				}
			});
		};

		colourizeRatings(selectedIndex);

		// Handle the hover events
		$self.find("li").hover(function() {
			ratingInfobox
				.empty()
				.stop()
				.animate({ opacity : 1 }, animationTime);

			$('<span class="badge ' + badge_classes[$(this).find("a").parent().index()]+'"/>')
				.html($(this).find('a').html())
				.appendTo(ratingInfobox);

			// Call the colourize function with the given index
			$self.find("li").find("a").stop().css({'background-color': '#fff', 'border-color': '#41a3fe'});
			colourizeRatings($(this).find("a").parent().index());
		}, function() {

			ratingInfobox.find('span').html('');

			// Restore all the rating to their original colours
			// $("#rating li a").stop().animate({ backgroundColor : "#333" } , animationTime);
			$self.find("li a").stop().css({'background-color': '#fff', 'border-color': '#41a3fe'});

			colourizeRatings(selectedIndex);
			ratingInfobox.find('span').removeAttr('class').addClass('badge ' + badge_classes[selectedIndex]).html(selectedText);

		});

		// Prevent the click event and show the rating
		$self.find("li a").click(function(e) {
			e.preventDefault();
			$(this).closest('label').find('input').val($(this).parent().index() + 1);
			selectedIndex = $(this).parent().index();
			selectedText = $(this).html();
		});
	});

	$('div.readonly-reading-wrapper').each(function() {
		const nrOfRatings = $(this).data('selected-value') - 1;
		$(this).find('ul#rating li a').each(function() {
			if($(this).parent().index() <= nrOfRatings) {
				//$(this).stop().animate({ backgroundColor : "#" + colours[nrOfRatings] } , animationTime);
				$(this).stop().css({'background-color': colours[nrOfRatings], 'border-color': colours[nrOfRatings]});
				const label = $(this).html();
				$(this).closest('div.readonly-reading-wrapper').find('span').removeAttr('class').addClass('badge ' + badge_classes[nrOfRatings]).html(label);
			}
		});
	});
});