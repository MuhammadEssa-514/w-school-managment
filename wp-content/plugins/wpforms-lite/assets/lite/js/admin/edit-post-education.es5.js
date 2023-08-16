(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/* global wpforms_edit_post_education */

/**
 * WPForms Edit Post Education function.
 *
 * @since 1.8.1
 */

'use strict';

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i.return && (_r = _i.return(), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
var WPFormsEditPostEducation = window.WPFormsEditPostEducation || function (document, window, $) {
  /**
   * Public functions and properties.
   *
   * @since 1.8.1
   *
   * @type {object}
   */
  var app = {
    /**
     * Determine if the notice was showed before.
     *
     * @since 1.8.1
     */
    isNoticeVisible: false,
    /**
     * Start the engine.
     *
     * @since 1.8.1
     */
    init: function init() {
      $(window).on('load', function () {
        // In the case of jQuery 3.+, we need to wait for a ready event first.
        if (typeof $.ready.then === 'function') {
          $.ready.then(app.load);
        } else {
          app.load();
        }
      });
    },
    /**
     * Page load.
     *
     * @since 1.8.1
     */
    load: function load() {
      if (!app.isGutenbergEditor()) {
        app.maybeShowClassicNotice();
        app.bindClassicEvents();
        return;
      }
      var blockLoadedInterval = setInterval(function () {
        if (!document.querySelector('.editor-post-title__input, iframe[name="editor-canvas"]')) {
          return;
        }
        clearInterval(blockLoadedInterval);
        if (!app.isFse()) {
          app.maybeShowGutenbergNotice();
          app.bindGutenbergEvents();
          return;
        }
        var iframe = document.querySelector('iframe[name="editor-canvas"]');
        var observer = new MutationObserver(function () {
          var iframeDocument = iframe.contentDocument || iframe.contentWindow.document || {};
          if (iframeDocument.readyState === 'complete' && iframeDocument.querySelector('.editor-post-title__input')) {
            app.maybeShowGutenbergNotice();
            app.bindFseEvents();
            observer.disconnect();
          }
        });
        observer.observe(document.body, {
          subtree: true,
          childList: true
        });
      }, 200);
    },
    /**
     * Bind events for Classic Editor.
     *
     * @since 1.8.1
     */
    bindClassicEvents: function bindClassicEvents() {
      var $document = $(document);
      if (!app.isNoticeVisible) {
        $document.on('input', '#title', app.maybeShowClassicNotice);
      }
      $document.on('click', '.wpforms-edit-post-education-notice-close', app.closeNotice);
    },
    /**
     * Bind events for Gutenberg Editor.
     *
     * @since 1.8.1
     */
    bindGutenbergEvents: function bindGutenbergEvents() {
      var $document = $(document);
      $document.on('DOMSubtreeModified', '.edit-post-layout', app.distractionFreeModeToggle);
      if (app.isNoticeVisible) {
        return;
      }
      $document.on('input', '.editor-post-title__input', app.maybeShowGutenbergNotice).on('DOMSubtreeModified', '.editor-post-title__input', app.maybeShowGutenbergNotice);
    },
    /**
     * Bind events for Gutenberg Editor in FSE mode.
     *
     * @since 1.8.1
     */
    bindFseEvents: function bindFseEvents() {
      var $iframe = $('iframe[name="editor-canvas"]');
      $(document).on('DOMSubtreeModified', '.edit-post-layout', app.distractionFreeModeToggle);
      $iframe.contents().on('DOMSubtreeModified', '.editor-post-title__input', app.maybeShowGutenbergNotice);
    },
    /**
     * Determine if the editor is Gutenberg.
     *
     * @since 1.8.1
     *
     * @returns {boolean} True if the editor is Gutenberg.
     */
    isGutenbergEditor: function isGutenbergEditor() {
      return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
    },
    /**
     * Determine if the editor is Gutenberg in FSE mode.
     *
     * @since 1.8.1
     *
     * @returns {boolean} True if the Gutenberg editor in FSE mode.
     */
    isFse: function isFse() {
      return Boolean($('iframe[name="editor-canvas"]').length);
    },
    /**
     * Create a notice for Gutenberg.
     *
     * @since 1.8.1
     */
    showGutenbergNotice: function showGutenbergNotice() {
      wp.data.dispatch('core/notices').createInfoNotice(wpforms_edit_post_education.gutenberg_notice.template, app.getGutenbergNoticeSettings());

      // The notice component doesn't have a way to add HTML id or class to the notice.
      // Also, the notice became visible with a delay on old Gutenberg versions.
      var hasNotice = setInterval(function () {
        var noticeBody = $('.wpforms-edit-post-education-notice-body');
        if (!noticeBody.length) {
          return;
        }
        var $notice = noticeBody.closest('.components-notice');
        $notice.addClass('wpforms-edit-post-education-notice');
        $notice.find('.is-secondary, .is-link').removeClass('is-secondary').removeClass('is-link').addClass('is-primary');
        clearInterval(hasNotice);
      }, 100);
    },
    /**
     * Get settings for the Gutenberg notice.
     *
     * @since 1.8.1
     *
     * @returns {object} Notice settings.
     */
    getGutenbergNoticeSettings: function getGutenbergNoticeSettings() {
      var pluginName = 'wpforms-edit-post-product-education-guide';
      var noticeSettings = {
        id: pluginName,
        isDismissible: true,
        HTML: true,
        __unstableHTML: true,
        actions: [{
          className: 'wpforms-edit-post-education-notice-guide-button',
          variant: 'primary',
          label: wpforms_edit_post_education.gutenberg_notice.button
        }]
      };
      if (!wpforms_edit_post_education.gutenberg_guide) {
        noticeSettings.actions[0].url = wpforms_edit_post_education.gutenberg_notice.url;
        return noticeSettings;
      }
      var Guide = wp.components.Guide;
      var useState = wp.element.useState;
      var registerPlugin = wp.plugins.registerPlugin;
      var unregisterPlugin = wp.plugins.unregisterPlugin;
      var GutenbergTutorial = function GutenbergTutorial() {
        var _useState = useState(true),
          _useState2 = _slicedToArray(_useState, 2),
          isOpen = _useState2[0],
          setIsOpen = _useState2[1];
        if (!isOpen) {
          return null;
        }
        return (
          /*#__PURE__*/
          // eslint-disable-next-line react/react-in-jsx-scope
          React.createElement(Guide, {
            className: "edit-post-welcome-guide",
            onFinish: function onFinish() {
              unregisterPlugin(pluginName);
              setIsOpen(false);
            },
            pages: app.getGuidePages()
          })
        );
      };
      noticeSettings.onDismiss = app.updateUserMeta;
      noticeSettings.actions[0].onClick = function () {
        return registerPlugin(pluginName, {
          render: GutenbergTutorial
        });
      };
      return noticeSettings;
    },
    /**
     * Get Guide pages in proper format.
     *
     * @since 1.8.1
     *
     * @returns {Array} Guide Pages.
     */
    getGuidePages: function getGuidePages() {
      var pages = [];
      wpforms_edit_post_education.gutenberg_guide.forEach(function (page) {
        pages.push({
          /* eslint-disable react/react-in-jsx-scope */
          content: /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("h1", {
            className: "edit-post-welcome-guide__heading"
          }, page.title), /*#__PURE__*/React.createElement("p", {
            className: "edit-post-welcome-guide__text"
          }, page.content)),
          image: /*#__PURE__*/React.createElement("img", {
            className: "edit-post-welcome-guide__image",
            src: page.image,
            alt: page.title
          })
          /* eslint-enable react/react-in-jsx-scope */
        });
      });

      return pages;
    },
    /**
     * Show notice if the page title matches some keywords for Classic Editor.
     *
     * @since 1.8.1
     */
    maybeShowClassicNotice: function maybeShowClassicNotice() {
      if (app.isNoticeVisible) {
        return;
      }
      if (app.isTitleMatchKeywords($('#title').val())) {
        app.isNoticeVisible = true;
        $('.wpforms-edit-post-education-notice').removeClass('wpforms-hidden');
      }
    },
    /**
     * Show notice if the page title matches some keywords for Gutenberg Editor.
     *
     * @since 1.8.1
     */
    maybeShowGutenbergNotice: function maybeShowGutenbergNotice() {
      if (app.isNoticeVisible) {
        return;
      }
      var $postTitle = app.isFse() ? $('iframe[name="editor-canvas"]').contents().find('.editor-post-title__input') : $('.editor-post-title__input');
      var tagName = $postTitle.prop('tagName');
      var title = tagName === 'TEXTAREA' ? $postTitle.val() : $postTitle.text();
      if (app.isTitleMatchKeywords(title)) {
        app.isNoticeVisible = true;
        app.showGutenbergNotice();
      }
    },
    /**
     * Add notice class when the distraction mode is enabled.
     *
     * @since 1.8.1.2
     */
    distractionFreeModeToggle: function distractionFreeModeToggle() {
      if (!app.isNoticeVisible) {
        return;
      }
      var $document = $(document);
      var isDistractionFreeMode = Boolean($document.find('.is-distraction-free').length);
      if (!isDistractionFreeMode) {
        return;
      }
      var isNoticeHasClass = Boolean($('.wpforms-edit-post-education-notice').length);
      if (isNoticeHasClass) {
        return;
      }
      var $noticeBody = $document.find('.wpforms-edit-post-education-notice-body');
      var $notice = $noticeBody.closest('.components-notice');
      $notice.addClass('wpforms-edit-post-education-notice');
    },
    /**
     * Determine if the title matches keywords.
     *
     * @since 1.8.1
     *
     * @param {string} titleValue Page title value.
     *
     * @returns {boolean} True if the title matches some keywords.
     */
    isTitleMatchKeywords: function isTitleMatchKeywords(titleValue) {
      var expectedTitleRegex = new RegExp(/\b(contact|form)\b/i);
      return expectedTitleRegex.test(titleValue);
    },
    /**
     * Close a notice.
     *
     * @since 1.8.1
     */
    closeNotice: function closeNotice() {
      $(this).closest('.wpforms-edit-post-education-notice').remove();
      app.updateUserMeta();
    },
    /**
     * Update user meta and don't show the notice next time.
     *
     * @since 1.8.1
     */
    updateUserMeta: function updateUserMeta() {
      $.post(wpforms_edit_post_education.ajax_url, {
        action: 'wpforms_education_dismiss',
        nonce: wpforms_edit_post_education.education_nonce,
        section: 'edit-post-notice'
      });
    }
  };
  return app;
}(document, window, jQuery);
WPFormsEditPostEducation.init();
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfc2xpY2VkVG9BcnJheSIsImFyciIsImkiLCJfYXJyYXlXaXRoSG9sZXMiLCJfaXRlcmFibGVUb0FycmF5TGltaXQiLCJfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkiLCJfbm9uSXRlcmFibGVSZXN0IiwiVHlwZUVycm9yIiwibyIsIm1pbkxlbiIsIl9hcnJheUxpa2VUb0FycmF5IiwibiIsIk9iamVjdCIsInByb3RvdHlwZSIsInRvU3RyaW5nIiwiY2FsbCIsInNsaWNlIiwiY29uc3RydWN0b3IiLCJuYW1lIiwiQXJyYXkiLCJmcm9tIiwidGVzdCIsImxlbiIsImxlbmd0aCIsImFycjIiLCJfaSIsIlN5bWJvbCIsIml0ZXJhdG9yIiwiX3MiLCJfZSIsIl94IiwiX3IiLCJfYXJyIiwiX24iLCJfZCIsIm5leHQiLCJkb25lIiwicHVzaCIsInZhbHVlIiwiZXJyIiwicmV0dXJuIiwiaXNBcnJheSIsIldQRm9ybXNFZGl0UG9zdEVkdWNhdGlvbiIsIndpbmRvdyIsImRvY3VtZW50IiwiJCIsImFwcCIsImlzTm90aWNlVmlzaWJsZSIsImluaXQiLCJvbiIsInJlYWR5IiwidGhlbiIsImxvYWQiLCJpc0d1dGVuYmVyZ0VkaXRvciIsIm1heWJlU2hvd0NsYXNzaWNOb3RpY2UiLCJiaW5kQ2xhc3NpY0V2ZW50cyIsImJsb2NrTG9hZGVkSW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsInF1ZXJ5U2VsZWN0b3IiLCJjbGVhckludGVydmFsIiwiaXNGc2UiLCJtYXliZVNob3dHdXRlbmJlcmdOb3RpY2UiLCJiaW5kR3V0ZW5iZXJnRXZlbnRzIiwiaWZyYW1lIiwib2JzZXJ2ZXIiLCJNdXRhdGlvbk9ic2VydmVyIiwiaWZyYW1lRG9jdW1lbnQiLCJjb250ZW50RG9jdW1lbnQiLCJjb250ZW50V2luZG93IiwicmVhZHlTdGF0ZSIsImJpbmRGc2VFdmVudHMiLCJkaXNjb25uZWN0Iiwib2JzZXJ2ZSIsImJvZHkiLCJzdWJ0cmVlIiwiY2hpbGRMaXN0IiwiJGRvY3VtZW50IiwiY2xvc2VOb3RpY2UiLCJkaXN0cmFjdGlvbkZyZWVNb2RlVG9nZ2xlIiwiJGlmcmFtZSIsImNvbnRlbnRzIiwid3AiLCJibG9ja3MiLCJCb29sZWFuIiwic2hvd0d1dGVuYmVyZ05vdGljZSIsImRhdGEiLCJkaXNwYXRjaCIsImNyZWF0ZUluZm9Ob3RpY2UiLCJ3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24iLCJndXRlbmJlcmdfbm90aWNlIiwidGVtcGxhdGUiLCJnZXRHdXRlbmJlcmdOb3RpY2VTZXR0aW5ncyIsImhhc05vdGljZSIsIm5vdGljZUJvZHkiLCIkbm90aWNlIiwiY2xvc2VzdCIsImFkZENsYXNzIiwiZmluZCIsInJlbW92ZUNsYXNzIiwicGx1Z2luTmFtZSIsIm5vdGljZVNldHRpbmdzIiwiaWQiLCJpc0Rpc21pc3NpYmxlIiwiSFRNTCIsIl9fdW5zdGFibGVIVE1MIiwiYWN0aW9ucyIsImNsYXNzTmFtZSIsInZhcmlhbnQiLCJsYWJlbCIsImJ1dHRvbiIsImd1dGVuYmVyZ19ndWlkZSIsInVybCIsIkd1aWRlIiwiY29tcG9uZW50cyIsInVzZVN0YXRlIiwiZWxlbWVudCIsInJlZ2lzdGVyUGx1Z2luIiwicGx1Z2lucyIsInVucmVnaXN0ZXJQbHVnaW4iLCJHdXRlbmJlcmdUdXRvcmlhbCIsIl91c2VTdGF0ZSIsIl91c2VTdGF0ZTIiLCJpc09wZW4iLCJzZXRJc09wZW4iLCJSZWFjdCIsImNyZWF0ZUVsZW1lbnQiLCJvbkZpbmlzaCIsInBhZ2VzIiwiZ2V0R3VpZGVQYWdlcyIsIm9uRGlzbWlzcyIsInVwZGF0ZVVzZXJNZXRhIiwib25DbGljayIsInJlbmRlciIsImZvckVhY2giLCJwYWdlIiwiY29udGVudCIsIkZyYWdtZW50IiwidGl0bGUiLCJpbWFnZSIsInNyYyIsImFsdCIsImlzVGl0bGVNYXRjaEtleXdvcmRzIiwidmFsIiwiJHBvc3RUaXRsZSIsInRhZ05hbWUiLCJwcm9wIiwidGV4dCIsImlzRGlzdHJhY3Rpb25GcmVlTW9kZSIsImlzTm90aWNlSGFzQ2xhc3MiLCIkbm90aWNlQm9keSIsInRpdGxlVmFsdWUiLCJleHBlY3RlZFRpdGxlUmVnZXgiLCJSZWdFeHAiLCJyZW1vdmUiLCJwb3N0IiwiYWpheF91cmwiLCJhY3Rpb24iLCJub25jZSIsImVkdWNhdGlvbl9ub25jZSIsInNlY3Rpb24iLCJqUXVlcnkiXSwic291cmNlcyI6WyJmYWtlXzRjMTE1MjczLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCB3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24gKi9cblxuLyoqXG4gKiBXUEZvcm1zIEVkaXQgUG9zdCBFZHVjYXRpb24gZnVuY3Rpb24uXG4gKlxuICogQHNpbmNlIDEuOC4xXG4gKi9cblxuJ3VzZSBzdHJpY3QnO1xuXG5jb25zdCBXUEZvcm1zRWRpdFBvc3RFZHVjYXRpb24gPSB3aW5kb3cuV1BGb3Jtc0VkaXRQb3N0RWR1Y2F0aW9uIHx8ICggZnVuY3Rpb24oIGRvY3VtZW50LCB3aW5kb3csICQgKSB7XG5cblx0LyoqXG5cdCAqIFB1YmxpYyBmdW5jdGlvbnMgYW5kIHByb3BlcnRpZXMuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjguMVxuXHQgKlxuXHQgKiBAdHlwZSB7b2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgYXBwID0ge1xuXG5cdFx0LyoqXG5cdFx0ICogRGV0ZXJtaW5lIGlmIHRoZSBub3RpY2Ugd2FzIHNob3dlZCBiZWZvcmUuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKi9cblx0XHRpc05vdGljZVZpc2libGU6IGZhbHNlLFxuXG5cdFx0LyoqXG5cdFx0ICogU3RhcnQgdGhlIGVuZ2luZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdGluaXQ6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHQkKCB3aW5kb3cgKS5vbiggJ2xvYWQnLCBmdW5jdGlvbigpIHtcblxuXHRcdFx0XHQvLyBJbiB0aGUgY2FzZSBvZiBqUXVlcnkgMy4rLCB3ZSBuZWVkIHRvIHdhaXQgZm9yIGEgcmVhZHkgZXZlbnQgZmlyc3QuXG5cdFx0XHRcdGlmICggdHlwZW9mICQucmVhZHkudGhlbiA9PT0gJ2Z1bmN0aW9uJyApIHtcblx0XHRcdFx0XHQkLnJlYWR5LnRoZW4oIGFwcC5sb2FkICk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0YXBwLmxvYWQoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBQYWdlIGxvYWQuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKi9cblx0XHRsb2FkOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0aWYgKCAhIGFwcC5pc0d1dGVuYmVyZ0VkaXRvcigpICkge1xuXHRcdFx0XHRhcHAubWF5YmVTaG93Q2xhc3NpY05vdGljZSgpO1xuXHRcdFx0XHRhcHAuYmluZENsYXNzaWNFdmVudHMoKTtcblxuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0IGJsb2NrTG9hZGVkSW50ZXJ2YWwgPSBzZXRJbnRlcnZhbCggZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0aWYgKCAhIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcuZWRpdG9yLXBvc3QtdGl0bGVfX2lucHV0LCBpZnJhbWVbbmFtZT1cImVkaXRvci1jYW52YXNcIl0nICkgKSB7XG5cdFx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Y2xlYXJJbnRlcnZhbCggYmxvY2tMb2FkZWRJbnRlcnZhbCApO1xuXG5cdFx0XHRcdGlmICggISBhcHAuaXNGc2UoKSApIHtcblxuXHRcdFx0XHRcdGFwcC5tYXliZVNob3dHdXRlbmJlcmdOb3RpY2UoKTtcblx0XHRcdFx0XHRhcHAuYmluZEd1dGVuYmVyZ0V2ZW50cygpO1xuXG5cdFx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Y29uc3QgaWZyYW1lID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJ2lmcmFtZVtuYW1lPVwiZWRpdG9yLWNhbnZhc1wiXScgKTtcblx0XHRcdFx0Y29uc3Qgb2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlciggZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0XHRjb25zdCBpZnJhbWVEb2N1bWVudCA9IGlmcmFtZS5jb250ZW50RG9jdW1lbnQgfHwgaWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQgfHwge307XG5cblx0XHRcdFx0XHRpZiAoIGlmcmFtZURvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScgJiYgaWZyYW1lRG9jdW1lbnQucXVlcnlTZWxlY3RvciggJy5lZGl0b3ItcG9zdC10aXRsZV9faW5wdXQnICkgKSB7XG5cdFx0XHRcdFx0XHRhcHAubWF5YmVTaG93R3V0ZW5iZXJnTm90aWNlKCk7XG5cdFx0XHRcdFx0XHRhcHAuYmluZEZzZUV2ZW50cygpO1xuXG5cdFx0XHRcdFx0XHRvYnNlcnZlci5kaXNjb25uZWN0KCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9ICk7XG5cdFx0XHRcdG9ic2VydmVyLm9ic2VydmUoIGRvY3VtZW50LmJvZHksIHsgc3VidHJlZTogdHJ1ZSwgY2hpbGRMaXN0OiB0cnVlIH0gKTtcblx0XHRcdH0sIDIwMCApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBCaW5kIGV2ZW50cyBmb3IgQ2xhc3NpYyBFZGl0b3IuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKi9cblx0XHRiaW5kQ2xhc3NpY0V2ZW50czogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGNvbnN0ICRkb2N1bWVudCA9ICQoIGRvY3VtZW50ICk7XG5cblx0XHRcdGlmICggISBhcHAuaXNOb3RpY2VWaXNpYmxlICkge1xuXHRcdFx0XHQkZG9jdW1lbnQub24oICdpbnB1dCcsICcjdGl0bGUnLCBhcHAubWF5YmVTaG93Q2xhc3NpY05vdGljZSApO1xuXHRcdFx0fVxuXG5cdFx0XHQkZG9jdW1lbnQub24oICdjbGljaycsICcud3Bmb3Jtcy1lZGl0LXBvc3QtZWR1Y2F0aW9uLW5vdGljZS1jbG9zZScsIGFwcC5jbG9zZU5vdGljZSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBCaW5kIGV2ZW50cyBmb3IgR3V0ZW5iZXJnIEVkaXRvci5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdGJpbmRHdXRlbmJlcmdFdmVudHM6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRjb25zdCAkZG9jdW1lbnQgPSAkKCBkb2N1bWVudCApO1xuXG5cdFx0XHQkZG9jdW1lbnRcblx0XHRcdFx0Lm9uKCAnRE9NU3VidHJlZU1vZGlmaWVkJywgJy5lZGl0LXBvc3QtbGF5b3V0JywgYXBwLmRpc3RyYWN0aW9uRnJlZU1vZGVUb2dnbGUgKTtcblxuXHRcdFx0aWYgKCBhcHAuaXNOb3RpY2VWaXNpYmxlICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdCRkb2N1bWVudFxuXHRcdFx0XHQub24oICdpbnB1dCcsICcuZWRpdG9yLXBvc3QtdGl0bGVfX2lucHV0JywgYXBwLm1heWJlU2hvd0d1dGVuYmVyZ05vdGljZSApXG5cdFx0XHRcdC5vbiggJ0RPTVN1YnRyZWVNb2RpZmllZCcsICcuZWRpdG9yLXBvc3QtdGl0bGVfX2lucHV0JywgYXBwLm1heWJlU2hvd0d1dGVuYmVyZ05vdGljZSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBCaW5kIGV2ZW50cyBmb3IgR3V0ZW5iZXJnIEVkaXRvciBpbiBGU0UgbW9kZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdGJpbmRGc2VFdmVudHM6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRjb25zdCAkaWZyYW1lID0gJCggJ2lmcmFtZVtuYW1lPVwiZWRpdG9yLWNhbnZhc1wiXScgKTtcblxuXHRcdFx0JCggZG9jdW1lbnQgKVxuXHRcdFx0XHQub24oICdET01TdWJ0cmVlTW9kaWZpZWQnLCAnLmVkaXQtcG9zdC1sYXlvdXQnLCBhcHAuZGlzdHJhY3Rpb25GcmVlTW9kZVRvZ2dsZSApO1xuXG5cdFx0XHQkaWZyYW1lLmNvbnRlbnRzKClcblx0XHRcdFx0Lm9uKCAnRE9NU3VidHJlZU1vZGlmaWVkJywgJy5lZGl0b3ItcG9zdC10aXRsZV9faW5wdXQnLCBhcHAubWF5YmVTaG93R3V0ZW5iZXJnTm90aWNlICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIERldGVybWluZSBpZiB0aGUgZWRpdG9yIGlzIEd1dGVuYmVyZy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59IFRydWUgaWYgdGhlIGVkaXRvciBpcyBHdXRlbmJlcmcuXG5cdFx0ICovXG5cdFx0aXNHdXRlbmJlcmdFZGl0b3I6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRyZXR1cm4gdHlwZW9mIHdwICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2Ygd3AuYmxvY2tzICE9PSAndW5kZWZpbmVkJztcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogRGV0ZXJtaW5lIGlmIHRoZSBlZGl0b3IgaXMgR3V0ZW5iZXJnIGluIEZTRSBtb2RlLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn0gVHJ1ZSBpZiB0aGUgR3V0ZW5iZXJnIGVkaXRvciBpbiBGU0UgbW9kZS5cblx0XHQgKi9cblx0XHRpc0ZzZTogZnVuY3Rpb24oKSB7XG5cblx0XHRcdHJldHVybiBCb29sZWFuKCAkKCAnaWZyYW1lW25hbWU9XCJlZGl0b3ItY2FudmFzXCJdJyApLmxlbmd0aCApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBDcmVhdGUgYSBub3RpY2UgZm9yIEd1dGVuYmVyZy5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdHNob3dHdXRlbmJlcmdOb3RpY2U6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHR3cC5kYXRhLmRpc3BhdGNoKCAnY29yZS9ub3RpY2VzJyApLmNyZWF0ZUluZm9Ob3RpY2UoXG5cdFx0XHRcdHdwZm9ybXNfZWRpdF9wb3N0X2VkdWNhdGlvbi5ndXRlbmJlcmdfbm90aWNlLnRlbXBsYXRlLFxuXHRcdFx0XHRhcHAuZ2V0R3V0ZW5iZXJnTm90aWNlU2V0dGluZ3MoKVxuXHRcdFx0KTtcblxuXHRcdFx0Ly8gVGhlIG5vdGljZSBjb21wb25lbnQgZG9lc24ndCBoYXZlIGEgd2F5IHRvIGFkZCBIVE1MIGlkIG9yIGNsYXNzIHRvIHRoZSBub3RpY2UuXG5cdFx0XHQvLyBBbHNvLCB0aGUgbm90aWNlIGJlY2FtZSB2aXNpYmxlIHdpdGggYSBkZWxheSBvbiBvbGQgR3V0ZW5iZXJnIHZlcnNpb25zLlxuXHRcdFx0Y29uc3QgaGFzTm90aWNlID0gc2V0SW50ZXJ2YWwoIGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRcdGNvbnN0IG5vdGljZUJvZHkgPSAkKCAnLndwZm9ybXMtZWRpdC1wb3N0LWVkdWNhdGlvbi1ub3RpY2UtYm9keScgKTtcblx0XHRcdFx0aWYgKCAhIG5vdGljZUJvZHkubGVuZ3RoICkge1xuXHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGNvbnN0ICRub3RpY2UgPSBub3RpY2VCb2R5LmNsb3Nlc3QoICcuY29tcG9uZW50cy1ub3RpY2UnICk7XG5cdFx0XHRcdCRub3RpY2UuYWRkQ2xhc3MoICd3cGZvcm1zLWVkaXQtcG9zdC1lZHVjYXRpb24tbm90aWNlJyApO1xuXHRcdFx0XHQkbm90aWNlLmZpbmQoICcuaXMtc2Vjb25kYXJ5LCAuaXMtbGluaycgKS5yZW1vdmVDbGFzcyggJ2lzLXNlY29uZGFyeScgKS5yZW1vdmVDbGFzcyggJ2lzLWxpbmsnICkuYWRkQ2xhc3MoICdpcy1wcmltYXJ5JyApO1xuXG5cdFx0XHRcdGNsZWFySW50ZXJ2YWwoIGhhc05vdGljZSApO1xuXHRcdFx0fSwgMTAwICk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEdldCBzZXR0aW5ncyBmb3IgdGhlIEd1dGVuYmVyZyBub3RpY2UuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtvYmplY3R9IE5vdGljZSBzZXR0aW5ncy5cblx0XHQgKi9cblx0XHRnZXRHdXRlbmJlcmdOb3RpY2VTZXR0aW5nczogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGNvbnN0IHBsdWdpbk5hbWUgPSAnd3Bmb3Jtcy1lZGl0LXBvc3QtcHJvZHVjdC1lZHVjYXRpb24tZ3VpZGUnO1xuXHRcdFx0Y29uc3Qgbm90aWNlU2V0dGluZ3MgPSB7XG5cdFx0XHRcdGlkOiBwbHVnaW5OYW1lLFxuXHRcdFx0XHRpc0Rpc21pc3NpYmxlOiB0cnVlLFxuXHRcdFx0XHRIVE1MOiB0cnVlLFxuXHRcdFx0XHRfX3Vuc3RhYmxlSFRNTDogdHJ1ZSxcblx0XHRcdFx0YWN0aW9uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGNsYXNzTmFtZTogJ3dwZm9ybXMtZWRpdC1wb3N0LWVkdWNhdGlvbi1ub3RpY2UtZ3VpZGUtYnV0dG9uJyxcblx0XHRcdFx0XHRcdHZhcmlhbnQ6ICdwcmltYXJ5Jyxcblx0XHRcdFx0XHRcdGxhYmVsOiB3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24uZ3V0ZW5iZXJnX25vdGljZS5idXR0b24sXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XSxcblx0XHRcdH07XG5cblx0XHRcdGlmICggISB3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24uZ3V0ZW5iZXJnX2d1aWRlICkge1xuXG5cdFx0XHRcdG5vdGljZVNldHRpbmdzLmFjdGlvbnNbMF0udXJsID0gd3Bmb3Jtc19lZGl0X3Bvc3RfZWR1Y2F0aW9uLmd1dGVuYmVyZ19ub3RpY2UudXJsO1xuXG5cdFx0XHRcdHJldHVybiBub3RpY2VTZXR0aW5ncztcblx0XHRcdH1cblxuXHRcdFx0Y29uc3QgR3VpZGUgPSB3cC5jb21wb25lbnRzLkd1aWRlO1xuXHRcdFx0Y29uc3QgdXNlU3RhdGUgPSB3cC5lbGVtZW50LnVzZVN0YXRlO1xuXHRcdFx0Y29uc3QgcmVnaXN0ZXJQbHVnaW4gPSB3cC5wbHVnaW5zLnJlZ2lzdGVyUGx1Z2luO1xuXHRcdFx0Y29uc3QgdW5yZWdpc3RlclBsdWdpbiA9IHdwLnBsdWdpbnMudW5yZWdpc3RlclBsdWdpbjtcblx0XHRcdGNvbnN0IEd1dGVuYmVyZ1R1dG9yaWFsID0gZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0Y29uc3QgWyBpc09wZW4sIHNldElzT3BlbiBdID0gdXNlU3RhdGUoIHRydWUgKTtcblxuXHRcdFx0XHRpZiAoICEgaXNPcGVuICkge1xuXHRcdFx0XHRcdHJldHVybiBudWxsO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0cmV0dXJuIChcblx0XHRcdFx0XHQvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgcmVhY3QvcmVhY3QtaW4tanN4LXNjb3BlXG5cdFx0XHRcdFx0PEd1aWRlXG5cdFx0XHRcdFx0XHRjbGFzc05hbWU9XCJlZGl0LXBvc3Qtd2VsY29tZS1ndWlkZVwiXG5cdFx0XHRcdFx0XHRvbkZpbmlzaD17ICgpID0+IHtcblx0XHRcdFx0XHRcdFx0dW5yZWdpc3RlclBsdWdpbiggcGx1Z2luTmFtZSApO1xuXHRcdFx0XHRcdFx0XHRzZXRJc09wZW4oIGZhbHNlICk7XG5cdFx0XHRcdFx0XHR9IH1cblx0XHRcdFx0XHRcdHBhZ2VzPXsgYXBwLmdldEd1aWRlUGFnZXMoKSB9XG5cdFx0XHRcdFx0Lz5cblx0XHRcdFx0KTtcblx0XHRcdH07XG5cblx0XHRcdG5vdGljZVNldHRpbmdzLm9uRGlzbWlzcyA9IGFwcC51cGRhdGVVc2VyTWV0YTtcblx0XHRcdG5vdGljZVNldHRpbmdzLmFjdGlvbnNbMF0ub25DbGljayA9ICgpID0+IHJlZ2lzdGVyUGx1Z2luKCBwbHVnaW5OYW1lLCB7IHJlbmRlcjogR3V0ZW5iZXJnVHV0b3JpYWwgfSApO1xuXG5cdFx0XHRyZXR1cm4gbm90aWNlU2V0dGluZ3M7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEdldCBHdWlkZSBwYWdlcyBpbiBwcm9wZXIgZm9ybWF0LlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7QXJyYXl9IEd1aWRlIFBhZ2VzLlxuXHRcdCAqL1xuXHRcdGdldEd1aWRlUGFnZXM6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRjb25zdCBwYWdlcyA9IFtdO1xuXG5cdFx0XHR3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24uZ3V0ZW5iZXJnX2d1aWRlLmZvckVhY2goIGZ1bmN0aW9uKCBwYWdlICkge1xuXHRcdFx0XHRwYWdlcy5wdXNoKFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdC8qIGVzbGludC1kaXNhYmxlIHJlYWN0L3JlYWN0LWluLWpzeC1zY29wZSAqL1xuXHRcdFx0XHRcdFx0Y29udGVudDogKFxuXHRcdFx0XHRcdFx0XHQ8PlxuXHRcdFx0XHRcdFx0XHRcdDxoMSBjbGFzc05hbWU9XCJlZGl0LXBvc3Qtd2VsY29tZS1ndWlkZV9faGVhZGluZ1wiPnsgcGFnZS50aXRsZSB9PC9oMT5cblx0XHRcdFx0XHRcdFx0XHQ8cCBjbGFzc05hbWU9XCJlZGl0LXBvc3Qtd2VsY29tZS1ndWlkZV9fdGV4dFwiPnsgcGFnZS5jb250ZW50IH08L3A+XG5cdFx0XHRcdFx0XHRcdDwvPlxuXHRcdFx0XHRcdFx0KSxcblx0XHRcdFx0XHRcdGltYWdlOiA8aW1nIGNsYXNzTmFtZT1cImVkaXQtcG9zdC13ZWxjb21lLWd1aWRlX19pbWFnZVwiIHNyYz17IHBhZ2UuaW1hZ2UgfSBhbHQ9eyBwYWdlLnRpdGxlIH0gLz4sXG5cdFx0XHRcdFx0XHQvKiBlc2xpbnQtZW5hYmxlIHJlYWN0L3JlYWN0LWluLWpzeC1zY29wZSAqL1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0KTtcblx0XHRcdH0gKTtcblxuXHRcdFx0cmV0dXJuIHBhZ2VzO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBTaG93IG5vdGljZSBpZiB0aGUgcGFnZSB0aXRsZSBtYXRjaGVzIHNvbWUga2V5d29yZHMgZm9yIENsYXNzaWMgRWRpdG9yLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICovXG5cdFx0bWF5YmVTaG93Q2xhc3NpY05vdGljZTogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGlmICggYXBwLmlzTm90aWNlVmlzaWJsZSApIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHRpZiAoIGFwcC5pc1RpdGxlTWF0Y2hLZXl3b3JkcyggJCggJyN0aXRsZScgKS52YWwoKSApICkge1xuXHRcdFx0XHRhcHAuaXNOb3RpY2VWaXNpYmxlID0gdHJ1ZTtcblxuXHRcdFx0XHQkKCAnLndwZm9ybXMtZWRpdC1wb3N0LWVkdWNhdGlvbi1ub3RpY2UnICkucmVtb3ZlQ2xhc3MoICd3cGZvcm1zLWhpZGRlbicgKTtcblx0XHRcdH1cblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogU2hvdyBub3RpY2UgaWYgdGhlIHBhZ2UgdGl0bGUgbWF0Y2hlcyBzb21lIGtleXdvcmRzIGZvciBHdXRlbmJlcmcgRWRpdG9yLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICovXG5cdFx0bWF5YmVTaG93R3V0ZW5iZXJnTm90aWNlOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0aWYgKCBhcHAuaXNOb3RpY2VWaXNpYmxlICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0ICRwb3N0VGl0bGUgPSBhcHAuaXNGc2UoKSA/XG5cdFx0XHRcdCQoICdpZnJhbWVbbmFtZT1cImVkaXRvci1jYW52YXNcIl0nICkuY29udGVudHMoKS5maW5kKCAnLmVkaXRvci1wb3N0LXRpdGxlX19pbnB1dCcgKSA6XG5cdFx0XHRcdCQoICcuZWRpdG9yLXBvc3QtdGl0bGVfX2lucHV0JyApO1xuXHRcdFx0Y29uc3QgdGFnTmFtZSA9ICRwb3N0VGl0bGUucHJvcCggJ3RhZ05hbWUnICk7XG5cdFx0XHRjb25zdCB0aXRsZSA9IHRhZ05hbWUgPT09ICdURVhUQVJFQScgPyAkcG9zdFRpdGxlLnZhbCgpIDogJHBvc3RUaXRsZS50ZXh0KCk7XG5cblx0XHRcdGlmICggYXBwLmlzVGl0bGVNYXRjaEtleXdvcmRzKCB0aXRsZSApICkge1xuXHRcdFx0XHRhcHAuaXNOb3RpY2VWaXNpYmxlID0gdHJ1ZTtcblxuXHRcdFx0XHRhcHAuc2hvd0d1dGVuYmVyZ05vdGljZSgpO1xuXHRcdFx0fVxuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBBZGQgbm90aWNlIGNsYXNzIHdoZW4gdGhlIGRpc3RyYWN0aW9uIG1vZGUgaXMgZW5hYmxlZC5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMS4yXG5cdFx0ICovXG5cdFx0ZGlzdHJhY3Rpb25GcmVlTW9kZVRvZ2dsZTogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGlmICggISBhcHAuaXNOb3RpY2VWaXNpYmxlICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0ICRkb2N1bWVudCA9ICQoIGRvY3VtZW50ICk7XG5cdFx0XHRjb25zdCBpc0Rpc3RyYWN0aW9uRnJlZU1vZGUgPSBCb29sZWFuKCAkZG9jdW1lbnQuZmluZCggJy5pcy1kaXN0cmFjdGlvbi1mcmVlJyApLmxlbmd0aCApO1xuXG5cdFx0XHRpZiAoICEgaXNEaXN0cmFjdGlvbkZyZWVNb2RlICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0IGlzTm90aWNlSGFzQ2xhc3MgPSBCb29sZWFuKCAkKCAnLndwZm9ybXMtZWRpdC1wb3N0LWVkdWNhdGlvbi1ub3RpY2UnICkubGVuZ3RoICk7XG5cblx0XHRcdGlmICggaXNOb3RpY2VIYXNDbGFzcyApIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHRjb25zdCAkbm90aWNlQm9keSA9ICRkb2N1bWVudC5maW5kKCAnLndwZm9ybXMtZWRpdC1wb3N0LWVkdWNhdGlvbi1ub3RpY2UtYm9keScgKTtcblx0XHRcdGNvbnN0ICRub3RpY2UgPSAkbm90aWNlQm9keS5jbG9zZXN0KCAnLmNvbXBvbmVudHMtbm90aWNlJyApO1xuXG5cdFx0XHQkbm90aWNlLmFkZENsYXNzKCAnd3Bmb3Jtcy1lZGl0LXBvc3QtZWR1Y2F0aW9uLW5vdGljZScgKTtcblx0XHR9LFxuXG5cdFx0LyoqXG5cdFx0ICogRGV0ZXJtaW5lIGlmIHRoZSB0aXRsZSBtYXRjaGVzIGtleXdvcmRzLlxuXHRcdCAqXG5cdFx0ICogQHNpbmNlIDEuOC4xXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gdGl0bGVWYWx1ZSBQYWdlIHRpdGxlIHZhbHVlLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59IFRydWUgaWYgdGhlIHRpdGxlIG1hdGNoZXMgc29tZSBrZXl3b3Jkcy5cblx0XHQgKi9cblx0XHRpc1RpdGxlTWF0Y2hLZXl3b3JkczogZnVuY3Rpb24oIHRpdGxlVmFsdWUgKSB7XG5cblx0XHRcdGNvbnN0IGV4cGVjdGVkVGl0bGVSZWdleCA9IG5ldyBSZWdFeHAoIC9cXGIoY29udGFjdHxmb3JtKVxcYi9pICk7XG5cblx0XHRcdHJldHVybiBleHBlY3RlZFRpdGxlUmVnZXgudGVzdCggdGl0bGVWYWx1ZSApO1xuXHRcdH0sXG5cblx0XHQvKipcblx0XHQgKiBDbG9zZSBhIG5vdGljZS5cblx0XHQgKlxuXHRcdCAqIEBzaW5jZSAxLjguMVxuXHRcdCAqL1xuXHRcdGNsb3NlTm90aWNlOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0JCggdGhpcyApLmNsb3Nlc3QoICcud3Bmb3Jtcy1lZGl0LXBvc3QtZWR1Y2F0aW9uLW5vdGljZScgKS5yZW1vdmUoKTtcblxuXHRcdFx0YXBwLnVwZGF0ZVVzZXJNZXRhKCk7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIFVwZGF0ZSB1c2VyIG1ldGEgYW5kIGRvbid0IHNob3cgdGhlIG5vdGljZSBuZXh0IHRpbWUuXG5cdFx0ICpcblx0XHQgKiBAc2luY2UgMS44LjFcblx0XHQgKi9cblx0XHR1cGRhdGVVc2VyTWV0YSgpIHtcblxuXHRcdFx0JC5wb3N0KFxuXHRcdFx0XHR3cGZvcm1zX2VkaXRfcG9zdF9lZHVjYXRpb24uYWpheF91cmwsXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRhY3Rpb246ICd3cGZvcm1zX2VkdWNhdGlvbl9kaXNtaXNzJyxcblx0XHRcdFx0XHRub25jZTogd3Bmb3Jtc19lZGl0X3Bvc3RfZWR1Y2F0aW9uLmVkdWNhdGlvbl9ub25jZSxcblx0XHRcdFx0XHRzZWN0aW9uOiAnZWRpdC1wb3N0LW5vdGljZScsXG5cdFx0XHRcdH1cblx0XHRcdCk7XG5cdFx0fSxcblx0fTtcblxuXHRyZXR1cm4gYXBwO1xuXG59KCBkb2N1bWVudCwgd2luZG93LCBqUXVlcnkgKSApO1xuXG5XUEZvcm1zRWRpdFBvc3RFZHVjYXRpb24uaW5pdCgpO1xuIl0sIm1hcHBpbmdzIjoiQUFBQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBLFlBQVk7O0FBQUMsU0FBQUEsZUFBQUMsR0FBQSxFQUFBQyxDQUFBLFdBQUFDLGVBQUEsQ0FBQUYsR0FBQSxLQUFBRyxxQkFBQSxDQUFBSCxHQUFBLEVBQUFDLENBQUEsS0FBQUcsMkJBQUEsQ0FBQUosR0FBQSxFQUFBQyxDQUFBLEtBQUFJLGdCQUFBO0FBQUEsU0FBQUEsaUJBQUEsY0FBQUMsU0FBQTtBQUFBLFNBQUFGLDRCQUFBRyxDQUFBLEVBQUFDLE1BQUEsU0FBQUQsQ0FBQSxxQkFBQUEsQ0FBQSxzQkFBQUUsaUJBQUEsQ0FBQUYsQ0FBQSxFQUFBQyxNQUFBLE9BQUFFLENBQUEsR0FBQUMsTUFBQSxDQUFBQyxTQUFBLENBQUFDLFFBQUEsQ0FBQUMsSUFBQSxDQUFBUCxDQUFBLEVBQUFRLEtBQUEsYUFBQUwsQ0FBQSxpQkFBQUgsQ0FBQSxDQUFBUyxXQUFBLEVBQUFOLENBQUEsR0FBQUgsQ0FBQSxDQUFBUyxXQUFBLENBQUFDLElBQUEsTUFBQVAsQ0FBQSxjQUFBQSxDQUFBLG1CQUFBUSxLQUFBLENBQUFDLElBQUEsQ0FBQVosQ0FBQSxPQUFBRyxDQUFBLCtEQUFBVSxJQUFBLENBQUFWLENBQUEsVUFBQUQsaUJBQUEsQ0FBQUYsQ0FBQSxFQUFBQyxNQUFBO0FBQUEsU0FBQUMsa0JBQUFULEdBQUEsRUFBQXFCLEdBQUEsUUFBQUEsR0FBQSxZQUFB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
},{}]},{},[1])