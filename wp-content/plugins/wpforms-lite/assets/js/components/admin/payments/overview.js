/* eslint-disable camelcase */
/* global flatpickr, Chart, moment, ajaxurl, wpforms_admin_payments_overview */

/**
 * Script for manipulating DOM events in the "Payments Overview" page.
 * This script will be accessible in the "WPForms" → "Payments" page.
 *
 * @since 1.8.2
 */
'use strict';

let WPFormsPaymentsOverview = window.WPFormsPaymentsOverview || ( function( document, window, $, ajaxurl, l10n ) {

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

		/**
		 * Chart.js instance.
		 *
		 * @since 1.8.2
		 */
		chart: null,

		/**
		 * Flatpickr instance.
		 *
		 * @since 1.8.2
		 */
		datepicker: null,

		/**
		 * The ISO 639-2 language code of the WordPress installation.
		 *
		 * @since 1.8.2
		 */
		locale: l10n.locale,

		/**
		 * Get the base currency code.
		 *
		 * @since 1.8.2
		 */
		currency: l10n.currency,

		/**
		 * Cryptographic token for validating authorized Ajax data exchange.
		 *
		 * @since 1.8.2
		 */
		nonce: l10n.nonce,

		/**
		 * Initial dataset that will appear on the chart.
		 *
		 * @since 1.8.2
		 */
		data: [],

		/**
		 * Chart type. Options are "Line" or "Bar".
		 * A line chart is a way of plotting data points on a line.
		 * A bar chart provides a way of showing data values represented as vertical bars.
		 *
		 * 1: Bar.
		 * 2: Line.
		 *
		 * @since 1.8.2
		 */
		type: l10n.settings.graph_style === 1 ? 'bar' : 'line',

		/**
		 * Timespan (date range) delimiter. By default: ' - '.
		 *
		 * @since 1.8.2
		 */
		delimiter: l10n.delimiter,

		/**
		 * The current page URI.
		 *
		 * @since 1.8.2
		 *
		 * @returns {URL} The current page URI.
		 */
		get currentPageUri() {

			return new URL( l10n.page_uri );
		},

		/**
		 * Generic CSS class names for applying visual changes.
		 *
		 * @since 1.8.2
		 */
		classNames: {
			hide: 'wpforms-hide',
			ready: 'is-ready',
			fetching: 'doing-ajax',
			selected: 'is-selected',
			calculated: 'is-calculated',
		},

		/**
		 * Start and end dates.
		 *
		 * @since 1.8.2
		 */
		timespan: '',

		/**
		 * Report stats that are currently being viewed.
		 *
		 * @since 1.8.2
		 */
		report: l10n.active_report,

		/**
		 * Whether the viewed stats are representing the price amounts.
		 *
		 * @since 1.8.2
		 */
		isAmount: false,

		/**
		 * Chart color options.
		 *
		 * @since 1.8.2
		 *
		 * @returns {object} Colors object specified for the graph.
		 */
		get colors() {

			const isLine = this.type === 'line';

			return {

				total_payments: { // Blue.
					hoverBorderColor: '#055f9a',
					hoverBackgroundColor: '#055f9a',
					borderColor: '#056aab',
					backgroundColor: isLine ? '#e6f0f7' : '#056aab',
				},
				total_sales: { // Green.
					hoverBorderColor: '#00831e',
					hoverBackgroundColor: '#00831e',
					borderColor: '#008a20',
					backgroundColor: isLine ? '#e3f3e4' : '#008a20',
				},
				default: { // Orange - WPForms.
					hoverBorderColor: '#cd6622',
					hoverBackgroundColor: '#cd6622',
					borderColor: '#e27730',
					backgroundColor: isLine ? '#fcf1ea' : '#e27730',
				},
			};
		},

		/**
		 * Translated texts.
		 *
		 * @since 1.8.2
		 *
		 * @returns {object} Localized strings.
		 */
		get i18n() {

			return l10n.i18n;
		},

		/**
		 * In case the time span extends to other years, the xAxes date display format is updated to include the year identifier.
		 *
		 * @since 1.8.2
		 *
		 * @returns {object} Localized strings.
		 */
		get xAxesDisplayFormat() {

			if ( ! this.timespan.length ) {
				return 'MMM D';
			}

			const dates = this.timespan.split( this.delimiter );

			if ( ! Array.isArray( dates ) || dates.length !== 2 ) {
				return 'MMM D';
			}

			const startYear = moment( dates[0] ).format( 'YYYY' );
			const endYear   = moment( dates[1] ).format( 'YYYY' );

			return startYear === endYear ? 'MMM D' : 'MMM D YYYY';
		},

		/**
		 * Returns language-sensitive number formatting instance.
		 *
		 * @since 1.8.2
		 *
		 * @returns {NumberFormat} Returns a new NumberFormat object.
		 */
		get amountFormatter() {

			return new Intl.NumberFormat( this.locale, {
				style: 'currency',
				useGrouping: true,
				currencyDisplay: 'narrowSymbol',
				currency: this.currency,
			} );
		},

		/**
		 * Retrieves the previewed dataset label.
		 *
		 * @since 1.8.2.2
		 *
		 * @returns {string} The dataset tooltip label.
		 */
		get datasetLabel() {

			const $statcard = $( `[data-stats=${this.report}]` );

			if ( ! $statcard.length ) {
				return this.i18n?.label;
			}

			return $statcard.find( '.statcard-label' ).text();
		},

		/**
		 * Chart.js settings.
		 *
		 * @since 1.8.2
		 *
		 * @returns {object} Scriptable options as a function which is called for the chart instances.
		 */
		get settings() { /* eslint max-lines-per-function: ["error", 200] */

			return {

				type: this.type,
				data: {
					labels: [],
					datasets: [
						{
							data: [],
							label: '',
							borderWidth: 2,
							pointRadius: 4,
							pointBorderWidth: 1,
							...{
								pointBackgroundColor: '#ffffff',
								...( this.colors[this.report] || this.colors.default ),
							},
						},
					],
				},
				options: {
					layout: {
						padding: {
							left: 15,
							right: 19,
							top: 25,
							bottom: 9,
						},
					},
					scales: {
						xAxes: [
							{
								type: 'time',
								offset: this.type === 'bar',
								maxBarThickness: 100,
								time: {
									unit: 'day',
									displayFormats: {
										day: this.xAxesDisplayFormat,
									},
								},
								distribution: 'series',
								ticks: {
									beginAtZero: true,
									source: 'labels',
									padding: 10,
									fontColor: '#a7aaad',
									labelOffset: 10,
									fontSize: 13,
									minRotation: 25,
									maxRotation: 25,
									callback: function( value, index, values ) {

										// Distribute the ticks equally starting from the right side of xAxis.
										const gap = Math.floor( values.length / 7 );

										if ( gap < 1 ) {
											return value;
										}

										if ( ( values.length - index - 1 ) % gap === 0 ) {
											return value;
										}
									},
								},
							},
						],
						yAxes: [
							{
								ticks: {
									beginAtZero: true,
									maxTicksLimit: 6,
									padding: 20,
									fontColor: '#a7aaad',
									fontSize: 13,
									callback: ( value ) => {

										// Update the scales if the dataset returned includes price amounts.
										if ( this.isAmount ) {
											return this.amountFormatter.format( value );
										}

										// Make sure the tick value has no decimals.
										if ( Math.floor( value ) === value ) {
											return value;
										}
									},
								},
							},
						],
					},
					elements: {
						line: {
							tension: 0,
						},
					},
					animation: {
						duration: 0,
					},
					hover: {
						animationDuration: 0,
					},
					legend: {
						display: false,
					},
					tooltips: {
						displayColors: false,
						callbacks: {
							label: ( { yLabel: value } ) => {

								let label = `${this.datasetLabel} `;

								// Update the scales if the dataset returned includes price amounts.
								if ( this.isAmount ) {
									label += this.amountFormatter.format( value );
									return label;
								}

								label += value;

								return label;
							},
						},
					},
					responsiveAnimationDuration: 0,
					maintainAspectRatio: false,
				},
			};
		},
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
			app.initDatePicker();
			app.initChart();
		},

		/**
		 * Setup. Prepare some variables.
		 *
		 * @since 1.8.2
		 */
		setup: function() {

			// Cache DOM elements.
			el.$document      = $( document );
			el.$wrapper       = $( '.wpforms-payments-wrap-overview' );
			el.$spinner       = $( '.wpforms-overview-chart .spinner' );
			el.$canvas        = $( '#wpforms-payments-overview-canvas' );
			el.$filterBtn     = $( '#wpforms-datepicker-popover-button' );
			el.$datepicker    = $( '#wpforms-payments-overview-datepicker' );
			el.$filterForm    = $( '.wpforms-overview-top-bar-filter-form' );
			el.$table         = $( '.wpforms-table-list' );
			el.$notice        = $( '.wpforms-overview-chart-notice' );
			el.$reports       = $( '.wpforms-payments-overview-reports' );
		},

		/**
		 * Bind events.
		 *
		 * @since 1.8.2
		 */
		bindEvents: function() {

			el.$document
				.on( 'click', { selectors: [ '.wpforms-datepicker-popover', '.wpforms-dash-widget-settings-menu' ] }, app.handleOnClickOutside );
			el.$wrapper
				.on( 'submit', '.wpforms-overview-top-bar-filter-form', app.handleOnSubmitDatepicker )
				.on( 'click', '.wpforms-overview-top-bar-filter-form [type="reset"]', app.handleOnResetDatepicker )
				.on( 'change', '.wpforms-overview-top-bar-filter-form [type="radio"]', app.handleOnUpdateDatepicker )
				.on( 'click', '.wpforms-payments-overview-reports button', app.handleOnChangeStatCard )
				.on( 'click', '.wpforms-dash-widget-settings-menu-save', app.handleOnSaveSettings )
				.on( 'click', '#wpforms-payments-mode-toggle', app.handleOnToggleMode )
				.on( 'click', '#wpforms-dash-widget-settings-button', { selector: '.wpforms-dash-widget-settings-menu', hide: '.wpforms-datepicker-popover' }, app.handleOnToggle )
				.on( 'click', '#wpforms-datepicker-popover-button', { selector: '.wpforms-datepicker-popover', hide: '.wpforms-dash-widget-settings-menu' }, app.handleOnToggle );
		},

		/**
		 * Create an instance of "flatpickr".
		 *
		 * @since 1.8.2
		 */
		initDatePicker: function() {

			if ( ! el.$datepicker.length ) {
				return;
			}

			vars.timespan   = el.$datepicker.val();
			vars.datepicker = flatpickr( el.$datepicker, {
				mode: 'range',
				inline: true,
				allowInput: false,
				enableTime: false,
				clickOpens: false,
				altInput: true,
				altFormat: 'M j, Y',
				dateFormat: 'Y-m-d',
				locale: {

					// Localized per-instance, if applicable.
					...flatpickr.l10ns[ vars.locale ] || {},
					rangeSeparator: vars.delimiter,
				},
				onChange: function( selectedDates, dateStr, instance ) {

					// Immediately after a user interacts with the datepicker, ensure that the "Custom" option is chosen.
					const $custom = el.$filterForm.find( 'input[value="custom"]' );

					$custom.prop( 'checked', true );
					app.selectDatepickerChoice( $custom.parent() );

					if ( dateStr ) {

						// Update filter button label when date range specified.
						el.$filterBtn.text( instance.altInput.value );
					}
				},
			} );

			// Determine if a custom date range was provided or selected.
			this.handleOnUpdateDatepicker( {}, el.$filterForm.find( 'input[value="custom"]' ).prop( 'checked' ) );
		},

		/**
		 * Callback which is called when the filter form gets submitted.
		 *
		 * @since 1.8.2
		 */
		handleOnSubmitDatepicker: function() {

			// Exclude radio inputs from the form submission.
			$( this ).find( 'input[type="radio"]' ).attr( 'name', '' );

			// Remove the popover from the view.
			// When the dropdown is closed, aria-expended="false".
			app.hideElm( el.$filterBtn.next() );
		},

		/**
		 * Callback which is called when the datepicker "Cancel" button clicked.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} event An event which takes place in the DOM.
		 */
		handleOnResetDatepicker: function( event ) {

			event.preventDefault();

			// To return the form to its original state, manually reset it.
			el.$filterForm.get( 0 ).reset();

			// Remove the popover from the view.
			// When the dropdown is closed, aria-expended="false".
			app.hideElm( el.$filterBtn.next() );

			app.handleOnUpdateDatepicker();
		},

		/**
		 * Callback which is called when the filter form elements change.
		 *
		 * @since 1.8.2
		 *
		 * @param {object}  event         An event which takes place in the DOM.
		 * @param {boolean} isCustomDates Determine whether a custom date range is provided.
		 */
		handleOnUpdateDatepicker: function( event = {}, isCustomDates = false ) {

			const $selected = el.$filterForm.find( 'input:checked' );
			const $parent   = $selected.parent();
			const $target   = isCustomDates ? el.$datepicker : $selected;
			const dates     = $target.val().split( vars.delimiter );

			el.$filterBtn.text( isCustomDates ? $target.next().val() : $parent.text() );

			app.selectDatepickerChoice( $parent );

			if ( Array.isArray( dates ) && dates.length === 2 ) {

				// Sets the current selected date(s).
				vars.datepicker.setDate( dates );
				return;
			}

			vars.datepicker.clear(); // Reset the datepicker.
		},

		/**
		 * Create an instance of chart.
		 *
		 * @since 1.8.2
		 */
		initChart: function() {

			if ( ! el.$canvas.length ) {
				return;
			}

			const elm       = el.$canvas.get( 0 ).getContext( '2d' );
			const $selected = el.$reports.find( `.${ vars.classNames.selected }` );

			vars.report   = $selected.data( 'stats' );
			vars.isAmount = $selected.hasClass( 'is-amount' );
			vars.chart    = new Chart( elm, vars.settings );

			this.updateChartByReport();
		},

		/**
		 * Updates main chart stats when user switches between different stat card.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} event An event which takes place in the DOM.
		 */
		handleOnChangeStatCard: function( event ) {

			event.preventDefault();

			const $this = $( this );

			// If the already selected stat card is clicked, don't process the dataset.
			if ( $this.hasClass( vars.classNames.selected ) || $this.hasClass( 'disabled' ) ) {
				return;
			}

			app.spinner();

			vars.report   = $this.data( 'stats' );
			vars.isAmount = $this.hasClass( 'is-amount' );

			el.$reports.find( 'button' ).removeClass( vars.classNames.selected );
			$this.addClass( vars.classNames.selected );

			// Update the chart stats with consideration to possible form stats being viewed.
			app.updateChartByReport();
		},

		/**
		 * Save the user's preferred graph style and color scheme.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} event An event which takes place in the DOM.
		 */
		handleOnSaveSettings: function( event ) {

			event.preventDefault();

			const $wrapper   = $( this ).closest( '.wpforms-dash-widget-settings-container' );
			const graphStyle = $wrapper.find( 'input[name="wpforms-style"]:checked' ).val();

			vars.type = Number( graphStyle ) === 1 ? 'bar' : 'line';

			const options                   = Object.assign( {}, vars.settings );
			options.data.labels             = vars.chart.data.labels;
			options.data.datasets[ 0 ].data = vars.chart.data.datasets[ 0 ].data;

			vars.chart.destroy();

			const elm  = el.$canvas.get( 0 ).getContext( '2d' );
			vars.chart = new Chart( elm, options );

			$.post(
				ajaxurl,
				{
					graphStyle,
					_ajax_nonce: vars.nonce,
					action: 'wpforms_payments_overview_save_chart_preference_settings',
				}
			).done( function() {
				el.$wrapper.find( '.wpforms-dash-widget-settings-menu' ).hide();
			} );
		},

		/**
		 * Callback which is called when the "Toggle Mode" button clicked.
		 *
		 * @since 1.8.2
		 */
		handleOnToggleMode: function() {

			const { currentPageUri: url } = vars;

			url.searchParams.set( 'mode', this.checked ? 'test' : 'live' );

			window.location.href = url.href;
		},

		/**
		 * Display or hide the matched elements.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} event An event which takes place in the DOM.
		 */
		handleOnToggle: function( event ) {

			event.preventDefault();

			event.stopPropagation();

			const { data: { selector, hide } } = event;

			// Toggle the visibility of the matched element.
			el.$wrapper.find( selector ).toggle( 0, function() {
				const $selector = $( selector );

				// When the dropdown is open, aria-expended="true".
				$selector.attr( 'aria-expanded', $selector.is( ':visible' ) );
			} );

			// In case the other popover is open, let’s hide it to avoid clutter.
			// When the dropdown is closed, aria-expended="false".
			app.hideElm( el.$wrapper.find( hide ) );
		},

		/**
		 * Hide the matched elements when clicked outside their container.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} event An event which takes place in the DOM.
		 */
		handleOnClickOutside: function( event ) {

			const { target, data: { selectors } } = event;

			$.each( selectors, function( index, selector ) {

				if ( ! $( target ).closest( `${selector}:visible` ).length ) {
					app.hideElm( el.$wrapper.find( selector ) );
				}
			} );
		},

		/**
		 * Either fills the container with placeholder data or determines
		 * whether actual data is available to process the chart dataset.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} data Chart dataset data.
		 *
		 * @returns {object} Labels and dataset data object.
		 */
		processDatasetData: function( data ) {

			const labels   = [];
			const datasets = [];

			if ( $.isPlainObject( data ) && Object.keys( data ).length > 0 ) {

				el.$notice.addClass( vars.classNames.hide );

				$.each( data || [], function( index, item ) {

					const date = moment( item.day );

					labels.push( date );
					datasets.push( {
						t: date,
						y: item?.count || 0,
					} );
				} );

				return { labels, datasets };
			}

			const { i18n: { no_dataset: placeholderText } } = vars;

			// If there is a placeholder text for the current report, use it.
			if ( placeholderText?.[vars.report] ) {
				el.$notice.find( 'h2' ).text( placeholderText[vars.report] );
			}


			el.$notice.removeClass( vars.classNames.hide );

			let date;
			const end  = moment().startOf( 'day' );
			const days = 30;
			const minY = 5;
			const maxY = 20;

			for ( let i = 1; i <= days; i++ ) {

				date = end.clone().subtract( i, 'days' );

				labels.push( date );
				datasets.push( {
					t: date,
					y: Math.floor( Math.random() * ( maxY - minY + 1 ) ) + minY, // NOSONAR not used in secure contexts.
				} );
			}

			return { labels, datasets };
		},

		/**
		 * Populate the chart with a fresh set of dataset data.
		 *
		 * @since 1.8.2
		 *
		 * @param {Array} data Chart dataset data.
		 */
		updateChart: function( data ) {

			const { labels, datasets } = app.processDatasetData( data || [] );

			vars.chart.data.labels             = labels;
			vars.chart.data.datasets[ 0 ]      = vars.settings.data.datasets[ 0 ];
			vars.chart.data.datasets[ 0 ].data = datasets;
			vars.chart.update();

			el.$spinner.addClass( vars.classNames.hide );
		},

		/**
		 * Fetch and process the chart dataset data for the selected stat card.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} args Optional. Additional arguments provided for the Ajax request.
		 */
		updateChartByReport: function( args ) {

			// Cache dataset of payments for the chart stats.
			if ( vars.report && Object.hasOwn( vars.data, vars.report ) ) {
				app.updateChart( vars.data[ vars.report ]?.data || [] );
				return;
			}

			// Add a class name indicating that the chart is fetching data.
			// This is mainly to avoid fast clicking on the stat cards to avoid multiple Ajax requests.
			el.$reports.addClass( vars.classNames.fetching );

			$.post(
				ajaxurl,
				$.extend(
					{},
					{
						report: vars.report,
						dates: vars.timespan,
						_ajax_nonce: vars.nonce,
						action: 'wpforms_payments_overview_refresh_chart_dataset_data',
					},
					args
				),
				function( { data } ) {

					vars.data = Object.assign( { [ vars.report ]: data }, vars.data );

					app.updateChart( data?.data || [] );
					app.updateReports( data?.reports || {} );
				}
			).done(
				function() {

					el.$reports.addClass( vars.classNames.ready );
					el.$reports.removeClass( vars.classNames.fetching );
				}
			);
		},

		/**
		 * Reflect payments summary stats in their corresponding areas (elements).
		 *
		 * @since 1.8.2
		 *
		 * @param {object} reports Reports summary stats queried from the database.
		 */
		updateReports: function( reports ) {

			// Bail early, in case given reports object is empty.
			if ( $.isEmptyObject( reports ) ) {
				return;
			}

			el.$reports
				.find( 'li' ).each(
					// eslint-disable-next-line complexity
					function() {

						const $this   = $( this );
						const $button = $this.find( 'button' );

						// Skip iterating over stat cards that are disabled.
						if ( $button.hasClass( 'disabled' ) ) {
							return true; // This is the same as 'continue'.
						}

						const stats  = $button.data( 'stats' );
						const value  = reports[stats] || 0;
						const delta  = Number( reports[`${stats}_delta`] ) || 0;
						const $value = $this.find( '.statcard-value' );
						const $delta = $this.find( '.statcard-delta' );

						$value.addClass( vars.classNames.calculated ).html( value );
						$delta.addClass( vars.classNames.calculated ).html( Math.abs( delta ) );

						if ( delta !== 0 ) {
							$delta.addClass( Number( delta > 0 ) ? 'is-upward' : 'is-downward' );
						}

						// Skip iterating over stat cards that do not represent an amount.
						if ( ! $button.hasClass( 'is-amount' ) ) {
							return; // This is the same as 'continue'.
						}

						// Add a title attribute to the stat card value if it does not have one.
						$value.attr( 'title', $value.text() );
					}
				);
		},

		/**
		 * Pick an option (given) from the datepicker’s choices.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} $this Reference to the DOM element.
		 */
		selectDatepickerChoice: function( $this ) {

			el.$filterForm.find( 'label' ).removeClass( vars.classNames.selected );
			$this.addClass(  vars.classNames.selected  );
		},

		/**
		 * Signal to users that the processing of their request is underway and will soon complete.
		 *
		 * @since 1.8.2
		 */
		spinner: function() {

			el.$spinner.removeClass( vars.classNames.hide );
		},

		/**
		 * Hides the given DOM element.
		 *
		 * @since 1.8.2
		 *
		 * @param {object} $elm Reference to the DOM element.
		 */
		hideElm: function( $elm ) {

			$elm.attr( 'aria-expanded', 'false' ).hide();
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery, ajaxurl, wpforms_admin_payments_overview ) );

// Initialize.
WPFormsPaymentsOverview.init();
