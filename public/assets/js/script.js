const toggler = document.querySelector(".navbar-toggler");
toggler.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("collapsed");
});
