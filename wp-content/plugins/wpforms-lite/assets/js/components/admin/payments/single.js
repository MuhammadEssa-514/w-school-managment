/* global wpforms_admin */

/**
 * WPForms Single Payment View page.
 *
 * @since 1.8.2
 */

'use strict';

var WPFormsPaymentsSingle = window.WPFormsPaymentsSingle || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.8.2
	 *
	 * @type {object}
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

			app.initTooltips();
			app.paymentDeletionAlert();
		},

		/**
		 * Initialize WPForms admin area tooltips.
		 *
		 * @since 1.8.2
		 */
		initTooltips: function() {

			if ( typeof jQuery.fn.tooltipster === 'undefined' ) {
				return;
			}

			jQuery( '.wpforms-single-payment-tooltip' ).tooltipster( {
				contentCloning: true,
				theme: 'borderless',
				contentAsHTML: true,
				position: 'top',
				maxWidth: 500,
				multiple: true,
				interactive: true,
				debug: false,
			} );
		},

		/**
		 * Alert user before deleting payment.
		 *
		 * @since 1.8.2
		 */
		paymentDeletionAlert: function() {

			$( document ).on( 'click', '.wpforms-payment-actions .button-delete', function( event ) {

				event.preventDefault();

				const url = $( this ).attr( 'href' );

				// Trigger alert modal to confirm.
				$.confirm( {
					title: wpforms_admin.heads_up,
					content: wpforms_admin.payment_delete_confirm,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function() {
								window.location = url;
							},
						},
						cancel: {
							text: wpforms_admin.cancel,
							keys: [ 'esc' ],
						},
					},
				} );
			} );
		},
	};

	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsPaymentsSingle.init();
