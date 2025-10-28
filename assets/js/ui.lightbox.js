const template = document.createElement('template');
template.innerHTML = `
  <div class="lightbox-backdrop fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-6">
    <div class="relative max-h-full max-w-4xl">
      <button type="button" aria-label="關閉" class="absolute right-2 top-2 rounded bg-black/70 px-2 py-1 text-sm text-white">✕</button>
      <img class="max-h-[80vh] max-w-full rounded bg-white/90 object-contain" alt="lightbox">
      <p class="mt-2 text-center text-sm text-white"></p>
    </div>
  </div>
`;

const element = template.content.firstElementChild.cloneNode(true);
const closeButton = element.querySelector('button');
const image = element.querySelector('img');
const caption = element.querySelector('p');

document.body.appendChild(element);

function closeLightbox() {
  element.classList.add('hidden');
  image.src = '';
  caption.textContent = '';
}

closeButton.addEventListener('click', closeLightbox);

element.addEventListener('click', (event) => {
  if (event.target === element) {
    closeLightbox();
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && !element.classList.contains('hidden')) {
    closeLightbox();
  }
});

function openLightbox(src, title = '') {
  image.src = src;
  image.alt = title || 'lightbox image';
  caption.textContent = title || '';
  element.classList.remove('hidden');
}

window.ui = window.ui || {};
window.ui.lightbox = {
  open: openLightbox,
  close: closeLightbox,
};
