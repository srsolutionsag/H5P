(function ($) {
	H5PEditor.init = function () {
		H5PEditor.$ = H5P.jQuery;

		H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;

		H5PEditor.baseUrl = "";
		H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
		H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
		H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
		H5PEditor.filesPath = "";

		H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

		H5PEditor.assets = H5PIntegration.editor.assets;

		var $editor = $("#xhfp_editor");

		// prevent remove id edtor
		var $tmp = $("<div></div>").appendTo($editor);

		h5peditor = new ns.Editor("", "", $tmp);

		var $frame = $("iframe", $editor);
		var frame = $frame[0];

		// TODO Hide HUB use buttons and replace list click with details click
		// TODO Remove upload button
		// TODO Hub immer auklappen

		$frame.on("load", function () {
			var frameWindow = frame.contentWindow;

			var appendTo = frameWindow.H5PEditor.LibrarySelector.prototype.appendTo;
			frameWindow.H5PEditor.LibrarySelector.prototype.appendTo = function () {
				// Original appendTo()
				appendTo.apply(this, arguments);

				client = this.selector.client;

				// Remove content create
				client.listeners.select = [];
				frameWindow.H5PEditor.SelectorHub.prototype.getSelectedLibrary = function () {
				};

				// Replace title
				var setPanelTitle = client.setPanelTitle;
				client.setPanelTitle = function () {
					// Original setPanelTitle()
					setPanelTitle.call(client, "HUB");
				}
				client.setPanelTitle();
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
