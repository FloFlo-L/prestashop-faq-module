document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".faq-accordion-question").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var isOpen = btn.getAttribute("aria-expanded") === "true";
      var pane = btn.closest(".faq-tab-pane");

      pane.querySelectorAll(".faq-accordion-question").forEach(function (b) {
        b.setAttribute("aria-expanded", "false");
      });
      pane.querySelectorAll(".faq-accordion-body").forEach(function (d) {
        d.style.maxHeight = "0";
      });

      if (!isOpen) {
        btn.setAttribute("aria-expanded", "true");
        var body = document.getElementById(btn.getAttribute("data-faq-target"));
        body.style.maxHeight = body.scrollHeight + "px";
      }
    });
  });
});
