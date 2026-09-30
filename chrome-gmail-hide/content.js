// content.js

// Semua aria-label Gmail (dalam bahasa Indonesia) yang mau disembunyikan
const labelsToHide = [
  "Lampirkan file",                     // paperclip
  "Sisipkan tautan",                     // link
  "Sisipkan file menggunakan Drive",     // Drive
  "Sisipkan foto"                        // photo
];

function hideIcons() {
  labelsToHide.forEach(label => {
    document
      .querySelectorAll(`[aria-label="${label}"]`)
      .forEach(el => el.style.display = "none");
  });
}

// Jalankan sekali saat load
hideIcons();

// Pasang MutationObserver supaya setiap kali Gmail merender ulang composer, ikon tetap disembunyikan
const observer = new MutationObserver(hideIcons);
observer.observe(document.body, { childList: true, subtree: true });
