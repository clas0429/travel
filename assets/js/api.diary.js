import { db, storage } from './firebase-init.js';
import { collection, doc, getDoc, getDocs, orderBy, query } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';
import { getDownloadURL, ref } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-storage.js';

async function renderLocationInfo(data) {
  const container = document.getElementById('location-info');
  if (!container) return;
  container.innerHTML = '';

  const title = document.createElement('h1');
  title.className = 'text-2xl font-semibold';
  title.textContent = data.name || '未命名地點';

  const description = document.createElement('p');
  description.className = 'text-sm text-[var(--ink)]/70';
  description.textContent = data.description || '這個地點目前僅提供基本資訊。';

  container.appendChild(title);
  container.appendChild(description);

  if (data.myMapsUrl) {
    const link = document.createElement('a');
    link.id = 'location-map';
    link.className = 'inline-flex items-center gap-2 text-sm text-[var(--accent)] underline';
    link.href = data.myMapsUrl;
    link.target = '_blank';
    link.rel = 'noopener';
    link.textContent = '開啟 Google My Maps';
    container.appendChild(link);
  }

  let coverUrl = data.cover || '';
  if (!coverUrl && data.coverImagePath) {
    try {
      coverUrl = await getDownloadURL(ref(storage, data.coverImagePath));
    } catch (error) {
      console.warn('Failed to load cover image', error);
    }
  }

  if (coverUrl) {
    const img = document.createElement('img');
    img.src = coverUrl;
    img.alt = data.name || '地點封面';
    img.className = 'mt-4 h-48 w-full rounded object-cover';
    container.appendChild(img);
  }
}

function renderDiaries(snapshot) {
  const list = document.getElementById('diary-list');
  if (!list) return;
  list.innerHTML = '';

  if (snapshot.empty) {
    const empty = document.createElement('p');
    empty.className = 'text-sm text-[var(--ink)]/70';
    empty.textContent = '目前沒有日誌。';
    list.appendChild(empty);
    return;
  }

  snapshot.forEach((docSnap) => {
    const data = docSnap.data();
    const article = document.createElement('article');
    article.className = 'rounded border border-[var(--border)] bg-white/90 p-4 shadow-sm';

    const heading = document.createElement('h3');
    heading.className = 'text-lg font-semibold';
    heading.textContent = data.title || '未命名日誌';

    const meta = document.createElement('p');
    meta.className = 'text-xs text-[var(--ink)]/50';
    if (data.createdAt?.toDate) {
      meta.textContent = `建立時間：${data.createdAt.toDate().toLocaleString()}`;
    }

    const content = document.createElement('div');
    content.className = 'prose max-w-none text-sm text-[var(--ink)]/80';
    content.innerHTML = data.content || '';

    article.appendChild(heading);
    if (meta.textContent) {
      article.appendChild(meta);
    }
    article.appendChild(content);

    list.appendChild(article);
  });
}

export async function loadLocation(loc) {
  const locationRef = doc(db, 'locations', loc);
  const snapshot = await getDoc(locationRef);
  if (!snapshot.exists()) {
    throw new Error('找不到地點資料');
  }
  return snapshot.data();
}

export async function listDiaries(loc) {
  const diariesRef = collection(db, 'locations', loc, 'diaries');
  const diaryQuery = query(diariesRef, orderBy('createdAt', 'desc'));
  return getDocs(diaryQuery);
}

export async function initDiaryPage() {
  const params = new URLSearchParams(window.location.search);
  const loc = params.get('loc');
  if (!loc) {
    const list = document.getElementById('diary-list');
    if (list) {
      list.innerHTML = '<p class="text-sm text-[var(--ink)]/70">請從首頁選擇地點後再查看日誌。</p>';
    }
    return;
  }

  try {
    const locationData = await loadLocation(loc);
    await renderLocationInfo(locationData);
    const diarySnapshot = await listDiaries(loc);
    renderDiaries(diarySnapshot);
  } catch (error) {
    console.error('Failed to load diaries', error);
    const list = document.getElementById('diary-list');
    if (list) {
      list.innerHTML = '<p class="text-sm text-red-600">載入日誌時發生錯誤，請稍後再試。</p>';
    }
  }
}
