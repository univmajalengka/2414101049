(function () {
  const STORAGE_KEY = "bbp_products";
  const productForm = document.getElementById("product-form");
  const nameInput = document.getElementById("product-name");
  const priceInput = document.getElementById("product-price");
  const categorySelect = document.getElementById("product-category");
  const imageInput = document.getElementById("product-image");
  const descriptionInput = document.getElementById("product-description");
  const submitButton = document.getElementById("form-submit-btn");
  const cancelEditButton = document.getElementById("cancel-edit");
  const formTitle = document.getElementById("form-title");
  const formSubtitle = document.getElementById("form-subtitle");
  const formAlert = document.getElementById("form-alert");
  const tableBody = document.getElementById("product-table-body");
  const resetButton = document.getElementById("reset-products");

  let products = loadProducts();
  let editingId = null;

  renderProducts();

  productForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const nameValue = nameInput.value.trim();
    const priceValue = Number(priceInput.value);
    const categoryValue = categorySelect.value;
    const imageValue = imageInput.value.trim();
    const descriptionValue = descriptionInput.value.trim();

    if (!nameValue) {
      showAlert("Nama produk wajib diisi", "error");
      return;
    }

    if (!Number.isFinite(priceValue) || priceValue < 0) {
      showAlert("Harga tidak valid", "error");
      return;
    }

    if (editingId) {
      products = products.map(function (item) {
        if (item.id === editingId) {
          return Object.assign({}, item, {
            name: nameValue,
            price: priceValue,
            category: categoryValue,
            image: imageValue,
            description: descriptionValue,
          });
        }
        return item;
      });
      persistProducts();
      renderProducts();
      resetForm();
      showAlert("Produk berhasil diperbarui", "success");
      return;
    }

    const newProduct = {
      id: generateId(),
      name: nameValue,
      price: priceValue,
      category: categoryValue,
      image: imageValue,
      description: descriptionValue,
      createdAt: Date.now(),
    };

    products.push(newProduct);
    persistProducts();
    renderProducts();
    productForm.reset();
    showAlert("Produk baru berhasil ditambahkan", "success");
  });

  cancelEditButton.addEventListener("click", function () {
    resetForm();
  });

  resetButton.addEventListener("click", function () {
    if (!products.length) {
      showAlert("Tidak ada produk untuk dihapus", "error");
      return;
    }

    const confirmation = window.confirm(
      "Apakah Anda yakin ingin menghapus semua produk?"
    );

    if (!confirmation) {
      return;
    }

    products = [];
    persistProducts();
    renderProducts();
    resetForm();
    showAlert("Seluruh produk sudah dihapus", "success");
  });

  tableBody.addEventListener("click", function (event) {
    const action = event.target.getAttribute("data-action");
    const productId = event.target.getAttribute("data-id");

    if (!action || !productId) {
      return;
    }

    if (action === "edit") {
      const product = products.find(function (item) {
        return item.id === productId;
      });

      if (!product) {
        showAlert("Produk tidak ditemukan", "error");
        return;
      }

      editingId = product.id;
      nameInput.value = product.name;
      priceInput.value = product.price;
      categorySelect.value = product.category;
      imageInput.value = product.image || "";
      descriptionInput.value = product.description || "";
      submitButton.textContent = "Perbarui Produk";
      cancelEditButton.classList.remove("hidden");
      formTitle.textContent = "Edit Produk";
      formSubtitle.textContent = "Perbarui detail produk kemudian simpan perubahan.";
      nameInput.focus();
      return;
    }

    if (action === "delete") {
      const confirmation = window.confirm(
        "Hapus produk ini dari daftar?"
      );

      if (!confirmation) {
        return;
      }

      products = products.filter(function (item) {
        return item.id !== productId;
      });

      persistProducts();
      renderProducts();
      resetForm();
      showAlert("Produk berhasil dihapus", "success");
    }
  });

  function renderProducts() {
    tableBody.innerHTML = "";

    if (!products.length) {
      const emptyRow = document.createElement("tr");
      emptyRow.innerHTML =
        '<td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada produk yang ditambahkan.</td>';
      tableBody.appendChild(emptyRow);
      return;
    }

    products
      .slice()
      .sort(function (a, b) {
        return a.name.localeCompare(b.name);
      })
      .forEach(function (product) {
        const row = document.createElement("tr");
        row.className = "border-t";
        row.innerHTML = createRowMarkup(product);
        tableBody.appendChild(row);
      });
  }

  function createRowMarkup(product) {
    const formattedPrice = formatCurrency(product.price);
    const categoryLabel = capitalize(product.category);
    const imageContent = product.image
      ? '<img src="' +
        product.image +
        '" alt="' +
        escapeHtml(product.name) +
        '" class="w-16 h-16 object-cover rounded" />'
      : '<span class="text-gray-400">Tidak ada gambar</span>';

    const descriptionContent = product.description
      ? '<span class="block max-w-xs text-sm text-gray-600">' +
        escapeHtml(product.description) +
        "</span>"
      : '<span class="text-gray-400 text-sm">-</span>';

    return (
      '<td class="px-6 py-4">' +
      "<div class=\"font-semibold\">" +
      escapeHtml(product.name) +
      "</div>" +
      "<p class=\"text-xs text-gray-500 mt-1\">Dibuat " +
      formatDate(product.createdAt) +
      "</p>" +
      "</td>" +
      '<td class="px-6 py-4">' +
      formattedPrice +
      "</td>" +
      '<td class="px-6 py-4">' +
      categoryLabel +
      "</td>" +
      '<td class="px-6 py-4">' +
      descriptionContent +
      "</td>" +
      '<td class="px-6 py-4">' +
      imageContent +
      "</td>" +
      '<td class="px-6 py-4 space-x-2">' +
      '<button data-action="edit" data-id="' +
      product.id +
      '" class="px-4 py-2 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition-colors">Edit</button>' +
      '<button data-action="delete" data-id="' +
      product.id +
      '" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">Hapus</button>' +
      "</td>"
    );
  }

  function resetForm() {
    editingId = null;
    productForm.reset();
    submitButton.textContent = "Simpan Produk";
    cancelEditButton.classList.add("hidden");
    formTitle.textContent = "Tambah Produk Baru";
    formSubtitle.textContent =
      "Lengkapi detail produk untuk menambahkannya ke daftar.";
  }

  function showAlert(message, type) {
    formAlert.textContent = message;
    formAlert.classList.remove(
      "hidden",
      "bg-red-50",
      "text-red-600",
      "border-red-200",
      "bg-green-50",
      "text-green-600",
      "border-green-200"
    );

    if (type === "error") {
      formAlert.classList.add("bg-red-50", "text-red-600", "border-red-200");
    } else {
      formAlert.classList.add("bg-green-50", "text-green-600", "border-green-200");
    }

    clearTimeout(showAlert.timeoutId);
    showAlert.timeoutId = setTimeout(function () {
      formAlert.classList.add("hidden");
    }, 4000);
  }

  function persistProducts() {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(products));
    } catch (error) {
      console.error("Gagal menyimpan produk", error);
    }
  }

  function loadProducts() {
    const defaultProducts = [
      {
        id: generateId(),
        name: "Bakso Urat",
        price: 12000,
        category: "makanan",
        image: "menu/baksourat.jpg",
        description: "Bakso urat dengan kuah kaldu gurih.",
        createdAt: Date.now() - 86400000,
      },
      {
        id: generateId(),
        name: "Es Buah Segar",
        price: 8000,
        category: "minuman",
        image: "menu/esbuah.jpg",
        description: "Es buah dengan potongan segar dan sirup manis.",
        createdAt: Date.now(),
      },
    ];

    try {
      const storedValue = window.localStorage.getItem(STORAGE_KEY);
      if (!storedValue) {
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(defaultProducts));
        return defaultProducts;
      }
      const parsed = JSON.parse(storedValue);
      if (!Array.isArray(parsed)) {
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(defaultProducts));
        return defaultProducts;
      }
      return parsed.map(function (item) {
        return Object.assign(
          {
            description: "",
          },
          item
        );
      });
    } catch (error) {
      console.error("Gagal memuat produk", error);
      return defaultProducts;
    }
  }

  function generateId() {
    if (window.crypto && typeof window.crypto.randomUUID === "function") {
      return window.crypto.randomUUID();
    }
    return (
      Date.now().toString(36) +
      Math.random().toString(36).substring(2, 8)
    );
  }

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

  function capitalize(text) {
    if (!text) {
      return "-";
    }
    return text.charAt(0).toUpperCase() + text.slice(1);
  }

  function formatDate(timestamp) {
    if (!timestamp) {
      return "-";
    }
    return new Intl.DateTimeFormat("id-ID", {
      day: "2-digit",
      month: "long",
      year: "numeric",
    }).format(timestamp);
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }
})();
