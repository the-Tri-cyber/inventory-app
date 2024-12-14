// Memuat status sidebar dari localStorage
document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector("#sidebar"); // Ganti dengan selector yang sesuai untuk sidebar Anda
  const isSidebarClosed = localStorage.getItem("sidebarClosed") === "true";

  if (isSidebarClosed) {
    sidebar.classList.add("collapsed"); // Ganti dengan class yang sesuai untuk menutup sidebar
  }
});

// Menyimpan status sidebar ke localStorage saat tombol diklik
document
  .querySelector(".navbar-toggler")
  .addEventListener("click", function () {
    const sidebar = document.querySelector("#sidebar"); // Ganti dengan selector yang sesuai untuk sidebar Anda
    const isClosed = sidebar.classList.toggle("collapsed"); // Ganti dengan class yang sesuai untuk menutup sidebar
    localStorage.setItem("sidebarClosed", isClosed);
  });

// button untuk memunculkan modal di laporan
document.querySelector(".btn-laporan").addEventListener("click", function () {
  console.log("Tombol Laporan Barang diklik");
});

// format tanggal dalam modal
$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy", // Format yang diinginkan
    autoclose: true, // Menutup datepicker setelah memilih tanggal
    todayHighlight: true, // Menyoroti tanggal hari ini
  });
});
