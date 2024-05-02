(function($) {
	OUWooTimer = function( params )
	{
		this.params 				= params;
		this.id 					= params.id;
		this.timertype				= params.timertype;
		this.timerid				= '#wootimer-' + params.id;
		this.timer_date				= params.timer_date;
		this.timer_format			= params.timer_format;
		this.timer_layout			= params.timer_layout;
		this.timer_labels			= params.timer_labels;
		this.timer_labels_singular 	= params.timer_labels_singular;
		this.redirect_link 			= params.redirect_link;
		this.redirect_link_target 	= params.redirect_link_target;
		this.fixed_timer_action 	= params.fixed_timer_action;
		this.timezone 				= params.time_zone;
		this.timer_exp_text			= "";

		if (this.timezone == 'NULL') {
			this.timezone = null;
		}

		if ( params.timer_exp_text ) {
			this.timer_exp_text	= params.timer_exp_text;
		}

		if( this.timertype == "fixed" ) {
			this._initFixedTimer();
		}

		this._initCountdown();
	};
	OUWooTimer.prototype = {

		_initCountdown: function() {
			fixed_timer_action = this.fixed_timer_action;
			params = this.params;
			var action = '';

			if( this.timertype == "fixed" ) {
				action = this.fixed_timer_action;
			}

			$.cookie( "timer-" + params.id + "expiremsg", null);
			$.cookie( "timer-" + params.id + "redirect", null);
			$.cookie( "timer-" + params.id + "redirectwindow", null);
			$.cookie( "timer-" + params.id + "hide", null);
			$.cookie( "timer-" + params.id + "reset", null);

			$.removeCookie( "timer-" + params.id + "expiremsg");
			$.removeCookie( "timer-" + params.id + "redirect");
			$.removeCookie( "timer-" + params.id + "redirectwindow");
			$.removeCookie( "timer-" + params.id + "hide");
			$.removeCookie( "timer-" + params.id + "reset");


			if( action == "msg") {

				$.cookie( "timer-" + params.id + "expiremsg", params.expire_message, { expires: 365 } );

			} else if( action == "redirect") {

				$.cookie( "timer-" + params.id + "redirect", params.redirect_link, { expires: 365 } );
				$.cookie( "timer-" + params.id + "redirectwindow", params.redirect_link_target, { expires: 365 } );

			} else if( action == "hide") {

				$.cookie( "timer-" + params.id + "hide", "yes", { expires: 365 } );

			} else if( action == 'reset' ) {
				$.cookie( "timer-" + params.id + "reset", "yes", { expires: 365 } );
			}
		},

		_initFixedTimer: function() {

			var dateNow = new Date();

			if( ( dateNow.getTime() - this.timer_date.getTime() ) > 0 ) {
				if( this.fixed_timer_action == 'msg' ) {
					if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
						$( this.timerid ).append(this.timer_exp_text);
					} else {
						$( this.timerid ).countdown({
							until: this.timer_date,
							format: this.timer_format,
							layout: this.timer_layout,
							labels: this.timer_labels.split(","),
							timezone: this.timezone,
				    		labels1: this.timer_labels_singular.split(","),
				        	expiryText: this.timer_exp_text
						});
					}

				} else if( this.fixed_timer_action == 'redirect' ) {

					if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
						window.open( this.redirect_link, this.redirect_link_target );
					} else {
						$( this.timerid ).countdown({
							until: this.timer_date,
							format: this.timer_format,
							layout: this.timer_layout,
							labels: this.timer_labels.split(","),
							timezone: this.timezone,
				    		labels1: this.timer_labels_singular.split(","),
				        	expiryText: this.timer_exp_text
						});
					}

				} else if( this.fixed_timer_action == 'hide' ) {
					if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
						$( this.timerid ).countdown('destroy');
					} else {
						$( this.timerid ).countdown({
							until: this.timer_date,
							format: this.timer_format,
							layout: this.timer_layout,
							labels: this.timer_labels.split(","),
							timezone: this.timezone,
				    		labels1: this.timer_labels_singular.split(","),
				        	expiryText: this.timer_exp_text
						});
					}

				} else {
					$( this.timerid ).countdown({
						until: this.timer_date,
						format: this.timer_format,
						layout: this.timer_layout,
						labels: this.timer_labels.split(","),
						timezone: this.timezone,
			    		labels1: this.timer_labels_singular.split(","),
					});
				}
			} else {
				if( this.fixed_timer_action == 'msg' ) {

					if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
						$( this.timerid ).countdown({
							until: this.timer_date,
							format: this.timer_format,
							layout: this.timer_layout,
							labels: this.timer_labels.split(","),
							timezone: this.timezone,
				    		labels1: this.timer_labels_singular.split(","),
				        	expiryText: this.timer_exp_text,
						});
					} else {
						$( this.timerid ).countdown({
							until: this.timer_date,
							format: this.timer_format,
							layout: this.timer_layout,
							labels: this.timer_labels.split(","),
							timezone: this.timezone,
				    		labels1: this.timer_labels_singular.split(","),
						});
					}
				} else if( this.fixed_timer_action == 'redirect' ) {

					$( this.timerid ).countdown({
						until: this.timer_date,
						format: this.timer_format,
						layout: this.timer_layout,
						labels: this.timer_labels.split(","),
						timezone: this.timezone,
			    		labels1: this.timer_labels_singular.split(","),
			        	expiryText: this.timer_exp_text,
			        	onExpiry: this._redirectCounter
					});

				} else if( this.fixed_timer_action == 'hide' ) {

					$( this.timerid ).countdown({
						until: this.timer_date,
						format: this.timer_format,
						layout: this.timer_layout,
						labels: this.timer_labels.split(","),
						timezone: this.timezone,
			    		labels1: this.timer_labels_singular.split(","),
			        	expiryText: this.timer_exp_text,
			        	onExpiry: this._destroyCounter
					});

				} else {
					$( this.timerid ).countdown({
						until: this.timer_date,
						format: this.timer_format,
						layout: this.timer_layout,
						labels: this.timer_labels.split(","),
						timezone: this.timezone,
			    		labels1: this.timer_labels_singular.split(","),
			        	expiryText: this.timer_exp_text
					});
				}
			}
		},

		_destroyCounter: function() {
			if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
				$( this ).countdown('destroy');
			}
		},

		_redirectCounter: function() {

			redirect_link = $.cookie( $(this)[0].id + "redirect" );
			redirect_link_target = $.cookie( $(this)[0].id + "redirectwindow" );

			if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(-1) ) {
				window.open( redirect_link, redirect_link_target );
			} else {
				return;
			}
		},
	};

})(jQuery);
