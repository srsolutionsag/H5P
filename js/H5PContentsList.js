var H5PContentsList = {

	base_link: '',

	init: function (base_link) {
		H5PContentsList.base_link = base_link;
	},

	up: function (event, cid) {
		event.preventDefault();
		xoctWaiter.show();
		var row = $('#xhfp_row_' + cid);
		var ajax_url = H5PContentsList.base_link;
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

	down: function (event, cid) {
		event.preventDefault();
		xoctWaiter.show();
		var row = $('#xhfp_row_' + cid);
		var ajax_url = H5PContentsList.base_link;
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
