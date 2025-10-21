(function () {
  const quantityInput = document.getElementById("quantity-input");
  const minusButton = document.getElementById("qty-minus");
  const plusButton = document.getElementById("qty-plus");
  const summaryQty = document.getElementById("summary-qty");
  const totalDisplay = document.getElementById("order-total");
  const unitPriceDisplay = document.getElementById("unit-price");
  const unitPriceInput = document.getElementById("unit-price-input");
  const totalInput = document.getElementById("total-price");
  const form = document.getElementById("purchase-form");
  const calcExpression = document.getElementById("calc-expression");
  const calcTotal = document.getElementById("calc-total");

  if (!quantityInput || !unitPriceInput || !totalDisplay || !totalInput) {
    return;
  }

  const unitPrice = Number(unitPriceInput.value || 0);

  function formatCurrency(value) {
    try {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(value);
    } catch (error) {
      return "Rp " + Number(value || 0).toLocaleString("id-ID");
    }
  }

  function normaliseQuantity(value) {
    const parsed = parseInt(value, 10);
    return Number.isFinite(parsed) && parsed > 0 ? parsed : 1;
  }

  function updateSummary() {
    const qty = normaliseQuantity(quantityInput.value);
    const total = qty * unitPrice;

    quantityInput.value = qty;

    if (summaryQty) {
      summaryQty.textContent = `${qty} porsi`;
    }

    if (unitPriceDisplay) {
      unitPriceDisplay.textContent = formatCurrency(unitPrice);
    }

    if (calcExpression) {
      calcExpression.textContent = `${qty} x ${formatCurrency(unitPrice)}`;
    }

    if (calcTotal) {
      calcTotal.textContent = formatCurrency(total);
    }

    totalDisplay.textContent = formatCurrency(total);
    totalInput.value = total;
  }

  quantityInput.addEventListener("input", updateSummary);

  minusButton?.addEventListener("click", function () {
    const current = normaliseQuantity(quantityInput.value);
    if (current > 1) {
      quantityInput.value = current - 1;
      updateSummary();
    }
  });

  plusButton?.addEventListener("click", function () {
    const current = normaliseQuantity(quantityInput.value);
    quantityInput.value = current + 1;
    updateSummary();
  });

  form?.addEventListener("submit", function () {
    updateSummary();
  });

  updateSummary();
})();
