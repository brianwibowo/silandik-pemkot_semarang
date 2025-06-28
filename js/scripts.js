document.getElementById("searchInput").addEventListener("input", function (e) {
  const searchTerm = e.target.value.toLowerCase().trim();
  const cards = document.querySelectorAll(".school-card");
  let visibleCount = 0;

  cards.forEach((card) => {
    const name = card.dataset.name;
    const desc = card.dataset.desc;
    const titleEl = card.querySelector(".school-title");
    const descEl = card.querySelector(".school-desc");

    // Remove previous highlights
    const originalTitle = titleEl.textContent;
    const originalDesc = descEl.textContent;
    titleEl.innerHTML = originalTitle;
    descEl.innerHTML = originalDesc;

    if (
      searchTerm === "" ||
      name.includes(searchTerm) ||
      desc.includes(searchTerm)
    ) {
      card.style.display = "block";
      visibleCount++;

      // Add highlighting
      if (searchTerm !== "") {
        if (name.includes(searchTerm)) {
          titleEl.innerHTML = highlightText(originalTitle, searchTerm);
        }
        if (desc.includes(searchTerm)) {
          descEl.innerHTML = highlightText(originalDesc, searchTerm);
        }
      }
    } else {
      card.style.display = "none";
    }
  });

  // Show/hide no results message
  const noResults = document.getElementById("noResults");
  if (noResults) {
    if (visibleCount === 0 && searchTerm !== "") {
      noResults.classList.remove("d-none");
    } else {
      noResults.classList.add("d-none");
    }
  }
});

// Highlight function
function highlightText(text, term) {
  const regex = new RegExp(`(${term})`, "gi");
  return text.replace(regex, '<span class="search-highlight">$1</span>');
}

// Sort functionality
document.getElementById("sortFilter").addEventListener("change", function (e) {
  const sortType = e.target.value;
  const grid = document.getElementById("schoolGrid");
  const cards = Array.from(document.querySelectorAll(".school-card"));

  if (sortType === "name") {
    cards.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
  } else if (sortType === "date") {
    // Sort by ID (reverse for newest first)
    cards.sort((a, b) => {
      const aId = parseInt(
        a.querySelector('a[href*="id="]').href.split("id=")[1]
      );
      const bId = parseInt(
        b.querySelector('a[href*="id="]').href.split("id=")[1]
      );
      return bId - aId;
    });
  }

  // Re-append sorted cards
  cards.forEach((card) => grid.appendChild(card));
});

// View toggle functionality
document.getElementById("viewToggle").addEventListener("click", function () {
  const grid = document.getElementById("schoolGrid");
  const icon = document.getElementById("viewIcon");

  if (grid.style.gridTemplateColumns.includes("240px")) {
    grid.style.gridTemplateColumns = "repeat(auto-fill, minmax(280px, 1fr))";
    icon.classList.remove("fa-th");
    icon.classList.add("fa-th-large");
    this.title = "View Compact";
  } else {
    grid.style.gridTemplateColumns = "repeat(auto-fill, minmax(240px, 1fr))";
    icon.classList.remove("fa-th-large");
    icon.classList.add("fa-th");
    this.title = "View Large";
  }
});

// Auto-hide alerts after 5 seconds
setTimeout(function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    if (window.bootstrap && window.bootstrap.Alert) {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }
  });
}, 5000);

// Smooth scroll for navigation
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({ behavior: "smooth" });
    }
  });
});
