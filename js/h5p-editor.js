(function ($) {
	H5PEditor.init = function () {
		H5PEditor.$ = H5P.jQuery;
		H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
		H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
		H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
		H5PEditor.filesPath = H5PIntegration.editor.filesPath;
		H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;

		H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

		H5PEditor.assets = H5PIntegration.editor.assets;

		H5PEditor.baseUrl = '';

		if (H5PIntegration.editor.nodeVersionId !== undefined) {
			H5PEditor.contentId = H5PIntegration.editor.nodeVersionId;
		}

		var $params = $('input[name="xhfp_params"]');
		var $editor = $("#xhfp_editor");
		var $xhfp_edit_form = $("#form_xhfp_edit_form");

		var library = H5PIntegration.editor.library;

		var params = $params.val();

		var h5peditor = new ns.Editor(library, params, $editor[0]);

		$xhfp_edit_form.submit(function () {
			var params = h5peditor.getParams();
			if (params !== undefined) {
				$params.val(JSON.stringify(params));
			}
		});
	};

	H5PEditor.getAjaxUrl = function (action, parameters) {
		var url = H5PIntegration.editor.ajaxPath + action;

		if (parameters !== undefined) {
			for (var property in parameters) {
				if (parameters.hasOwnProperty(property)) {
					url += '&' + property + '=' + parameters[property];
				}
			}
		}

		return url;
	};

	$(document).ready(H5PEditor.init);
})(H5P.jQuery);
