(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/* global wpforms_gutenberg_form_selector */
/* jshint es3: false, esversion: 6 */

'use strict';

var _wp = wp,
  _wp$serverSideRender = _wp.serverSideRender,
  ServerSideRender = _wp$serverSideRender === void 0 ? wp.components.ServerSideRender : _wp$serverSideRender;
var _wp$element = wp.element,
  createElement = _wp$element.createElement,
  Fragment = _wp$element.Fragment;
var registerBlockType = wp.blocks.registerBlockType;
var _ref = wp.blockEditor || wp.editor,
  InspectorControls = _ref.InspectorControls;
var _wp$components = wp.components,
  SelectControl = _wp$components.SelectControl,
  ToggleControl = _wp$components.ToggleControl,
  PanelBody = _wp$components.PanelBody,
  Placeholder = _wp$components.Placeholder;
var __ = wp.i18n.__;
var wpformsIcon = createElement('svg', {
  width: 20,
  height: 20,
  viewBox: '0 0 612 612',
  className: 'dashicon'
}, createElement('path', {
  fill: 'currentColor',
  d: 'M544,0H68C30.445,0,0,30.445,0,68v476c0,37.556,30.445,68,68,68h476c37.556,0,68-30.444,68-68V68 C612,30.445,581.556,0,544,0z M464.44,68L387.6,120.02L323.34,68H464.44z M288.66,68l-64.26,52.02L147.56,68H288.66z M544,544H68 V68h22.1l136,92.14l79.9-64.6l79.56,64.6l136-92.14H544V544z M114.24,263.16h95.88v-48.28h-95.88V263.16z M114.24,360.4h95.88 v-48.62h-95.88V360.4z M242.76,360.4h255v-48.62h-255V360.4L242.76,360.4z M242.76,263.16h255v-48.28h-255V263.16L242.76,263.16z M368.22,457.3h129.54V408H368.22V457.3z'
}));

/**
 * Popup container.
 *
 * @since 1.8.3
 *
 * @type {object}
 */
var $popup = {};

/**
 * Close button (inside the form builder) click event.
 *
 * @since 1.8.3
 *
 * @param {string} clientID Block Client ID.
 */
var builderCloseButtonEvent = function builderCloseButtonEvent(clientID) {
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
};

/**
 * Open builder popup.
 *
 * @since 1.6.2
 *
 * @param {string} clientID Block Client ID.
 */
var openBuilderPopup = function openBuilderPopup(clientID) {
  if (jQuery.isEmptyObject($popup)) {
    var tmpl = jQuery('#wpforms-gutenberg-popup');
    var parent = jQuery('#wpwrap');
    parent.after(tmpl);
    $popup = parent.siblings('#wpforms-gutenberg-popup');
  }
  var url = wpforms_gutenberg_form_selector.get_started_url,
    $iframe = $popup.find('iframe');
  builderCloseButtonEvent(clientID);
  $iframe.attr('src', url);
  $popup.fadeIn();
};
var hasForms = function hasForms() {
  return wpforms_gutenberg_form_selector.forms.length > 0;
};
registerBlockType('wpforms/form-selector', {
  title: wpforms_gutenberg_form_selector.strings.title,
  description: wpforms_gutenberg_form_selector.strings.description,
  icon: wpformsIcon,
  keywords: wpforms_gutenberg_form_selector.strings.form_keywords,
  category: 'widgets',
  attributes: {
    formId: {
      type: 'string'
    },
    displayTitle: {
      type: 'boolean'
    },
    displayDesc: {
      type: 'boolean'
    },
    preview: {
      type: 'boolean'
    }
  },
  example: {
    attributes: {
      preview: true
    }
  },
  supports: {
    customClassName: hasForms()
  },
  edit: function edit(props) {
    // eslint-disable-line max-lines-per-function
    var _props$attributes = props.attributes,
      _props$attributes$for = _props$attributes.formId,
      formId = _props$attributes$for === void 0 ? '' : _props$attributes$for,
      _props$attributes$dis = _props$attributes.displayTitle,
      displayTitle = _props$attributes$dis === void 0 ? false : _props$attributes$dis,
      _props$attributes$dis2 = _props$attributes.displayDesc,
      displayDesc = _props$attributes$dis2 === void 0 ? false : _props$attributes$dis2,
      _props$attributes$pre = _props$attributes.preview,
      preview = _props$attributes$pre === void 0 ? false : _props$attributes$pre,
      setAttributes = props.setAttributes;
    var formOptions = wpforms_gutenberg_form_selector.forms.map(function (value) {
      return {
        value: value.ID,
        label: value.post_title
      };
    });
    var strings = wpforms_gutenberg_form_selector.strings;
    var jsx;
    formOptions.unshift({
      value: '',
      label: wpforms_gutenberg_form_selector.strings.form_select
    });
    function selectForm(value) {
      // eslint-disable-line jsdoc/require-jsdoc
      setAttributes({
        formId: value
      });
    }
    function toggleDisplayTitle(value) {
      // eslint-disable-line jsdoc/require-jsdoc
      setAttributes({
        displayTitle: value
      });
    }
    function toggleDisplayDesc(value) {
      // eslint-disable-line jsdoc/require-jsdoc
      setAttributes({
        displayDesc: value
      });
    }

    /**
     * Get block empty JSX code.
     *
     * @since 1.8.3
     *
     * @param {object} props Block properties.
     * @returns {JSX.Element} Block empty JSX code.
     */
    function getEmptyFormsPreview(props) {
      var clientId = props.clientId;
      return /*#__PURE__*/React.createElement(Fragment, {
        key: "wpforms-gutenberg-form-selector-fragment-block-empty"
      }, /*#__PURE__*/React.createElement("div", {
        className: "wpforms-no-form-preview"
      }, /*#__PURE__*/React.createElement("img", {
        src: wpforms_gutenberg_form_selector.block_empty_url
      }), /*#__PURE__*/React.createElement("p", {
        dangerouslySetInnerHTML: {
          __html: strings.wpforms_empty_info
        }
      }), /*#__PURE__*/React.createElement("button", {
        type: "button",
        className: "get-started-button components-button is-button is-primary",
        onClick: function onClick() {
          openBuilderPopup(clientId);
        }
      }, __('Get Started', 'wpforms-lite')), /*#__PURE__*/React.createElement("p", {
        className: "empty-desc",
        dangerouslySetInnerHTML: {
          __html: strings.wpforms_empty_help
        }
      }), /*#__PURE__*/React.createElement("div", {
        id: "wpforms-gutenberg-popup",
        className: "wpforms-builder-popup"
      }, /*#__PURE__*/React.createElement("iframe", {
        src: "about:blank",
        width: "100%",
        height: "100%",
        id: "wpforms-builder-iframe"
      }))));
    }

    /**
     * Print empty forms notice.
     *
     * @since 1.8.3
     *
     * @param {string} clientId Block client ID.
     *
     * @returns {JSX.Element} Field styles JSX code.
     */
    function printEmptyFormsNotice(clientId) {
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
        className: "get-started-button components-button is-button is-secondary",
        onClick: function onClick() {
          openBuilderPopup(clientId);
        }
      }, __('Get Started', 'wpforms-lite'))));
    }
    if (!hasForms()) {
      jsx = [printEmptyFormsNotice(props.clientId)];
      jsx.push(getEmptyFormsPreview(props));
      return jsx;
    }
    jsx = [/*#__PURE__*/React.createElement(InspectorControls, {
      key: "wpforms-gutenberg-form-selector-inspector-controls"
    }, /*#__PURE__*/React.createElement(PanelBody, {
      title: wpforms_gutenberg_form_selector.strings.form_settings
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: wpforms_gutenberg_form_selector.strings.form_selected,
      value: formId,
      options: formOptions,
      onChange: selectForm
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: wpforms_gutenberg_form_selector.strings.show_title,
      checked: displayTitle,
      onChange: toggleDisplayTitle
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: wpforms_gutenberg_form_selector.strings.show_description,
      checked: displayDesc,
      onChange: toggleDisplayDesc
    }), /*#__PURE__*/React.createElement("p", {
      className: "wpforms-gutenberg-panel-notice"
    }, /*#__PURE__*/React.createElement("strong", null, strings.update_wp_notice_head), strings.update_wp_notice_text, " ", /*#__PURE__*/React.createElement("a", {
      href: strings.update_wp_notice_link,
      rel: "noreferrer",
      target: "_blank"
    }, strings.learn_more))))];
    if (formId) {
      jsx.push( /*#__PURE__*/React.createElement(ServerSideRender, {
        key: "wpforms-gutenberg-form-selector-server-side-renderer",
        block: "wpforms/form-selector",
        attributes: props.attributes
      }));
    } else if (preview) {
      jsx.push( /*#__PURE__*/React.createElement(Fragment, {
        key: "wpforms-gutenberg-form-selector-fragment-block-preview"
      }, /*#__PURE__*/React.createElement("img", {
        src: wpforms_gutenberg_form_selector.block_preview_url,
        style: {
          width: '100%'
        }
      })));
    } else {
      jsx.push( /*#__PURE__*/React.createElement(Placeholder, {
        key: "wpforms-gutenberg-form-selector-wrap",
        className: "wpforms-gutenberg-form-selector-wrap"
      }, /*#__PURE__*/React.createElement("img", {
        src: wpforms_gutenberg_form_selector.logo_url
      }), /*#__PURE__*/React.createElement("h3", null, wpforms_gutenberg_form_selector.strings.title), /*#__PURE__*/React.createElement(SelectControl, {
        key: "wpforms-gutenberg-form-selector-select-control",
        value: formId,
        options: formOptions,
        onChange: selectForm
      })));
    }
    return jsx;
  },
  save: function save() {
    return null;
  }
});
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfd3AiLCJ3cCIsIl93cCRzZXJ2ZXJTaWRlUmVuZGVyIiwic2VydmVyU2lkZVJlbmRlciIsIlNlcnZlclNpZGVSZW5kZXIiLCJjb21wb25lbnRzIiwiX3dwJGVsZW1lbnQiLCJlbGVtZW50IiwiY3JlYXRlRWxlbWVudCIsIkZyYWdtZW50IiwicmVnaXN0ZXJCbG9ja1R5cGUiLCJibG9ja3MiLCJfcmVmIiwiYmxvY2tFZGl0b3IiLCJlZGl0b3IiLCJJbnNwZWN0b3JDb250cm9scyIsIl93cCRjb21wb25lbnRzIiwiU2VsZWN0Q29udHJvbCIsIlRvZ2dsZUNvbnRyb2wiLCJQYW5lbEJvZHkiLCJQbGFjZWhvbGRlciIsIl9fIiwiaTE4biIsIndwZm9ybXNJY29uIiwid2lkdGgiLCJoZWlnaHQiLCJ2aWV3Qm94IiwiY2xhc3NOYW1lIiwiZmlsbCIsImQiLCIkcG9wdXAiLCJidWlsZGVyQ2xvc2VCdXR0b25FdmVudCIsImNsaWVudElEIiwib2ZmIiwib24iLCJlIiwiYWN0aW9uIiwiZm9ybUlkIiwiZm9ybVRpdGxlIiwibmV3QmxvY2siLCJjcmVhdGVCbG9jayIsInRvU3RyaW5nIiwid3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3RvciIsImZvcm1zIiwiSUQiLCJwb3N0X3RpdGxlIiwiZGF0YSIsImRpc3BhdGNoIiwicmVtb3ZlQmxvY2siLCJpbnNlcnRCbG9ja3MiLCJvcGVuQnVpbGRlclBvcHVwIiwialF1ZXJ5IiwiaXNFbXB0eU9iamVjdCIsInRtcGwiLCJwYXJlbnQiLCJhZnRlciIsInNpYmxpbmdzIiwidXJsIiwiZ2V0X3N0YXJ0ZWRfdXJsIiwiJGlmcmFtZSIsImZpbmQiLCJhdHRyIiwiZmFkZUluIiwiaGFzRm9ybXMiLCJsZW5ndGgiLCJ0aXRsZSIsInN0cmluZ3MiLCJkZXNjcmlwdGlvbiIsImljb24iLCJrZXl3b3JkcyIsImZvcm1fa2V5d29yZHMiLCJjYXRlZ29yeSIsImF0dHJpYnV0ZXMiLCJ0eXBlIiwiZGlzcGxheVRpdGxlIiwiZGlzcGxheURlc2MiLCJwcmV2aWV3IiwiZXhhbXBsZSIsInN1cHBvcnRzIiwiY3VzdG9tQ2xhc3NOYW1lIiwiZWRpdCIsInByb3BzIiwiX3Byb3BzJGF0dHJpYnV0ZXMiLCJfcHJvcHMkYXR0cmlidXRlcyRmb3IiLCJfcHJvcHMkYXR0cmlidXRlcyRkaXMiLCJfcHJvcHMkYXR0cmlidXRlcyRkaXMyIiwiX3Byb3BzJGF0dHJpYnV0ZXMkcHJlIiwic2V0QXR0cmlidXRlcyIsImZvcm1PcHRpb25zIiwibWFwIiwidmFsdWUiLCJsYWJlbCIsImpzeCIsInVuc2hpZnQiLCJmb3JtX3NlbGVjdCIsInNlbGVjdEZvcm0iLCJ0b2dnbGVEaXNwbGF5VGl0bGUiLCJ0b2dnbGVEaXNwbGF5RGVzYyIsImdldEVtcHR5Rm9ybXNQcmV2aWV3IiwiY2xpZW50SWQiLCJSZWFjdCIsImtleSIsInNyYyIsImJsb2NrX2VtcHR5X3VybCIsImRhbmdlcm91c2x5U2V0SW5uZXJIVE1MIiwiX19odG1sIiwid3Bmb3Jtc19lbXB0eV9pbmZvIiwib25DbGljayIsIndwZm9ybXNfZW1wdHlfaGVscCIsImlkIiwicHJpbnRFbXB0eUZvcm1zTm90aWNlIiwiZm9ybV9zZXR0aW5ncyIsInN0eWxlIiwiZGlzcGxheSIsInB1c2giLCJmb3JtX3NlbGVjdGVkIiwib3B0aW9ucyIsIm9uQ2hhbmdlIiwic2hvd190aXRsZSIsImNoZWNrZWQiLCJzaG93X2Rlc2NyaXB0aW9uIiwidXBkYXRlX3dwX25vdGljZV9oZWFkIiwidXBkYXRlX3dwX25vdGljZV90ZXh0IiwiaHJlZiIsInVwZGF0ZV93cF9ub3RpY2VfbGluayIsInJlbCIsInRhcmdldCIsImxlYXJuX21vcmUiLCJibG9jayIsImJsb2NrX3ByZXZpZXdfdXJsIiwibG9nb191cmwiLCJzYXZlIl0sInNvdXJjZXMiOlsiZmFrZV9iNDYyMjNlNi5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3RvciAqL1xuLyoganNoaW50IGVzMzogZmFsc2UsIGVzdmVyc2lvbjogNiAqL1xuXG4ndXNlIHN0cmljdCc7XG5cbmNvbnN0IHsgc2VydmVyU2lkZVJlbmRlcjogU2VydmVyU2lkZVJlbmRlciA9IHdwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlciB9ID0gd3A7XG5jb25zdCB7IGNyZWF0ZUVsZW1lbnQsIEZyYWdtZW50IH0gPSB3cC5lbGVtZW50O1xuY29uc3QgeyByZWdpc3RlckJsb2NrVHlwZSB9ID0gd3AuYmxvY2tzO1xuY29uc3QgeyBJbnNwZWN0b3JDb250cm9scyB9ID0gd3AuYmxvY2tFZGl0b3IgfHwgd3AuZWRpdG9yO1xuY29uc3QgeyBTZWxlY3RDb250cm9sLCBUb2dnbGVDb250cm9sLCBQYW5lbEJvZHksIFBsYWNlaG9sZGVyIH0gPSB3cC5jb21wb25lbnRzO1xuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcblxuY29uc3Qgd3Bmb3Jtc0ljb24gPSBjcmVhdGVFbGVtZW50KCAnc3ZnJywgeyB3aWR0aDogMjAsIGhlaWdodDogMjAsIHZpZXdCb3g6ICcwIDAgNjEyIDYxMicsIGNsYXNzTmFtZTogJ2Rhc2hpY29uJyB9LFxuXHRjcmVhdGVFbGVtZW50KCAncGF0aCcsIHtcblx0XHRmaWxsOiAnY3VycmVudENvbG9yJyxcblx0XHRkOiAnTTU0NCwwSDY4QzMwLjQ0NSwwLDAsMzAuNDQ1LDAsNjh2NDc2YzAsMzcuNTU2LDMwLjQ0NSw2OCw2OCw2OGg0NzZjMzcuNTU2LDAsNjgtMzAuNDQ0LDY4LTY4VjY4IEM2MTIsMzAuNDQ1LDU4MS41NTYsMCw1NDQsMHogTTQ2NC40NCw2OEwzODcuNiwxMjAuMDJMMzIzLjM0LDY4SDQ2NC40NHogTTI4OC42Niw2OGwtNjQuMjYsNTIuMDJMMTQ3LjU2LDY4SDI4OC42NnogTTU0NCw1NDRINjggVjY4aDIyLjFsMTM2LDkyLjE0bDc5LjktNjQuNmw3OS41Niw2NC42bDEzNi05Mi4xNEg1NDRWNTQ0eiBNMTE0LjI0LDI2My4xNmg5NS44OHYtNDguMjhoLTk1Ljg4VjI2My4xNnogTTExNC4yNCwzNjAuNGg5NS44OCB2LTQ4LjYyaC05NS44OFYzNjAuNHogTTI0Mi43NiwzNjAuNGgyNTV2LTQ4LjYyaC0yNTVWMzYwLjRMMjQyLjc2LDM2MC40eiBNMjQyLjc2LDI2My4xNmgyNTV2LTQ4LjI4aC0yNTVWMjYzLjE2TDI0Mi43NiwyNjMuMTZ6IE0zNjguMjIsNDU3LjNoMTI5LjU0VjQwOEgzNjguMjJWNDU3LjN6Jyxcblx0fSApXG4pO1xuXG4vKipcbiAqIFBvcHVwIGNvbnRhaW5lci5cbiAqXG4gKiBAc2luY2UgMS44LjNcbiAqXG4gKiBAdHlwZSB7b2JqZWN0fVxuICovXG5sZXQgJHBvcHVwID0ge307XG5cbi8qKlxuICogQ2xvc2UgYnV0dG9uIChpbnNpZGUgdGhlIGZvcm0gYnVpbGRlcikgY2xpY2sgZXZlbnQuXG4gKlxuICogQHNpbmNlIDEuOC4zXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGNsaWVudElEIEJsb2NrIENsaWVudCBJRC5cbiAqL1xuY29uc3QgYnVpbGRlckNsb3NlQnV0dG9uRXZlbnQgPSBmdW5jdGlvbiggY2xpZW50SUQgKSB7XG5cblx0JHBvcHVwXG5cdFx0Lm9mZiggJ3dwZm9ybXNCdWlsZGVySW5Qb3B1cENsb3NlJyApXG5cdFx0Lm9uKCAnd3Bmb3Jtc0J1aWxkZXJJblBvcHVwQ2xvc2UnLCBmdW5jdGlvbiggZSwgYWN0aW9uLCBmb3JtSWQsIGZvcm1UaXRsZSApIHtcblx0XHRcdGlmICggYWN0aW9uICE9PSAnc2F2ZWQnIHx8ICEgZm9ybUlkICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8vIEluc2VydCBhIG5ldyBibG9jayB3aGVuIGEgbmV3IGZvcm0gaXMgY3JlYXRlZCBmcm9tIHRoZSBwb3B1cCB0byB1cGRhdGUgdGhlIGZvcm0gbGlzdCBhbmQgYXR0cmlidXRlcy5cblx0XHRcdGNvbnN0IG5ld0Jsb2NrID0gd3AuYmxvY2tzLmNyZWF0ZUJsb2NrKCAnd3Bmb3Jtcy9mb3JtLXNlbGVjdG9yJywge1xuXHRcdFx0XHRmb3JtSWQ6IGZvcm1JZC50b1N0cmluZygpLCAvLyBFeHBlY3RzIHN0cmluZyB2YWx1ZSwgbWFrZSBzdXJlIHdlIGluc2VydCBzdHJpbmcuXG5cdFx0XHR9ICk7XG5cblx0XHRcdC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBjYW1lbGNhc2Vcblx0XHRcdHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuZm9ybXMgPSBbIHsgSUQ6IGZvcm1JZCwgcG9zdF90aXRsZTogZm9ybVRpdGxlIH0gXTtcblxuXHRcdFx0Ly8gSW5zZXJ0IGEgbmV3IGJsb2NrLlxuXHRcdFx0d3AuZGF0YS5kaXNwYXRjaCggJ2NvcmUvYmxvY2stZWRpdG9yJyApLnJlbW92ZUJsb2NrKCBjbGllbnRJRCApO1xuXHRcdFx0d3AuZGF0YS5kaXNwYXRjaCggJ2NvcmUvYmxvY2stZWRpdG9yJyApLmluc2VydEJsb2NrcyggbmV3QmxvY2sgKTtcblxuXHRcdH0gKTtcbn07XG5cbi8qKlxuICogT3BlbiBidWlsZGVyIHBvcHVwLlxuICpcbiAqIEBzaW5jZSAxLjYuMlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBjbGllbnRJRCBCbG9jayBDbGllbnQgSUQuXG4gKi9cbmNvbnN0IG9wZW5CdWlsZGVyUG9wdXAgPSBmdW5jdGlvbiggY2xpZW50SUQgKSB7XG5cblx0aWYgKCBqUXVlcnkuaXNFbXB0eU9iamVjdCggJHBvcHVwICkgKSB7XG5cdFx0bGV0IHRtcGwgPSBqUXVlcnkoICcjd3Bmb3Jtcy1ndXRlbmJlcmctcG9wdXAnICk7XG5cdFx0bGV0IHBhcmVudCA9IGpRdWVyeSggJyN3cHdyYXAnICk7XG5cblx0XHRwYXJlbnQuYWZ0ZXIoIHRtcGwgKTtcblxuXHRcdCRwb3B1cCA9IHBhcmVudC5zaWJsaW5ncyggJyN3cGZvcm1zLWd1dGVuYmVyZy1wb3B1cCcgKTtcblx0fVxuXG5cdGNvbnN0IHVybCA9IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuZ2V0X3N0YXJ0ZWRfdXJsLFxuXHRcdCRpZnJhbWUgPSAkcG9wdXAuZmluZCggJ2lmcmFtZScgKTtcblxuXHRidWlsZGVyQ2xvc2VCdXR0b25FdmVudCggY2xpZW50SUQgKTtcblx0JGlmcmFtZS5hdHRyKCAnc3JjJywgdXJsICk7XG5cdCRwb3B1cC5mYWRlSW4oKTtcbn07XG5cbmNvbnN0IGhhc0Zvcm1zID0gZnVuY3Rpb24oKSB7XG5cdHJldHVybiB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmZvcm1zLmxlbmd0aCA+IDA7XG59O1xuXG5yZWdpc3RlckJsb2NrVHlwZSggJ3dwZm9ybXMvZm9ybS1zZWxlY3RvcicsIHtcblx0dGl0bGU6IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3Iuc3RyaW5ncy50aXRsZSxcblx0ZGVzY3JpcHRpb246IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3Iuc3RyaW5ncy5kZXNjcmlwdGlvbixcblx0aWNvbjogd3Bmb3Jtc0ljb24sXG5cdGtleXdvcmRzOiB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLnN0cmluZ3MuZm9ybV9rZXl3b3Jkcyxcblx0Y2F0ZWdvcnk6ICd3aWRnZXRzJyxcblx0YXR0cmlidXRlczoge1xuXHRcdGZvcm1JZDoge1xuXHRcdFx0dHlwZTogJ3N0cmluZycsXG5cdFx0fSxcblx0XHRkaXNwbGF5VGl0bGU6IHtcblx0XHRcdHR5cGU6ICdib29sZWFuJyxcblx0XHR9LFxuXHRcdGRpc3BsYXlEZXNjOiB7XG5cdFx0XHR0eXBlOiAnYm9vbGVhbicsXG5cdFx0fSxcblx0XHRwcmV2aWV3OiB7XG5cdFx0XHR0eXBlOiAnYm9vbGVhbicsXG5cdFx0fSxcblx0fSxcblx0ZXhhbXBsZToge1xuXHRcdGF0dHJpYnV0ZXM6IHtcblx0XHRcdHByZXZpZXc6IHRydWUsXG5cdFx0fSxcblx0fSxcblx0c3VwcG9ydHM6IHtcblx0XHRjdXN0b21DbGFzc05hbWU6IGhhc0Zvcm1zKCksXG5cdH0sXG5cdGVkaXQoIHByb3BzICkgeyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG1heC1saW5lcy1wZXItZnVuY3Rpb25cblx0XHRjb25zdCB7IGF0dHJpYnV0ZXM6IHsgZm9ybUlkID0gJycsIGRpc3BsYXlUaXRsZSA9IGZhbHNlLCBkaXNwbGF5RGVzYyA9IGZhbHNlLCBwcmV2aWV3ID0gZmFsc2UgfSwgc2V0QXR0cmlidXRlcyB9ID0gcHJvcHM7XG5cdFx0Y29uc3QgZm9ybU9wdGlvbnMgPSB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLmZvcm1zLm1hcCggdmFsdWUgPT4gKFxuXHRcdFx0eyB2YWx1ZTogdmFsdWUuSUQsIGxhYmVsOiB2YWx1ZS5wb3N0X3RpdGxlIH1cblx0XHQpICk7XG5cblx0XHRjb25zdCBzdHJpbmdzID0gd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5zdHJpbmdzO1xuXHRcdGxldCBqc3g7XG5cblx0XHRmb3JtT3B0aW9ucy51bnNoaWZ0KCB7IHZhbHVlOiAnJywgbGFiZWw6IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3Iuc3RyaW5ncy5mb3JtX3NlbGVjdCB9ICk7XG5cblx0XHRmdW5jdGlvbiBzZWxlY3RGb3JtKCB2YWx1ZSApIHsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBqc2RvYy9yZXF1aXJlLWpzZG9jXG5cdFx0XHRzZXRBdHRyaWJ1dGVzKCB7IGZvcm1JZDogdmFsdWUgfSApO1xuXHRcdH1cblxuXHRcdGZ1bmN0aW9uIHRvZ2dsZURpc3BsYXlUaXRsZSggdmFsdWUgKSB7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUganNkb2MvcmVxdWlyZS1qc2RvY1xuXHRcdFx0c2V0QXR0cmlidXRlcyggeyBkaXNwbGF5VGl0bGU6IHZhbHVlIH0gKTtcblx0XHR9XG5cblx0XHRmdW5jdGlvbiB0b2dnbGVEaXNwbGF5RGVzYyggdmFsdWUgKSB7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUganNkb2MvcmVxdWlyZS1qc2RvY1xuXHRcdFx0c2V0QXR0cmlidXRlcyggeyBkaXNwbGF5RGVzYzogdmFsdWUgfSApO1xuXHRcdH1cblxuXHRcdC8qKlxuXHRcdCAqIEdldCBibG9jayBlbXB0eSBKU1ggY29kZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguM1xuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IHByb3BzIEJsb2NrIHByb3BlcnRpZXMuXG5cdFx0ICogQHJldHVybnMge0pTWC5FbGVtZW50fSBCbG9jayBlbXB0eSBKU1ggY29kZS5cblx0XHQgKi9cblx0XHRmdW5jdGlvbiBnZXRFbXB0eUZvcm1zUHJldmlldyggcHJvcHMgKSB7XG5cblx0XHRcdGNvbnN0IGNsaWVudElkID0gcHJvcHMuY2xpZW50SWQ7XG5cblx0XHRcdHJldHVybiAoXG5cdFx0XHRcdDxGcmFnbWVudFxuXHRcdFx0XHRcdGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3ItZnJhZ21lbnQtYmxvY2stZW1wdHlcIj5cblx0XHRcdFx0XHQ8ZGl2IGNsYXNzTmFtZT1cIndwZm9ybXMtbm8tZm9ybS1wcmV2aWV3XCI+XG5cdFx0XHRcdFx0XHQ8aW1nIHNyYz17IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuYmxvY2tfZW1wdHlfdXJsIH0gLz5cblx0XHRcdFx0XHRcdDxwIGRhbmdlcm91c2x5U2V0SW5uZXJIVE1MPXt7IF9faHRtbDogc3RyaW5ncy53cGZvcm1zX2VtcHR5X2luZm8gfX0+PC9wPlxuXHRcdFx0XHRcdFx0PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3NOYW1lPVwiZ2V0LXN0YXJ0ZWQtYnV0dG9uIGNvbXBvbmVudHMtYnV0dG9uIGlzLWJ1dHRvbiBpcy1wcmltYXJ5XCJcblx0XHRcdFx0XHRcdFx0b25DbGljaz17XG5cdFx0XHRcdFx0XHRcdFx0KCkgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdFx0b3BlbkJ1aWxkZXJQb3B1cCggY2xpZW50SWQgKTtcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdFx0eyBfXyggJ0dldCBTdGFydGVkJywgJ3dwZm9ybXMtbGl0ZScgKSB9XG5cdFx0XHRcdFx0XHQ8L2J1dHRvbj5cblx0XHRcdFx0XHRcdDxwIGNsYXNzTmFtZT1cImVtcHR5LWRlc2NcIiBkYW5nZXJvdXNseVNldElubmVySFRNTD17eyBfX2h0bWw6IHN0cmluZ3Mud3Bmb3Jtc19lbXB0eV9oZWxwIH19PjwvcD5cblxuXHRcdFx0XHRcdFx0ey8qIFRlbXBsYXRlIGZvciBwb3B1cCB3aXRoIGJ1aWxkZXIgaWZyYW1lICovfVxuXHRcdFx0XHRcdFx0PGRpdiBpZD1cIndwZm9ybXMtZ3V0ZW5iZXJnLXBvcHVwXCIgY2xhc3NOYW1lPVwid3Bmb3Jtcy1idWlsZGVyLXBvcHVwXCI+XG5cdFx0XHRcdFx0XHRcdDxpZnJhbWUgc3JjPVwiYWJvdXQ6YmxhbmtcIiB3aWR0aD1cIjEwMCVcIiBoZWlnaHQ9XCIxMDAlXCIgaWQ9XCJ3cGZvcm1zLWJ1aWxkZXItaWZyYW1lXCI+PC9pZnJhbWU+XG5cdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0PC9GcmFnbWVudD5cblx0XHRcdCk7XG5cdFx0fVxuXG5cdFx0LyoqXG5cdFx0ICogUHJpbnQgZW1wdHkgZm9ybXMgbm90aWNlLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4zXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gY2xpZW50SWQgQmxvY2sgY2xpZW50IElELlxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge0pTWC5FbGVtZW50fSBGaWVsZCBzdHlsZXMgSlNYIGNvZGUuXG5cdFx0ICovXG5cdFx0ZnVuY3Rpb24gcHJpbnRFbXB0eUZvcm1zTm90aWNlKCBjbGllbnRJZCApIHtcblx0XHRcdHJldHVybiAoXG5cdFx0XHRcdDxJbnNwZWN0b3JDb250cm9scyBrZXk9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWluc3BlY3Rvci1tYWluLXNldHRpbmdzXCI+XG5cdFx0XHRcdFx0PFBhbmVsQm9keSBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1wYW5lbFwiIHRpdGxlPXsgc3RyaW5ncy5mb3JtX3NldHRpbmdzIH0+XG5cdFx0XHRcdFx0XHQ8cCBjbGFzc05hbWU9XCJ3cGZvcm1zLWd1dGVuYmVyZy1wYW5lbC1ub3RpY2Ugd3Bmb3Jtcy13YXJuaW5nIHdwZm9ybXMtZW1wdHktZm9ybS1ub3RpY2VcIiBzdHlsZT17eyBkaXNwbGF5OiAnYmxvY2snIH19PlxuXHRcdFx0XHRcdFx0XHQ8c3Ryb25nPnsgX18oICdZb3UgaGF2ZW7igJl0IGNyZWF0ZWQgYSBmb3JtLCB5ZXQhJywgJ3dwZm9ybXMtbGl0ZScgKSB9PC9zdHJvbmc+XG5cdFx0XHRcdFx0XHRcdHsgX18oICdXaGF0IGFyZSB5b3Ugd2FpdGluZyBmb3I/JywgJ3dwZm9ybXMtbGl0ZScgKSB9XG5cdFx0XHRcdFx0XHQ8L3A+XG5cdFx0XHRcdFx0XHQ8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzc05hbWU9XCJnZXQtc3RhcnRlZC1idXR0b24gY29tcG9uZW50cy1idXR0b24gaXMtYnV0dG9uIGlzLXNlY29uZGFyeVwiXG5cdFx0XHRcdFx0XHRcdG9uQ2xpY2s9e1xuXHRcdFx0XHRcdFx0XHRcdCgpID0+IHtcblx0XHRcdFx0XHRcdFx0XHRcdG9wZW5CdWlsZGVyUG9wdXAoIGNsaWVudElkICk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdHsgX18oICdHZXQgU3RhcnRlZCcsICd3cGZvcm1zLWxpdGUnICkgfVxuXHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0PC9QYW5lbEJvZHk+XG5cdFx0XHRcdDwvSW5zcGVjdG9yQ29udHJvbHM+XG5cdFx0XHQpO1xuXHRcdH1cblxuXG5cdFx0aWYgKCAhIGhhc0Zvcm1zKCkgKSB7XG5cblx0XHRcdGpzeCA9IFsgcHJpbnRFbXB0eUZvcm1zTm90aWNlKCBwcm9wcy5jbGllbnRJZCApIF07XG5cblx0XHRcdGpzeC5wdXNoKCBnZXRFbXB0eUZvcm1zUHJldmlldyggcHJvcHMgKSApO1xuXHRcdFx0cmV0dXJuIGpzeDtcblx0XHR9XG5cblx0XHRqc3ggPSBbXG5cdFx0XHQ8SW5zcGVjdG9yQ29udHJvbHMga2V5PVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1pbnNwZWN0b3ItY29udHJvbHNcIj5cblx0XHRcdFx0PFBhbmVsQm9keSB0aXRsZT17IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3Iuc3RyaW5ncy5mb3JtX3NldHRpbmdzIH0+XG5cdFx0XHRcdFx0PFNlbGVjdENvbnRyb2xcblx0XHRcdFx0XHRcdGxhYmVsPXsgd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5zdHJpbmdzLmZvcm1fc2VsZWN0ZWQgfVxuXHRcdFx0XHRcdFx0dmFsdWU9eyBmb3JtSWQgfVxuXHRcdFx0XHRcdFx0b3B0aW9ucz17IGZvcm1PcHRpb25zIH1cblx0XHRcdFx0XHRcdG9uQ2hhbmdlPXsgc2VsZWN0Rm9ybSB9XG5cdFx0XHRcdFx0Lz5cblx0XHRcdFx0XHQ8VG9nZ2xlQ29udHJvbFxuXHRcdFx0XHRcdFx0bGFiZWw9eyB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLnN0cmluZ3Muc2hvd190aXRsZSB9XG5cdFx0XHRcdFx0XHRjaGVja2VkPXsgZGlzcGxheVRpdGxlIH1cblx0XHRcdFx0XHRcdG9uQ2hhbmdlPXsgdG9nZ2xlRGlzcGxheVRpdGxlIH1cblx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdDxUb2dnbGVDb250cm9sXG5cdFx0XHRcdFx0XHRsYWJlbD17IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3Iuc3RyaW5ncy5zaG93X2Rlc2NyaXB0aW9uIH1cblx0XHRcdFx0XHRcdGNoZWNrZWQ9eyBkaXNwbGF5RGVzYyB9XG5cdFx0XHRcdFx0XHRvbkNoYW5nZT17IHRvZ2dsZURpc3BsYXlEZXNjIH1cblx0XHRcdFx0XHQvPlxuXHRcdFx0XHRcdDxwIGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLXBhbmVsLW5vdGljZVwiPlxuXHRcdFx0XHRcdFx0PHN0cm9uZz57IHN0cmluZ3MudXBkYXRlX3dwX25vdGljZV9oZWFkIH08L3N0cm9uZz5cblx0XHRcdFx0XHRcdHsgc3RyaW5ncy51cGRhdGVfd3Bfbm90aWNlX3RleHQgfSA8YSBocmVmPXtzdHJpbmdzLnVwZGF0ZV93cF9ub3RpY2VfbGlua30gcmVsPVwibm9yZWZlcnJlclwiIHRhcmdldD1cIl9ibGFua1wiPnsgc3RyaW5ncy5sZWFybl9tb3JlIH08L2E+XG5cdFx0XHRcdFx0PC9wPlxuXG5cdFx0XHRcdDwvUGFuZWxCb2R5PlxuXHRcdFx0PC9JbnNwZWN0b3JDb250cm9scz4sXG5cdFx0XTtcblxuXHRcdGlmICggZm9ybUlkICkge1xuXHRcdFx0anN4LnB1c2goXG5cdFx0XHRcdDxTZXJ2ZXJTaWRlUmVuZGVyXG5cdFx0XHRcdFx0a2V5PVwid3Bmb3Jtcy1ndXRlbmJlcmctZm9ybS1zZWxlY3Rvci1zZXJ2ZXItc2lkZS1yZW5kZXJlclwiXG5cdFx0XHRcdFx0YmxvY2s9XCJ3cGZvcm1zL2Zvcm0tc2VsZWN0b3JcIlxuXHRcdFx0XHRcdGF0dHJpYnV0ZXM9eyBwcm9wcy5hdHRyaWJ1dGVzIH1cblx0XHRcdFx0Lz5cblx0XHRcdCk7XG5cdFx0fSBlbHNlIGlmICggcHJldmlldyApIHtcblx0XHRcdGpzeC5wdXNoKFxuXHRcdFx0XHQ8RnJhZ21lbnRcblx0XHRcdFx0XHRrZXk9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLWZyYWdtZW50LWJsb2NrLXByZXZpZXdcIj5cblx0XHRcdFx0XHQ8aW1nIHNyYz17IHdwZm9ybXNfZ3V0ZW5iZXJnX2Zvcm1fc2VsZWN0b3IuYmxvY2tfcHJldmlld191cmwgfSBzdHlsZT17eyB3aWR0aDogJzEwMCUnIH19Lz5cblx0XHRcdFx0PC9GcmFnbWVudD5cblx0XHRcdCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGpzeC5wdXNoKFxuXHRcdFx0XHQ8UGxhY2Vob2xkZXJcblx0XHRcdFx0XHRrZXk9XCJ3cGZvcm1zLWd1dGVuYmVyZy1mb3JtLXNlbGVjdG9yLXdyYXBcIlxuXHRcdFx0XHRcdGNsYXNzTmFtZT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itd3JhcFwiPlxuXHRcdFx0XHRcdDxpbWcgc3JjPXsgd3Bmb3Jtc19ndXRlbmJlcmdfZm9ybV9zZWxlY3Rvci5sb2dvX3VybCB9Lz5cblx0XHRcdFx0XHQ8aDM+eyB3cGZvcm1zX2d1dGVuYmVyZ19mb3JtX3NlbGVjdG9yLnN0cmluZ3MudGl0bGUgfTwvaDM+XG5cdFx0XHRcdFx0PFNlbGVjdENvbnRyb2xcblx0XHRcdFx0XHRcdGtleT1cIndwZm9ybXMtZ3V0ZW5iZXJnLWZvcm0tc2VsZWN0b3Itc2VsZWN0LWNvbnRyb2xcIlxuXHRcdFx0XHRcdFx0dmFsdWU9eyBmb3JtSWQgfVxuXHRcdFx0XHRcdFx0b3B0aW9ucz17IGZvcm1PcHRpb25zIH1cblx0XHRcdFx0XHRcdG9uQ2hhbmdlPXsgc2VsZWN0Rm9ybSB9XG5cdFx0XHRcdFx0Lz5cblx0XHRcdFx0PC9QbGFjZWhvbGRlcj5cblx0XHRcdCk7XG5cdFx0fVxuXG5cdFx0cmV0dXJuIGpzeDtcblx0fSxcblx0c2F2ZSgpIHtcblx0XHRyZXR1cm4gbnVsbDtcblx0fSxcbn0gKTtcbiJdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTs7QUFFQSxZQUFZOztBQUVaLElBQUFBLEdBQUEsR0FBZ0ZDLEVBQUU7RUFBQUMsb0JBQUEsR0FBQUYsR0FBQSxDQUExRUcsZ0JBQWdCO0VBQUVDLGdCQUFnQixHQUFBRixvQkFBQSxjQUFHRCxFQUFFLENBQUNJLFVBQVUsQ0FBQ0QsZ0JBQWdCLEdBQUFGLG9CQUFBO0FBQzNFLElBQUFJLFdBQUEsR0FBb0NMLEVBQUUsQ0FBQ00sT0FBTztFQUF0Q0MsYUFBYSxHQUFBRixXQUFBLENBQWJFLGFBQWE7RUFBRUMsUUFBUSxHQUFBSCxXQUFBLENBQVJHLFFBQVE7QUFDL0IsSUFBUUMsaUJBQWlCLEdBQUtULEVBQUUsQ0FBQ1UsTUFBTSxDQUEvQkQsaUJBQWlCO0FBQ3pCLElBQUFFLElBQUEsR0FBOEJYLEVBQUUsQ0FBQ1ksV0FBVyxJQUFJWixFQUFFLENBQUNh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
},{}]},{},[1])