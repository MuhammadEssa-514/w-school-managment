/* global wpforms_builder, wpforms_builder_stripe */

/**
 * Stripe builder function.
 *
 * @since 1.8.2
 */
'use strict';

var WPFormsStripe = window.WPFormsStripe || ( function( document, window, $ ) {

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

			app.bindUIActions();

			$( app.ready );
		},

		/**
		 * Initialized once the DOM is fully loaded.
		 *
		 * @since 1.8.2
		 */
		ready: function() {

			app.settingsDisplay();
			app.settingsConditions();
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.8.2
		 */
		bindUIActions: function() {

			$( document )
				.on( 'wpformsFieldUpdate', app.settingsDisplay )
				.on( 'wpformsFieldUpdate', app.settingsConditions )
				.on( 'wpformsSaved', app.requiredFieldsCheck )
				.on( 'wpformsFieldDelete', app.disableNotifications );
		},

		/**
		 * Toggles visibility of the Stripe settings.
		 *
		 * If a credit card field has been added then reveal the settings,
		 * otherwise hide them.
		 *
		 * @since 1.8.2
		 */
		settingsDisplay: function() {

			const $alert   = $( '#stripe-credit-card-alert' );
			const $content = $( '#stripe-provider' );

			// Check if any Credit Card fields were added to the form.
			const ccFieldsAdded = wpforms_builder_stripe.field_slugs.filter( function( fieldSlug ) {

				const $el = $( '.wpforms-field-option-' + fieldSlug );

				return $el.length ? $el : null;
			} );

			if ( ccFieldsAdded.length ) {
				$alert.hide();
				$content.find( '.wpforms-panel-field, .wpforms-conditional-block-panel, h2' ).show();
			} else {
				$alert.show();
				$content.find( '.wpforms-panel-field, .wpforms-conditional-block-panel, h2' ).hide();
				$content.find( '#wpforms-panel-field-stripe-enable' ).prop( 'checked', false );
			}
		},

		/**
		 * Toggles the visibility of the related settings.
		 *
		 * @since 1.8.2
		 */
		settingsConditions: function() {

			$( '#wpforms-panel-field-stripe-enable' ).conditions( {
				conditions: {
					element: '#wpforms-panel-field-stripe-enable',
					type: 'checked',
					operator: 'is',
				},
				actions: {
					if: {
						element: '.wpforms-panel-content-section-stripe-body',
						action: 'show',
					},
					else: {
						element: '.wpforms-panel-content-section-stripe-body',
						action:  'hide',
					},
				},
				effect: 'appear',
			} );

			$( '#wpforms-panel-field-stripe-recurring-enable' ).conditions( {
				conditions: {
					element: '#wpforms-panel-field-stripe-recurring-enable',
					type: 'checked',
					operator: 'is',
				},
				actions: {
					if: {
						element: '#wpforms-panel-field-stripe-recurring-period-wrap,#wpforms-panel-field-stripe-recurring-conditional_logic-wrap,#wpforms-conditional-groups-payments-stripe-recurring,#wpforms-panel-field-stripe-recurring-email-wrap,#wpforms-panel-field-stripe-recurring-name-wrap',
						action: 'show',
					},
					else: {
						element: '#wpforms-panel-field-stripe-recurring-period-wrap,#wpforms-panel-field-stripe-recurring-conditional_logic-wrap,#wpforms-conditional-groups-payments-stripe-recurring,#wpforms-panel-field-stripe-recurring-email-wrap,#wpforms-panel-field-stripe-recurring-name-wrap',
						action:  'hide',
					},
				},
				effect: 'appear',
			} );
		},

		/**
		 * On form save notify users about required fields.
		 *
		 * @since 1.8.2
		 */
		requiredFieldsCheck: function() {

			if (
				! $( '#wpforms-panel-field-stripe-enable' ).is( ':checked' ) ||
				! $( '#wpforms-panel-field-stripe-recurring-enable' ).is( ':checked' )
			) {
				return;
			}

			if ( $( '#wpforms-panel-field-stripe-recurring-email' ).val() ) {
				return;
			}

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.stripe_recurring_email,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Disable notifications.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} e Event object.
		 * @param {number} id Field ID.
		 * @param {string} type Field type.
		 */
		disableNotifications: function( e, id, type ) {

			if ( type === 'stripe-credit-card' ) {

				let $notificationWrap = $( '.wpforms-panel-content-section-notifications [id*="-stripe-wrap"]' );

				$notificationWrap.find( 'input[id*="-stripe"]' ).prop( 'checked', false );
				$notificationWrap.addClass( 'wpforms-hidden' );
			}
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsStripe.init();
