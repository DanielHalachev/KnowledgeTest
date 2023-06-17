function toggleNav() {
  var sidebar = document.querySelector("aside");
  var body = document.querySelector("body");
  sidebar.classList.toggle('hidden');
  body.classList.toggle('no-sidebar');
}
