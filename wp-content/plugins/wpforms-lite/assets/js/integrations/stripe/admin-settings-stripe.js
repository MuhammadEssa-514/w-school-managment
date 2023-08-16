/* global wpforms_admin_settings_stripe, wpforms_admin */

/**
 * Stripe integration settings script.
 *
 * @since 1.8.2
 */
'use strict';

let WPFormsSettingsStripe = window.WPFormsSettingsStripe || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 1.8.2
	 *
	 * @type {object}
	 */
	const el = {};

	/**
	 * Runtime variables.
	 *
	 * @since 1.8.2
	 *
	 * @type {object}
	 */
	const vars = {
		alertTitle: wpforms_admin.heads_up,
		alertContent: wpforms_admin_settings_stripe.mode_update,
		ok: wpforms_admin.ok,
		hideClassName: 'wpforms-hide',
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.8.2
	 */
	const app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.8.2
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.8.2
		 */
		ready: function() {

			app.setup();
			app.bindEvents();
		},

		/**
		 * Setup. Prepare some variables.
		 *
		 * @since 1.8.2
		 */
		setup: function() {

			// Cache DOM elements.
			el.$wrapper             = $( '.wpforms-admin-content-payments' );
			el.$liveConnectionBlock = $( '.wpforms-stripe-connection-status-live' );
			el.$testConnectionBlock = $( '.wpforms-stripe-connection-status-test' );
			el.$testModeCheckbox    = $( '#wpforms-setting-stripe-test-mode' );
		},

		/**
		 * Bind events.
		 *
		 * @since 1.8.2
		 */
		bindEvents: function() {

			el.$wrapper
				.on( 'change', '#wpforms-setting-stripe-test-mode', app.triggerModeSwitchAlert );
		},

		/**
		 * Conditionally show Stripe mode switch warning.
		 *
		 * @since 1.8.2
		 */
		triggerModeSwitchAlert: function() {

			if ( el.$testModeCheckbox.is( ':checked' ) ) {
				el.$liveConnectionBlock.addClass( vars.hideClassName );
				el.$testConnectionBlock.removeClass( vars.hideClassName );
			} else {
				el.$testConnectionBlock.addClass( vars.hideClassName );
				el.$liveConnectionBlock.removeClass( vars.hideClassName );
			}

			if ( $( '#wpforms-setting-row-stripe-connection-status .wpforms-connected' ).is( ':visible' ) ) {
				return;
			}

			$.alert( {
				title: vars.alertTitle,
				content: vars.alertContent,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: vars.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsSettingsStripe.init();
