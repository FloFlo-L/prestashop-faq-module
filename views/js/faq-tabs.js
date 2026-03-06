document.addEventListener("DOMContentLoaded", function () {
  var tabs = document.querySelectorAll(".faq-category-tab");
  var panes = document.querySelectorAll(".faq-tab-pane");
  var scroll = document.querySelector(".faq-categories-scroll");

  tabs.forEach(function (tab) {
    tab.addEventListener("click", function () {
      var targetId = tab.getAttribute("data-bs-target").replace("#", "");

      tabs.forEach(function (t) {
        t.classList.remove("active");
      });
      panes.forEach(function (p) {
        p.classList.remove("active");
      });

      tab.classList.add("active");
      document.getElementById(targetId).classList.add("active");

      // Scroll the clicked tab into view if the container is scrollable
      if (scroll) {
        var tabLeft = tab.offsetLeft;
        var tabWidth = tab.offsetWidth;
        var scrollWidth = scroll.offsetWidth;
        scroll.scrollTo({
          left: tabLeft - scrollWidth / 2 + tabWidth / 2,
          behavior: "smooth",
        });
      }
    });
  });
});
