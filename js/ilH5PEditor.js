(function ($) {
	H5PEditor.init = function () {
		H5PEditor.$ = H5P.jQuery;

		H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;

		H5PEditor.baseUrl = "";
		H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
		H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
		H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
		H5PEditor.filesPath = H5PIntegration.editor.filesPath;

		H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

		H5PEditor.assets = H5PIntegration.editor.assets;

		H5PEditor.contentId = H5PIntegration.editor.contentId;

		var $library = $('input[name="xhfp_library"]');
		var $params = $('input[name="xhfp_params"]');
		var $editor = $("#xhfp_editor");
		var $form = $("#form_xhfp_edit_form");
		var $toolbar = $("#xhfp_edit_toolbar");
		var $toolbar_tutorial = $("#xhfp_edit_toolbar_tutorial");
		var $toolbar_example = $("#xhfp_edit_toolbar_example");
		var $editor_error = $("#xhfp_editor_error");

		var library = $library.val();
		var params = $params.val();

		// prevent remove id edtor
		var $tmp = $("<div></div>").appendTo($editor);

		var h5peditor = new ns.Editor(library, params, $tmp);

		var $frame = $("iframe", $editor);
		var frame = $frame[0];

		$frame.on("load", function () {
			var frameWindow = frame.contentWindow;

			if (library !== "") {
				// Library already selected. Disable selector
				var appendTo = frameWindow.H5PEditor.LibrarySelector.prototype.appendTo;
				frameWindow.H5PEditor.LibrarySelector.prototype.appendTo = function () {
					// Original appendTo()
					appendTo.apply(this, arguments);
					// Force disable selector
					var attr = this.$selector.attr;
					this.$selector.attr = function (name) {
						if (name === "disabled") {
							attr.call(this, "disabled", true);
						} else {
							// Original attr()
							attr.apply(this, arguments);
						}
					}
				};
			}

			var loadSemantics = frameWindow.H5PEditor.LibrarySelector.prototype.loadSemantics;
			frameWindow.H5PEditor.LibrarySelector.prototype.loadSemantics = function (library) {
				// Original loadSemantics()
				loadSemantics.apply(this, arguments);

				// Hide error message
				$editor_error.addClass("ilNoDisplay");

				// Tutorial and example button
				$toolbar.addClass("ilNoDisplay");
				$toolbar_tutorial.addClass("ilNoDisplay");
				$toolbar_example.addClass("ilNoDisplay");

				if (typeof library === "string" && library !== "" && library !== "-") {
					var get_url = H5PEditor.getAjaxUrl("getTutorial", {
						library: h5peditor.getLibrary()
					});

					$.get(get_url, function (data) {
						data = JSON.parse(data);

						var button_count = 0;

						if ("tutorial_urL" in data) {
							$toolbar_tutorial.attr("href", data.tutorial_urL);

							$toolbar_tutorial.removeClass("ilNoDisplay");

							button_count++;
						}

						if ("example_url" in data) {
							$toolbar_example.attr("href", data.example_url);

							$toolbar_example.removeClass("ilNoDisplay");

							button_count++;
						}

						if (button_count > 0) {
							$toolbar.removeClass("ilNoDisplay");
						}
					});
				}
			};
		});

		$form.submit(function () {
			$editor_error.addClass("ilNoDisplay");

			var $button = $form.find('input[type="submit"]:focus, input[type="SUBMIT"]:focus');

			// Only submit button, no cancel button
			if ($button.attr("id") === "xhfp_edit_form_submit" || $button.attr("id") === "xhfp_edit_form_submit_top") {
				var library = h5peditor.getLibrary();
				var params = h5peditor.getParams();

				// Prevent submit when errors
				var errors = $(".h5p-errors", frame.contentDocument).filter(function (i, el) {
					return ($(el).html() !== "");
				});
				if (errors.length > 0) {
					// General error message
					$editor_error.removeClass("ilNoDisplay");

					// Scroll to first h5p content error message
					$("html").scrollTop($(errors[0]).offset().top);

					return false;
				}

				if (typeof library === "string" && library !== "" && library !== "-" && typeof params === "object") {
					$library.val(library);

					$params.val(JSON.stringify(params));
				}
			}

			return true;
		});
	};

	H5PEditor.getAjaxUrl = function (action, parameters) {
		var url = H5PIntegration.editor.ajaxPath + action;

		if (parameters !== undefined) {
			Object.keys(parameters).forEach(function (property) {
				url += "&" + property + "=" + parameters[property];
			});
		}

		return url;
	};

	$(document).ready(H5PEditor.init);
})(H5P.jQuery);