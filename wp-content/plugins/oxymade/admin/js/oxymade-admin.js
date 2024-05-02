(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
})(jQuery);

function copyColors() {
  event.preventDefault();
  var copyColors = document.getElementById("export_color_palette");
  copyColors.select();
  document.execCommand("copy");
  document.getElementById("mcolors_export").innerText = "Copied";

  setTimeout(function () {
    document.getElementById("mcolors_export").innerText = "Copy Colors";
  }, 2000);
}
function copyWhitelistClasses() {
  event.preventDefault();
  var copyWhitelistClasses = document.getElementById(
    "export_whitelist_classes"
  );
  copyWhitelistClasses.select();
  document.execCommand("copy");
  document.getElementById("whitelist_classes").innerText = "Copied";

  setTimeout(function () {
    document.getElementById("whitelist_classes").innerText = "Copy classes";
  }, 2000);
}
function copySelectors() {
  event.preventDefault();
  var copySelectors = document.getElementById("export_selectors_area");
  copySelectors.select();
  document.execCommand("copy");
  document.getElementById("mselectors_export").innerText = "Copied";

  setTimeout(function () {
    document.getElementById("mselectors_export").innerText = "Copy Selectors";
  }, 2000);
}
function copyStylesheets() {
  event.preventDefault();
  var copyStylesheets = document.getElementById("export_stylesheets_area");
  copyStylesheets.select();
  document.execCommand("copy");
  document.getElementById("mstylesheets_export").innerText = "Copied";

  setTimeout(function () {
    document.getElementById("mstylesheets_export").innerText =
      "Copy Stylesheets";
  }, 2000);
}

jQuery(document).ready(function () {
  setTimeout(function () {
    jQuery(".content-visibility-auto").each(function () {
      jQuery(this).removeClass("hidden");
    });
  }, 2000);
});

function activateButton(element) {
  if (element.checked) {
    document.getElementById("purge_submit").disabled = false;
  } else {
    document.getElementById("purge_submit").disabled = true;
  }
}

// console.log("finding blogzine");
jQuery(window).load(function () {
  // console.log("page loaded");
  setTimeout(function () {
    var gbeditor = jQuery(".block-editor-block-list__block");
    var gbblocktype = gbeditor.data("type");
    if (typeof gbblocktype !== 'undefined') {
      var gboxyblock = gbblocktype.includes("oxygen-vsb/ovsb");
      // console.log(gbeditor.data("type"));
      console.log("OxyBerg Block Status " + gboxyblock);
    }
    if (gboxyblock) {
      console.log("OxyBerg Loaded - Removed blogzine class");
      jQuery("body").removeClass("blogzine mx-auto");
    }
  }, 3000);
});
