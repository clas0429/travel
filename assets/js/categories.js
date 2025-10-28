export const CATEGORY_PAGES = {
  diary: 'diary.php',
  photos: 'photos.php',
  videos: 'videos.php',
};

export const CATEGORY_LABELS = {
  diary: '旅遊日誌',
  photos: '旅途相片',
  videos: '影音花絮',
};

export function resolveCategoryPage(category) {
  return CATEGORY_PAGES[category] || '';
}

export function resolveCategoryLabel(category) {
  return CATEGORY_LABELS[category] || category;
}
