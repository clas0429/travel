import { db, storage } from './firebase-init.js';
import { collection, doc, getDoc, getDocs, orderBy, query } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';
import { getDownloadURL, ref } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-storage.js';

function renderLocationSummary(data) {
  const container = document.getElementById('photos-info');
  if (!container) return;
  container.innerHTML = '';

  const title = document.createElement('h1');
  title.className = 'text-2xl font-semibold';
  title.textContent = data.name || '未命名地點';
  container.appendChild(title);

  if (data.description) {
    const description = document.createElement('p');
    description.className = 'text-sm text-[var(--ink)]/70';
    description.textContent = data.description;
    container.appendChild(description);
  }
}

async function createPhotoCard(data) {
  const item = document.createElement('button');
  item.type = 'button';
  item.className = 'group relative overflow-hidden rounded-lg border border-[var(--border)] bg-white/80 text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]';

  let thumbUrl = '';
  let fullUrl = '';

  try {
    if (data.thumbPath) {
      thumbUrl = await getDownloadURL(ref(storage, data.thumbPath));
    }
  } catch (error) {
    console.warn('Failed to load thumbnail', error);
  }

  try {
    if (data.imagePath) {
      fullUrl = await getDownloadURL(ref(storage, data.imagePath));
    }
  } catch (error) {
    console.warn('Failed to load full image', error);
  }

  if (!thumbUrl) {
    thumbUrl = fullUrl;
  }

  if (!thumbUrl) {
    const placeholder = document.createElement('div');
    placeholder.className = 'flex h-48 w-full items-center justify-center bg-[var(--border)]/40 text-sm text-[var(--ink)]/70';
    placeholder.textContent = '無法載入圖片';
    item.appendChild(placeholder);
  } else {
    const img = document.createElement('img');
    img.src = thumbUrl;
    img.alt = data.title || '旅途相片';
    img.className = 'h-48 w-full object-cover transition group-hover:scale-105';
    item.appendChild(img);
  }

  const resolvedFullUrl = fullUrl || thumbUrl;

  if (data.title) {
    const caption = document.createElement('div');
    caption.className = 'p-3 text-sm';
    caption.textContent = data.title;
    item.appendChild(caption);
  }

  item.addEventListener('click', () => {
    if (window.ui?.lightbox && resolvedFullUrl) {
      window.ui.lightbox.open(resolvedFullUrl, data.title);
    }
  });

  return item;
}

export async function initPhotosPage() {
  const params = new URLSearchParams(window.location.search);
  const loc = params.get('loc');
  const grid = document.getElementById('photo-grid');
  if (!grid) return;

  if (!loc) {
    grid.innerHTML = '<p class="text-sm text-[var(--ink)]/70">請指定地點參數 loc。</p>';
    return;
  }

  grid.innerHTML = '<p class="text-sm text-[var(--ink)]/70">載入中...</p>';

  try {
    const locationSnapshot = await getDoc(doc(db, 'locations', loc));
    if (!locationSnapshot.exists()) {
      grid.innerHTML = '<p class="text-sm text-red-600">找不到地點資料。</p>';
      return;
    }
    renderLocationSummary(locationSnapshot.data());

    const photosRef = collection(db, 'locations', loc, 'photos');
    const photoQuery = query(photosRef, orderBy('order', 'asc'));
    const photoSnapshot = await getDocs(photoQuery);

    grid.innerHTML = '';
    if (photoSnapshot.empty) {
      grid.innerHTML = '<p class="text-sm text-[var(--ink)]/70">目前沒有相片。</p>';
      return;
    }

    for (const docSnap of photoSnapshot.docs) {
      const card = await createPhotoCard(docSnap.data());
      grid.appendChild(card);
    }
  } catch (error) {
    console.error('Failed to load photos', error);
    grid.innerHTML = '<p class="text-sm text-red-600">載入相片時發生錯誤。</p>';
  }
}
