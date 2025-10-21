// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById("mobile-menu-btn");
const mobileMenu = document.getElementById("mobile-menu");

mobileMenuBtn.addEventListener("click", () => {
  mobileMenu.classList.toggle("hidden");
});

// Carousel Functionality
let currentSlide = 0;
const slides = document.querySelectorAll(".carousel-slide");
const totalSlides = slides.length;

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.style.opacity = i === index ? "1" : "0";
  });
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % totalSlides;
  showSlide(currentSlide);
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  showSlide(currentSlide);
}

// Auto-rotate carousel
setInterval(nextSlide, 5000);

// Carousel controls
document.getElementById("next-btn").addEventListener("click", nextSlide);
document.getElementById("prev-btn").addEventListener("click", prevSlide);

// Menu Filter Functionality
const filterBtns = document.querySelectorAll(".filter-btn");
const menuItems = document.querySelectorAll(".menu-item");

filterBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    // Remove active class from all buttons
    filterBtns.forEach((b) => {
      b.classList.remove("active", "bg-orange-deep", "text-white");
      b.classList.add("text-gray-600");
    });

    // Add active class to clicked button
    btn.classList.add("active", "bg-orange-deep", "text-white");
    btn.classList.remove("text-gray-600");

    const filter = btn.getAttribute("data-filter");

    // Show/hide menu items
    menuItems.forEach((item) => {
      if (filter === "all" || item.getAttribute("data-category") === filter) {
        item.style.display = "block";
      } else {
        item.style.display = "none";
      }
    });
  });
});

// Initialize first filter button as active
document
  .querySelector('.filter-btn[data-filter="all"]')
  .classList.add("bg-orange-deep", "text-white");
document
  .querySelector('.filter-btn[data-filter="all"]')
  .classList.remove("text-gray-600");

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
    // Close mobile menu if open
    mobileMenu.classList.add("hidden");
  });
});
