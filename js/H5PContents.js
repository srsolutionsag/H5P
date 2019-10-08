$(document).ready(function () {
	// Get H5P frame
	var $frame = $('iframe[id^="h5p-iframe-"]');
	var frame = $frame[0];
	var frameWindow;
	var contentInstance;

	if (frame !== undefined) {
		// iFrame
		$frame.on("load", function () {
			frameWindow = frame.contentWindow;

			o();
		});
	} else {
		// Div
		frameWindow = window;
		$(frameWindow).on("load", o);
	}

	/**
	 *
	 */
	function o() {
		// Current H5P content
		contentInstance = frameWindow.H5P.instances[0];

		if (contentInstance !== undefined) {
			/*// Override setFinished
			var setFinished = frameWindow.H5P.setFinished;
			frameWindow.H5P.setFinished = function () {
				// Call setFinsihed
				setFinished.apply(this, arguments);

				// ...
			};*/
		}
	}

	// Fix H5P contents in accordions
	$(".il_HAccordionToggleDef, .il_VAccordionToggleDef").click(function() {
		$(window).trigger("resize");
	});
});
