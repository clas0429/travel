const config = window.FIREBASE_CONFIG || {};

import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js';
import { getAuth, onAuthStateChanged as firebaseOnAuthStateChanged } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js';
import { getFirestore } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';
import { getStorage } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-storage.js';

const app = initializeApp(config);
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

const authListeners = new Set();
let resolved = false;
let resolveAuthReady;
const authReadyPromise = new Promise((resolve) => {
  resolveAuthReady = resolve;
});

firebaseOnAuthStateChanged(auth, (user) => {
  authListeners.forEach((callback) => {
    try {
      callback(user);
    } catch (error) {
      console.error('Auth listener error', error);
    }
  });
  if (!resolved) {
    resolved = true;
    resolveAuthReady(user);
  }
});

export { app, auth, db, storage };

export function onAuthState(callback) {
  authListeners.add(callback);
  return () => authListeners.delete(callback);
}

export async function waitForAuthReady() {
  if (auth.currentUser !== null) {
    return auth.currentUser;
  }
  if (resolved) {
    return null;
  }
  return authReadyPromise;
}
