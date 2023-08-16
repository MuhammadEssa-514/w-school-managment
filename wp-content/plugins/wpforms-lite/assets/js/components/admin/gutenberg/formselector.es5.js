(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/* global wpforms_gutenberg_form_selector, Choices */
/* jshint es3: false, esversion: 6 */

'use strict';

/**
 * Gutenberg editor block.
 *
 * @since 1.8.1
 */
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i.return && (_r = _i.return(), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
var WPForms = window.WPForms || {};
WPForms.FormSelector = WPForms.FormSelector || function (document, window, $) {
  var _wp = wp,
    _wp$serverSideRender = _wp.serverSideRender,
    ServerSideRender = _wp$serverSideRender === void 0 ? wp.components.ServerSideRender : _wp$serverSideRender;
  var _wp$element = wp.element,
    createElement = _wp$element.createElement,
    Fragment = _wp$element.Fragment,
    useState = _wp$element.useState,
    createInterpolateElement = _wp$element.createInterpolateElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var _ref = wp.blockEditor || wp.editor,
    InspectorControls = _ref.InspectorControls,
    InspectorAdvancedControls = _ref.InspectorAdvancedControls,
    PanelColorSettings = _ref.PanelColorSettings;
  var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    ToggleControl = _wp$components.ToggleControl,
    PanelBody = _wp$components.PanelBody,
    Placeholder = _wp$components.Placeholder,
    Flex = _wp$components.Flex,
    FlexBlock = _wp$components.FlexBlock,
    __experimentalUnitControl = _wp$components.__experimentalUnitControl,
    TextareaControl = _wp$components.TextareaControl,
    Button = _wp$components.Button,
    Modal = _wp$components.Modal;
  var _wpforms_gutenberg_fo = wpforms_gutenberg_form_selector,
    strings = _wpforms_gutenberg_fo.strings,
    defaults = _wpforms_gutenberg_fo.defaults,
    sizes = _wpforms_gutenberg_fo.sizes;
  var defaultStyleSettings = defaults;
  var __ = wp.i18n.__;

  /**
   * Blocks runtime data.
   *
   * @since 1.8.1
   *
   * @type {object}
   */
  var blocks = {};

  /**
   * Whether it is needed to trigger server rendering.
   *
   * @since 1.8.1
   *
   * @type {boolean}
   */
  var triggerServerRender = true;

  /**
   * Popup container.
   *
   * @since 1.8.3
   *
   * @type {object}
   */
  var $popup = {};

  /**
   * Public functions and properties.
   *
   * @since 1.8.1
   *
   * @type {object}
   */
  var app = {
    /**
     * Start the engine.
     *
     * @since 1.8.1
     */
    init: function init() {
      app.initDefaults();
      app.registerBlock();
      $(app.ready);
    },
    /**
     * Document ready.
     *
     * @since 1.8.1
     */
    ready: function ready() {
      app.events();
    },
    /**
     * Events.
     *
     * @since 1.8.1
     */
    events: function events() {
      $(window).on('wpformsFormSelectorEdit', _.debounce(app.blockEdit, 250)).on('wpformsFormSelectorFormLoaded', _.debounce(app.formLoaded, 250));
    },
    /**
     * Open builder popup.
     *
     * @since 1.6.2
     *
     * @param {string} clientID Block Client ID.
     */
    openBuilderPopup: function openBuilderPopup(clientID) {
      if ($.isEmptyObject($popup)) {
        var tmpl = $('#wpforms-gutenberg-popup');
        var parent = $('#wpwrap');
        parent.after(tmpl);
        $popup = parent.siblings('#wpforms-gutenberg-popup');
      }
      var url = wpforms_gutenberg_form_selector.get_started_url,
        $iframe = $popup.find('iframe');
      app.builderCloseButtonEvent(clientID);
      $iframe.attr('src', url);
      $popup.fadeIn();
    },
    /**
     * Close button (inside the form builder) click event.
     *
     * @since 1.8.3
     *
     * @param {string} clientID Block Client ID.
     */
    builderCloseButtonEvent: function builderCloseButtonEvent(clientID) {
      $popup.off('wpformsBuilderInPopupClose').on('wpformsBuilderInPopupClose', function (e, action, formId, formTitle) {
        if (action !== 'saved' || !formId) {
          return;
        }

        // Insert a new block when a new form is created from the popup to update the form list and attributes.
        var newBlock = wp.blocks.createBlock('wpforms/form-selector', {
          formId: formId.toString() // Expects string value, make sure we insert string.
        });

        // eslint-disable-next-line camelcase
        wpforms_gutenberg_form_selector.forms = [{
          ID: formId,
          post_title: formTitle
        }];

        // Insert a new block.
        wp.data.dispatch('core/block-editor').removeBlock(clientID);
        wp.data.dispatch('core/block-editor').insertBlocks(newBlock);
      });
    },
    /**
     * Register block.
     *
     * @since 1.8.1
     */
    // eslint-disable-next-line max-lines-per-function
    registerBlock: function registerBlock() {
      registerBlockType('wpforms/form-selector', {
        title: strings.title,
        description: strings.description,
        icon: app.getIcon(),
        keywords: strings.form_keywords,
        category: 'widgets',
        attributes: app.getBlockAttributes(),
        supports: {
          customClassName: app.hasForms()
        },
        example: {
          attributes: {
            preview: true
          }
        },
        edit: function edit(props) {
          var attributes = props.attributes;
          var formOptions = app.getFormOptions();
          var sizeOptions = app.getSizeOptions();
          var handlers = app.getSettingsFieldsHandlers(props);

          // Store block clientId in attributes.
          if (!attributes.clientId) {
            // We just want client ID to update once.
            // The block editor doesn't have a fixed block ID, so we need to get it on the initial load, but only once.
            props.setAttributes({
              clientId: props.clientId
            });
          }

          // Main block settings.
          var jsx = [app.jsxParts.getMainSettings(attributes, handlers, formOptions)];

          // Block preview picture.
          if (!app.hasForms()) {
            jsx.push(app.jsxParts.getEmptyFormsPreview(props));
            return jsx;
          }

          // Form style settings & block content.
          if (attributes.formId) {
            jsx.push(app.jsxParts.getStyleSettings(attributes, handlers, sizeOptions), app.jsxParts.getAdvancedSettings(attributes, handlers), app.jsxParts.getBlockFormContent(props));
            handlers.updateCopyPasteContent();
            $(window).trigger('wpformsFormSelectorEdit', [props]);
            return jsx;
          }

          // Block preview picture.
          if (attributes.preview) {
            jsx.push(app.jsxParts.getBlockPreview());
            return jsx;
          }

          // Block placeholder (form selector).
          jsx.push(app.jsxParts.getBlockPlaceholder(props.attributes, handlers, formOptions));
          return jsx;
        },
        save: function save() {
          return null;
        }
      });
    },
    /**
     * Init default style settings.
     *
     * @since 1.8.1
     */
    initDefaults: function initDefaults() {
      ['formId', 'copyPasteJsonValue'].forEach(function (key) {
        return delete defaultStyleSettings[key];
      });
    },
    /**
     * Check if site has forms.
     *
     * @since 1.8.3
     *
     * @returns {boolean} Whether site has atleast one form.
     */
    hasForms: function hasForms() {
      return app.getFormOptions().length > 1;
    },
    /**
     * Block JSX parts.
     *
     * @since 1.8.1
     *
     * @type {object}
     */
    jsxParts: {
      /**
       * Get main settings JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} formOptions Form selector options.
       *
       * @returns {JSX.Element} Main setting JSX code.
       */
      getMainSettings: function getMainSettings(attributes, handlers, formOptions) {
        if (!app.hasForms()) {
          return app.jsxParts.printEmptyFormsNotice(attributes.clientId);
        }
        return /*#__PURE__*/React.createElement(InspectorControls, {
          key: "wpforms-gutenberg-form-selector-inspector-main-settings"
        }, /*#__PURE__*/React.createElement(PanelBody, {
          className: "wpforms-gutenberg-panel",
          title: strings.form_settings
        }, /*#__PURE__*/React.createElement(SelectControl, {
          label: strings.form_selected,
          value: attributes.formId,
          options: formOptions,
          onChange: function onChange(value) {
            return handlers.attrChange('formId', value);
          }
        }), /*#__PURE__*/React.createElement(ToggleControl, {
          label: strings.show_title,
          checked: attributes.displayTitle,
          onChange: function onChange(value) {
            return handlers.attrChange('displayTitle', value);
          }
        }), /*#__PURE__*/React.createElement(ToggleControl, {
          label: strings.show_description,
          checked: attributes.displayDesc,
          onChange: function onChange(value) {
            return handlers.attrChange('displayDesc', value);
          }
        }), /*#__PURE__*/React.createElement("p", {
          className: "wpforms-gutenberg-panel-notice"
        }, /*#__PURE__*/React.createElement("strong", null, strings.panel_notice_head), strings.panel_notice_text, /*#__PURE__*/React.createElement("a", {
          href: strings.panel_notice_link,
          rel: "noreferrer",
          target: "_blank"
        }, strings.panel_notice_link_text))));
      },
      /**
       * Print empty forms notice.
       *
       * @since 1.8.3
       *
       * @param {string} clientId Block client ID.
       *
       * @returns {JSX.Element} Field styles JSX code.
       */
      printEmptyFormsNotice: function printEmptyFormsNotice(clientId) {
        return /*#__PURE__*/React.createElement(InspectorControls, {
          key: "wpforms-gutenberg-form-selector-inspector-main-settings"
        }, /*#__PURE__*/React.createElement(PanelBody, {
          className: "wpforms-gutenberg-panel",
          title: strings.form_settings
        }, /*#__PURE__*/React.createElement("p", {
          className: "wpforms-gutenberg-panel-notice wpforms-warning wpforms-empty-form-notice",
          style: {
            display: 'block'
          }
        }, /*#__PURE__*/React.createElement("strong", null, __('You haven’t created a form, yet!', 'wpforms-lite')), __('What are you waiting for?', 'wpforms-lite')), /*#__PURE__*/React.createElement("button", {
          type: "button",
          className: "get-started-button components-button is-secondary",
          onClick: function onClick() {
            app.openBuilderPopup(clientId);
          }
        }, __('Get Started', 'wpforms-lite'))));
      },
      /**
       * Get Field styles JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} sizeOptions Size selector options.
       *
       * @returns {JSX.Element} Field styles JSX code.
       */
      getFieldStyles: function getFieldStyles(attributes, handlers, sizeOptions) {
        // eslint-disable-line max-lines-per-function

        return /*#__PURE__*/React.createElement(PanelBody, {
          className: app.getPanelClass(attributes),
          title: strings.field_styles
        }, /*#__PURE__*/React.createElement("p", {
          className: "wpforms-gutenberg-panel-notice wpforms-use-modern-notice"
        }, /*#__PURE__*/React.createElement("strong", null, strings.use_modern_notice_head), strings.use_modern_notice_text, " ", /*#__PURE__*/React.createElement("a", {
          href: strings.use_modern_notice_link,
          rel: "noreferrer",
          target: "_blank"
        }, strings.learn_more)), /*#__PURE__*/React.createElement("p", {
          className: "wpforms-gutenberg-panel-notice wpforms-warning wpforms-lead-form-notice",
          style: {
            display: 'none'
          }
        }, /*#__PURE__*/React.createElement("strong", null, strings.lead_forms_panel_notice_head), strings.lead_forms_panel_notice_text), /*#__PURE__*/React.createElement(Flex, {
          gap: 4,
          align: "flex-start",
          className: 'wpforms-gutenberg-form-selector-flex',
          justify: "space-between"
        }, /*#__PURE__*/React.createElement(FlexBlock, null, /*#__PURE__*/React.createElement(SelectControl, {
          label: strings.size,
          value: attributes.fieldSize,
          options: sizeOptions,
          onChange: function onChange(value) {
            return handlers.styleAttrChange('fieldSize', value);
          }
        })), /*#__PURE__*/React.createElement(FlexBlock, null, /*#__PURE__*/React.createElement(__experimentalUnitControl, {
          label: strings.border_radius,
          value: attributes.fieldBorderRadius,
          isUnitSelectTabbable: true,
          onChange: function onChange(value) {
            return handlers.styleAttrChange('fieldBorderRadius', value);
          }
        }))), /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-color-picker"
        }, /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-control-label"
        }, strings.colors), /*#__PURE__*/React.createElement(PanelColorSettings, {
          __experimentalIsRenderedInSidebar: true,
          enableAlpha: true,
          showTitle: false,
          className: "wpforms-gutenberg-form-selector-color-panel",
          colorSettings: [{
            value: attributes.fieldBackgroundColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('fieldBackgroundColor', value);
            },
            label: strings.background
          }, {
            value: attributes.fieldBorderColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('fieldBorderColor', value);
            },
            label: strings.border
          }, {
            value: attributes.fieldTextColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('fieldTextColor', value);
            },
            label: strings.text
          }]
        })));
      },
      /**
       * Get Label styles JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} sizeOptions Size selector options.
       *
       * @returns {JSX.Element} Label styles JSX code.
       */
      getLabelStyles: function getLabelStyles(attributes, handlers, sizeOptions) {
        return /*#__PURE__*/React.createElement(PanelBody, {
          className: app.getPanelClass(attributes),
          title: strings.label_styles
        }, /*#__PURE__*/React.createElement(SelectControl, {
          label: strings.size,
          value: attributes.labelSize,
          className: "wpforms-gutenberg-form-selector-fix-bottom-margin",
          options: sizeOptions,
          onChange: function onChange(value) {
            return handlers.styleAttrChange('labelSize', value);
          }
        }), /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-color-picker"
        }, /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-control-label"
        }, strings.colors), /*#__PURE__*/React.createElement(PanelColorSettings, {
          __experimentalIsRenderedInSidebar: true,
          enableAlpha: true,
          showTitle: false,
          className: "wpforms-gutenberg-form-selector-color-panel",
          colorSettings: [{
            value: attributes.labelColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('labelColor', value);
            },
            label: strings.label
          }, {
            value: attributes.labelSublabelColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('labelSublabelColor', value);
            },
            label: strings.sublabel_hints.replace('&amp;', '&')
          }, {
            value: attributes.labelErrorColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('labelErrorColor', value);
            },
            label: strings.error_message
          }]
        })));
      },
      /**
       * Get Button styles JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} sizeOptions Size selector options.
       *
       * @returns {JSX.Element}  Button styles JSX code.
       */
      getButtonStyles: function getButtonStyles(attributes, handlers, sizeOptions) {
        return /*#__PURE__*/React.createElement(PanelBody, {
          className: app.getPanelClass(attributes),
          title: strings.button_styles
        }, /*#__PURE__*/React.createElement(Flex, {
          gap: 4,
          align: "flex-start",
          className: 'wpforms-gutenberg-form-selector-flex',
          justify: "space-between"
        }, /*#__PURE__*/React.createElement(FlexBlock, null, /*#__PURE__*/React.createElement(SelectControl, {
          label: strings.size,
          value: attributes.buttonSize,
          options: sizeOptions,
          onChange: function onChange(value) {
            return handlers.styleAttrChange('buttonSize', value);
          }
        })), /*#__PURE__*/React.createElement(FlexBlock, null, /*#__PURE__*/React.createElement(__experimentalUnitControl, {
          onChange: function onChange(value) {
            return handlers.styleAttrChange('buttonBorderRadius', value);
          },
          label: strings.border_radius,
          isUnitSelectTabbable: true,
          value: attributes.buttonBorderRadius
        }))), /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-color-picker"
        }, /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-control-label"
        }, strings.colors), /*#__PURE__*/React.createElement(PanelColorSettings, {
          __experimentalIsRenderedInSidebar: true,
          enableAlpha: true,
          showTitle: false,
          className: "wpforms-gutenberg-form-selector-color-panel",
          colorSettings: [{
            value: attributes.buttonBackgroundColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('buttonBackgroundColor', value);
            },
            label: strings.background
          }, {
            value: attributes.buttonTextColor,
            onChange: function onChange(value) {
              return handlers.styleAttrChange('buttonTextColor', value);
            },
            label: strings.text
          }]
        }), /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-legend wpforms-button-color-notice"
        }, strings.button_color_notice)));
      },
      /**
       * Get style settings JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} sizeOptions Size selector options.
       *
       * @returns {JSX.Element} Inspector controls JSX code.
       */
      getStyleSettings: function getStyleSettings(attributes, handlers, sizeOptions) {
        return /*#__PURE__*/React.createElement(InspectorControls, {
          key: "wpforms-gutenberg-form-selector-style-settings"
        }, app.jsxParts.getFieldStyles(attributes, handlers, sizeOptions), app.jsxParts.getLabelStyles(attributes, handlers, sizeOptions), app.jsxParts.getButtonStyles(attributes, handlers, sizeOptions));
      },
      /**
       * Get advanced settings JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes Block attributes.
       * @param {object} handlers   Block event handlers.
       *
       * @returns {JSX.Element} Inspector advanced controls JSX code.
       */
      getAdvancedSettings: function getAdvancedSettings(attributes, handlers) {
        var _useState = useState(false),
          _useState2 = _slicedToArray(_useState, 2),
          isOpen = _useState2[0],
          setOpen = _useState2[1];
        var openModal = function openModal() {
          return setOpen(true);
        };
        var closeModal = function closeModal() {
          return setOpen(false);
        };
        return /*#__PURE__*/React.createElement(InspectorAdvancedControls, null, /*#__PURE__*/React.createElement("div", {
          className: app.getPanelClass(attributes)
        }, /*#__PURE__*/React.createElement(TextareaControl, {
          label: strings.copy_paste_settings,
          rows: "4",
          spellCheck: "false",
          value: attributes.copyPasteJsonValue,
          onChange: function onChange(value) {
            return handlers.pasteSettings(value);
          }
        }), /*#__PURE__*/React.createElement("div", {
          className: "wpforms-gutenberg-form-selector-legend",
          dangerouslySetInnerHTML: {
            __html: strings.copy_paste_notice
          }
        }), /*#__PURE__*/React.createElement(Button, {
          className: "wpforms-gutenberg-form-selector-reset-button",
          onClick: openModal
        }, strings.reset_style_settings)), isOpen && /*#__PURE__*/React.createElement(Modal, {
          className: "wpforms-gutenberg-modal",
          title: strings.reset_style_settings,
          onRequestClose: closeModal
        }, /*#__PURE__*/React.createElement("p", null, strings.reset_settings_confirm_text), /*#__PURE__*/React.createElement(Flex, {
          gap: 3,
          align: "center",
          justify: "flex-end"
        }, /*#__PURE__*/React.createElement(Button, {
          isSecondary: true,
          onClick: closeModal
        }, strings.btn_no), /*#__PURE__*/React.createElement(Button, {
          isPrimary: true,
          onClick: function onClick() {
            closeModal();
            handlers.resetSettings();
          }
        }, strings.btn_yes_reset))));
      },
      /**
       * Get block content JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} props Block properties.
       *
       * @returns {JSX.Element} Block content JSX code.
       */
      getBlockFormContent: function getBlockFormContent(props) {
        if (triggerServerRender) {
          return /*#__PURE__*/React.createElement(ServerSideRender, {
            key: "wpforms-gutenberg-form-selector-server-side-renderer",
            block: "wpforms/form-selector",
            attributes: props.attributes
          });
        }
        var clientId = props.clientId;
        var block = app.getBlockContainer(props);

        // In the case of empty content, use server side renderer.
        // This happens when the block is duplicated or converted to a reusable block.
        if (!block || !block.innerHTML) {
          triggerServerRender = true;
          return app.jsxParts.getBlockFormContent(props);
        }
        blocks[clientId] = blocks[clientId] || {};
        blocks[clientId].blockHTML = block.innerHTML;
        blocks[clientId].loadedFormId = props.attributes.formId;
        return /*#__PURE__*/React.createElement(Fragment, {
          key: "wpforms-gutenberg-form-selector-fragment-form-html"
        }, /*#__PURE__*/React.createElement("div", {
          dangerouslySetInnerHTML: {
            __html: blocks[clientId].blockHTML
          }
        }));
      },
      /**
       * Get block preview JSX code.
       *
       * @since 1.8.1
       *
       * @returns {JSX.Element} Block preview JSX code.
       */
      getBlockPreview: function getBlockPreview() {
        return /*#__PURE__*/React.createElement(Fragment, {
          key: "wpforms-gutenberg-form-selector-fragment-block-preview"
        }, /*#__PURE__*/React.createElement("img", {
          src: wpforms_gutenberg_form_selector.block_preview_url,
          style: {
            width: '100%'
          }
        }));
      },
      /**
       * Get block empty JSX code.
       *
       * @since 1.8.3
       *
       * @param {object} props Block properties.
       * @returns {JSX.Element} Block empty JSX code.
       */
      getEmptyFormsPreview: function getEmptyFormsPreview(props) {
        var clientId = props.clientId;
        return /*#__PURE__*/React.createElement(Fragment, {
          key: "wpforms-gutenberg-form-selector-fragment-block-empty"
        }, /*#__PURE__*/React.createElement("div", {
          className: "wpforms-no-form-preview"
        }, /*#__PURE__*/React.createElement("img", {
          src: wpforms_gutenberg_form_selector.block_empty_url
        }), /*#__PURE__*/React.createElement("p", null, createInterpolateElement(__('You can use <b>WPForms</b> to build contact forms, surveys, payment forms, and more with just a few clicks.', 'wpforms-lite'), {
          b: /*#__PURE__*/React.createElement("strong", null)
        })), /*#__PURE__*/React.createElement("button", {
          type: "button",
          className: "get-started-button components-button is-primary",
          onClick: function onClick() {
            app.openBuilderPopup(clientId);
          }
        }, __('Get Started', 'wpforms-lite')), /*#__PURE__*/React.createElement("p", {
          className: "empty-desc"
        }, createInterpolateElement(__('Need some help? Check out our <a>comprehensive guide.</a>', 'wpforms-lite'), {
          a: /*#__PURE__*/React.createElement("a", {
            href: wpforms_gutenberg_form_selector.wpforms_guide,
            target: "_blank",
            rel: "noopener noreferrer"
          })
        })), /*#__PURE__*/React.createElement("div", {
          id: "wpforms-gutenberg-popup",
          className: "wpforms-builder-popup"
        }, /*#__PURE__*/React.createElement("iframe", {
          src: "about:blank",
          width: "100%",
          height: "100%",
          id: "wpforms-builder-iframe"
        }))));
      },
      /**
       * Get block placeholder (form selector) JSX code.
       *
       * @since 1.8.1
       *
       * @param {object} attributes  Block attributes.
       * @param {object} handlers    Block event handlers.
       * @param {object} formOptions Form selector options.
       *
       * @returns {JSX.Element} Block placeholder JSX code.
       */
      getBlockPlaceholder: function getBlockPlaceholder(attributes, handlers, formOptions) {
        return /*#__PURE__*/React.createElement(Placeholder, {
          key: "wpforms-gutenberg-form-selector-wrap",
          className: "wpforms-gutenberg-form-selector-wrap"
        }, /*#__PURE__*/React.createElement("img", {
          src: wpforms_gutenberg_form_selector.logo_url
        }), /*#__PURE__*/React.createElement("h3", null, strings.title), /*#__PURE__*/React.createElement(SelectControl, {
          key: "wpforms-gutenberg-form-selector-select-control",
          value: attributes.formId,
          options: formOptions,
          onChange: function onChange(value) {
            return handlers.attrChange('formId', value);
          }
        }));
      }
    },
    /**
     * Get Style Settings panel class.
     *
     * @since 1.8.1
     *
     * @param {object} attributes Block attributes.
     *
     * @returns {string} Style Settings panel class.
     */
    getPanelClass: function getPanelClass(attributes) {
      var cssClass = 'wpforms-gutenberg-panel wpforms-block-settings-' + attributes.clientId;
      if (!app.isFullStylingEnabled()) {
        cssClass += ' disabled_panel';
      }
      return cssClass;
    },
    /**
     * Determine whether the full styling is enabled.
     *
     * @since 1.8.1
     *
     * @returns {boolean} Whether the full styling is enabled.
     */
    isFullStylingEnabled: function isFullStylingEnabled() {
      return wpforms_gutenberg_form_selector.is_modern_markup && wpforms_gutenberg_form_selector.is_full_styling;
    },
    /**
     * Get block container DOM element.
     *
     * @since 1.8.1
     *
     * @param {object} props Block properties.
     *
     * @returns {Element} Block container.
     */
    getBlockContainer: function getBlockContainer(props) {
      var blockSelector = "#block-".concat(props.clientId, " > div");
      var block = document.querySelector(blockSelector);

      // For FSE / Gutenberg plugin we need to take a look inside the iframe.
      if (!block) {
        var editorCanvas = document.querySelector('iframe[name="editor-canvas"]');
        block = editorCanvas && editorCanvas.contentWindow.document.querySelector(blockSelector);
      }
      return block;
    },
    /**
     * Get settings fields event handlers.
     *
     * @since 1.8.1
     *
     * @param {object} props Block properties.
     *
     * @returns {object} Object that contains event handlers for the settings fields.
     */
    getSettingsFieldsHandlers: function getSettingsFieldsHandlers(props) {
      // eslint-disable-line max-lines-per-function

      return {
        /**
         * Field style attribute change event handler.
         *
         * @since 1.8.1
         *
         * @param {string} attribute Attribute name.
         * @param {string} value     New attribute value.
         */
        styleAttrChange: function styleAttrChange(attribute, value) {
          var block = app.getBlockContainer(props),
            container = block.querySelector("#wpforms-".concat(props.attributes.formId)),
            property = attribute.replace(/[A-Z]/g, function (letter) {
              return "-".concat(letter.toLowerCase());
            }),
            setAttr = {};
          if (container) {
            switch (property) {
              case 'field-size':
              case 'label-size':
              case 'button-size':
                for (var key in sizes[property][value]) {
                  container.style.setProperty("--wpforms-".concat(property, "-").concat(key), sizes[property][value][key]);
                }
                break;
              default:
                container.style.setProperty("--wpforms-".concat(property), value);
            }
          }
          setAttr[attribute] = value;
          props.setAttributes(setAttr);
          triggerServerRender = false;
          this.updateCopyPasteContent();
          $(window).trigger('wpformsFormSelectorStyleAttrChange', [block, props, attribute, value]);
        },
        /**
         * Field regular attribute change event handler.
         *
         * @since 1.8.1
         *
         * @param {string} attribute Attribute name.
         * @param {string} value     New attribute value.
         */
        attrChange: function attrChange(attribute, value) {
          var setAttr = {};
          setAttr[attribute] = value;
          props.setAttributes(setAttr);
          triggerServerRender = true;
          this.updateCopyPasteContent();
        },
        /**
         * Reset Form Styles settings to defaults.
         *
         * @since 1.8.1
         */
        resetSettings: function resetSettings() {
          for (var key in defaultStyleSettings) {
            this.styleAttrChange(key, defaultStyleSettings[key]);
          }
        },
        /**
         * Update content of the "Copy/Paste" fields.
         *
         * @since 1.8.1
         */
        updateCopyPasteContent: function updateCopyPasteContent() {
          var content = {};
          var atts = wp.data.select('core/block-editor').getBlockAttributes(props.clientId);
          for (var key in defaultStyleSettings) {
            content[key] = atts[key];
          }
          props.setAttributes({
            'copyPasteJsonValue': JSON.stringify(content)
          });
        },
        /**
         * Paste settings handler.
         *
         * @since 1.8.1
         *
         * @param {string} value New attribute value.
         */
        pasteSettings: function pasteSettings(value) {
          var pasteAttributes = app.parseValidateJson(value);
          if (!pasteAttributes) {
            wp.data.dispatch('core/notices').createErrorNotice(strings.copy_paste_error, {
              id: 'wpforms-json-parse-error'
            });
            this.updateCopyPasteContent();
            return;
          }
          pasteAttributes.copyPasteJsonValue = value;
          props.setAttributes(pasteAttributes);
          triggerServerRender = true;
        }
      };
    },
    /**
     * Parse and validate JSON string.
     *
     * @since 1.8.1
     *
     * @param {string} value JSON string.
     *
     * @returns {boolean|object} Parsed JSON object OR false on error.
     */
    parseValidateJson: function parseValidateJson(value) {
      if (typeof value !== 'string') {
        return false;
      }
      var atts;
      try {
        atts = JSON.parse(value);
      } catch (error) {
        atts = false;
      }
      return atts;
    },
    /**
     * Get WPForms icon DOM element.
     *
     * @since 1.8.1
     *
     * @returns {DOM.element} WPForms icon DOM element.
     */
    getIcon: function getIcon() {
      return createElement('svg', {
        width: 20,
        height: 20,
        viewBox: '0 0 612 612',
        className: 'dashicon'
      }, createElement('path', {
        fill: 'currentColor',
        d: 'M544,0H68C30.445,0,0,30.445,0,68v476c0,37.556,30.445,68,68,68h476c37.556,0,68-30.444,68-68V68 C612,30.445,581.556,0,544,0z M464.44,68L387.6,120.02L323.34,68H464.44z M288.66,68l-64.26,52.02L147.56,68H288.66z M544,544H68 V68h22.1l136,92.14l79.9-64.6l79.56,64.6l136-92.14H544V544z M114.24,263.16h95.88v-48.28h-95.88V263.16z M114.24,360.4h95.88 v-48.62h-95.88V360.4z M242.76,360.4h255v-48.62h-255V360.4L242.76,360.4z M242.76,263.16h255v-48.28h-255V263.16L242.76,263.16z M368.22,457.3h129.54V408H368.22V457.3z'
      }));
    },
    /**
     * Get block attributes.
     *
     * @since 1.8.1
     *
     * @returns {object} Block attributes.
     */
    getBlockAttributes: function getBlockAttributes() {
      // eslint-disable-line max-lines-per-function

      return {
        clientId: {
          type: 'string',
          default: ''
        },
        formId: {
          type: 'string',
          default: defaults.formId
        },
        displayTitle: {
          type: 'boolean',
          default: defaults.displayTitle
        },
        displayDesc: {
          type: 'boolean',
          default: defaults.displayDesc
        },
        preview: {
          type: 'boolean'
        },
        fieldSize: {
          type: 'string',
          default: defaults.fieldSize
        },
        fieldBorderRadius: {
          type: 'string',
          default: defaults.fieldBorderRadius
        },
        fieldBackgroundColor: {
          type: 'string',
          default: defaults.fieldBackgroundColor
        },
        fieldBorderColor: {
          type: 'string',
          default: defaults.fieldBorderColor
        },
        fieldTextColor: {
          type: 'string',
          default: defaults.fieldTextColor
        },
        labelSize: {
          type: 'string',
          default: defaults.labelSize
        },
        labelColor: {
          type: 'string',
          default: defaults.labelColor
        },
        labelSublabelColor: {
          type: 'string',
          default: defaults.labelSublabelColor
        },
        labelErrorColor: {
          type: 'string',
          default: defaults.labelErrorColor
        },
        buttonSize: {
          type: 'string',
          default: defaults.buttonSize
        },
        buttonBorderRadius: {
          type: 'string',
          default: defaults.buttonBorderRadius
        },
        buttonBackgroundColor: {
          type: 'string',
          default: defaults.buttonBackgroundColor
        },
        buttonTextColor: {
          type: 'string',
          default: defaults.buttonTextColor
        },
        copyPasteJsonValue: {
          type: 'string',
          default: defaults.copyPasteJsonValue
        }
      };
    },
    /**
     * Get form selector options.
     *
     * @since 1.8.1
     *
     * @returns {Array} Form options.
     */
    getFormOptions: function getFormOptions() {
      var formOptions = wpforms_gutenberg_form_selector.forms.map(function (value) {
        return {
          value: value.ID,
          label: value.post_title
        };
      });
      formOptions.unshift({
        value: '',
        label: strings.form_select
      });
      return formOptions;
    },
    /**
     * Get size selector options.
     *
     * @since 1.8.1
     *
     * @returns {Array} Size options.
     */
    getSizeOptions: function getSizeOptions() {
      return [{
        label: strings.small,
        value: 'small'
      }, {
        label: strings.medium,
        value: 'medium'
      }, {
        label: strings.large,
        value: 'large'
      }];
    },
    /**
     * Event `wpformsFormSelectorEdit` handler.
     *
     * @since 1.8.1
     *
     * @param {object} e     Event object.
     * @param {object} props Block properties.
     */
    blockEdit: function blockEdit(e, props) {
      var block = app.getBlockContainer(props);
      if (!block || !block.dataset) {
        return;
      }
      app.initLeadFormSettings(block.parentElement);
    },
    /**
     * Init Lead Form Settings panels.
     *
     * @since 1.8.1
     *
     * @param {Element} block Block element.
     */
    initLeadFormSettings: function initLeadFormSettings(block) {
      if (!block || !block.dataset) {
        return;
      }
      if (!app.isFullStylingEnabled()) {
        return;
      }
      var clientId = block.dataset.block;
      var $form = $(block.querySelector('.wpforms-container'));
      var $panel = $(".wpforms-block-settings-".concat(clientId));
      if ($form.hasClass('wpforms-lead-forms-container')) {
        $panel.addClass('disabled_panel').find('.wpforms-gutenberg-panel-notice.wpforms-lead-form-notice').css('display', 'block');
        $panel.find('.wpforms-gutenberg-panel-notice.wpforms-use-modern-notice').css('display', 'none');
        return;
      }
      $panel.removeClass('disabled_panel').find('.wpforms-gutenberg-panel-notice.wpforms-lead-form-notice').css('display', 'none');
      $panel.find('.wpforms-gutenberg-panel-notice.wpforms-use-modern-notice').css('display', null);
    },
    /**
     * Event `wpformsFormSelectorFormLoaded` handler.
     *
     * @since 1.8.1
     *
     * @param {object} e Event object.
     */
    formLoaded: function formLoaded(e) {
      app.initLeadFormSettings(e.detail.block);
      app.updateAccentColors(e.detail);
      app.loadChoicesJS(e.detail);
      app.initRichTextField(e.detail.formId);
      $(e.detail.block).off('click').on('click', app.blockClick);
    },
    /**
     * Click on the block event handler.
     *
     * @since 1.8.1
     *
     * @param {object} e Event object.
     */
    blockClick: function blockClick(e) {
      app.initLeadFormSettings(e.currentTarget);
    },
    /**
     * Update accent colors of some fields in GB block in Modern Markup mode.
     *
     * @since 1.8.1
     *
     * @param {object} detail Event details object.
     */
    updateAccentColors: function updateAccentColors(detail) {
      if (!wpforms_gutenberg_form_selector.is_modern_markup || !window.WPForms || !window.WPForms.FrontendModern || !detail.block) {
        return;
      }
      var $form = $(detail.block.querySelector("#wpforms-".concat(detail.formId))),
        FrontendModern = window.WPForms.FrontendModern;
      FrontendModern.updateGBBlockPageIndicatorColor($form);
      FrontendModern.updateGBBlockIconChoicesColor($form);
      FrontendModern.updateGBBlockRatingColor($form);
    },
    /**
     * Init Modern style Dropdown fields (<select>).
     *
     * @since 1.8.1
     *
     * @param {object} detail Event details object.
     */
    loadChoicesJS: function loadChoicesJS(detail) {
      if (typeof window.Choices !== 'function') {
        return;
      }
      var $form = $(detail.block.querySelector("#wpforms-".concat(detail.formId)));
      $form.find('.choicesjs-select').each(function (idx, el) {
        var $el = $(el);
        if ($el.data('choice') === 'active') {
          return;
        }
        var args = window.wpforms_choicesjs_config || {},
          searchEnabled = $el.data('search-enabled'),
          $field = $el.closest('.wpforms-field');
        args.searchEnabled = 'undefined' !== typeof searchEnabled ? searchEnabled : true;
        args.callbackOnInit = function () {
          var self = this,
            $element = $(self.passedElement.element),
            $input = $(self.input.element),
            sizeClass = $element.data('size-class');

          // Add CSS-class for size.
          if (sizeClass) {
            $(self.containerOuter.element).addClass(sizeClass);
          }

          /**
           * If a multiple select has selected choices - hide a placeholder text.
           * In case if select is empty - we return placeholder text back.
           */
          if ($element.prop('multiple')) {
            // On init event.
            $input.data('placeholder', $input.attr('placeholder'));
            if (self.getValue(true).length) {
              $input.removeAttr('placeholder');
            }
          }
          this.disable();
          $field.find('.is-disabled').removeClass('is-disabled');
        };
        try {
          var choicesInstance = new Choices(el, args);

          // Save Choices.js instance for future access.
          $el.data('choicesjs', choicesInstance);
        } catch (e) {} // eslint-disable-line no-empty
      });
    },

    /**
     * Initialize RichText field.
     *
     * @since 1.8.1
     *
     * @param {int} formId Form ID.
     */
    initRichTextField: function initRichTextField(formId) {
      // Set default tab to `Visual`.
      $("#wpforms-".concat(formId, " .wp-editor-wrap")).removeClass('html-active').addClass('tmce-active');
    }
  };

  // Provide access to public functions/properties.
  return app;
}(document, window, jQuery);

