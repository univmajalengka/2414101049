(function () {
  const printButton = document.getElementById("print-invoice");
  printButton?.addEventListener("click", function () {
    window.print();
  });
})();
