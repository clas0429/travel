import { auth, db, storage } from './firebase-init.js';
import { setRedirectState } from './auth.js';
import { resolveCategoryLabel, resolveCategoryPage } from './categories.js';
import { doc, getDoc } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';
import { getDownloadURL, ref } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-storage.js';

async function sha256Hex(input) {
  const encoder = new TextEncoder();
  const data = encoder.encode(input);
  const digest = await crypto.subtle.digest('SHA-256', data);
  return Array.from(new Uint8Array(digest))
    .map((byte) => byte.toString(16).padStart(2, '0'))
    .join('');
}

function getQueryValues() {
  const params = new URLSearchParams(window.location.search);
  return {
    loc: params.get('loc') || '',
    cat: params.get('cat') || '',
    token: params.get('k') || '',
  };
}

function clearHint(element) {
  element.innerHTML = '';
  element.classList.remove('hidden');
}

function renderErrorHint(element, message) {
  if (!element) return;
  clearHint(element);
  const text = document.createElement('p');
  text.className = 'text-sm text-[var(--ink-muted)]';
  text.textContent = message;
  element.appendChild(text);
}

function renderSuccessHint(element, payload) {
  if (!element) return;
  clearHint(element);

  const wrapper = document.createElement('div');
  wrapper.className = 'gm-hint';

  if (payload.coverUrl) {
    const figure = document.createElement('figure');
    figure.className = 'overflow-hidden rounded-lg border border-[var(--border)] bg-white/60 shadow-sm';
    const img = document.createElement('img');
    img.src = payload.coverUrl;
    img.alt = `${payload.locationName} 封面圖`;
    img.className = 'h-full w-full object-cover';
    figure.appendChild(img);
    wrapper.appendChild(figure);
  }

  const body = document.createElement('div');
  body.className = 'space-y-3';

  const title = document.createElement('h2');
  title.className = 'text-xl font-semibold';
  title.textContent = `歡迎來到 ${payload.locationName}`;
  body.appendChild(title);

  const description = document.createElement('p');
  description.className = 'text-sm text-[var(--ink-muted)]';
  if (auth.currentUser) {
    description.textContent = `驗證成功！立即瀏覽 ${payload.categoryLabel}，掌握旅程亮點。`;
  } else {
    description.textContent = `驗證成功！請登入以瀏覽 ${payload.categoryLabel}。`;
  }
  body.appendChild(description);

  const actions = document.createElement('div');
  actions.className = 'flex flex-wrap gap-3';

  const categoryPage = resolveCategoryPage(payload.category);
  if (auth.currentUser && categoryPage) {
    const link = document.createElement('a');
    const url = new URL(categoryPage, window.location.origin);
    url.searchParams.set('loc', payload.loc);
    link.href = url.toString();
    link.className = 'gm-button gm-button-accent';
    link.textContent = `立即瀏覽 ${payload.categoryLabel}`;
    actions.appendChild(link);
  } else {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'gm-button gm-button-accent';
    button.textContent = `請登入以瀏覽 ${payload.categoryLabel}`;
    button.addEventListener('click', () => {
      const params = new URLSearchParams(window.location.search);
      params.set('cat', payload.category);
      params.set('loc', payload.loc);
      if (payload.token) {
        params.set('k', payload.token);
      }
      const queryString = params.toString();
      const path = categoryPage ? `/${categoryPage}` : '/index.php';
      setRedirectState(path, queryString ? `?${queryString}` : '');
      window.location.href = `login.php${queryString ? `?${queryString}` : ''}`;
    });
    actions.appendChild(button);
  }

  if (payload.mapUrl) {
    const mapLink = document.createElement('a');
    mapLink.href = payload.mapUrl;
    mapLink.target = '_blank';
    mapLink.rel = 'noopener';
    mapLink.className = 'gm-button gm-button-outline';
    mapLink.textContent = '查看地圖';
    actions.appendChild(mapLink);
  }

  body.appendChild(actions);
  wrapper.appendChild(body);
  element.appendChild(wrapper);
}

export async function initQrFlow() {
  const hintElement = document.querySelector('[data-qr-alert]');
  if (!hintElement) {
    return;
  }

  const { loc, cat, token } = getQueryValues();
  if (!loc || !cat || !token) {
    hintElement.classList.add('hidden');
    return;
  }

  const categoryPage = resolveCategoryPage(cat);
  if (!categoryPage) {
    renderErrorHint(hintElement, '無法辨識的分類。');
    return;
  }

  try {
    const hashed = await sha256Hex(token);
    const snapshot = await getDoc(doc(db, 'locations', loc));
    if (!snapshot.exists()) {
      renderErrorHint(hintElement, '找不到對應的地點。');
      return;
    }
    const data = snapshot.data();
    if (data.qrKeyHash !== hashed) {
      renderErrorHint(hintElement, 'QR 驗證失敗，請重新掃描或聯絡管理員。');
      return;
    }

    let coverUrl = '';
    if (data.coverImagePath) {
      try {
        coverUrl = await getDownloadURL(ref(storage, data.coverImagePath));
      } catch (error) {
        console.warn('Failed to load cover image', error);
      }
    }

    renderSuccessHint(hintElement, {
      loc,
      token,
      category: cat,
      categoryLabel: resolveCategoryLabel(cat),
      locationName: data.name || loc,
      coverUrl,
      mapUrl: data.myMapsUrl || '',
    });
  } catch (error) {
    console.error('QR validation failed', error);
    renderErrorHint(hintElement, '驗證時發生錯誤，請稍後再試。');
  }
}
