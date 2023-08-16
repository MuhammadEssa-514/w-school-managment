/* global elementor, elementorCommon, wpformsElementorVars, elementorFrontend, Choices */

'use strict';

/**
 * WPForms integration with Elementor (modern widget).
 *
 * @since 1.8.3
 */
var WPFormsElementorModern = window.WPFormsElementorModern || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.8.3
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.8.3
		 */
		init: function() {

			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.8.3
		 */
		events: function() {

			// Widget events.
			$( window )
				.on( 'elementor/frontend/init', function( event, id, instance ) {

					elementor.channels.editor.on( 'elementorWPFormsResetStyleSettings', app.confirmResetStyleSettings );
					elementor.channels.editor.on( 'section:activated', app.checkForLeadForms );
					elementor.hooks.addAction( 'panel/open_editor/widget/wpforms', app.widgetPanelOpen );
					elementorFrontend.hooks.addAction( 'frontend/element_ready/wpforms.default', app.widgetReady );
				} );
		},

		/**
		 * On section change event handler.
		 *
		 * @since 1.8.3
		 *
		 * @param {string} sectionName The current section name.
		 * @param {object} editor      Editor instance.
		 *
		 */
		checkForLeadForms( sectionName, editor ) {

			if ( sectionName !== 'field_styles' || editor.model.attributes.widgetType !== 'wpforms' ) {
				return;
			}

			let $panelContent = editor.$childViewContainer[0];
			let widgetView	  = editor.options.editedElementView.$el[0];
			let formId		  = editor.model.attributes.settings.attributes.form_id;
			let $form         = $( widgetView ).find( `#wpforms-${formId}` );

			if ( $form.length === 0  ) {
				return;
			}

			if ( $form.hasClass( 'wpforms-lead-forms-container' ) ) {
				$( $panelContent ).addClass( 'wpforms-elementor-disabled' );
				$( $panelContent ).find( '.wpforms-elementor-lead-forms-notice' ).css( 'display', 'block' );
			}
		},

		/**
		 * Initialize widget controls when widget is activated.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} panel Panel object.
		 * @param {object} model Model object.
		 * @param {object} view  View object.
		 */
		widgetPanelOpen: function( panel, model, view ) {

			const settingsModel = model.get( 'settings' );

			// Apply settings from the textarea.
			settingsModel.on( 'change:copyPasteJsonValue', ( changedModel ) => {
				app.pasteSettings( changedModel );
			} );

			// Change style settings.
			settingsModel.on( 'change', ( changedModel ) => {
				app.changeStyleSettings( changedModel, view );

				if ( ! changedModel.changed.copyPasteJsonValue && ! changedModel.changed.form_id ) {
					app.updateCopyPasteContent( changedModel );
				}
			} );
		},

		/**
		 * Widget ready events.
		 *
		 * @since 1.8.3
		 *
		 * @param {jQuery} $scope The current element wrapped with jQuery.
		 */
		widgetReady: function( $scope ) {

			let formId = $scope.find( '.wpforms-form' ).data( 'formid' );

			app.updateAccentColors( $scope, formId );
			app.loadChoicesJS( $scope, formId );
			app.initRichTextField( formId );
		},

		/**
		 * Show the reset style settings confirm popup.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} event Event object.
		 */
		confirmResetStyleSettings: function( event ) {

			elementorCommon.dialogsManager.createWidget( 'confirm', {
				message: wpformsElementorVars.strings.reset_settings_confirm_text,
				headerMessage: wpformsElementorVars.strings.reset_style_settings,
				strings: {
					confirm: wpformsElementorVars.strings.continue,
					cancel: wpformsElementorVars.strings.cancel,
				},
				defaultOption: 'cancel',
				onConfirm: function onConfirm() {
					app.resetStyleSettings( event );
				},
			} ).show();
		},

		/**
		 * Reset style settings button handler.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} event Event object.
		 */
		resetStyleSettings: function( event ) {

			let model           = event.options.elementSettingsModel;
			let container       = event.options.container;
			let widgetContainer = container.view.$el[0];
			let defaults        = model.defaults;
			let styleSettings   = app.getStyleAttributesKeys();
			let globals         = model.get( '__globals__' );
			let defaultValues   = {};
			let $widgetStyles   = $( widgetContainer ).find( '#wpforms-css-vars-root' ).next( 'style' );

			// Prepare default style settings values.
			styleSettings.forEach( function( element ) {
				defaultValues[ element ] = defaults[element];
			} );

			// Reset global style settings.
			if ( globals ) {
				elementorCommon.api.run( 'document/globals/settings', {
					container: container,
					settings: {},
					options: {
						external: true,
						render: false,
					},
				} );
			}

			// Reset widget settings to default.
			elementorCommon.api.run( 'document/elements/settings', {
				container,
				options: {
					external: true,
				},
				settings: defaultValues,
			} );

			// Reset CSS vars for widget container and form specific <style> tag.
			widgetContainer.style = '';
			$widgetStyles.text( '' );
		},

		/**
		 * Change style setting handler.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} changedModel Changed model.
		 * @param {object} view         View.
		 */
		// eslint-disable-next-line complexity
		changeStyleSettings: function( changedModel, view ) {

			let widgetContainer = view.$el[0];
			let parsedAtts      = changedModel.parseGlobalSettings( changedModel );

			for ( let element in changedModel.changed ) {

				if ( ! app.getStyleAttributesKeys().includes( element ) ) {
					view.allowRender = element !== 'copyPasteJsonValue';
					continue;
				}

				view.allowRender = false;
				let attrValue    = app.getParsedValue( element, parsedAtts );
				let property     =  element.replace( /[A-Z]/g, letter => `-${letter.toLowerCase()}` );
				let borderRadiusItems = [ 'fieldBorderRadius', 'buttonBorderRadius' ];

				if ( borderRadiusItems.includes( element ) ) {
					attrValue = attrValue + 'px';
				}

				switch ( property ) {
					case 'field-size':
					case 'label-size':
					case 'button-size':
						for ( const key in wpformsElementorVars.sizes[ property ][ attrValue ] ) {
							widgetContainer.style.setProperty(
								`--wpforms-${property}-${key}`,
								wpformsElementorVars.sizes[ property ][ attrValue ][ key ],
							);
						}

						break;

					default:
						widgetContainer.style.setProperty( `--wpforms-${property}`, attrValue );
				}
			}
		},

		/**
		 * Copy/paste widget settings.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} model Settings model.
		 */
		updateCopyPasteContent: function( model ) {

			let styleSettings = app.getStyleAttributesKeys();
			let content       = {};
			let atts          = model.parseGlobalSettings( model );

			styleSettings.forEach( function( element ) {
				content[element] = app.getParsedValue( element, atts );
			} );

			model.setExternalChange( 'copyPasteJsonValue', JSON.stringify( content ) );
		},

		/**
		 * Paste settings.
		 *
		 * @since 1.8.3
		 *
		 * @param {object} model Settings model.
		 */
		pasteSettings: function( model ) {

			let copyPasteJsonValue  = model.changed.copyPasteJsonValue;
			let pasteAttributes = app.parseValidateJson( copyPasteJsonValue );

			if ( ! pasteAttributes ) {

				elementorCommon.dialogsManager.createWidget( 'alert', {
					message: wpformsElementorVars.strings.copy_paste_error,
					headerMessage: wpformsElementorVars.strings.heads_up,
				} ).show();

				this.updateCopyPasteContent( model );

				return;
			}

			model.set( pasteAttributes );
		},


		/**
		 * Parse and validate JSON string.
		 *
		 * @since 1.8.3
		 *
		 * @param {string} value JSON string.
		 *
		 * @returns {boolean|object} Parsed JSON object OR false on error.
		 */
		parseValidateJson: function( value ) {

			if ( typeof value !== 'string' ) {
				return false;
			}

			let atts;

			try {
				atts = JSON.parse( value );
			} catch ( error ) {
				atts = false;
			}

			return atts;
		},

		/**
		 * Get list of the style attributes keys.
		 *
		 * @since 1.8.3
		 *
		 * @returns {Array} Style attributes keys.
		 */
		getStyleAttributesKeys: function() {

			return [
				'fieldSize',
				'fieldBorderRadius',
				'fieldBackgroundColor',
				'fieldBorderColor',
				'fieldTextColor',
				'labelSize',
				'labelColor',
				'labelSublabelColor',
				'labelErrorColor',
				'buttonSize',
				'buttonBorderRadius',
				'buttonBackgroundColor',
				'buttonTextColor',
			];
		},

		/**
		 * Get parsed attribute value.
		 *
		 * @since 1.8.3
		 *
		 * @param {string} attrName   Attribute name.
		 * @param {object} parsedAtts Parsed attributes.
		 *
		 * @returns {string} Attribute value.
		 */
		getParsedValue: function( attrName, parsedAtts ) {

			let rawValue = parsedAtts[ attrName ];
			let value;

			if ( typeof rawValue === 'undefined' ) {
				value = false;
			} else if ( typeof rawValue === 'object' && Object.prototype.hasOwnProperty.call( rawValue, 'value' ) ) {
				value = rawValue.value;
			} else {
				value = rawValue;
			}

			return value;
		},

		/**
		 * Initialize RichText field.
		 *
		 * @since 1.8.3
		 *
		 * @param {int} formId Form ID.
		 */
		initRichTextField: function( formId ) {

			// Set default tab to `Visual`.
			$( `#wpforms-${formId} .wp-editor-wrap` ).removeClass( 'html-active' ).addClass( 'tmce-active' );
		},


		/**
		 * Update accent colors of some fields in Elementor widget.
		 *
		 * @since 1.8.3
		 *
		 * @param {jQuery}  widgetContainer Widget container.
		 * @param {integer} formId          Event details object.
		 */
		updateAccentColors: function( widgetContainer, formId ) {

			const $form = widgetContainer.find( `#wpforms-${formId}` ),
				FrontendModern = window.WPForms.FrontendModern;

			FrontendModern.updateGBBlockPageIndicatorColor( $form );
			FrontendModern.updateGBBlockIconChoicesColor( $form );
			FrontendModern.updateGBBlockRatingColor( $form );
		},

		/**
		 * Init Modern style Dropdown fields (<select>).
		 *
		 * @since 1.8.3
		 *
		 * @param {jQuery}  widgetContainer Widget container.
		 * @param {integer} formId          Form id.
		 */
		loadChoicesJS: function( widgetContainer, formId ) {

			if ( typeof window.Choices !== 'function' ) {
				return;
			}

			const $form = widgetContainer.find( `#wpforms-${formId}` );

			$form.find( '.choicesjs-select' ).each( function( idx, el ) {

				const $el = $( el );

				if ( $el.data( 'choice' ) === 'active' ) {
					return;
				}

				var args = window.wpforms_choicesjs_config || {},
					searchEnabled = $el.data( 'search-enabled' ),
					$field = $el.closest( '.wpforms-field' );

				args.searchEnabled  = 'undefined' !== typeof searchEnabled ? searchEnabled : true;
				args.callbackOnInit = function() {

					var self = this,
						$element = $( self.passedElement.element ),
						$input = $( self.input.element ),
						sizeClass = $element.data( 'size-class' );

					// Add CSS-class for size.
					if ( sizeClass ) {
						$( self.containerOuter.element ).addClass( sizeClass );
					}

					/**
					 * If a multiple select has selected choices - hide a placeholder text.
					 * In case if select is empty - we return placeholder text back.
					 */
					if ( $element.prop( 'multiple' ) ) {

						// On init event.
						$input.data( 'placeholder', $input.attr( 'placeholder' ) );

						if ( self.getValue( true ).length ) {
							$input.removeAttr( 'placeholder' );
						}
					}

					this.disable();
					$field.find( '.is-disabled' ).removeClass( 'is-disabled' );
				};

				try {
					const choicesInstance =  new Choices( el, args );

					// Save Choices.js instance for future access.
					$el.data( 'choicesjs', choicesInstance );

				} catch ( e ) {} // eslint-disable-line no-empty
			} );
		},
	};

	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsElementorModern.init();
