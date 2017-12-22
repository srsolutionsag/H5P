$(document).ready(function () {
	// Get H5P frame
	var $frame = $('iframe[id^="h5p-iframe-"]');
	var frame = $frame[0];

	// TODO Supports for div

	if (frame !== undefined) {
		$frame.on("load", function () {
			frameWindow = frame.contentWindow;

			// Current H5P content
			var instance = frameWindow.H5P.instances[0];

			if (instance !== undefined) {
				/*// Override setFinished
				var setFinished = frameWindow.H5P.setFinished;
				frameWindow.H5P.setFinished = function () {
					// Call setFinsihed
					setFinished.apply(this, arguments);

					// ...
				};
			}
		});
	}
});
