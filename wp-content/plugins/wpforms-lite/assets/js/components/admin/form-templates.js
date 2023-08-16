/* global List, wpforms_form_templates, wpforms_addons, wpf */

/**
 * Form Templates function.
 *
 * @since 1.7.7
 */

'use strict';

var WPFormsFormTemplates = window.WPFormsFormTemplates || ( function( document, window, $ ) {

	/**
	 * Runtime variables.
	 *
	 * @since 1.7.7
	 *
	 * @type {object}
	 */
	var vars = {};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.7.7
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.7.7
		 */
		init: function() {

			$( app.ready );
			$( window ).on( 'load', function() {

				// in case of jQuery 3.+ we need to wait for an `ready` event first.
				if ( typeof $.ready.then === 'function' ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.7.7
		 */
		ready: function() {

			app.setup();
			app.events();
		},

		/**
		 * Window load.
		 *
		 * @since 1.7.7
		 */
		load: function() {

			app.showUpgradeBanner();
		},

		/**
		 * Setup. Prepare some variables.
		 *
		 * @since 1.7.7
		 */
		setup: function() {

			// Template list object.
			vars.templateList = new List( 'wpforms-setup-templates-list', {
				valueNames: [
					'wpforms-template-name',
					'wpforms-template-desc',
					{
						name: 'slug',
						attr: 'data-slug',
					},
					{
						name: 'categories',
						attr: 'data-categories',
					},
					{
						name: 'has-access',
						attr: 'data-has-access',
					},
					{
						name: 'favorite',
						attr: 'data-favorite',
					},
				],
			} );
		},

		/**
		 * Bind events.
		 *
		 * @since 1.7.7
		 */
		events: function() {

			$( '#wpforms-setup-templates-list' )
				.on( 'click', '.wpforms-template-favorite i', app.selectFavorite );
		},

		/**
		 * Select Favorite Templates.
		 *
		 * @since 1.7.7
		 *
		 * @param {object} e Event object.
		 */
		// eslint-disable-next-line max-lines-per-function
		selectFavorite: function( e ) {

			let $heartIcon = $( this ),
				favorite = $heartIcon.hasClass( 'fa-heart-o' ),
				$favorite = $heartIcon.closest( '.wpforms-template-favorite' ),
				$template = $heartIcon.closest( '.wpforms-template' ),
				$templateName = $template.find( '.wpforms-template-name' ),
				templateSlug = $template.find( '.wpforms-template-select' ).data( 'slug' ),
				$favoritesCategory = $( '.wpforms-setup-templates-categories' ).find( '[data-category=\'favorites\']' ),
				$favoritesCount = $favoritesCategory.find( 'span' ),
				favoritesCount = parseInt( $favoritesCount.html(), 10 ),
				data = {
					action: 'wpforms_templates_favorite',
					slug: templateSlug,
					favorite: favorite,
					nonce: wpforms_form_templates.nonce,
				};

			let item = vars.templateList.get( 'slug', templateSlug )[0],
				values = item.values();

			let toggleHeartIcon = function() {

				$favorite.find( '.fa-heart-o' ).toggleClass( 'wpforms-hidden', values.favorite );
				$favorite.find( '.fa-heart' ).toggleClass( 'wpforms-hidden', ! values.favorite );
			};

			let unMarkFavorite = function() {

				values.favorite = false;
				favoritesCount = favoritesCount - 1;

				item.values( values );

				toggleHeartIcon();
				$templateName.data( 'data-favorite', 0 );
				$favoritesCount.html( favoritesCount );

				app.maybeHideFavoritesCategory();
			};

			let markFavorite = function() {

				values.favorite = true;
				favoritesCount = favoritesCount + 1;

				item.values( values );

				toggleHeartIcon();
				$templateName.data( 'data-favorite', 1 );
				$favoritesCount.html( favoritesCount );

				app.maybeHideFavoritesCategory();
			};

			$.post( wpforms_form_templates.ajaxurl, data, function( res ) {

				if ( ! res.success ) {

					if ( favorite ) {
						unMarkFavorite();

						return;
					}

					markFavorite();
				}
			} );

			if ( favorite ) {
				markFavorite();

				return;
			}

			unMarkFavorite();
		},

		/**
		 * Maybe hide favorites category if there are no templates.
		 *
		 * @since 1.7.7
		 */
		maybeHideFavoritesCategory: function() {

			let $categoriesList = $( '.wpforms-setup-templates-categories' ),
				$favoritesCategory = $categoriesList.find( '[data-category=\'favorites\']' ),
				favoritesCount = parseInt( $favoritesCategory.find( 'span' ).html(), 10 );

			$favoritesCategory.toggleClass( 'wpforms-hidden', ! favoritesCount );

			if ( $favoritesCategory.hasClass( 'active' ) ) {

				if ( ! favoritesCount ) {
					$categoriesList.find( '[data-category=\'all\']' ).trigger( 'click' );

					return;
				}

				$favoritesCategory.trigger( 'click' );
			}
		},

		/**
		 * Search template callback.
		 *
		 * @since 1.7.7
		 *
		 * @param {object} e Event object.
		 */
		searchTemplate: function( e ) {

			app.performSearch( $( this ).val() );
			app.showUpgradeBanner();
		},

		/**
		 * Perform search value.
		 *
		 * @since 1.7.7.2
		 *
		 * @param {string} query Value to search.
		 */
		performSearch( query ) {

			let searchResult = vars.templateList.search( query );

			$( '.wpforms-templates-no-results' ).toggle( ! searchResult.length );
		},

		/**
		 * Select category.
		 *
		 * @since 1.7.7
		 *
		 * @param {object} e Event object.
		 */
		selectCategory: function( e ) {

			e.preventDefault();

			let $item       = $( this ),
				$active     = $item.closest( 'ul' ).find( '.active' ),
				category    = $item.data( 'category' ),
				searchQuery = $( '#wpforms-setup-template-search' ).val();

			$active.removeClass( 'active' );
			$item.addClass( 'active' );

			vars.templateList.filter( function( item ) {

				if ( category === 'available' ) {
					return item.values()['has-access'];
				}

				if ( category === 'favorites' ) {
					return item.values().favorite;
				}

				return category === 'all' || item.values().categories.split( ',' ).indexOf( category ) > -1;
			} );

			if ( searchQuery !== '' ) {
				app.performSearch( searchQuery );
			}

			app.showUpgradeBanner();
		},

		/**
		 * Cancel button click routine.
		 *
		 * @since 1.7.7
		 */
		selectTemplateCancel: function( ) {

			let $template = $( '#wpforms-setup-templates-list' ).find( '.wpforms-template.active' ),
				$button = $template.find( '.wpforms-template-select' );

			$template.removeClass( 'active' );
			$button.html( $button.data( 'labelOriginal' ) );
		},

		/**
		 * Show upgrade banner if licence type is less than Pro.
		 *
		 * @since 1.7.7
		 */
		showUpgradeBanner: function() {

			if ( ! $( '#tmpl-wpforms-templates-upgrade-banner' ).length ) {
				return;
			}

			let template = wp.template( 'wpforms-templates-upgrade-banner' );

			if ( ! template ) {
				return;
			}

			const $templates = $( '#wpforms-setup-templates-list .wpforms-template' );

			if ( $templates.length > 5 ) {
				$templates.eq( 5 ).after( template() );

				return;
			}

			$templates.last().after( template() );
		},

		/**
		 * Select template.
		 *
		 * @since 1.8.2
		 *
		 * @param {string}   formName Name of the form.
		 * @param {string}   template Template slug.
		 * @param {jQuery}   $button  Use template button object.
		 * @param {Function} callback The function to set the template.
		 */
		selectTemplateProcess: function( formName, template, $button, callback ) {

			if ( $button.data( 'addons' ) ) {
				app.addonsModal( formName, template, $button, callback );

				return;
			}

			callback( formName, template );
		},

		/**
		 * Open required addons alert.
		 *
		 * @since 1.8.2
		 *
		 * @param {string}   formName Name of the form.
		 * @param {string}   template Template slug.
		 * @param {jQuery}   $button  Use template button object.
		 * @param {Function} callback The function to set the template.
		 */
		addonsModal: function( formName, template, $button, callback ) {

			const templateName = $button.data( 'template-name-raw' );
			const addonsNames = $button.data( 'addons-names' );
			const addonsSlugs = $button.data( 'addons' );
			const addons = addonsSlugs.split( ',' );
			let prompt = addons.length > 1 ? wpforms_form_templates.template_addons_prompt : wpforms_form_templates.template_addon_prompt;

			prompt = prompt.replace( /%template%/g, templateName ).replace( /%addons%/g, addonsNames );

			if ( ! addons.length ) {
				return;
			}

			if ( ! wpforms_form_templates.can_install_addons ) {
				app.userCannotInstallAddonsModal( prompt );

				return;
			}

			app.userCanInstallAddonsModal( formName, template, addons, prompt, callback );
		},

		/**
		 * Open the template addon alert for admins.
		 *
		 * @since 1.8.2
		 *
		 * @param {string}   formName Name of the form.
		 * @param {string}   template Template slug.
		 * @param {Array}    addons   Array of addon slugs.
		 * @param {string}   prompt   Modal content.
		 * @param {Function} callback The function to set the template.
		 */
		userCanInstallAddonsModal: function( formName, template, addons, prompt, callback ) {

			const spinner = '<i class="wpforms-loading-spinner wpforms-loading-white wpforms-loading-inline"></i>';

			$.confirm( {
				title: wpforms_form_templates.heads_up,
				content: prompt,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_form_templates.install_confirm,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							this.$$confirm
								.prop( 'disabled', true )
								.html( spinner + wpforms_form_templates.activating );

							this.$$cancel
								.prop( 'disabled', true );

							app.installActivateAddons( addons, this, formName, template, callback );

							return false;
						},
					},
					cancel: {
						text: wpforms_form_templates.cancel,
						action: function() {

							WPFormsFormTemplates.selectTemplateCancel();
						},
					},
				},
			} );
		},

		/**
		 * Open the template addon alert for non-admins.
		 *
		 * @since 1.8.2
		 *
		 * @param {string} prompt Modal content.
		 */
		userCannotInstallAddonsModal: function( prompt ) {

			$.alert( {
				title: wpforms_form_templates.heads_up,
				content: prompt,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					ok: {
						text: wpforms_form_templates.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							WPFormsFormTemplates.selectTemplateCancel();
						},
					},
				},
			} );
		},

		/**
		 * Install & Activate addons via AJAX.
		 *
		 * @since 1.8.2
		 *
		 * @param {Array}  addons        Addons slugs.
		 * @param {object} previousModal Previous modal instance.
		 * @param {string} formName      Name of the form.
		 * @param {string} template      Template slug.
		 * @param {Function} callback    The function to set the template.
		 */
		installActivateAddons: function( addons, previousModal, formName, template, callback ) {

			const ajaxResults = [];
			const ajaxErrors = [];
			let promiseChain = false;

			// Put each of the ajax call promise to the chain.
			addons.forEach( function( addon ) {

				if ( typeof promiseChain.done !== 'function' ) {
					promiseChain = app.installActivateAddonAjax( addon );

					return;
				}

				promiseChain = promiseChain
					.done( function( value ) {

						ajaxResults.push( value );

						return app.installActivateAddonAjax( addon );
					} )
					.fail( function( error ) {
						ajaxErrors.push( error );
					} );
			} );

			promiseChain

				// Latest promise result and error.
				.done( function( value ) {
					ajaxResults.push( value );
				} )
				.fail( function( error ) {
					ajaxErrors.push( error );
				} )

				// Finally, resolve all the promises.
				.always( function() {

					previousModal.close();

					if (
						ajaxResults.length > 0 &&
						wpf.listPluck( ajaxResults, 'success' ).every( Boolean ) && // Check if every `success` is true.
						ajaxErrors.length === 0
					) {
						callback( formName, template );

						return;
					}

					app.installActivateAddonsError( formName, template, callback );
				} );
		},

		/**
		 * Install & Activate addons error modal.
		 *
		 * @since 1.8.2
		 *
		 * @param {string} formName Name of the form.
		 * @param {string} template Template slug.
		 * @param {Function} callback The function to set the template.
		 */
		installActivateAddonsError: function( formName, template, callback ) {

			$.confirm( {
				title: wpforms_form_templates.heads_up,
				content: wpforms_form_templates.template_addons_error,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_form_templates.use_template,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							callback( formName, template );
						},
					},
					cancel: {
						text: wpforms_form_templates.cancel,
						action: function() {

							app.selectTemplateCancel();
						},
					},
				},
			} );
		},

		/**
		 * Install & Activate single addon via AJAX.
		 *
		 * @since 1.8.2
		 *
		 * @param {string} addon Addon slug.
		 *
		 * @returns {Promise} jQuery ajax call promise.
		 */
		installActivateAddonAjax: function( addon ) {

			const addonData = wpforms_addons[ addon ];
			const deferred = new $.Deferred();

			if (
				! addonData ||
				[ 'activate', 'install' ].indexOf( addonData.action ) < 0
			) {
				deferred.resolve( false );

				return deferred.promise();
			}

			return $.post(
				wpforms_form_templates.ajaxurl,
				{
					action: 'wpforms_' + addonData.action + '_addon',
					nonce: wpforms_form_templates.admin_nonce,
					plugin: addonData.action === 'activate' ? addon + '/' + addon + '.php' : addonData.url,
				}
			);
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsFormTemplates.init();
