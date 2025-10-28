import { db } from './firebase-init.js';
import { ensureSignedInOrRedirect } from './auth.js';
import { doc, getDoc } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js';

export async function requireAdmin() {
  const user = await ensureSignedInOrRedirect();
  const snapshot = await getDoc(doc(db, 'users', user.uid));
  const data = snapshot.exists() ? snapshot.data() : null;
  if (!data || data.role !== 'admin') {
    alert('僅限管理員存取，將返回首頁。');
    window.location.replace('/index.php');
    throw new Error('Forbidden');
  }
  return { user, profile: data };
}
