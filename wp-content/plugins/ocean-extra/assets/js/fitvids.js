var $j = jQuery.noConflict();

$j( document ).ready( function() {
	"use strict";
    // Responsive Video
	oceanwpInitFitVids();
} );

/* ==============================================
RESPONSIVE VIDEOS
============================================== */
function oceanwpInitFitVids() {
	"use strict"

	$j( '.responsive-video-wrap, .responsive-audio-wrap' ).fitVids();

}