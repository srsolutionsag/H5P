/**
 * @type {Object}
 */
var ilH5PContentsTable = {
	/**
	 * @type {string}
	 */
	base_link: '',

	/**
	 * @param {string} base_link
	 */
	init: function (base_link) {
		ilH5PContentsTable.base_link = base_link;
	},

	/**
	 * @param {Event} event
	 * @param {string} cid
	 */
	up: function (event, cid) {
		event.preventDefault();
		xoctWaiter.show();
		var row = $('#xhfp_row_' + cid);
		var ajax_url = ilH5PContentsTable.base_link;
		$.ajax({
			url: ajax_url,
			type: "GET",
			data: {
				"cmd": "moveContentUp",
				"xhfp_content": cid
			}
		}).always(function (data, textStatus, jqXHR) {
			row.insertBefore(row.prev());
			xoctWaiter.hide();
		});
	},

	/**
	 * @param {Event} event
	 * @param {string} cid
	 */
	down: function (event, cid) {
		event.preventDefault();
		xoctWaiter.show();
		var row = $('#xhfp_row_' + cid);
		var ajax_url = ilH5PContentsTable.base_link;
		$.ajax({
			url: ajax_url,
			type: "GET",
			data: {
				"cmd": "moveContentDown",
				"xhfp_content": cid
			}
		}).always(function (data, textStatus, jqXHR) {
			row.insertAfter(row.next());
			xoctWaiter.hide();
		});
	}
};
