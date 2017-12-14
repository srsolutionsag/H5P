(function ($) {
	H5PEditor.init = function () {
		H5PEditor.$ = H5P.jQuery;
		H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
		H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
		H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
		H5PEditor.filesPath = "";
		H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;

		H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

		H5PEditor.assets = H5PIntegration.editor.assets;

		H5PEditor.baseUrl = "";

		var $editor = $("#xhfp_editor");

		// prevent remove id edtor
		var $tmp = $("<div></div>").appendTo($editor);

		h5peditor = new ns.Editor("", "", $tmp);

		var $frame = $("iframe", $editor);
		var frame = $frame[0];

		// TODO Hide HUB use buttons and replace list click with details click

		$frame.on("load", function () {
			var frameWindow = frame.contentWindow;

			var appendTo = frameWindow.H5PEditor.LibrarySelector.prototype.appendTo;
			frameWindow.H5PEditor.LibrarySelector.prototype.appendTo = function () {
				// Append selector
				appendTo.apply(this, arguments);

				// Remove content create
				this.selector.client.listeners.select = [];
				frameWindow.H5PEditor.SelectorHub.prototype.getSelectedLibrary = function () {
				};
			};
		});
	};

	H5PEditor.getAjaxUrl = function (action, parameters) {
		var url = H5PIntegration.editor.ajaxPath + action;

		if (parameters !== undefined) {
			for (var property in parameters) {
				if (parameters.hasOwnProperty(property)) {
					url += "&" + property + "=" + parameters[property];
				}
			}
		}

		return url;
	};

	$(document).ready(H5PEditor.init);
})(H5P.jQuery);
