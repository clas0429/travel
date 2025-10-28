import { auth, onAuthState, waitForAuthReady } from './firebase-init.js';
import { signInWithEmailAndPassword, signOut } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js';

export function setRedirectState(pathname = '', search = '') {
  if (typeof window === 'undefined') {
    return;
  }
  const normalizedPath = pathname
    ? pathname.startsWith('/')
      ? pathname
      : `/${pathname}`
    : '';
  const normalizedSearch = search
    ? search.startsWith('?')
      ? search
      : `?${search}`
    : '';

  if (normalizedPath) {
    sessionStorage.setItem('redirectPath', normalizedPath);
  } else {
    sessionStorage.removeItem('redirectPath');
  }

  if (normalizedSearch) {
    sessionStorage.setItem('redirectQuery', normalizedSearch);
  } else {
    sessionStorage.removeItem('redirectQuery');
  }
}

export async function ensureSignedInOrRedirect() {
  const user = await waitForAuthReady();
  if (user) {
    return user;
  }
  setRedirectState(window.location.pathname, window.location.search);
  window.location.href = 'login.php';
  throw new Error('Redirecting to login');
}

export async function loginWithEmailPassword(email, password) {
  if (!email || !password) {
    throw new Error('請輸入 Email 與密碼');
  }
  const credential = await signInWithEmailAndPassword(auth, email, password);
  return credential.user;
}

export async function logout() {
  await signOut(auth);
  window.location.href = 'index.php';
}

export function onAuthStateChanged(callback) {
  return onAuthState(callback);
}

document.addEventListener('click', (event) => {
  const target = event.target instanceof HTMLElement ? event.target : null;
  if (!target) {
    return;
  }
  if (target.matches('[data-action="logout"]')) {
    event.preventDefault();
    logout().catch((error) => {
      console.error('Logout failed', error);
      alert('登出失敗，請稍後再試。');
    });
  }
});
