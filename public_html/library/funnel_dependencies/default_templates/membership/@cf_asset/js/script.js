jQuery(function ($) {
  $(".sidebar-dropdown > a").click(function () {
    $(".sidebar-submenu").slideUp(200);

    if (
      $(this)
        .parent()

        .hasClass("active")
    ) {
      $(".sidebar-dropdown").removeClass("active");

      $(this)
        .parent()

        .removeClass("active");
    } else {
      $(".sidebar-dropdown").removeClass("active");

      $(this)
        .next(".sidebar-submenu")

        .slideDown(200);

      $(this)
        .parent()

        .addClass("active");
    }
  });

  $("#close-sidebar").click(function () {
    $(".page-wrapper").removeClass("toggled");
  });

  $("#show-sidebar").click(function () {
    $(".page-wrapper").addClass("toggled");
  });
});

jQuery(document).ready(function () {
  jQuery('[data-bs-toggle="tooltip"]').tooltip();
});

// Counter test

$(".count").each(function () {
  $(this)
    .prop("Counter", 0)
    .animate(
      {
        Counter: $(this).text(),
      },
      {
        duration: 4000,

        easing: "swing",

        step: function (now) {
          $(this).text(Math.ceil(now));
        },
      }
    );
});

var dropdown = document.getElementsByClassName("dropdown-btn");

var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function () {
    this.classList.toggle("active");

    var dropdownContent = this.nextElementSibling;

    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