// Initialize.
WPForms.FormSelector.init();
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfc2xpY2VkVG9BcnJheSIsImFyciIsImkiLCJfYXJyYXlXaXRoSG9sZXMiLCJfaXRlcmFibGVUb0FycmF5TGltaXQiLCJfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkiLCJfbm9uSXRlcmFibGVSZXN0IiwiVHlwZUVycm9yIiwibyIsIm1pbkxlbiIsIl9hcnJheUxpa2VUb0FycmF5IiwibiIsIk9iamVjdCIsInByb3RvdHlwZSIsInRvU3RyaW5nIiwiY2FsbCIsInNsaWNlIiwiY29uc3RydWN0b3IiLCJuYW1lIiwiQXJyYXkiLCJmcm9tIiwidGVzdCIsImxlbiIsImxlbmd0aCIsImFycjIiLCJfaSIsIlN5bWJvbCIsIml0ZXJhdG9yIiwiX3MiLCJfZSIsIl94IiwiX3IiLCJfYXJyIiwiX24iLCJfZCIsIm5leHQiLCJkb25lIiwicHVzaCIsInZhbHVlIiwiZXJyIiwicmV0dXJuIiwiaXNBcnJheSIsIldQRm9ybXMiLCJ3aW5kb3ciLCJGb3JtU2VsZWN0b3IiLCJkb2N1bWVudCIsIiQiLCJfd3AiLCJ3cCIsIl93cCRzZXJ2ZXJTaWRlUmVuZGVyIiwic2VydmVyU2lkZVJlbmRlciIsIlNlcnZlclNpZGVSZW5kZXIiLCJjb21wb25lbnRzIiwiX3dwJGVsZW1lbnQiLCJlbGVtZW50IiwiY3JlYXRlRWxlbWVudCIsIkZyYWdtZW50IiwidXNlU3RhdGUiLCJjcmVhdGVJbnRlcnBvbGF0ZUVsZW1lbnQiLCJyZWdpc3RlckJsb2NrVHlwZSIsImJsb2NrcyIsIl9yZWYiLCJibG9ja0VkaXRvciIsImVkaXRvciIsIkluc3BlY3RvckNvbnRyb2xzIiwiSW5zcGVjdG9yQWR2YW5jZWRDb250cm9scyIsIlBhbmVsQ29sb3JTZXR0aW5ncyIsIl93cCRjb21wb25lbnRzIiwiU2VsZWN0Q29udHJvbCIsIlRvZ2dsZUNvbnRyb2wiLCJQYW5lbEJvZHkiLCJQbGFjZWhvbGRlciIsIkZsZXgiLCJGbGV4QmxvY2siLCJfX2V4cGVyaW1lbnRhbFVuaXRDb250cm9sIiwiVGV4dGFyZWFDb250cm9sIiwiQnV0dG9uIiwiTW9kYWwiLCJfd3Bmb3Jtc19ndXRlbmJlcmdfZm8iLCJ3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yIiwic3RyaW5ncyIsImRlZmF1bHRzIiwic2l6ZXMiLCJkZWZhdWx0U3R5bGVTZXR0aW5ncyIsIl9fIiwiaTE4biIsInRyaWdnZXJTZXJ2ZXJSZW5kZXIiLCIkcG9wdXAiLCJhcHAiLCJpbml0IiwiaW5pdERlZmF1bHRzIiwicmVnaXN0ZXJCbG9jayIsInJlYWR5IiwiZXZlbnRzIiwib24iLCJfIiwiZGVib3VuY2UiLCJibG9ja0VkaXQiLCJmb3JtTG9hZGVkIiwib3BlbkJ1aWxkZXJQb3B1cCIsImNsaWVudElEIiwiaXNFbXB0eU9iamVjdCIsInRtcGwiLCJwYXJlbnQiLCJhZnRlciIsInNpYmxpbmdzIiwidXJsIiwiZ2V0X3N0YXJ0ZWRfdXJsIiwiJGlmcmFtZSIsImZpbmQiLCJidWlsZGVyQ2xvc2VCdXR0b25FdmVudCIsImF0dHIiLCJmYWRlSW4iLCJvZmYiLCJlIiwiYWN0aW9uIiwiZm9ybUlkIiwiZm9ybVRpdGxlIiwibmV3QmxvY2siLCJjcmVhdGVCbG9jayIsImZvcm1zIiwiSUQiLCJwb3N0X3RpdGxlIiwiZGF0YSIsImRpc3BhdGNoIiwicmVtb3ZlQmxvY2siLCJpbnNlcnRCbG9ja3MiLCJ0aXRsZSIsImRlc2NyaXB0aW9uIiwiaWNvbiIsImdldEljb24iLCJrZXl3b3JkcyIsImZvcm1fa2V5d29yZHMiLCJjYXRlZ29yeSIsImF0dHJpYnV0ZXMiLCJnZXRCbG9ja0F0dHJpYnV0ZXMiLCJzdXBwb3J0cyIsImN1c3RvbUNsYXNzTmFtZSIsImhhc0Zvcm1zIiwiZXhhbXBsZSIsInByZXZpZXciLCJlZGl0IiwicHJvcHMiLCJmb3JtT3B0aW9ucyIsImdldEZvcm1PcHRpb25zIiwic2l6ZU9wdGlvbnMiLCJnZXRTaXplT3B0aW9ucyIsImhhbmRsZXJzIiwiZ2V0U2V0dGluZ3NGaWVsZHNIYW5kbGVycyIsImNsaWVudElkIiwic2V0QXR0cmlidXRlcyIsImpzeCIsImpzeFBhcnRzIiwiZ2V0TWFpblNldHRpbmdzIiwiZ2V0RW1wdHlGb3Jtc1ByZXZpZXciLCJnZXRTdHlsZVNldHRpbmdzIiwiZ2V0QWR2YW5jZWRTZXR0aW5ncyIsImdldEJsb2NrRm9ybUNvbnRlbnQiLCJ1cGRhdGVDb3B5UGFzdGVDb250ZW50IiwidHJpZ2dlciIsImdldEJsb2NrUHJldmlldyIsImdldEJsb2NrUGxhY2Vob2xkZXIiLCJzYXZlIiwiZm9yRWFjaCIsImtleSIsInByaW50RW1wdHlGb3Jtc05vdGljZSIsIlJlYWN0IiwiY2xhc3NOYW1lIiwiZm9ybV9zZXR0aW5ncyIsImxhYmVsIiwiZm9ybV9zZWxlY3RlZCIsIm9wdGlvbnMiLCJvbkNoYW5nZSIsImF0dHJDaGFuZ2UiLCJzaG93X3RpdGxlIiwiY2hlY2tlZCIsImRpc3BsYXlUaXRsZSIsInNob3dfZGVzY3JpcHRpb24iLCJkaXNwbGF5RGVzYyIsInBhbmVsX25vdGljZV9oZWFkIiwicGFuZWxfbm90aWNlX3RleHQiLCJocmVmIiwicGFuZWxfbm90aWNlX2xpbmsiLCJyZWwiLCJ0YXJnZXQiLCJwYW5lbF9ub3RpY2VfbGlua190ZXh0Iiwic3R5bGUiLCJkaXNwbGF5IiwidHlwZSIsIm9uQ2xpY2siLCJnZXRGaWVsZFN0eWxlcyIsImdldFBhbmVsQ2xhc3MiLCJmaWVsZF9zdHlsZXMiLCJ1c2VfbW9kZXJuX25vdGljZV9oZWFkIiwidXNlX21vZGVybl9ub3RpY2VfdGV4dCIsInVzZV9tb2Rlcm5fbm90aWNlX2xpbmsiLCJsZWFybl9tb3JlIiwibGVhZF9mb3Jtc19wYW5lbF9ub3RpY2VfaGVhZCIsImxlYWRfZm9ybXNfcGFuZWxfbm90aWNlX3RleHQiLCJnYXAiLCJhbGlnbiIsImp1c3RpZnkiLCJzaXplIiwiZmllbGRTaXplIiwic3R5bGVBdHRyQ2hhbmdlIiwiYm9yZGVyX3JhZGl1cyIsImZpZWxkQm9yZGVyUmFkaXVzIiwiaXNVbml0U2VsZWN0VGFiYmFibGUiLCJjb2xvcnMiLCJfX2V4cGVyaW1lbnRhbElzUmVuZGVyZWRJblNpZGViYXIiLCJlbmFibGVBbHBoYSIsInNob3dUaXRsZSIsImNvbG9yU2V0dGluZ3MiLCJmaWVsZEJhY2tncm91bmRDb2xvciIsImJhY2tncm91bmQiLCJmaWVsZEJvcmRlckNvbG9yIiwiYm9yZGVyIiwiZmllbGRUZXh0Q29sb3IiLCJ0ZXh0IiwiZ2V0TGFiZWxTdHlsZXMiLCJsYWJlbF9zdHlsZXMiLCJsYWJlbFNpemUiLCJsYWJlbENvbG9yIiwibGFiZWxTdWJsYWJlbENvbG9yIiwic3VibGFiZWxfaGludHMiLCJyZXBsYWNlIiwibGFiZWxFcnJvckNvbG9yIiwiZXJyb3JfbWVzc2FnZSIsImdldEJ1dHRvblN0eWxlcyIsImJ1dHRvbl9zdHlsZXMiLCJidXR0b25TaXplIiwiYnV0dG9uQm9yZGVyUmFkaXVzIiwiYnV0dG9uQmFja2dyb3VuZENvbG9yIiwiYnV0dG9uVGV4dENvbG9yIiwiYnV0dG9uX2NvbG9yX25vdGljZSIsIl91c2VTdGF0ZSIsIl91c2VTdGF0ZTIiLCJpc09wZW4iLCJzZXRPcGVuIiwib3Blbk1vZGFsIiwiY2xvc2VNb2RhbCIsImNvcHlfcGFzdGVfc2V0dGluZ3MiLCJyb3dzIiwic3BlbGxDaGVjayIsImNvcHlQYXN0ZUpzb25WYWx1ZSIsInBhc3RlU2V0dGluZ3MiLCJkYW5nZXJvdXNseVNldElubmVySFRNTCIsIl9faHRtbCIsImNvcHlfcGFzdGVfbm90aWNlIiwicmVzZXRfc3R5bGVfc2V0dGluZ3MiLCJvblJlcXVlc3RDbG9zZSIsInJlc2V0X3NldHRpbmdzX2NvbmZpcm1fdGV4dCIsImlzU2Vjb25kYXJ5IiwiYnRuX25vIiwiaXNQcmltYXJ5IiwicmVzZXRTZXR0aW5ncyIsImJ0bl95ZXNfcmVzZXQiLCJibG9jayIsImdldEJsb2NrQ29udGFpbmVyIiwiaW5uZXJIVE1MIiwiYmxvY2tIVE1MIiwibG9hZGVkRm9ybUlkIiwic3JjIiwiYmxvY2tfcHJldmlld191cmwiLCJ3aWR0aCIsImJsb2NrX2VtcHR5X3VybCIsImIiLCJhIiwid3Bmb3Jtc19ndWlkZSIsImlkIiwiaGVpZ2h0IiwibG9nb191cmwiLCJjc3NDbGFzcyIsImlzRnVsbFN0eWxpbmdFbmFibGVkIiwiaXNfbW9kZXJuX21hcmt1cCIsImlzX2Z1bGxfc3R5bGluZyIsImJsb2NrU2VsZWN0b3IiLCJjb25jYXQiLCJxdWVyeVNlbGVjdG9yIiwiZWRpdG9yQ2FudmFzIiwiY29udGVudFdpbmRvdyIsImF0dHJpYnV0ZSIsImNvbnRhaW5lciIsInByb3BlcnR5IiwibGV0dGVyIiwidG9Mb3dlckNhc2UiLCJzZXRBdHRyIiwic2V0UHJvcGVydHkiLCJjb250ZW50IiwiYXR0cyIsInNlbGVjdCIsIkpTT04iLCJzdHJpbmdpZnkiLCJwYXN0ZUF0dHJpYnV0ZXMiLCJwYXJzZVZhbGlkYXRlSnNvbiIsImNyZWF0ZUVycm9yTm90aWNlIiwiY29weV9wYXN0ZV9lcnJvciIsInBhcnNlIiwiZXJyb3IiLCJ2aWV3Qm94IiwiZmlsbCIsImQiLCJkZWZhdWx0IiwibWFwIiwidW5zaGlmdCIsImZvcm1fc2VsZWN0Iiwic21hbGwiLCJtZWRpdW0iLCJsYXJnZSIsImRhdGFzZXQiLCJpbml0TGVhZEZvcm1TZXR0aW5ncyIsInBhcmVudEVsZW1lbnQiLCIkZm9ybSIsIiRwYW5lbCIsImhhc0NsYXNzIiwiYWRkQ2xhc3MiLCJjc3MiLCJyZW1vdmVDbGFzcyIsImRldGFpbCIsInVwZGF0ZUFjY2VudENvbG9ycyIsImxvYWRDaG9pY2VzSlMiLCJpbml0UmljaFRleHRGaWVsZCIsImJsb2NrQ2xpY2siLCJjdXJyZW50VGFyZ2V0IiwiRnJvbnRlbmRNb2Rlcm4iLCJ1cGRhdGVHQkJsb2NrUGFnZUluZGljYXRvckNvbG9yIiwidXBkYXRlR0JCbG9ja0ljb25DaG9pY2VzQ29sb3IiLCJ1cGRhdGVHQkJsb2NrUmF0aW5nQ29sb3IiLCJDaG9pY2VzIiwiZWFjaCIsImlkeCIsImVsIiwiJGVsIiwiYXJncyIsIndwZm9ybXNfY2hvaWNlc2pzX2NvbmZpZyIsInNlYXJjaEVuYWJsZWQiLCIkZmllbGQiLCJjbG9zZXN0IiwiY2FsbGJhY2tPbkluaXQiLCJzZWxmIiwiJGVsZW1lbnQiLCJwYXNzZWRFbGVtZW50IiwiJGlucHV0IiwiaW5wdXQiLCJzaXplQ2xhc3MiLCJjb250YWluZXJPdXRlciIsInByb3AiLCJnZXRWYWx1ZSIsInJlbW92ZUF0dHIiLCJkaXNhYmxlIiwiY2hvaWNlc0luc3RhbmNlIiwialF1ZXJ5Il0sInNvdXJjZXMiOlsiZmFrZV9iMzcyMWZjOS5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3RvciwgQ2hvaWNlcyAqL1xuLyoganNoaW50IGVzMzogZmFsc2UsIGVzdmVyc2lvbjogNiAqL1xuXG4ndXNlIHN0cmljdCc7XG5cbi8qKlxuICogR3V0ZW5iZXJnIGVkaXRvciBibG9jay5cbiAqXG4gKiBAc2luY2UgMS44LjFcbiAqL1xudmFyIFdQRm9ybXMgPSB3aW5kb3cuV1BGb3JtcyB8fCB7fTtcblxuV1BGb3Jtcy5Gb3JtU2VsZWN0b3IgPSBXUEZvcm1zLkZvcm1TZWxlY3RvciB8fCAoIGZ1bmN0aW9uKCBkb2N1bWVudCwgd2luZG93LCAkICkge1xuXG5cdGNvbnN0IHsgc2VydmVyU2lkZVJlbmRlcjogU2VydmVyU2lkZVJlbmRlciA9IHdwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlciB9ID0gd3A7XG5cdGNvbnN0IHsgY3JlYXRlRWxlbWVudCwgRnJhZ21lbnQsIHVzZVN0YXRlLCBjcmVhdGVJbnRlcnBvbGF0ZUVsZW1lbnQgfSA9IHdwLmVsZW1lbnQ7XG5cdGNvbnN0IHsgcmVnaXN0ZXJCbG9ja1R5cGUgfSA9IHdwLmJsb2Nrcztcblx0Y29uc3QgeyBJbnNwZWN0b3JDb250cm9scywgSW5zcGVjdG9yQWR2YW5jZWRDb250cm9scywgUGFuZWxDb2xvclNldHRpbmdzIH0gPSB3cC5ibG9ja0VkaXRvciB8fCB3cC5lZGl0b3I7XG5cdGNvbnN0IHsgU2VsZWN0Q29udHJvbCwgVG9nZ2xlQ29udHJvbCwgUGFuZWxCb2R5LCBQbGFjZWhvbGRlciwgRmxleCwgRmxleEJsb2NrLCBfX2V4cGVyaW1lbnRhbFVuaXRDb250cm9sLCBUZXh0YXJlYUNvbnRyb2wsIEJ1dHRvbiwgTW9kYWwgfSA9IHdwLmNvbXBvbmVudHM7XG5cdGNvbnN0IHsgc3RyaW5ncywgZGVmYXVsdHMsIHNpemVzIH0gPSB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yO1xuXHRjb25zdCBkZWZhdWx0U3R5bGVTZXR0aW5ncyA9IGRlZmF1bHRzO1xuXHRjb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuXG5cdC8qKlxuXHQgKiBCbG9ja3MgcnVudGltZSBkYXRhLlxuXHQgKlxuXHQgKiBAc2luY2UgMS44LjFcblx0ICpcblx0ICogQHR5cGUge29iamVjdH1cblx0ICovXG5cdGxldCBibG9ja3MgPSB7fTtcblxuXHQvKipcblx0ICogV2hldGhlciBpdCBpcyBuZWVkZWQgdG8gdHJpZ2dlciBzZXJ2ZXIgcmVuZGVyaW5nLlxuXHQgKlxuXHQgKiBAc2luY2UgMS44LjFcblx0ICpcblx0ICogQHR5cGUge2Jvb2xlYW59XG5cdCAqL1xuXHRsZXQgdHJpZ2dlclNlcnZlclJlbmRlciA9IHRydWU7XG5cblx0LyoqXG5cdCAqIFBvcHVwIGNvbnRhaW5lci5cblx0ICpcblx0ICogQHNpbmNlIDEuOC4zXG5cdCAqXG5cdCAqIEB0eXBlIHtvYmplY3R9XG5cdCAqL1xuXHRsZXQgJHBvcHVwID0ge307XG5cblx0LyoqXG5cdCAqIFB1YmxpYyBmdW5jdGlvbnMgYW5kIHByb3BlcnRpZXMuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjguMVxuXHQgKlxuXHQgKiBAdHlwZSB7b2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgYXBwID0ge1xuXG5cdFx0LyoqXG5cdFx0ICogU3RhcnQgdGhlIGVuZ2luZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdGluaXQ6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRhcHAuaW5pdERlZmF1bHRzKCk7XG5cdFx0XHRhcHAucmVnaXN0ZXJCbG9jaygpO1xuXG5cdFx0XHQkKCBhcHAucmVhZHkgKTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogRG9jdW1lbnQgcmVhZHkuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKi9cblx0XHRyZWFkeTogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGFwcC5ldmVudHMoKTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnRzLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICovXG5cdFx0ZXZlbnRzOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0JCggd2luZG93IClcblx0XHRcdFx0Lm9uKCAnd3Bmb3Jtc0Zvcm1TZWxlY3RvckVkaXQnLCBfLmRlYm91bmNlKCBhcHAuYmxvY2tFZGl0LCAyNTAgKSApXG5cdFx0XHRcdC5vbiggJ3dwZm9ybXNGb3JtU2VsZWN0b3JGb3JtTG9hZGVkJywgXy5kZWJvdW5jZSggYXBwLmZvcm1Mb2FkZWQsIDI1MCApICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIE9wZW4gYnVpbGRlciBwb3B1cC5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjYuMlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGNsaWVudElEIEJsb2NrIENsaWVudCBJRC5cblx0XHQgKi9cblx0XHRvcGVuQnVpbGRlclBvcHVwOiBmdW5jdGlvbiggY2xpZW50SUQgKSB7XG5cblx0XHRcdGlmICggJC5pc0VtcHR5T2JqZWN0KCAkcG9wdXAgKSApIHtcblx0XHRcdFx0bGV0IHRtcGwgPSAkKCAnI3dwZm9ybXMtZ3V0ZW5iZXJnLXBvcHVwJyApO1xuXHRcdFx0XHRsZXQgcGFyZW50ID0gJCggJyN3cHdyYXAnICk7XG5cblx0XHRcdFx0cGFyZW50LmFmdGVyKCB0bXBsICk7XG5cblx0XHRcdFx0JHBvcHVwID0gcGFyZW50LnNpYmxpbmdzKCAnI3dwZm9ybXMtZ3V0ZW5iZXJnLXBvcHVwJyApO1xuXHRcdFx0fVxuXG5cdFx0XHRjb25zdCB1cmwgPSB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmdldF9zdGFydGVkX3VybCxcblx0XHRcdFx0JGlmcmFtZSA9ICRwb3B1cC5maW5kKCAnaWZyYW1lJyApO1xuXG5cdFx0XHRhcHAuYnVpbGRlckNsb3NlQnV0dG9uRXZlbnQoIGNsaWVudElEICk7XG5cdFx0XHQkaWZyYW1lLmF0dHIoICdzcmMnLCB1cmwgKTtcblx0XHRcdCRwb3B1cC5mYWRlSW4oKTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogQ2xvc2UgYnV0dG9uIChpbnNpZGUgdGhlIGZvcm0gYnVpbGRlcikgY2xpY2sgZXZlbnQuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjNcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBjbGllbnRJRCBCbG9jayBDbGllbnQgSUQuXG5cdFx0ICovXG5cdFx0YnVpbGRlckNsb3NlQnV0dG9uRXZlbnQ6IGZ1bmN0aW9uKCBjbGllbnRJRCApIHtcblxuXHRcdFx0JHBvcHVwXG5cdFx0XHRcdC5vZmYoICd3cGZvcm1zQnVpbGRlckluUG9wdXBDbG9zZScgKVxuXHRcdFx0XHQub24oICd3cGZvcm1zQnVpbGRlckluUG9wdXBDbG9zZScsIGZ1bmN0aW9uKCBlLCBhY3Rpb24sIGZvcm1JZCwgZm9ybVRpdGxlICkge1xuXG5cdFx0XHRcdFx0aWYgKCBhY3Rpb24gIT09ICdzYXZlZCcgfHwgISBmb3JtSWQgKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gSW5zZXJ0IGEgbmV3IGJsb2NrIHdoZW4gYSBuZXcgZm9ybSBpcyBjcmVhdGVkIGZyb20gdGhlIHBvcHVwIHRvIHVwZGF0ZSB0aGUgZm9ybSBsaXN0IGFuZCBhdHRyaWJ1dGVzLlxuXHRcdFx0XHRcdGNvbnN0IG5ld0Jsb2NrID0gd3AuYmxvY2tzLmNyZWF0ZUJsb2NrKCAnd3Bmb3Jtcy9mb3JtLXNlbGVjdG9yJywge1xuXHRcdFx0XHRcdFx0Zm9ybUlkOiBmb3JtSWQudG9TdHJpbmcoKSwgLy8gRXhwZWN0cyBzdHJpbmcgdmFsdWUsIG1ha2Ugc3VyZSB3ZSBpbnNlcnQgc3RyaW5nLlxuXHRcdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRcdC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBjYW1lbGNhc2Vcblx0XHRcdFx0XHR3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmZvcm1zID0gWyB7IElEOiBmb3JtSWQsIHBvc3RfdGl0bGU6IGZvcm1UaXRsZSB9IF07XG5cblx0XHRcdFx0XHQvLyBJbnNlcnQgYSBuZXcgYmxvY2suXG5cdFx0XHRcdFx0d3AuZGF0YS5kaXNwYXRjaCggJ2NvcmUvYmxvY2stZWRpdG9yJyApLnJlbW92ZUJsb2NrKCBjbGllbnRJRCApO1xuXHRcdFx0XHRcdHdwLmRhdGEuZGlzcGF0Y2goICdjb3JlL2Jsb2NrLWVkaXRvcicgKS5pbnNlcnRCbG9ja3MoIG5ld0Jsb2NrICk7XG5cblx0XHRcdFx0fSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBSZWdpc3RlciBibG9jay5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBtYXgtbGluZXMtcGVyLWZ1bmN0aW9uXG5cdFx0cmVnaXN0ZXJCbG9jazogZnVuY3Rpb24oKSB7XG5cblx0XHRcdHJlZ2lzdGVyQmxvY2tUeXBlKCAnd3Bmb3Jtcy9mb3JtLXNlbGVjdG9yJywge1xuXHRcdFx0XHR0aXRsZTogc3RyaW5ncy50aXRsZSxcblx0XHRcdFx0ZGVzY3JpcHRpb246IHN0cmluZ3MuZGVzY3JpcHRpb24sXG5cdFx0XHRcdGljb246IGFwcC5nZXRJY29uKCksXG5cdFx0XHRcdGtleXdvcmRzOiBzdHJpbmdzLmZvcm1fa2V5d29yZHMsXG5cdFx0XHRcdGNhdGVnb3J5OiAnd2lkZ2V0cycsXG5cdFx0XHRcdGF0dHJpYnV0ZXM6IGFwcC5nZXRCbG9ja0F0dHJpYnV0ZXMoKSxcblx0XHRcdFx0c3VwcG9ydHM6IHtcblx0XHRcdFx0XHRjdXN0b21DbGFzc05hbWU6IGFwcC5oYXNGb3JtcygpLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRleGFtcGxlOiB7XG5cdFx0XHRcdFx0YXR0cmlidXRlczoge1xuXHRcdFx0XHRcdFx0cHJldmlldzogdHJ1ZSxcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRlZGl0OiBmdW5jdGlvbiggcHJvcHMgKSB7XG5cblx0XHRcdFx0XHRjb25zdCB7IGF0dHJpYnV0ZXMgfSA9IHByb3BzO1xuXHRcdFx0XHRcdGNvbnN0IGZvcm1PcHRpb25zID0gYXBwLmdldEZvcm1PcHRpb25zKCk7XG5cdFx0XHRcdFx0Y29uc3Qgc2l6ZU9wdGlvbnMgPSBhcHAuZ2V0U2l6ZU9wdGlvbnMoKTtcblx0XHRcdFx0XHRjb25zdCBoYW5kbGVycyA9IGFwcC5nZXRTZXR0aW5nc0ZpZWxkc0hhbmRsZXJzKCBwcm9wcyApO1xuXG5cblx0XHRcdFx0XHQvLyBTdG9yZSBibG9jayBjbGllbnRJZCBpbiBhdHRyaWJ1dGVzLlxuXHRcdFx0XHRcdGlmICggISBhdHRyaWJ1dGVzLmNsaWVudElkICkge1xuXG5cdFx0XHRcdFx0XHQvLyBXZSBqdXN0IHdhbnQgY2xpZW50IElEIHRvIHVwZGF0ZSBvbmNlLlxuXHRcdFx0XHRcdFx0Ly8gVGhlIGJsb2NrIGVkaXRvciBkb2Vzbid0IGhhdmUgYSBmaXhlZCBibG9jayBJRCwgc28gd2UgbmVlZCB0byBnZXQgaXQgb24gdGhlIGluaXRpYWwgbG9hZCwgYnV0IG9ubHkgb25jZS5cblx0XHRcdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgY2xpZW50SWQ6IHByb3BzLmNsaWVudElkIH0gKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvLyBNYWluIGJsb2NrIHNldHRpbmdzLlxuXHRcdFx0XHRcdGxldCBqc3ggPSBbXG5cdFx0XHRcdFx0XHRhcHAuanN4UGFydHMuZ2V0TWFpblNldHRpbmdzKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgZm9ybU9wdGlvbnMgKSxcblx0XHRcdFx0XHRdO1xuXG5cdFx0XHRcdFx0Ly8gQmxvY2sgcHJldmlldyBwaWN0dXJlLlxuXHRcdFx0XHRcdGlmICggISBhcHAuaGFzRm9ybXMoKSApIHtcblx0XHRcdFx0XHRcdGpzeC5wdXNoKFxuXHRcdFx0XHRcdFx0XHRhcHAuanN4UGFydHMuZ2V0RW1wdHlGb3Jtc1ByZXZpZXcoIHByb3BzICksXG5cdFx0XHRcdFx0XHQpO1xuXG5cdFx0XHRcdFx0XHRyZXR1cm4ganN4O1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEZvcm0gc3R5bGUgc2V0dGluZ3MgJiBibG9jayBjb250ZW50LlxuXHRcdFx0XHRcdGlmICggYXR0cmlidXRlcy5mb3JtSWQgKSB7XG5cdFx0XHRcdFx0XHRqc3gucHVzaChcblx0XHRcdFx0XHRcdFx0YXBwLmpzeFBhcnRzLmdldFN0eWxlU2V0dGluZ3MoIGF0dHJpYnV0ZXMsIGhhbmRsZXJzLCBzaXplT3B0aW9ucyApLFxuXHRcdFx0XHRcdFx0XHRhcHAuanN4UGFydHMuZ2V0QWR2YW5jZWRTZXR0aW5ncyggYXR0cmlidXRlcywgaGFuZGxlcnMgKSxcblx0XHRcdFx0XHRcdFx0YXBwLmpzeFBhcnRzLmdldEJsb2NrRm9ybUNvbnRlbnQoIHByb3BzICksXG5cdFx0XHRcdFx0XHQpO1xuXG5cdFx0XHRcdFx0XHRoYW5kbGVycy51cGRhdGVDb3B5UGFzdGVDb250ZW50KCk7XG5cblx0XHRcdFx0XHRcdCQoIHdpbmRvdyApLnRyaWdnZXIoICd3cGZvcm1zRm9ybVNlbGVjdG9yRWRpdCcsIFsgcHJvcHMgXSApO1xuXG5cdFx0XHRcdFx0XHRyZXR1cm4ganN4O1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEJsb2NrIHByZXZpZXcgcGljdHVyZS5cblx0XHRcdFx0XHRpZiAoIGF0dHJpYnV0ZXMucHJldmlldyApIHtcblx0XHRcdFx0XHRcdGpzeC5wdXNoKFxuXHRcdFx0XHRcdFx0XHRhcHAuanN4UGFydHMuZ2V0QmxvY2tQcmV2aWV3KCksXG5cdFx0XHRcdFx0XHQpO1xuXG5cdFx0XHRcdFx0XHRyZXR1cm4ganN4O1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEJsb2NrIHBsYWNlaG9sZGVyIChmb3JtIHNlbGVjdG9yKS5cblx0XHRcdFx0XHRqc3gucHVzaChcblx0XHRcdFx0XHRcdGFwcC5qc3hQYXJ0cy5nZXRCbG9ja1BsYWNlaG9sZGVyKCBwcm9wcy5hdHRyaWJ1dGVzLCBoYW5kbGVycywgZm9ybU9wdGlvbnMgKSxcblx0XHRcdFx0XHQpO1xuXG5cdFx0XHRcdFx0cmV0dXJuIGpzeDtcblx0XHRcdFx0fSxcblx0XHRcdFx0c2F2ZTogKCkgPT4gbnVsbCxcblx0XHRcdH0gKTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBkZWZhdWx0IHN0eWxlIHNldHRpbmdzLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICovXG5cdFx0aW5pdERlZmF1bHRzOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0WyAnZm9ybUlkJywgJ2NvcHlQYXN0ZUpzb25WYWx1ZScgXS5mb3JFYWNoKCBrZXkgPT4gZGVsZXRlIGRlZmF1bHRTdHlsZVNldHRpbmdzWyBrZXkgXSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBDaGVjayBpZiBzaXRlIGhhcyBmb3Jtcy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguM1xuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59IFdoZXRoZXIgc2l0ZSBoYXMgYXRsZWFzdCBvbmUgZm9ybS5cblx0XHQgKi9cblx0XHRoYXNGb3JtczogZnVuY3Rpb24oKSB7XG5cdFx0XHRyZXR1cm4gYXBwLmdldEZvcm1PcHRpb25zKCkubGVuZ3RoID4gMTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogQmxvY2sgSlNYIHBhcnRzLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdCAqL1xuXHRcdGpzeFBhcnRzOiB7XG5cblx0XHRcdC8qKlxuXHRcdFx0ICogR2V0IG1haW4gc2V0dGluZ3MgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGF0dHJpYnV0ZXMgIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICAgQmxvY2sgZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gZm9ybU9wdGlvbnMgRm9ybSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gTWFpbiBzZXR0aW5nIEpTWCBjb2RlLlxuXHRcdFx0ICovXG5cdFx0XHRnZXRNYWluU2V0dGluZ3M6IGZ1bmN0aW9uKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgZm9ybU9wdGlvbnMgKSB7XG5cblx0XHRcdFx0aWYgKCAhIGFwcC5oYXNGb3JtcygpICkge1xuXHRcdFx0XHRcdHJldHVybiBhcHAuanN4UGFydHMucHJpbnRFbXB0eUZvcm1zTm90aWNlKCBhdHRyaWJ1dGVzLmNsaWVudElkICk7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRyZXR1cm4gKFxuXHRcdFx0XHRcdDxJbnNwZWN0b3JDb250cm9scyBrZXk9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWluc3BlY3Rvci1tYWluLXNldHRpbmdzXCI+XG5cdFx0XHRcdFx0XHQ8UGFuZWxCb2R5IGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLXBhbmVsXCIgdGl0bGU9eyBzdHJpbmdzLmZvcm1fc2V0dGluZ3MgfT5cblx0XHRcdFx0XHRcdFx0PFNlbGVjdENvbnRyb2xcblx0XHRcdFx0XHRcdFx0XHRsYWJlbD17IHN0cmluZ3MuZm9ybV9zZWxlY3RlZCB9XG5cdFx0XHRcdFx0XHRcdFx0dmFsdWU9eyBhdHRyaWJ1dGVzLmZvcm1JZCB9XG5cdFx0XHRcdFx0XHRcdFx0b3B0aW9ucz17IGZvcm1PcHRpb25zIH1cblx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZT17IHZhbHVlID0+IGhhbmRsZXJzLmF0dHJDaGFuZ2UoICdmb3JtSWQnLCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0Lz5cblx0XHRcdFx0XHRcdFx0PFRvZ2dsZUNvbnRyb2xcblx0XHRcdFx0XHRcdFx0XHRsYWJlbD17IHN0cmluZ3Muc2hvd190aXRsZSB9XG5cdFx0XHRcdFx0XHRcdFx0Y2hlY2tlZD17IGF0dHJpYnV0ZXMuZGlzcGxheVRpdGxlIH1cblx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZT17IHZhbHVlID0+IGhhbmRsZXJzLmF0dHJDaGFuZ2UoICdkaXNwbGF5VGl0bGUnLCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0Lz5cblx0XHRcdFx0XHRcdFx0PFRvZ2dsZUNvbnRyb2xcblx0XHRcdFx0XHRcdFx0XHRsYWJlbD17IHN0cmluZ3Muc2hvd19kZXNjcmlwdGlvbiB9XG5cdFx0XHRcdFx0XHRcdFx0Y2hlY2tlZD17IGF0dHJpYnV0ZXMuZGlzcGxheURlc2MgfVxuXHRcdFx0XHRcdFx0XHRcdG9uQ2hhbmdlPXsgdmFsdWUgPT4gaGFuZGxlcnMuYXR0ckNoYW5nZSggJ2Rpc3BsYXlEZXNjJywgdmFsdWUgKSB9XG5cdFx0XHRcdFx0XHRcdC8+XG5cdFx0XHRcdFx0XHRcdDxwIGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLXBhbmVsLW5vdGljZVwiPlxuXHRcdFx0XHRcdFx0XHRcdDxzdHJvbmc+eyBzdHJpbmdzLnBhbmVsX25vdGljZV9oZWFkIH08L3N0cm9uZz5cblx0XHRcdFx0XHRcdFx0XHR7IHN0cmluZ3MucGFuZWxfbm90aWNlX3RleHQgfVxuXHRcdFx0XHRcdFx0XHRcdDxhIGhyZWY9e3N0cmluZ3MucGFuZWxfbm90aWNlX2xpbmt9IHJlbD1cIm5vcmVmZXJyZXJcIiB0YXJnZXQ9XCJfYmxhbmtcIj57IHN0cmluZ3MucGFuZWxfbm90aWNlX2xpbmtfdGV4dCB9PC9hPlxuXHRcdFx0XHRcdFx0XHQ8L3A+XG5cdFx0XHRcdFx0XHQ8L1BhbmVsQm9keT5cblx0XHRcdFx0XHQ8L0luc3BlY3RvckNvbnRyb2xzPlxuXHRcdFx0XHQpO1xuXHRcdFx0fSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBQcmludCBlbXB0eSBmb3JtcyBub3RpY2UuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtzdHJpbmd9IGNsaWVudElkIEJsb2NrIGNsaWVudCBJRC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJucyB7SlNYLkVsZW1lbnR9IEZpZWxkIHN0eWxlcyBKU1ggY29kZS5cblx0XHRcdCAqL1xuXHRcdFx0cHJpbnRFbXB0eUZvcm1zTm90aWNlOiBmdW5jdGlvbiggY2xpZW50SWQgKSB7XG5cdFx0XHRcdHJldHVybiAoXG5cdFx0XHRcdFx0PEluc3BlY3RvckNvbnRyb2xzIGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItaW5zcGVjdG9yLW1haW4tc2V0dGluZ3NcIj5cblx0XHRcdFx0XHRcdDxQYW5lbEJvZHkgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctcGFuZWxcIiB0aXRsZT17IHN0cmluZ3MuZm9ybV9zZXR0aW5ncyB9PlxuXHRcdFx0XHRcdFx0XHQ8cCBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1wYW5lbC1ub3RpY2Ugd3Bmb3Jtcy13YXJuaW5nIHdwZm9ybXMtZW1wdHktZm9ybS1ub3RpY2VcIiBzdHlsZT17eyBkaXNwbGF5OiAnYmxvY2snIH19PlxuXHRcdFx0XHRcdFx0XHRcdDxzdHJvbmc+eyBfXyggJ1lvdSBoYXZlbuKAmXQgY3JlYXRlZCBhIGZvcm0sIHlldCEnLCAnd3Bmb3Jtcy1saXRlJyApIH08L3N0cm9uZz5cblx0XHRcdFx0XHRcdFx0XHR7IF9fKCAnV2hhdCBhcmUgeW91IHdhaXRpbmcgZm9yPycsICd3cGZvcm1zLWxpdGUnICkgfVxuXHRcdFx0XHRcdFx0XHQ8L3A+XG5cdFx0XHRcdFx0XHRcdDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzTmFtZT1cImdldC1zdGFydGVkLWJ1dHRvbiBjb21wb25lbnRzLWJ1dHRvbiBpcy1zZWNvbmRhcnlcIlxuXHRcdFx0XHRcdFx0XHRcdG9uQ2xpY2s9e1xuXHRcdFx0XHRcdFx0XHRcdFx0KCkgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRhcHAub3BlbkJ1aWxkZXJQb3B1cCggY2xpZW50SWQgKTtcblx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdFx0XHR7IF9fKCAnR2V0IFN0YXJ0ZWQnLCAnd3Bmb3Jtcy1saXRlJyApIH1cblx0XHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0XHQ8L1BhbmVsQm9keT5cblx0XHRcdFx0XHQ8L0luc3BlY3RvckNvbnRyb2xzPlxuXHRcdFx0XHQpO1xuXHRcdFx0fSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBHZXQgRmllbGQgc3R5bGVzIEpTWCBjb2RlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBhdHRyaWJ1dGVzICBCbG9jayBhdHRyaWJ1dGVzLlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGhhbmRsZXJzICAgIEJsb2NrIGV2ZW50IGhhbmRsZXJzLlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IHNpemVPcHRpb25zIFNpemUgc2VsZWN0b3Igb3B0aW9ucy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJucyB7SlNYLkVsZW1lbnR9IEZpZWxkIHN0eWxlcyBKU1ggY29kZS5cblx0XHRcdCAqL1xuXHRcdFx0Z2V0RmllbGRTdHlsZXM6IGZ1bmN0aW9uKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgc2l6ZU9wdGlvbnMgKSB7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbWF4LWxpbmVzLXBlci1mdW5jdGlvblxuXG5cdFx0XHRcdHJldHVybiAoXG5cdFx0XHRcdFx0PFBhbmVsQm9keSBjbGFzc05hbWU9eyBhcHAuZ2V0UGFuZWxDbGFzcyggYXR0cmlidXRlcyApIH0gdGl0bGU9eyBzdHJpbmdzLmZpZWxkX3N0eWxlcyB9PlxuXHRcdFx0XHRcdFx0PHAgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctcGFuZWwtbm90aWNlIHdwZm9ybXMtdXNlLW1vZGVybi1ub3RpY2VcIj5cblx0XHRcdFx0XHRcdFx0PHN0cm9uZz57IHN0cmluZ3MudXNlX21vZGVybl9ub3RpY2VfaGVhZCB9PC9zdHJvbmc+XG5cdFx0XHRcdFx0XHRcdHsgc3RyaW5ncy51c2VfbW9kZXJuX25vdGljZV90ZXh0IH0gPGEgaHJlZj17c3RyaW5ncy51c2VfbW9kZXJuX25vdGljZV9saW5rfSByZWw9XCJub3JlZmVycmVyXCIgdGFyZ2V0PVwiX2JsYW5rXCI+eyBzdHJpbmdzLmxlYXJuX21vcmUgfTwvYT5cblx0XHRcdFx0XHRcdDwvcD5cblxuXHRcdFx0XHRcdFx0PHAgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctcGFuZWwtbm90aWNlIHdwZm9ybXMtd2FybmluZyB3cGZvcm1zLWxlYWQtZm9ybS1ub3RpY2VcIiBzdHlsZT17eyBkaXNwbGF5OiAnbm9uZScgfX0+XG5cdFx0XHRcdFx0XHRcdDxzdHJvbmc+eyBzdHJpbmdzLmxlYWRfZm9ybXNfcGFuZWxfbm90aWNlX2hlYWQgfTwvc3Ryb25nPlxuXHRcdFx0XHRcdFx0XHR7IHN0cmluZ3MubGVhZF9mb3Jtc19wYW5lbF9ub3RpY2VfdGV4dCB9XG5cdFx0XHRcdFx0XHQ8L3A+XG5cblx0XHRcdFx0XHRcdDxGbGV4IGdhcD17NH0gYWxpZ249XCJmbGV4LXN0YXJ0XCIgY2xhc3NOYW1lPXsnd3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1mbGV4J30ganVzdGlmeT1cInNwYWNlLWJldHdlZW5cIj5cblx0XHRcdFx0XHRcdFx0PEZsZXhCbG9jaz5cblx0XHRcdFx0XHRcdFx0XHQ8U2VsZWN0Q29udHJvbFxuXHRcdFx0XHRcdFx0XHRcdFx0bGFiZWw9eyBzdHJpbmdzLnNpemUgfVxuXHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU9eyBhdHRyaWJ1dGVzLmZpZWxkU2l6ZSB9XG5cdFx0XHRcdFx0XHRcdFx0XHRvcHRpb25zPXsgc2l6ZU9wdGlvbnMgfVxuXHRcdFx0XHRcdFx0XHRcdFx0b25DaGFuZ2U9eyB2YWx1ZSA9PiBoYW5kbGVycy5zdHlsZUF0dHJDaGFuZ2UoICdmaWVsZFNpemUnLCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdFx0XHQ8L0ZsZXhCbG9jaz5cblx0XHRcdFx0XHRcdFx0PEZsZXhCbG9jaz5cblx0XHRcdFx0XHRcdFx0XHQ8X19leHBlcmltZW50YWxVbml0Q29udHJvbFxuXHRcdFx0XHRcdFx0XHRcdFx0bGFiZWw9eyBzdHJpbmdzLmJvcmRlcl9yYWRpdXMgfVxuXHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU9eyBhdHRyaWJ1dGVzLmZpZWxkQm9yZGVyUmFkaXVzIH1cblx0XHRcdFx0XHRcdFx0XHRcdGlzVW5pdFNlbGVjdFRhYmJhYmxlXG5cdFx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZT17IHZhbHVlID0+IGhhbmRsZXJzLnN0eWxlQXR0ckNoYW5nZSggJ2ZpZWxkQm9yZGVyUmFkaXVzJywgdmFsdWUgKSB9XG5cdFx0XHRcdFx0XHRcdFx0Lz5cblx0XHRcdFx0XHRcdFx0PC9GbGV4QmxvY2s+XG5cdFx0XHRcdFx0XHQ8L0ZsZXg+XG5cblx0XHRcdFx0XHRcdDxkaXYgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1jb2xvci1waWNrZXJcIj5cblx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWNvbnRyb2wtbGFiZWxcIj57IHN0cmluZ3MuY29sb3JzIH08L2Rpdj5cblx0XHRcdFx0XHRcdFx0PFBhbmVsQ29sb3JTZXR0aW5nc1xuXHRcdFx0XHRcdFx0XHRcdF9fZXhwZXJpbWVudGFsSXNSZW5kZXJlZEluU2lkZWJhclxuXHRcdFx0XHRcdFx0XHRcdGVuYWJsZUFscGhhXG5cdFx0XHRcdFx0XHRcdFx0c2hvd1RpdGxlPXsgZmFsc2UgfVxuXHRcdFx0XHRcdFx0XHRcdGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItY29sb3ItcGFuZWxcIlxuXHRcdFx0XHRcdFx0XHRcdGNvbG9yU2V0dGluZ3M9e1tcblx0XHRcdFx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU6IGF0dHJpYnV0ZXMuZmllbGRCYWNrZ3JvdW5kQ29sb3IsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdG9uQ2hhbmdlOiB2YWx1ZSA9PiBoYW5kbGVycy5zdHlsZUF0dHJDaGFuZ2UoICdmaWVsZEJhY2tncm91bmRDb2xvcicsIHZhbHVlICksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsOiBzdHJpbmdzLmJhY2tncm91bmQsXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZTogYXR0cmlidXRlcy5maWVsZEJvcmRlckNvbG9yLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZTogdmFsdWUgPT4gaGFuZGxlcnMuc3R5bGVBdHRyQ2hhbmdlKCAnZmllbGRCb3JkZXJDb2xvcicsIHZhbHVlICksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsOiBzdHJpbmdzLmJvcmRlcixcblx0XHRcdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlOiBhdHRyaWJ1dGVzLmZpZWxkVGV4dENvbG9yLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZTogdmFsdWUgPT4gaGFuZGxlcnMuc3R5bGVBdHRyQ2hhbmdlKCAnZmllbGRUZXh0Q29sb3InLCB2YWx1ZSApLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRsYWJlbDogc3RyaW5ncy50ZXh0LFxuXHRcdFx0XHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdFx0XHRdfVxuXHRcdFx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0PC9QYW5lbEJvZHk+XG5cdFx0XHRcdCk7XG5cdFx0XHR9LFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEdldCBMYWJlbCBzdHlsZXMgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGF0dHJpYnV0ZXMgIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICAgQmxvY2sgZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gc2l6ZU9wdGlvbnMgU2l6ZSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gTGFiZWwgc3R5bGVzIEpTWCBjb2RlLlxuXHRcdFx0ICovXG5cdFx0XHRnZXRMYWJlbFN0eWxlczogZnVuY3Rpb24oIGF0dHJpYnV0ZXMsIGhhbmRsZXJzLCBzaXplT3B0aW9ucyApIHtcblxuXHRcdFx0XHRyZXR1cm4gKFxuXHRcdFx0XHRcdDxQYW5lbEJvZHkgY2xhc3NOYW1lPXsgYXBwLmdldFBhbmVsQ2xhc3MoIGF0dHJpYnV0ZXMgKSB9IHRpdGxlPXsgc3RyaW5ncy5sYWJlbF9zdHlsZXMgfT5cblx0XHRcdFx0XHRcdDxTZWxlY3RDb250cm9sXG5cdFx0XHRcdFx0XHRcdGxhYmVsPXsgc3RyaW5ncy5zaXplIH1cblx0XHRcdFx0XHRcdFx0dmFsdWU9eyBhdHRyaWJ1dGVzLmxhYmVsU2l6ZSB9XG5cdFx0XHRcdFx0XHRcdGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItZml4LWJvdHRvbS1tYXJnaW5cIlxuXHRcdFx0XHRcdFx0XHRvcHRpb25zPXsgc2l6ZU9wdGlvbnN9XG5cdFx0XHRcdFx0XHRcdG9uQ2hhbmdlPXsgdmFsdWUgPT4gaGFuZGxlcnMuc3R5bGVBdHRyQ2hhbmdlKCAnbGFiZWxTaXplJywgdmFsdWUgKSB9XG5cdFx0XHRcdFx0XHQvPlxuXG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItY29sb3ItcGlja2VyXCI+XG5cdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1jb250cm9sLWxhYmVsXCI+eyBzdHJpbmdzLmNvbG9ycyB9PC9kaXY+XG5cdFx0XHRcdFx0XHRcdDxQYW5lbENvbG9yU2V0dGluZ3Ncblx0XHRcdFx0XHRcdFx0XHRfX2V4cGVyaW1lbnRhbElzUmVuZGVyZWRJblNpZGViYXJcblx0XHRcdFx0XHRcdFx0XHRlbmFibGVBbHBoYVxuXHRcdFx0XHRcdFx0XHRcdHNob3dUaXRsZT17IGZhbHNlIH1cblx0XHRcdFx0XHRcdFx0XHRjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWNvbG9yLXBhbmVsXCJcblx0XHRcdFx0XHRcdFx0XHRjb2xvclNldHRpbmdzPXtbXG5cdFx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlOiBhdHRyaWJ1dGVzLmxhYmVsQ29sb3IsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdG9uQ2hhbmdlOiB2YWx1ZSA9PiBoYW5kbGVycy5zdHlsZUF0dHJDaGFuZ2UoICdsYWJlbENvbG9yJywgdmFsdWUgKSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0bGFiZWw6IHN0cmluZ3MubGFiZWwsXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZTogYXR0cmlidXRlcy5sYWJlbFN1YmxhYmVsQ29sb3IsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdG9uQ2hhbmdlOiB2YWx1ZSA9PiBoYW5kbGVycy5zdHlsZUF0dHJDaGFuZ2UoICdsYWJlbFN1YmxhYmVsQ29sb3InLCB2YWx1ZSApLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRsYWJlbDogc3RyaW5ncy5zdWJsYWJlbF9oaW50cy5yZXBsYWNlKCAnJmFtcDsnLCAnJicgKSxcblx0XHRcdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlOiBhdHRyaWJ1dGVzLmxhYmVsRXJyb3JDb2xvcixcblx0XHRcdFx0XHRcdFx0XHRcdFx0b25DaGFuZ2U6IHZhbHVlID0+IGhhbmRsZXJzLnN0eWxlQXR0ckNoYW5nZSggJ2xhYmVsRXJyb3JDb2xvcicsIHZhbHVlICksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsOiBzdHJpbmdzLmVycm9yX21lc3NhZ2UsXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdF19XG5cdFx0XHRcdFx0XHRcdC8+XG5cdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHQ8L1BhbmVsQm9keT5cblx0XHRcdFx0KTtcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogR2V0IEJ1dHRvbiBzdHlsZXMgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGF0dHJpYnV0ZXMgIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICAgQmxvY2sgZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gc2l6ZU9wdGlvbnMgU2l6ZSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gIEJ1dHRvbiBzdHlsZXMgSlNYIGNvZGUuXG5cdFx0XHQgKi9cblx0XHRcdGdldEJ1dHRvblN0eWxlczogZnVuY3Rpb24oIGF0dHJpYnV0ZXMsIGhhbmRsZXJzLCBzaXplT3B0aW9ucyApIHtcblxuXHRcdFx0XHRyZXR1cm4gKFxuXHRcdFx0XHRcdDxQYW5lbEJvZHkgY2xhc3NOYW1lPXsgYXBwLmdldFBhbmVsQ2xhc3MoIGF0dHJpYnV0ZXMgKSB9IHRpdGxlPXsgc3RyaW5ncy5idXR0b25fc3R5bGVzIH0+XG5cdFx0XHRcdFx0XHQ8RmxleCBnYXA9ezR9IGFsaWduPVwiZmxleC1zdGFydFwiIGNsYXNzTmFtZT17J3dwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItZmxleCd9IGp1c3RpZnk9XCJzcGFjZS1iZXR3ZWVuXCI+XG5cdFx0XHRcdFx0XHRcdDxGbGV4QmxvY2s+XG5cdFx0XHRcdFx0XHRcdFx0PFNlbGVjdENvbnRyb2xcblx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsPXsgc3RyaW5ncy5zaXplIH1cblx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlPXsgYXR0cmlidXRlcy5idXR0b25TaXplIH1cblx0XHRcdFx0XHRcdFx0XHRcdG9wdGlvbnM9eyBzaXplT3B0aW9ucyB9XG5cdFx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZT17IHZhbHVlID0+IGhhbmRsZXJzLnN0eWxlQXR0ckNoYW5nZSggJ2J1dHRvblNpemUnLCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdFx0XHQ8L0ZsZXhCbG9jaz5cblx0XHRcdFx0XHRcdFx0PEZsZXhCbG9jaz5cblx0XHRcdFx0XHRcdFx0XHQ8X19leHBlcmltZW50YWxVbml0Q29udHJvbFxuXHRcdFx0XHRcdFx0XHRcdFx0b25DaGFuZ2U9eyB2YWx1ZSA9PiBoYW5kbGVycy5zdHlsZUF0dHJDaGFuZ2UoICdidXR0b25Cb3JkZXJSYWRpdXMnLCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsPXsgc3RyaW5ncy5ib3JkZXJfcmFkaXVzIH1cblx0XHRcdFx0XHRcdFx0XHRcdGlzVW5pdFNlbGVjdFRhYmJhYmxlXG5cdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT17IGF0dHJpYnV0ZXMuYnV0dG9uQm9yZGVyUmFkaXVzIH0gLz5cblx0XHRcdFx0XHRcdFx0PC9GbGV4QmxvY2s+XG5cdFx0XHRcdFx0XHQ8L0ZsZXg+XG5cblx0XHRcdFx0XHRcdDxkaXYgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1jb2xvci1waWNrZXJcIj5cblx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWNvbnRyb2wtbGFiZWxcIj57IHN0cmluZ3MuY29sb3JzIH08L2Rpdj5cblx0XHRcdFx0XHRcdFx0PFBhbmVsQ29sb3JTZXR0aW5nc1xuXHRcdFx0XHRcdFx0XHRcdF9fZXhwZXJpbWVudGFsSXNSZW5kZXJlZEluU2lkZWJhclxuXHRcdFx0XHRcdFx0XHRcdGVuYWJsZUFscGhhXG5cdFx0XHRcdFx0XHRcdFx0c2hvd1RpdGxlPXsgZmFsc2UgfVxuXHRcdFx0XHRcdFx0XHRcdGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItY29sb3ItcGFuZWxcIlxuXHRcdFx0XHRcdFx0XHRcdGNvbG9yU2V0dGluZ3M9e1tcblx0XHRcdFx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU6IGF0dHJpYnV0ZXMuYnV0dG9uQmFja2dyb3VuZENvbG9yLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRvbkNoYW5nZTogdmFsdWUgPT4gaGFuZGxlcnMuc3R5bGVBdHRyQ2hhbmdlKCAnYnV0dG9uQmFja2dyb3VuZENvbG9yJywgdmFsdWUgKSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0bGFiZWw6IHN0cmluZ3MuYmFja2dyb3VuZCxcblx0XHRcdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlOiBhdHRyaWJ1dGVzLmJ1dHRvblRleHRDb2xvcixcblx0XHRcdFx0XHRcdFx0XHRcdFx0b25DaGFuZ2U6IHZhbHVlID0+IGhhbmRsZXJzLnN0eWxlQXR0ckNoYW5nZSggJ2J1dHRvblRleHRDb2xvcicsIHZhbHVlICksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGxhYmVsOiBzdHJpbmdzLnRleHQsXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdF19IC8+XG5cdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3NOYW1lPVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1sZWdlbmQgd3Bmb3Jtcy1idXR0b24tY29sb3Itbm90aWNlXCI+XG5cdFx0XHRcdFx0XHRcdFx0eyBzdHJpbmdzLmJ1dHRvbl9jb2xvcl9ub3RpY2UgfVxuXHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdDwvUGFuZWxCb2R5PlxuXHRcdFx0XHQpO1xuXHRcdFx0fSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBHZXQgc3R5bGUgc2V0dGluZ3MgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGF0dHJpYnV0ZXMgIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICAgQmxvY2sgZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gc2l6ZU9wdGlvbnMgU2l6ZSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gSW5zcGVjdG9yIGNvbnRyb2xzIEpTWCBjb2RlLlxuXHRcdFx0ICovXG5cdFx0XHRnZXRTdHlsZVNldHRpbmdzOiBmdW5jdGlvbiggYXR0cmlidXRlcywgaGFuZGxlcnMsIHNpemVPcHRpb25zICkge1xuXG5cdFx0XHRcdHJldHVybiAoXG5cdFx0XHRcdFx0PEluc3BlY3RvckNvbnRyb2xzIGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itc3R5bGUtc2V0dGluZ3NcIj5cblx0XHRcdFx0XHRcdHsgYXBwLmpzeFBhcnRzLmdldEZpZWxkU3R5bGVzKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgc2l6ZU9wdGlvbnMgKSB9XG5cdFx0XHRcdFx0XHR7IGFwcC5qc3hQYXJ0cy5nZXRMYWJlbFN0eWxlcyggYXR0cmlidXRlcywgaGFuZGxlcnMsIHNpemVPcHRpb25zICkgfVxuXHRcdFx0XHRcdFx0eyBhcHAuanN4UGFydHMuZ2V0QnV0dG9uU3R5bGVzKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgc2l6ZU9wdGlvbnMgKSB9XG5cdFx0XHRcdFx0PC9JbnNwZWN0b3JDb250cm9scz5cblx0XHRcdFx0KTtcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogR2V0IGFkdmFuY2VkIHNldHRpbmdzIEpTWCBjb2RlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBhdHRyaWJ1dGVzIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICBCbG9jayBldmVudCBoYW5kbGVycy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJucyB7SlNYLkVsZW1lbnR9IEluc3BlY3RvciBhZHZhbmNlZCBjb250cm9scyBKU1ggY29kZS5cblx0XHRcdCAqL1xuXHRcdFx0Z2V0QWR2YW5jZWRTZXR0aW5nczogZnVuY3Rpb24oIGF0dHJpYnV0ZXMsIGhhbmRsZXJzICkge1xuXG5cdFx0XHRcdGNvbnN0IFsgaXNPcGVuLCBzZXRPcGVuIF0gPSB1c2VTdGF0ZSggZmFsc2UgKTtcblx0XHRcdFx0Y29uc3Qgb3Blbk1vZGFsID0gKCkgPT4gc2V0T3BlbiggdHJ1ZSApO1xuXHRcdFx0XHRjb25zdCBjbG9zZU1vZGFsID0gKCkgPT4gc2V0T3BlbiggZmFsc2UgKTtcblxuXHRcdFx0XHRyZXR1cm4gKFxuXHRcdFx0XHRcdDxJbnNwZWN0b3JBZHZhbmNlZENvbnRyb2xzPlxuXHRcdFx0XHRcdFx0PGRpdiBjbGFzc05hbWU9eyBhcHAuZ2V0UGFuZWxDbGFzcyggYXR0cmlidXRlcyApIH0+XG5cdFx0XHRcdFx0XHRcdDxUZXh0YXJlYUNvbnRyb2xcblx0XHRcdFx0XHRcdFx0XHRsYWJlbD17IHN0cmluZ3MuY29weV9wYXN0ZV9zZXR0aW5ncyB9XG5cdFx0XHRcdFx0XHRcdFx0cm93cz1cIjRcIlxuXHRcdFx0XHRcdFx0XHRcdHNwZWxsQ2hlY2s9XCJmYWxzZVwiXG5cdFx0XHRcdFx0XHRcdFx0dmFsdWU9eyBhdHRyaWJ1dGVzLmNvcHlQYXN0ZUpzb25WYWx1ZSB9XG5cdFx0XHRcdFx0XHRcdFx0b25DaGFuZ2U9eyB2YWx1ZSA9PiBoYW5kbGVycy5wYXN0ZVNldHRpbmdzKCB2YWx1ZSApIH1cblx0XHRcdFx0XHRcdFx0Lz5cblx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWxlZ2VuZFwiIGRhbmdlcm91c2x5U2V0SW5uZXJIVE1MPXt7IF9faHRtbDogc3RyaW5ncy5jb3B5X3Bhc3RlX25vdGljZSB9fT48L2Rpdj5cblxuXHRcdFx0XHRcdFx0XHQ8QnV0dG9uIGNsYXNzTmFtZT0nd3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1yZXNldC1idXR0b24nIG9uQ2xpY2s9eyBvcGVuTW9kYWwgfT57IHN0cmluZ3MucmVzZXRfc3R5bGVfc2V0dGluZ3MgfTwvQnV0dG9uPlxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cblx0XHRcdFx0XHRcdHsgaXNPcGVuICYmIChcblx0XHRcdFx0XHRcdFx0PE1vZGFsICBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1tb2RhbFwiXG5cdFx0XHRcdFx0XHRcdFx0dGl0bGU9eyBzdHJpbmdzLnJlc2V0X3N0eWxlX3NldHRpbmdzfVxuXHRcdFx0XHRcdFx0XHRcdG9uUmVxdWVzdENsb3NlPXsgY2xvc2VNb2RhbCB9PlxuXG5cdFx0XHRcdFx0XHRcdFx0PHA+eyBzdHJpbmdzLnJlc2V0X3NldHRpbmdzX2NvbmZpcm1fdGV4dCB9PC9wPlxuXG5cdFx0XHRcdFx0XHRcdFx0PEZsZXggZ2FwPXszfSBhbGlnbj1cImNlbnRlclwiIGp1c3RpZnk9XCJmbGV4LWVuZFwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0PEJ1dHRvbiBpc1NlY29uZGFyeSBvbkNsaWNrPXsgY2xvc2VNb2RhbCB9PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHR7c3RyaW5ncy5idG5fbm99XG5cdFx0XHRcdFx0XHRcdFx0XHQ8L0J1dHRvbj5cblxuXHRcdFx0XHRcdFx0XHRcdFx0PEJ1dHRvbiBpc1ByaW1hcnkgb25DbGljaz17ICgpID0+IHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0Y2xvc2VNb2RhbCgpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRoYW5kbGVycy5yZXNldFNldHRpbmdzKCk7XG5cdFx0XHRcdFx0XHRcdFx0XHR9IH0+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHsgc3RyaW5ncy5idG5feWVzX3Jlc2V0IH1cblx0XHRcdFx0XHRcdFx0XHRcdDwvQnV0dG9uPlxuXHRcdFx0XHRcdFx0XHRcdDwvRmxleD5cblx0XHRcdFx0XHRcdFx0PC9Nb2RhbD5cblx0XHRcdFx0XHRcdCkgfVxuXHRcdFx0XHRcdDwvSW5zcGVjdG9yQWR2YW5jZWRDb250cm9scz5cblx0XHRcdFx0KTtcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogR2V0IGJsb2NrIGNvbnRlbnQgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IHByb3BzIEJsb2NrIHByb3BlcnRpZXMuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybnMge0pTWC5FbGVtZW50fSBCbG9jayBjb250ZW50IEpTWCBjb2RlLlxuXHRcdFx0ICovXG5cdFx0XHRnZXRCbG9ja0Zvcm1Db250ZW50OiBmdW5jdGlvbiggcHJvcHMgKSB7XG5cblx0XHRcdFx0aWYgKCB0cmlnZ2VyU2VydmVyUmVuZGVyICkge1xuXG5cdFx0XHRcdFx0cmV0dXJuIChcblx0XHRcdFx0XHRcdDxTZXJ2ZXJTaWRlUmVuZGVyXG5cdFx0XHRcdFx0XHRcdGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itc2VydmVyLXNpZGUtcmVuZGVyZXJcIlxuXHRcdFx0XHRcdFx0XHRibG9jaz1cIndwZm9ybXMvZm9ybS1zZWxlY3RvclwiXG5cdFx0XHRcdFx0XHRcdGF0dHJpYnV0ZXM9eyBwcm9wcy5hdHRyaWJ1dGVzIH1cblx0XHRcdFx0XHRcdC8+XG5cdFx0XHRcdFx0KTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGNvbnN0IGNsaWVudElkID0gcHJvcHMuY2xpZW50SWQ7XG5cdFx0XHRcdGNvbnN0IGJsb2NrID0gYXBwLmdldEJsb2NrQ29udGFpbmVyKCBwcm9wcyApO1xuXG5cdFx0XHRcdC8vIEluIHRoZSBjYXNlIG9mIGVtcHR5IGNvbnRlbnQsIHVzZSBzZXJ2ZXIgc2lkZSByZW5kZXJlci5cblx0XHRcdFx0Ly8gVGhpcyBoYXBwZW5zIHdoZW4gdGhlIGJsb2NrIGlzIGR1cGxpY2F0ZWQgb3IgY29udmVydGVkIHRvIGEgcmV1c2FibGUgYmxvY2suXG5cdFx0XHRcdGlmICggISBibG9jayB8fCAhIGJsb2NrLmlubmVySFRNTCApIHtcblx0XHRcdFx0XHR0cmlnZ2VyU2VydmVyUmVuZGVyID0gdHJ1ZTtcblxuXHRcdFx0XHRcdHJldHVybiBhcHAuanN4UGFydHMuZ2V0QmxvY2tGb3JtQ29udGVudCggcHJvcHMgKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGJsb2Nrc1sgY2xpZW50SWQgXSA9IGJsb2Nrc1sgY2xpZW50SWQgXSB8fCB7fTtcblx0XHRcdFx0YmxvY2tzWyBjbGllbnRJZCBdLmJsb2NrSFRNTCA9IGJsb2NrLmlubmVySFRNTDtcblx0XHRcdFx0YmxvY2tzWyBjbGllbnRJZCBdLmxvYWRlZEZvcm1JZCA9IHByb3BzLmF0dHJpYnV0ZXMuZm9ybUlkO1xuXG5cdFx0XHRcdHJldHVybiAoXG5cdFx0XHRcdFx0PEZyYWdtZW50IGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItZnJhZ21lbnQtZm9ybS1odG1sXCI+XG5cdFx0XHRcdFx0XHQ8ZGl2IGRhbmdlcm91c2x5U2V0SW5uZXJIVE1MPXt7IF9faHRtbDogYmxvY2tzWyBjbGllbnRJZCBdLmJsb2NrSFRNTCB9fSAvPlxuXHRcdFx0XHRcdDwvRnJhZ21lbnQ+XG5cdFx0XHRcdCk7XG5cdFx0XHR9LFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEdldCBibG9jayBwcmV2aWV3IEpTWCBjb2RlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gQmxvY2sgcHJldmlldyBKU1ggY29kZS5cblx0XHRcdCAqL1xuXHRcdFx0Z2V0QmxvY2tQcmV2aWV3OiBmdW5jdGlvbigpIHtcblxuXHRcdFx0XHRyZXR1cm4gKFxuXHRcdFx0XHRcdDxGcmFnbWVudFxuXHRcdFx0XHRcdFx0a2V5PVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1mcmFnbWVudC1ibG9jay1wcmV2aWV3XCI+XG5cdFx0XHRcdFx0XHQ8aW1nIHNyYz17IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuYmxvY2tfcHJldmlld191cmwgfSBzdHlsZT17eyB3aWR0aDogJzEwMCUnIH19IC8+XG5cdFx0XHRcdFx0PC9GcmFnbWVudD5cblx0XHRcdFx0KTtcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogR2V0IGJsb2NrIGVtcHR5IEpTWCBjb2RlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBzaW5jZSAxLjguM1xuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBwcm9wcyBCbG9jayBwcm9wZXJ0aWVzLlxuXHRcdFx0ICogQHJldHVybnMge0pTWC5FbGVtZW50fSBCbG9jayBlbXB0eSBKU1ggY29kZS5cblx0XHRcdCAqL1xuXHRcdFx0Z2V0RW1wdHlGb3Jtc1ByZXZpZXc6IGZ1bmN0aW9uKCBwcm9wcyApIHtcblxuXHRcdFx0XHRjb25zdCBjbGllbnRJZCA9IHByb3BzLmNsaWVudElkO1xuXG5cdFx0XHRcdHJldHVybiAoXG5cdFx0XHRcdFx0PEZyYWdtZW50XG5cdFx0XHRcdFx0XHRrZXk9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWZyYWdtZW50LWJsb2NrLWVtcHR5XCI+XG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzTmFtZT1cIndwZm9ybXMtbm8tZm9ybS1wcmV2aWV3XCI+XG5cdFx0XHRcdFx0XHRcdDxpbWcgc3JjPXsgd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5ibG9ja19lbXB0eV91cmwgfSAvPlxuXHRcdFx0XHRcdFx0XHQ8cD5cblx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRjcmVhdGVJbnRlcnBvbGF0ZUVsZW1lbnQoXG5cdFx0XHRcdFx0XHRcdFx0XHRcdF9fKFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdZb3UgY2FuIHVzZSA8Yj5XUEZvcm1zPC9iPiB0byBidWlsZCBjb250YWN0IGZvcm1zLCBzdXJ2ZXlzLCBwYXltZW50IGZvcm1zLCBhbmQgbW9yZSB3aXRoIGp1c3QgYSBmZXcgY2xpY2tzLicsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3dwZm9ybXMtbGl0ZSdcblx0XHRcdFx0XHRcdFx0XHRcdFx0KSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGI6IDxzdHJvbmcgLz4sXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRcdClcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdDwvcD5cblx0XHRcdFx0XHRcdFx0PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3NOYW1lPVwiZ2V0LXN0YXJ0ZWQtYnV0dG9uIGNvbXBvbmVudHMtYnV0dG9uIGlzLXByaW1hcnlcIlxuXHRcdFx0XHRcdFx0XHRcdG9uQ2xpY2s9e1xuXHRcdFx0XHRcdFx0XHRcdFx0KCkgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRhcHAub3BlbkJ1aWxkZXJQb3B1cCggY2xpZW50SWQgKTtcblx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdFx0XHR7IF9fKCAnR2V0IFN0YXJ0ZWQnLCAnd3Bmb3Jtcy1saXRlJyApIH1cblx0XHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0XHRcdDxwIGNsYXNzTmFtZT1cImVtcHR5LWRlc2NcIj5cblx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRjcmVhdGVJbnRlcnBvbGF0ZUVsZW1lbnQoXG5cdFx0XHRcdFx0XHRcdFx0XHRcdF9fKFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdOZWVkIHNvbWUgaGVscD8gQ2hlY2sgb3V0IG91ciA8YT5jb21wcmVoZW5zaXZlIGd1aWRlLjwvYT4nLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3cGZvcm1zLWxpdGUnXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRhOiA8YSBocmVmPXt3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLndwZm9ybXNfZ3VpZGV9IHRhcmdldD1cIl9ibGFua1wiIHJlbD1cIm5vb3BlbmVyIG5vcmVmZXJyZXJcIi8+LFxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHQpXG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHQ8L3A+XG5cblx0XHRcdFx0XHRcdFx0ey8qIFRlbXBsYXRlIGZvciBwb3B1cCB3aXRoIGJ1aWxkZXIgaWZyYW1lICovfVxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGlkPVwid3Bmb3Jtcy1ndXRlbmJlcmctcG9wdXBcIiBjbGFzc05hbWU9XCJ3cGZvcm1zLWJ1aWxkZXItcG9wdXBcIj5cblx0XHRcdFx0XHRcdFx0XHQ8aWZyYW1lIHNyYz1cImFib3V0OmJsYW5rXCIgd2lkdGg9XCIxMDAlXCIgaGVpZ2h0PVwiMTAwJVwiIGlkPVwid3Bmb3Jtcy1idWlsZGVyLWlmcmFtZVwiPjwvaWZyYW1lPlxuXHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdDwvRnJhZ21lbnQ+XG5cdFx0XHRcdCk7XG5cdFx0XHR9LFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEdldCBibG9jayBwbGFjZWhvbGRlciAoZm9ybSBzZWxlY3RvcikgSlNYIGNvZGUuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtvYmplY3R9IGF0dHJpYnV0ZXMgIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gaGFuZGxlcnMgICAgQmxvY2sgZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHQgKiBAcGFyYW0ge29iamVjdH0gZm9ybU9wdGlvbnMgRm9ybSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm5zIHtKU1guRWxlbWVudH0gQmxvY2sgcGxhY2Vob2xkZXIgSlNYIGNvZGUuXG5cdFx0XHQgKi9cblx0XHRcdGdldEJsb2NrUGxhY2Vob2xkZXI6IGZ1bmN0aW9uKCBhdHRyaWJ1dGVzLCBoYW5kbGVycywgZm9ybU9wdGlvbnMgKSB7XG5cblx0XHRcdFx0cmV0dXJuIChcblx0XHRcdFx0XHQ8UGxhY2Vob2xkZXJcblx0XHRcdFx0XHRcdGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itd3JhcFwiXG5cdFx0XHRcdFx0XHRjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLXdyYXBcIj5cblx0XHRcdFx0XHRcdDxpbWcgc3JjPXt3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmxvZ29fdXJsfSAvPlxuXHRcdFx0XHRcdFx0PGgzPnsgc3RyaW5ncy50aXRsZSB9PC9oMz5cblx0XHRcdFx0XHRcdDxTZWxlY3RDb250cm9sXG5cdFx0XHRcdFx0XHRcdGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itc2VsZWN0LWNvbnRyb2xcIlxuXHRcdFx0XHRcdFx0XHR2YWx1ZT17IGF0dHJpYnV0ZXMuZm9ybUlkIH1cblx0XHRcdFx0XHRcdFx0b3B0aW9ucz17IGZvcm1PcHRpb25zIH1cblx0XHRcdFx0XHRcdFx0b25DaGFuZ2U9eyB2YWx1ZSA9PiBoYW5kbGVycy5hdHRyQ2hhbmdlKCAnZm9ybUlkJywgdmFsdWUgKSB9XG5cdFx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdDwvUGxhY2Vob2xkZXI+XG5cdFx0XHRcdCk7XG5cdFx0XHR9LFxuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgU3R5bGUgU2V0dGluZ3MgcGFuZWwgY2xhc3MuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBhdHRyaWJ1dGVzIEJsb2NrIGF0dHJpYnV0ZXMuXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBTdHlsZSBTZXR0aW5ncyBwYW5lbCBjbGFzcy5cblx0XHQgKi9cblx0XHRnZXRQYW5lbENsYXNzOiBmdW5jdGlvbiggYXR0cmlidXRlcyApIHtcblxuXHRcdFx0bGV0IGNzc0NsYXNzID0gJ3dwZm9ybXMtZ3V0ZW5iZXJnLXBhbmVsIHdwZm9ybXMtYmxvY2stc2V0dGluZ3MtJyArIGF0dHJpYnV0ZXMuY2xpZW50SWQ7XG5cblx0XHRcdGlmICggISBhcHAuaXNGdWxsU3R5bGluZ0VuYWJsZWQoKSApIHtcblx0XHRcdFx0Y3NzQ2xhc3MgKz0gJyBkaXNhYmxlZF9wYW5lbCc7XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiBjc3NDbGFzcztcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogRGV0ZXJtaW5lIHdoZXRoZXIgdGhlIGZ1bGwgc3R5bGluZyBpcyBlbmFibGVkLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn0gV2hldGhlciB0aGUgZnVsbCBzdHlsaW5nIGlzIGVuYWJsZWQuXG5cdFx0ICovXG5cdFx0aXNGdWxsU3R5bGluZ0VuYWJsZWQ6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRyZXR1cm4gd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5pc19tb2Rlcm5fbWFya3VwICYmIHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuaXNfZnVsbF9zdHlsaW5nO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgYmxvY2sgY29udGFpbmVyIERPTSBlbGVtZW50LlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gcHJvcHMgQmxvY2sgcHJvcGVydGllcy5cblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtFbGVtZW50fSBCbG9jayBjb250YWluZXIuXG5cdFx0ICovXG5cdFx0Z2V0QmxvY2tDb250YWluZXI6IGZ1bmN0aW9uKCBwcm9wcyApIHtcblxuXHRcdFx0Y29uc3QgYmxvY2tTZWxlY3RvciA9IGAjYmxvY2stJHtwcm9wcy5jbGllbnRJZH0gPiBkaXZgO1xuXHRcdFx0bGV0IGJsb2NrID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvciggYmxvY2tTZWxlY3RvciApO1xuXG5cdFx0XHQvLyBGb3IgRlNFIC8gR3V0ZW5iZXJnIHBsdWdpbiB3ZSBuZWVkIHRvIHRha2UgYSBsb29rIGluc2lkZSB0aGUgaWZyYW1lLlxuXHRcdFx0aWYgKCAhIGJsb2NrICkge1xuXHRcdFx0XHRjb25zdCBlZGl0b3JDYW52YXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCAnaWZyYW1lW25hbWU9XCJlZGl0b3ItY2FudmFzXCJdJyApO1xuXG5cdFx0XHRcdGJsb2NrID0gZWRpdG9yQ2FudmFzICYmIGVkaXRvckNhbnZhcy5jb250ZW50V2luZG93LmRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoIGJsb2NrU2VsZWN0b3IgKTtcblx0XHRcdH1cblxuXHRcdFx0cmV0dXJuIGJsb2NrO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgc2V0dGluZ3MgZmllbGRzIGV2ZW50IGhhbmRsZXJzLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gcHJvcHMgQmxvY2sgcHJvcGVydGllcy5cblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtvYmplY3R9IE9iamVjdCB0aGF0IGNvbnRhaW5zIGV2ZW50IGhhbmRsZXJzIGZvciB0aGUgc2V0dGluZ3MgZmllbGRzLlxuXHRcdCAqL1xuXHRcdGdldFNldHRpbmdzRmllbGRzSGFuZGxlcnM6IGZ1bmN0aW9uKCBwcm9wcyApIHsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBtYXgtbGluZXMtcGVyLWZ1bmN0aW9uXG5cblx0XHRcdHJldHVybiB7XG5cblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIEZpZWxkIHN0eWxlIGF0dHJpYnV0ZSBjaGFuZ2UgZXZlbnQgaGFuZGxlci5cblx0XHRcdFx0ICpcblx0XHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBhdHRyaWJ1dGUgQXR0cmlidXRlIG5hbWUuXG5cdFx0XHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAgICAgTmV3IGF0dHJpYnV0ZSB2YWx1ZS5cblx0XHRcdFx0ICovXG5cdFx0XHRcdHN0eWxlQXR0ckNoYW5nZTogZnVuY3Rpb24oIGF0dHJpYnV0ZSwgdmFsdWUgKSB7XG5cblx0XHRcdFx0XHRjb25zdCBibG9jayA9IGFwcC5nZXRCbG9ja0NvbnRhaW5lciggcHJvcHMgKSxcblx0XHRcdFx0XHRcdGNvbnRhaW5lciA9IGJsb2NrLnF1ZXJ5U2VsZWN0b3IoIGAjd3Bmb3Jtcy0ke3Byb3BzLmF0dHJpYnV0ZXMuZm9ybUlkfWAgKSxcblx0XHRcdFx0XHRcdHByb3BlcnR5ID0gYXR0cmlidXRlLnJlcGxhY2UoIC9bQS1aXS9nLCBsZXR0ZXIgPT4gYC0ke2xldHRlci50b0xvd2VyQ2FzZSgpfWAgKSxcblx0XHRcdFx0XHRcdHNldEF0dHIgPSB7fTtcblxuXHRcdFx0XHRcdGlmICggY29udGFpbmVyICkge1xuXHRcdFx0XHRcdFx0c3dpdGNoICggcHJvcGVydHkgKSB7XG5cdFx0XHRcdFx0XHRcdGNhc2UgJ2ZpZWxkLXNpemUnOlxuXHRcdFx0XHRcdFx0XHRjYXNlICdsYWJlbC1zaXplJzpcblx0XHRcdFx0XHRcdFx0Y2FzZSAnYnV0dG9uLXNpemUnOlxuXHRcdFx0XHRcdFx0XHRcdGZvciAoIGNvbnN0IGtleSBpbiBzaXplc1sgcHJvcGVydHkgXVsgdmFsdWUgXSApIHtcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnRhaW5lci5zdHlsZS5zZXRQcm9wZXJ0eShcblx0XHRcdFx0XHRcdFx0XHRcdFx0YC0td3Bmb3Jtcy0ke3Byb3BlcnR5fS0ke2tleX1gLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRzaXplc1sgcHJvcGVydHkgXVsgdmFsdWUgXVsga2V5IF0sXG5cdFx0XHRcdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXG5cdFx0XHRcdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0XHRcdFx0Y29udGFpbmVyLnN0eWxlLnNldFByb3BlcnR5KCBgLS13cGZvcm1zLSR7cHJvcGVydHl9YCwgdmFsdWUgKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRzZXRBdHRyWyBhdHRyaWJ1dGUgXSA9IHZhbHVlO1xuXG5cdFx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggc2V0QXR0ciApO1xuXG5cdFx0XHRcdFx0dHJpZ2dlclNlcnZlclJlbmRlciA9IGZhbHNlO1xuXG5cdFx0XHRcdFx0dGhpcy51cGRhdGVDb3B5UGFzdGVDb250ZW50KCk7XG5cblx0XHRcdFx0XHQkKCB3aW5kb3cgKS50cmlnZ2VyKCAnd3Bmb3Jtc0Zvcm1TZWxlY3RvclN0eWxlQXR0ckNoYW5nZScsIFsgYmxvY2ssIHByb3BzLCBhdHRyaWJ1dGUsIHZhbHVlIF0gKTtcblx0XHRcdFx0fSxcblxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogRmllbGQgcmVndWxhciBhdHRyaWJ1dGUgY2hhbmdlIGV2ZW50IGhhbmRsZXIuXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0XHQgKlxuXHRcdFx0XHQgKiBAcGFyYW0ge3N0cmluZ30gYXR0cmlidXRlIEF0dHJpYnV0ZSBuYW1lLlxuXHRcdFx0XHQgKiBAcGFyYW0ge3N0cmluZ30gdmFsdWUgICAgIE5ldyBhdHRyaWJ1dGUgdmFsdWUuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRhdHRyQ2hhbmdlOiBmdW5jdGlvbiggYXR0cmlidXRlLCB2YWx1ZSApIHtcblxuXHRcdFx0XHRcdGNvbnN0IHNldEF0dHIgPSB7fTtcblxuXHRcdFx0XHRcdHNldEF0dHJbIGF0dHJpYnV0ZSBdID0gdmFsdWU7XG5cblx0XHRcdFx0XHRwcm9wcy5zZXRBdHRyaWJ1dGVzKCBzZXRBdHRyICk7XG5cblx0XHRcdFx0XHR0cmlnZ2VyU2VydmVyUmVuZGVyID0gdHJ1ZTtcblxuXHRcdFx0XHRcdHRoaXMudXBkYXRlQ29weVBhc3RlQ29udGVudCgpO1xuXHRcdFx0XHR9LFxuXG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBSZXNldCBGb3JtIFN0eWxlcyBzZXR0aW5ncyB0byBkZWZhdWx0cy5cblx0XHRcdFx0ICpcblx0XHRcdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRyZXNldFNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0XHRcdGZvciAoIGxldCBrZXkgaW4gZGVmYXVsdFN0eWxlU2V0dGluZ3MgKSB7XG5cdFx0XHRcdFx0XHR0aGlzLnN0eWxlQXR0ckNoYW5nZSgga2V5LCBkZWZhdWx0U3R5bGVTZXR0aW5nc1sga2V5IF0gKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0sXG5cblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIFVwZGF0ZSBjb250ZW50IG9mIHRoZSBcIkNvcHkvUGFzdGVcIiBmaWVsZHMuXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0XHQgKi9cblx0XHRcdFx0dXBkYXRlQ29weVBhc3RlQ29udGVudDogZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0XHRsZXQgY29udGVudCA9IHt9O1xuXHRcdFx0XHRcdGxldCBhdHRzID0gd3AuZGF0YS5zZWxlY3QoICdjb3JlL2Jsb2NrLWVkaXRvcicgKS5nZXRCbG9ja0F0dHJpYnV0ZXMoIHByb3BzLmNsaWVudElkICk7XG5cblx0XHRcdFx0XHRmb3IgKCBsZXQga2V5IGluIGRlZmF1bHRTdHlsZVNldHRpbmdzICkge1xuXHRcdFx0XHRcdFx0Y29udGVudFtrZXldID0gYXR0c1sga2V5IF07XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyAnY29weVBhc3RlSnNvblZhbHVlJzogSlNPTi5zdHJpbmdpZnkoIGNvbnRlbnQgKSB9ICk7XG5cdFx0XHRcdH0sXG5cblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIFBhc3RlIHNldHRpbmdzIGhhbmRsZXIuXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdFx0XHQgKlxuXHRcdFx0XHQgKiBAcGFyYW0ge3N0cmluZ30gdmFsdWUgTmV3IGF0dHJpYnV0ZSB2YWx1ZS5cblx0XHRcdFx0ICovXG5cdFx0XHRcdHBhc3RlU2V0dGluZ3M6IGZ1bmN0aW9uKCB2YWx1ZSApIHtcblxuXHRcdFx0XHRcdGxldCBwYXN0ZUF0dHJpYnV0ZXMgPSBhcHAucGFyc2VWYWxpZGF0ZUpzb24oIHZhbHVlICk7XG5cblx0XHRcdFx0XHRpZiAoICEgcGFzdGVBdHRyaWJ1dGVzICkge1xuXG5cdFx0XHRcdFx0XHR3cC5kYXRhLmRpc3BhdGNoKCAnY29yZS9ub3RpY2VzJyApLmNyZWF0ZUVycm9yTm90aWNlKFxuXHRcdFx0XHRcdFx0XHRzdHJpbmdzLmNvcHlfcGFzdGVfZXJyb3IsXG5cdFx0XHRcdFx0XHRcdHsgaWQ6ICd3cGZvcm1zLWpzb24tcGFyc2UtZXJyb3InIH1cblx0XHRcdFx0XHRcdCk7XG5cblx0XHRcdFx0XHRcdHRoaXMudXBkYXRlQ29weVBhc3RlQ29udGVudCgpO1xuXG5cdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0cGFzdGVBdHRyaWJ1dGVzLmNvcHlQYXN0ZUpzb25WYWx1ZSA9IHZhbHVlO1xuXG5cdFx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggcGFzdGVBdHRyaWJ1dGVzICk7XG5cblx0XHRcdFx0XHR0cmlnZ2VyU2VydmVyUmVuZGVyID0gdHJ1ZTtcblx0XHRcdFx0fSxcblx0XHRcdH07XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIFBhcnNlIGFuZCB2YWxpZGF0ZSBKU09OIHN0cmluZy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIEpTT04gc3RyaW5nLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW58b2JqZWN0fSBQYXJzZWQgSlNPTiBvYmplY3QgT1IgZmFsc2Ugb24gZXJyb3IuXG5cdFx0ICovXG5cdFx0cGFyc2VWYWxpZGF0ZUpzb246IGZ1bmN0aW9uKCB2YWx1ZSApIHtcblxuXHRcdFx0aWYgKCB0eXBlb2YgdmFsdWUgIT09ICdzdHJpbmcnICkge1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cblx0XHRcdGxldCBhdHRzO1xuXG5cdFx0XHR0cnkge1xuXHRcdFx0XHRhdHRzID0gSlNPTi5wYXJzZSggdmFsdWUgKTtcblx0XHRcdH0gY2F0Y2ggKCBlcnJvciApIHtcblx0XHRcdFx0YXR0cyA9IGZhbHNlO1xuXHRcdFx0fVxuXG5cdFx0XHRyZXR1cm4gYXR0cztcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogR2V0IFdQRm9ybXMgaWNvbiBET00gZWxlbWVudC5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge0RPTS5lbGVtZW50fSBXUEZvcm1zIGljb24gRE9NIGVsZW1lbnQuXG5cdFx0ICovXG5cdFx0Z2V0SWNvbjogZnVuY3Rpb24oKSB7XG5cblx0XHRcdHJldHVybiBjcmVhdGVFbGVtZW50KFxuXHRcdFx0XHQnc3ZnJyxcblx0XHRcdFx0eyB3aWR0aDogMjAsIGhlaWdodDogMjAsIHZpZXdCb3g6ICcwIDAgNjEyIDYxMicsIGNsYXNzTmFtZTogJ2Rhc2hpY29uJyB9LFxuXHRcdFx0XHRjcmVhdGVFbGVtZW50KFxuXHRcdFx0XHRcdCdwYXRoJyxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRmaWxsOiAnY3VycmVudENvbG9yJyxcblx0XHRcdFx0XHRcdGQ6ICdNNTQ0LDBINjhDMzAuNDQ1LDAsMCwzMC40NDUsMCw2OHY0NzZjMCwzNy41NTYsMzAuNDQ1LDY4LDY4LDY4aDQ3NmMzNy41NTYsMCw2OC0zMC40NDQsNjgtNjhWNjggQzYxMiwzMC40NDUsNTgxLjU1NiwwLDU0NCwweiBNNDY0LjQ0LDY4TDM4Ny42LDEyMC4wMkwzMjMuMzQsNjhINDY0LjQ0eiBNMjg4LjY2LDY4bC02NC4yNiw1Mi4wMkwxNDcuNTYsNjhIMjg4LjY2eiBNNTQ0LDU0NEg2OCBWNjhoMjIuMWwxMzYsOTIuMTRsNzkuOS02NC42bDc5LjU2LDY0LjZsMTM2LTkyLjE0SDU0NFY1NDR6IE0xMTQuMjQsMjYzLjE2aDk1Ljg4di00OC4yOGgtOTUuODhWMjYzLjE2eiBNMTE0LjI0LDM2MC40aDk1Ljg4IHYtNDguNjJoLTk1Ljg4VjM2MC40eiBNMjQyLjc2LDM2MC40aDI1NXYtNDguNjJoLTI1NVYzNjAuNEwyNDIuNzYsMzYwLjR6IE0yNDIuNzYsMjYzLjE2aDI1NXYtNDguMjhoLTI1NVYyNjMuMTZMMjQyLjc2LDI2My4xNnogTTM2OC4yMiw0NTcuM2gxMjkuNTRWNDA4SDM2OC4yMlY0NTcuM3onLFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdCksXG5cdFx0XHQpO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgYmxvY2sgYXR0cmlidXRlcy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge29iamVjdH0gQmxvY2sgYXR0cmlidXRlcy5cblx0XHQgKi9cblx0XHRnZXRCbG9ja0F0dHJpYnV0ZXM6IGZ1bmN0aW9uKCkgeyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG1heC1saW5lcy1wZXItZnVuY3Rpb25cblxuXHRcdFx0cmV0dXJuIHtcblx0XHRcdFx0Y2xpZW50SWQ6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiAnJyxcblx0XHRcdFx0fSxcblx0XHRcdFx0Zm9ybUlkOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMuZm9ybUlkLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRkaXNwbGF5VGl0bGU6IHtcblx0XHRcdFx0XHR0eXBlOiAnYm9vbGVhbicsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMuZGlzcGxheVRpdGxlLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRkaXNwbGF5RGVzYzoge1xuXHRcdFx0XHRcdHR5cGU6ICdib29sZWFuJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5kaXNwbGF5RGVzYyxcblx0XHRcdFx0fSxcblx0XHRcdFx0cHJldmlldzoge1xuXHRcdFx0XHRcdHR5cGU6ICdib29sZWFuJyxcblx0XHRcdFx0fSxcblx0XHRcdFx0ZmllbGRTaXplOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMuZmllbGRTaXplLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRmaWVsZEJvcmRlclJhZGl1czoge1xuXHRcdFx0XHRcdHR5cGU6ICdzdHJpbmcnLFxuXHRcdFx0XHRcdGRlZmF1bHQ6IGRlZmF1bHRzLmZpZWxkQm9yZGVyUmFkaXVzLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRmaWVsZEJhY2tncm91bmRDb2xvcjoge1xuXHRcdFx0XHRcdHR5cGU6ICdzdHJpbmcnLFxuXHRcdFx0XHRcdGRlZmF1bHQ6IGRlZmF1bHRzLmZpZWxkQmFja2dyb3VuZENvbG9yLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRmaWVsZEJvcmRlckNvbG9yOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMuZmllbGRCb3JkZXJDb2xvcixcblx0XHRcdFx0fSxcblx0XHRcdFx0ZmllbGRUZXh0Q29sb3I6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5maWVsZFRleHRDb2xvcixcblx0XHRcdFx0fSxcblx0XHRcdFx0bGFiZWxTaXplOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMubGFiZWxTaXplLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRsYWJlbENvbG9yOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMubGFiZWxDb2xvcixcblx0XHRcdFx0fSxcblx0XHRcdFx0bGFiZWxTdWJsYWJlbENvbG9yOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMubGFiZWxTdWJsYWJlbENvbG9yLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRsYWJlbEVycm9yQ29sb3I6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5sYWJlbEVycm9yQ29sb3IsXG5cdFx0XHRcdH0sXG5cdFx0XHRcdGJ1dHRvblNpemU6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5idXR0b25TaXplLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRidXR0b25Cb3JkZXJSYWRpdXM6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5idXR0b25Cb3JkZXJSYWRpdXMsXG5cdFx0XHRcdH0sXG5cdFx0XHRcdGJ1dHRvbkJhY2tncm91bmRDb2xvcjoge1xuXHRcdFx0XHRcdHR5cGU6ICdzdHJpbmcnLFxuXHRcdFx0XHRcdGRlZmF1bHQ6IGRlZmF1bHRzLmJ1dHRvbkJhY2tncm91bmRDb2xvcixcblx0XHRcdFx0fSxcblx0XHRcdFx0YnV0dG9uVGV4dENvbG9yOiB7XG5cdFx0XHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0XHRcdFx0ZGVmYXVsdDogZGVmYXVsdHMuYnV0dG9uVGV4dENvbG9yLFxuXHRcdFx0XHR9LFxuXHRcdFx0XHRjb3B5UGFzdGVKc29uVmFsdWU6IHtcblx0XHRcdFx0XHR0eXBlOiAnc3RyaW5nJyxcblx0XHRcdFx0XHRkZWZhdWx0OiBkZWZhdWx0cy5jb3B5UGFzdGVKc29uVmFsdWUsXG5cdFx0XHRcdH0sXG5cdFx0XHR9O1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgZm9ybSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7QXJyYXl9IEZvcm0gb3B0aW9ucy5cblx0XHQgKi9cblx0XHRnZXRGb3JtT3B0aW9uczogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGNvbnN0IGZvcm1PcHRpb25zID0gd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5mb3Jtcy5tYXAoIHZhbHVlID0+IChcblx0XHRcdFx0eyB2YWx1ZTogdmFsdWUuSUQsIGxhYmVsOiB2YWx1ZS5wb3N0X3RpdGxlIH1cblx0XHRcdCkgKTtcblxuXHRcdFx0Zm9ybU9wdGlvbnMudW5zaGlmdCggeyB2YWx1ZTogJycsIGxhYmVsOiBzdHJpbmdzLmZvcm1fc2VsZWN0IH0gKTtcblxuXHRcdFx0cmV0dXJuIGZvcm1PcHRpb25zO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBHZXQgc2l6ZSBzZWxlY3RvciBvcHRpb25zLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7QXJyYXl9IFNpemUgb3B0aW9ucy5cblx0XHQgKi9cblx0XHRnZXRTaXplT3B0aW9uczogZnVuY3Rpb24oKSB7XG5cblx0XHRcdHJldHVybiBbXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRsYWJlbDogc3RyaW5ncy5zbWFsbCxcblx0XHRcdFx0XHR2YWx1ZTogJ3NtYWxsJyxcblx0XHRcdFx0fSxcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGxhYmVsOiBzdHJpbmdzLm1lZGl1bSxcblx0XHRcdFx0XHR2YWx1ZTogJ21lZGl1bScsXG5cdFx0XHRcdH0sXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRsYWJlbDogc3RyaW5ncy5sYXJnZSxcblx0XHRcdFx0XHR2YWx1ZTogJ2xhcmdlJyxcblx0XHRcdFx0fSxcblx0XHRcdF07XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGB3cGZvcm1zRm9ybVNlbGVjdG9yRWRpdGAgaGFuZGxlci5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGUgICAgIEV2ZW50IG9iamVjdC5cblx0XHQgKiBAcGFyYW0ge29iamVjdH0gcHJvcHMgQmxvY2sgcHJvcGVydGllcy5cblx0XHQgKi9cblx0XHRibG9ja0VkaXQ6IGZ1bmN0aW9uKCBlLCBwcm9wcyApIHtcblxuXHRcdFx0Y29uc3QgYmxvY2sgPSBhcHAuZ2V0QmxvY2tDb250YWluZXIoIHByb3BzICk7XG5cblx0XHRcdGlmICggISBibG9jayB8fCAhIGJsb2NrLmRhdGFzZXQgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0YXBwLmluaXRMZWFkRm9ybVNldHRpbmdzKCBibG9jay5wYXJlbnRFbGVtZW50ICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgTGVhZCBGb3JtIFNldHRpbmdzIHBhbmVscy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtFbGVtZW50fSBibG9jayBCbG9jayBlbGVtZW50LlxuXHRcdCAqL1xuXHRcdGluaXRMZWFkRm9ybVNldHRpbmdzOiBmdW5jdGlvbiggYmxvY2sgKSB7XG5cblx0XHRcdGlmICggISBibG9jayB8fCAhIGJsb2NrLmRhdGFzZXQgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0aWYgKCAhIGFwcC5pc0Z1bGxTdHlsaW5nRW5hYmxlZCgpICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0IGNsaWVudElkID0gYmxvY2suZGF0YXNldC5ibG9jaztcblx0XHRcdGNvbnN0ICRmb3JtID0gJCggYmxvY2sucXVlcnlTZWxlY3RvciggJy53cGZvcm1zLWNvbnRhaW5lcicgKSApO1xuXHRcdFx0Y29uc3QgJHBhbmVsID0gJCggYC53cGZvcm1zLWJsb2NrLXNldHRpbmdzLSR7Y2xpZW50SWR9YCApO1xuXG5cdFx0XHRpZiAoICRmb3JtLmhhc0NsYXNzKCAnd3Bmb3Jtcy1sZWFkLWZvcm1zLWNvbnRhaW5lcicgKSApIHtcblxuXHRcdFx0XHQkcGFuZWxcblx0XHRcdFx0XHQuYWRkQ2xhc3MoICdkaXNhYmxlZF9wYW5lbCcgKVxuXHRcdFx0XHRcdC5maW5kKCAnLndwZm9ybXMtZ3V0ZW5iZXJnLXBhbmVsLW5vdGljZS53cGZvcm1zLWxlYWQtZm9ybS1ub3RpY2UnIClcblx0XHRcdFx0XHQuY3NzKCAnZGlzcGxheScsICdibG9jaycgKTtcblxuXHRcdFx0XHQkcGFuZWxcblx0XHRcdFx0XHQuZmluZCggJy53cGZvcm1zLWd1dGVuYmVyZy1wYW5lbC1ub3RpY2Uud3Bmb3Jtcy11c2UtbW9kZXJuLW5vdGljZScgKVxuXHRcdFx0XHRcdC5jc3MoICdkaXNwbGF5JywgJ25vbmUnICk7XG5cblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHQkcGFuZWxcblx0XHRcdFx0LnJlbW92ZUNsYXNzKCAnZGlzYWJsZWRfcGFuZWwnIClcblx0XHRcdFx0LmZpbmQoICcud3Bmb3Jtcy1ndXRlbmJlcmctcGFuZWwtbm90aWNlLndwZm9ybXMtbGVhZC1mb3JtLW5vdGljZScgKVxuXHRcdFx0XHQuY3NzKCAnZGlzcGxheScsICdub25lJyApO1xuXG5cdFx0XHQkcGFuZWxcblx0XHRcdFx0LmZpbmQoICcud3Bmb3Jtcy1ndXRlbmJlcmctcGFuZWwtbm90aWNlLndwZm9ybXMtdXNlLW1vZGVybi1ub3RpY2UnIClcblx0XHRcdFx0LmNzcyggJ2Rpc3BsYXknLCBudWxsICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGB3cGZvcm1zRm9ybVNlbGVjdG9yRm9ybUxvYWRlZGAgaGFuZGxlci5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGUgRXZlbnQgb2JqZWN0LlxuXHRcdCAqL1xuXHRcdGZvcm1Mb2FkZWQ6IGZ1bmN0aW9uKCBlICkge1xuXG5cdFx0XHRhcHAuaW5pdExlYWRGb3JtU2V0dGluZ3MoIGUuZGV0YWlsLmJsb2NrICk7XG5cdFx0XHRhcHAudXBkYXRlQWNjZW50Q29sb3JzKCBlLmRldGFpbCApO1xuXHRcdFx0YXBwLmxvYWRDaG9pY2VzSlMoIGUuZGV0YWlsICk7XG5cdFx0XHRhcHAuaW5pdFJpY2hUZXh0RmllbGQoIGUuZGV0YWlsLmZvcm1JZCApO1xuXG5cdFx0XHQkKCBlLmRldGFpbC5ibG9jayApXG5cdFx0XHRcdC5vZmYoICdjbGljaycgKVxuXHRcdFx0XHQub24oICdjbGljaycsIGFwcC5ibG9ja0NsaWNrICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIENsaWNrIG9uIHRoZSBibG9jayBldmVudCBoYW5kbGVyLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZSBFdmVudCBvYmplY3QuXG5cdFx0ICovXG5cdFx0YmxvY2tDbGljazogZnVuY3Rpb24oIGUgKSB7XG5cblx0XHRcdGFwcC5pbml0TGVhZEZvcm1TZXR0aW5ncyggZS5jdXJyZW50VGFyZ2V0ICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIFVwZGF0ZSBhY2NlbnQgY29sb3JzIG9mIHNvbWUgZmllbGRzIGluIEdCIGJsb2NrIGluIE1vZGVybiBNYXJrdXAgbW9kZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGRldGFpbCBFdmVudCBkZXRhaWxzIG9iamVjdC5cblx0XHQgKi9cblx0XHR1cGRhdGVBY2NlbnRDb2xvcnM6IGZ1bmN0aW9uKCBkZXRhaWwgKSB7XG5cblx0XHRcdGlmIChcblx0XHRcdFx0ISB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmlzX21vZGVybl9tYXJrdXAgfHxcblx0XHRcdFx0ISB3aW5kb3cuV1BGb3JtcyB8fFxuXHRcdFx0XHQhIHdpbmRvdy5XUEZvcm1zLkZyb250ZW5kTW9kZXJuIHx8XG5cdFx0XHRcdCEgZGV0YWlsLmJsb2NrXG5cdFx0XHQpIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHRjb25zdCAkZm9ybSA9ICQoIGRldGFpbC5ibG9jay5xdWVyeVNlbGVjdG9yKCBgI3dwZm9ybXMtJHtkZXRhaWwuZm9ybUlkfWAgKSApLFxuXHRcdFx0XHRGcm9udGVuZE1vZGVybiA9IHdpbmRvdy5XUEZvcm1zLkZyb250ZW5kTW9kZXJuO1xuXG5cdFx0XHRGcm9udGVuZE1vZGVybi51cGRhdGVHQkJsb2NrUGFnZUluZGljYXRvckNvbG9yKCAkZm9ybSApO1xuXHRcdFx0RnJvbnRlbmRNb2Rlcm4udXBkYXRlR0JCbG9ja0ljb25DaG9pY2VzQ29sb3IoICRmb3JtICk7XG5cdFx0XHRGcm9udGVuZE1vZGVybi51cGRhdGVHQkJsb2NrUmF0aW5nQ29sb3IoICRmb3JtICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgTW9kZXJuIHN0eWxlIERyb3Bkb3duIGZpZWxkcyAoPHNlbGVjdD4pLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZGV0YWlsIEV2ZW50IGRldGFpbHMgb2JqZWN0LlxuXHRcdCAqL1xuXHRcdGxvYWRDaG9pY2VzSlM6IGZ1bmN0aW9uKCBkZXRhaWwgKSB7XG5cblx0XHRcdGlmICggdHlwZW9mIHdpbmRvdy5DaG9pY2VzICE9PSAnZnVuY3Rpb24nICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0ICRmb3JtID0gJCggZGV0YWlsLmJsb2NrLnF1ZXJ5U2VsZWN0b3IoIGAjd3Bmb3Jtcy0ke2RldGFpbC5mb3JtSWR9YCApICk7XG5cblx0XHRcdCRmb3JtLmZpbmQoICcuY2hvaWNlc2pzLXNlbGVjdCcgKS5lYWNoKCBmdW5jdGlvbiggaWR4LCBlbCApIHtcblxuXHRcdFx0XHRjb25zdCAkZWwgPSAkKCBlbCApO1xuXG5cdFx0XHRcdGlmICggJGVsLmRhdGEoICdjaG9pY2UnICkgPT09ICdhY3RpdmUnICkge1xuXHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHZhciBhcmdzID0gd2luZG93LndwZm9ybXNfY2hvaWNlc2pzX2NvbmZpZyB8fCB7fSxcblx0XHRcdFx0XHRzZWFyY2hFbmFibGVkID0gJGVsLmRhdGEoICdzZWFyY2gtZW5hYmxlZCcgKSxcblx0XHRcdFx0XHQkZmllbGQgPSAkZWwuY2xvc2VzdCggJy53cGZvcm1zLWZpZWxkJyApO1xuXG5cdFx0XHRcdGFyZ3Muc2VhcmNoRW5hYmxlZCA9ICd1bmRlZmluZWQnICE9PSB0eXBlb2Ygc2VhcmNoRW5hYmxlZCA/IHNlYXJjaEVuYWJsZWQgOiB0cnVlO1xuXHRcdFx0XHRhcmdzLmNhbGxiYWNrT25Jbml0ID0gZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0XHR2YXIgc2VsZiA9IHRoaXMsXG5cdFx0XHRcdFx0XHQkZWxlbWVudCA9ICQoIHNlbGYucGFzc2VkRWxlbWVudC5lbGVtZW50ICksXG5cdFx0XHRcdFx0XHQkaW5wdXQgPSAkKCBzZWxmLmlucHV0LmVsZW1lbnQgKSxcblx0XHRcdFx0XHRcdHNpemVDbGFzcyA9ICRlbGVtZW50LmRhdGEoICdzaXplLWNsYXNzJyApO1xuXG5cdFx0XHRcdFx0Ly8gQWRkIENTUy1jbGFzcyBmb3Igc2l6ZS5cblx0XHRcdFx0XHRpZiAoIHNpemVDbGFzcyApIHtcblx0XHRcdFx0XHRcdCQoIHNlbGYuY29udGFpbmVyT3V0ZXIuZWxlbWVudCApLmFkZENsYXNzKCBzaXplQ2xhc3MgKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvKipcblx0XHRcdFx0XHQgKiBJZiBhIG11bHRpcGxlIHNlbGVjdCBoYXMgc2VsZWN0ZWQgY2hvaWNlcyAtIGhpZGUgYSBwbGFjZWhvbGRlciB0ZXh0LlxuXHRcdFx0XHRcdCAqIEluIGNhc2UgaWYgc2VsZWN0IGlzIGVtcHR5IC0gd2UgcmV0dXJuIHBsYWNlaG9sZGVyIHRleHQgYmFjay5cblx0XHRcdFx0XHQgKi9cblx0XHRcdFx0XHRpZiAoICRlbGVtZW50LnByb3AoICdtdWx0aXBsZScgKSApIHtcblxuXHRcdFx0XHRcdFx0Ly8gT24gaW5pdCBldmVudC5cblx0XHRcdFx0XHRcdCRpbnB1dC5kYXRhKCAncGxhY2Vob2xkZXInLCAkaW5wdXQuYXR0ciggJ3BsYWNlaG9sZGVyJyApICk7XG5cblx0XHRcdFx0XHRcdGlmICggc2VsZi5nZXRWYWx1ZSggdHJ1ZSApLmxlbmd0aCApIHtcblx0XHRcdFx0XHRcdFx0JGlucHV0LnJlbW92ZUF0dHIoICdwbGFjZWhvbGRlcicgKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHR0aGlzLmRpc2FibGUoKTtcblx0XHRcdFx0XHQkZmllbGQuZmluZCggJy5pcy1kaXNhYmxlZCcgKS5yZW1vdmVDbGFzcyggJ2lzLWRpc2FibGVkJyApO1xuXHRcdFx0XHR9O1xuXG5cdFx0XHRcdHRyeSB7XG5cdFx0XHRcdFx0Y29uc3QgY2hvaWNlc0luc3RhbmNlID0gIG5ldyBDaG9pY2VzKCBlbCwgYXJncyApO1xuXG5cdFx0XHRcdFx0Ly8gU2F2ZSBDaG9pY2VzLmpzIGluc3RhbmNlIGZvciBmdXR1cmUgYWNjZXNzLlxuXHRcdFx0XHRcdCRlbC5kYXRhKCAnY2hvaWNlc2pzJywgY2hvaWNlc0luc3RhbmNlICk7XG5cblx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7fSAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLWVtcHR5XG5cdFx0XHR9ICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgUmljaFRleHQgZmllbGQuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7aW50fSBmb3JtSWQgRm9ybSBJRC5cblx0XHQgKi9cblx0XHRpbml0UmljaFRleHRGaWVsZDogZnVuY3Rpb24oIGZvcm1JZCApIHtcblxuXHRcdFx0Ly8gU2V0IGRlZmF1bHQgdGFiIHRvIGBWaXN1YWxgLlxuXHRcdFx0JCggYCN3cGZvcm1zLSR7Zm9ybUlkfSAud3AtZWRpdG9yLXdyYXBgICkucmVtb3ZlQ2xhc3MoICdodG1sLWFjdGl2ZScgKS5hZGRDbGFzcyggJ3RtY2UtYWN0aXZlJyApO1xuXHRcdH0sXG5cdH07XG5cblx0Ly8gUHJvdmlkZSBhY2Nlc3MgdG8gcHVibGljIGZ1bmN0aW9ucy9wcm9wZXJ0aWVzLlxuXHRyZXR1cm4gYXBwO1xuXG59KCBkb2N1bWVudCwgd2luZG93LCBqUXVlcnkgKSApO1xuXG4vLyBJbml0aWFsaXplLlxuV1BGb3Jtcy5Gb3JtU2VsZWN0b3IuaW5pdCgpO1xuIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUNBOztBQUVBLFlBQVk7O0FBRVo7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUpBLFNBQUFBLGVBQUFDLEdBQUEsRUFBQUMsQ0FBQSxXQUFBQyxlQUFBLENBQUFGLEdBQUEsS0FBQUcscUJBQUEsQ0FBQUgsR0FB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
},{}]},{},[1])