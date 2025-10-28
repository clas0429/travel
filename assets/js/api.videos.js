import { db, storage } from './firebase-init.js';
import { collection, doc, getDoc, getDocs, orderBy, query } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';
import { getDownloadURL, ref } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-storage.js';

function renderLocationHeader(data) {
  const container = document.getElementById('videos-info');
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

function renderYoutubeVideo(data) {
  const wrapper = document.createElement('article');
  wrapper.className = 'overflow-hidden rounded-lg border border-[var(--border)] bg-white/90 shadow-sm';

  const iframe = document.createElement('iframe');
  iframe.src = `https://www.youtube.com/embed/${data.youtubeId}`;
  iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
  iframe.allowFullscreen = true;
  iframe.title = data.title || 'YouTube 影片';
  iframe.className = 'h-64 w-full';
  wrapper.appendChild(iframe);

  if (data.title) {
    const caption = document.createElement('div');
    caption.className = 'p-3 text-sm';
    caption.textContent = data.title;
    wrapper.appendChild(caption);
  }

  return wrapper;
}

async function renderFileVideo(data) {
  const wrapper = document.createElement('article');
  wrapper.className = 'overflow-hidden rounded-lg border border-[var(--border)] bg-white/90 shadow-sm';

  const sourceUrl = await getDownloadURL(ref(storage, data.filePath));

  const video = document.createElement('video');
  video.controls = true;
  video.src = sourceUrl;
  video.className = 'w-full';
  video.title = data.title || '檔案影片';
  wrapper.appendChild(video);

  if (data.title) {
    const caption = document.createElement('div');
    caption.className = 'p-3 text-sm';
    caption.textContent = data.title;
    wrapper.appendChild(caption);
  }

  return wrapper;
}

export async function initVideosPage() {
  const params = new URLSearchParams(window.location.search);
  const loc = params.get('loc');
  const list = document.getElementById('video-list');
  if (!list) return;

  if (!loc) {
    list.innerHTML = '<p class="text-sm text-[var(--ink)]/70">請指定地點參數 loc。</p>';
    return;
  }

  list.innerHTML = '<p class="text-sm text-[var(--ink)]/70">載入中...</p>';

  try {
    const locationSnapshot = await getDoc(doc(db, 'locations', loc));
    if (!locationSnapshot.exists()) {
      list.innerHTML = '<p class="text-sm text-red-600">找不到地點資料。</p>';
      return;
    }
    renderLocationHeader(locationSnapshot.data());

    const videosRef = collection(db, 'locations', loc, 'videos');
    const videoQuery = query(videosRef, orderBy('order', 'asc'));
    const videoSnapshot = await getDocs(videoQuery);

    list.innerHTML = '';
    if (videoSnapshot.empty) {
      list.innerHTML = '<p class="text-sm text-[var(--ink)]/70">目前沒有影片。</p>';
      return;
    }

    for (const docSnap of videoSnapshot.docs) {
      const data = docSnap.data();
      let node;
      if (data.type === 'youtube' && data.youtubeId) {
        node = renderYoutubeVideo(data);
      } else if (data.type === 'file' && data.filePath) {
        node = await renderFileVideo(data);
      } else {
        continue;
      }
      list.appendChild(node);
    }
  } catch (error) {
    console.error('Failed to load videos', error);
    list.innerHTML = '<p class="text-sm text-red-600">載入影片時發生錯誤。</p>';
  }
}
