$(document).ready(function () {
	// Get H5P frame
	var $frame = $('iframe[id^="h5p-iframe-"]');
	var frame = $frame[0];

	// Next content button
	var $xhfp_next_content = $("#xhfp_next_content_top, #xhfp_next_content_bottom");

	if (frame !== undefined) {
		$frame.on("load", function () {
			var frameWindow = frame.contentWindow;

			// Current H5P content
			var instance = frameWindow.H5P.instances[0];

			if (instance !== undefined) {
				// Override setFinished
				var setFinished = frameWindow.H5P.setFinished;
				frameWindow.H5P.setFinished = function () {
					// Call setFinsihed
					setFinished.apply(this, arguments);

					// Enable next content button
					$xhfp_next_content.removeAttr("disabled");
				};

				// There are contents in which you can not score points. To support this an empty result is saved.
				if (instance.getMaxScore() === 0) {
					frameWindow.H5P.setFinished(instance.contentId, 0, 0);
				}
			}
		});
	}
});
