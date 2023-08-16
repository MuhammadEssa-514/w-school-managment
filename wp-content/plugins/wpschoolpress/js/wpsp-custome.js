jQuery(document).ready(function (p) {
    p(".wpsp-navigation li:has(ul)").prepend('<span class="wpsp-droparrow"></span>'), p(".wpsp-droparrow").click(function () {
        p(this).siblings(".sub-menu").slideToggle("slow"), p(this).toggleClass("up")
    }), p(".sidebarScroll").slimScroll({
        height: "100%",
        size: "6px"
    }), p(".wpsp-dropdown-toggle").click(function () {
        p(this).toggleClass("wpsp-dropdown-active"), p(this).siblings(".wpsp-dropdown").slideToggle("slow"), p(this).parents(".wpsp-dropdownmain").toggleClass("wpsp-dropdown-open")
    }), p(document).mouseup(function (s) {
        var o = p(".wpsp-dropdown-open");
        o.is(s.target) || 0 !== o.has(s.target).length || (p(".wpsp-dropdown").fadeOut(), p(".wpsp-dropdown-toggle").removeClass("wpsp-dropdown-active"), p(".wpsp-dropdownmain").removeClass("wpsp-dropdown-open"))
    }), p(".wpsp-menuIcon").click(function () {
        p(this).toggleClass("wpsp-close"), p(".wpsp-sidebar").toggleClass("wpsp-slideMenu"), p("body").toggleClass("wpsp-bodyFix")
    }), p(".wpsp-overlay").click(function () {
        p(".wpsp-menuIcon").removeClass("wpsp-close"), p(".wpsp-sidebar").removeClass("wpsp-slideMenu"), p(".wpsp-bodyFix").removeClass("wpsp-bodyFix")
    }), p("#datetimepicker1").datepicker({
        autoclose: !0,
        todayHighlight: !0
    }).datepicker("update", new Date), p(".wpsp-popclick").off("click"), p(document).ready(function () {
        p("body").on("click", ".wpsp-popclick", function () {
            var s = p(this).attr("data-pop");
            p("#" + s).addClass("wpsp-popVisible"), p("body").addClass("wpsp-bodyFixed")
        })
    }), p(".wpsp-closePopup, .wpsp-popup-cancel, .wpsp-overlayer").click(function () {
        p("body").removeClass("wpsp-bodyFixed"), p("#SavingModal").css("display", "none"), p("#WarningModal").css("display", "none"), p("#SuccessModal").css("display", "none"), p("#DeleteModal").css("display", "none"), p(".wpsp-popupMain").removeClass("wpsp-popVisible")
    }), p("input[type=file]").change(function () {
        var s = this.value.split("\\").pop();
        p(this).closest(".wpsp-btn-file").next(".text").text(s)
    }), p(".wpsp-closeLoading, .wpsp-preLoading-onsubmit").click(function () {
        p(".wpsp-preLoading-onsubmit").css("display", "none")
    })
});
(function ($) {
    $(window).load(function () {
        $(".wpsp-preLoading").fadeOut()
    })
})(jQuery);