/**
 * xoctWaiter
 *
 * GUI-Overlay
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
var xoctWaiter = {
	possible_types: ['waiter', 'percentage'],
	type: 'waiter',
	count: 0,
	timer: null,

	init: function (type) {
		this.type = type ? type : this.type;
		if (this.type == 'waiter') {
			console.log('xoctWaiter: added xoct_waiter to body');
			$('body').append('<div id="xoct_waiter" class="xoct_waiter"></div>')
		} else {
			console.log('xoctWaiter: added xoct_percentage to body');
			$('body').append('<div id="xoct_waiter" class="xoct_percentage">' +
				'<div class="progress" >' +
				'<div id="xoct_progress" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' +
				'</div></div></div>')
		}
	},

	show: function () {
		if (this.count == 0) {
			this.timer = setTimeout(function () {
				$('#xoct_waiter').show();
			}, 10);

		}
		this.count = this.count + 1;
	},
	/**
	 *
	 * @param type
	 */
	reinit: function (type) {
		var type = type ? type : this.type;
		this.count = 0;

		$('#xoct_waiter').attr('id', 'xoct_waiter2');
		this.init(type);
		$('#xoct_waiter2').remove();
	},

	hide: function () {
		this.count = this.count - 1;
		if (this.count == 0) {
			window.clearTimeout(this.timer);
			$('#xoct_waiter').fadeOut(200);
		}
	},
	/**
	 * @param percent
	 */
	setPercentage: function (percent) {
		$('#xoct_progress').css('width', percent + '%').attr('aria-valuenow', percent);
	},
	/**
	 * @param dom_selector_string
	 */
	addListener: function (dom_selector_string) {
		var self = this;
		$(document).ready(function () {
			$(dom_selector_string).on("click", function () {

				self.show();
			});
		});
	},
	addLinkOverlay: function (dom_selector_string) {
		var self = this;
		console.log('xoctWaiter: registred LinkOverlay: ' + dom_selector_string);
		$(document).ready(function () {
			$(dom_selector_string).on("click", function (e) {
				e.preventDefault();
				console.log('xoctWaiter: clicked on registred link');
				self.show();
				var href = $(this).attr('href');
				setTimeout(function () {
					document.location.href = href;
				}, 1000);
			});
		});
	},
};