document.addEventListener("DOMContentLoaded", () => {
  const themeToggleButton = document.getElementById("theme-toggle");

  const savedTheme = localStorage.getItem("preferredTheme");
  if (savedTheme) {
    if (savedTheme === "dark") {
      document.documentElement.setAttribute("data-theme", "dark");
    } else {
      document.documentElement.removeAttribute("data-theme");
    }
  } else if (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  ) {
    document.documentElement.setAttribute("data-theme", "dark");
  }

  window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", (e) => {
      if (e.matches) {
        document.documentElement.setAttribute("data-theme", "dark");
        localStorage.setItem("preferredTheme", "dark");
      } else {
        document.documentElement.removeAttribute("data-theme");
        localStorage.setItem("preferredTheme", "light");
      }
    });

  if (themeToggleButton) {
    themeToggleButton.addEventListener("click", () => {
      if (document.documentElement.getAttribute("data-theme") === "dark") {
        document.documentElement.removeAttribute("data-theme");
        localStorage.setItem("preferredTheme", "light");
      } else {
        document.documentElement.setAttribute("data-theme", "dark");
        localStorage.setItem("preferredTheme", "dark");
      }
    });
  }

  const accordionHeaders = document.querySelectorAll(".accordion-header");

  accordionHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const currentlyOpenContent = document.querySelector(
        ".accordion-content.show"
      );
      const currentlyActiveHeader = document.querySelector(
        ".accordion-header.active"
      );
      const content = header.nextElementSibling;

      if (currentlyOpenContent && currentlyOpenContent !== content) {
        currentlyOpenContent.classList.remove("show");
        currentlyOpenContent.style.maxHeight = null;
      }
      if (currentlyActiveHeader && currentlyActiveHeader !== header) {
        currentlyActiveHeader.classList.remove("active");
      }

      // Toggle the clicked accordion
      if (content.classList.contains("show")) {
        content.classList.remove("show");
        content.style.maxHeight = null;
        header.classList.remove("active");
      } else {
        content.classList.add("show");
        content.style.maxHeight = content.scrollHeight + "px";
        header.classList.add("active");
      }
    });
  });

  const cookieBanner = document.getElementById("cookie-banner");
  const cookieAccept = document.getElementById("cookie-accept");

  if (cookieBanner && cookieAccept) {
    if (!localStorage.getItem("cookiesAccepted")) {
      cookieBanner.style.display = "flex";
    } else {
      cookieBanner.style.display = "none";
    }

    cookieAccept.addEventListener("click", () => {
      localStorage.setItem("cookiesAccepted", "true");
      cookieBanner.style.display = "none";
    });
  }
});
