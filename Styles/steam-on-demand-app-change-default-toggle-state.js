jQuery(document).ready(function ($) {
  var delay = 100;
  setTimeout(function () {
    $(".elementor-tab-title").removeClass("elementor-active");
    $(".elementor-tab-content").css("display", "none");
  }, delay);
});
