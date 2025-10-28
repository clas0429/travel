# Guide Magnets QR Portal

Guide Magnets 是一個以 PHP 為核心、結合 Firebase 生態系的旅遊內容平台。每個實體磁鐵都對應一組 QR Code，掃描後會導向特定地點與分類（日誌、相片或影音），僅限登入會員檢視。專案同時提供基本的管理後台以維護地點與素材。

## 環境需求
- PHP 8.0 以上（建議 8.1+）。
- 可執行 `php -S 127.0.0.1:8000 -t public` 的本地環境，或 Apache / Nginx 等支援 PHP 的伺服器。
- Firebase 專案並啟用 Authentication、Cloud Firestore、Cloud Storage。

### 本地快速啟動
```bash
cp config/env.sample.php config/env.php
# 編輯 env.php，填入 Firebase Web App 設定
php -S 127.0.0.1:8000 -t public
```
瀏覽 `http://127.0.0.1:8000/index.php`，即可測試首頁與登入流程。若要模擬 QR 驗證，可帶入 `loc`、`cat`、`k` 參數。

### 本地 Demo（免 Firebase 設定）
若想快速展示 UI 與內容結構，可改走示範資料模式：

1. 直接啟動同一個 PHP 內建伺服器：
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
2. 造訪 `http://127.0.0.1:8000/demo/index.php`。

Demo 版本的資料存放於 `config/demo-data.php`，可依需求自行新增地點、旅遊日誌、相片或影片資訊。

### 第二版：Local v2 全功能頁面
若需要在離線或未配置 Firebase 的環境展示完整體驗，可使用全新的 Local v2：

1. 啟動 PHP 伺服器：
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
2. 造訪 `http://127.0.0.1:8000/v2/index.php`。

Local v2 使用 `config/local-data.php` 內的靜態資料，並搭配 `assets/css/local-tailwind.css`、`assets/images/v2/` 等本地資源呈現旅遊日誌、相片與影音花絮。可依需求調整檔案內容以擴充地點與素材。

## Firebase 設定流程
1. **建立 Web App**：於 Firebase Console 新增 Web App，取得 `firebaseConfig` 物件。
2. **設定環境檔**：複製 `config/env.sample.php` 為 `config/env.php`，將上述 `firebaseConfig` 值依序填入。
3. **啟用登入方式**：在 Authentication 啟用 Email/Password，視需求可再擴充。
4. **Firestore & Storage 初始化**：先建立 Cloud Firestore（建議 Production 模式）及 Cloud Storage Bucket。

### 套用安全性規則
將 `security/firestore.rules` 與 `security/storage.rules` 套用至對應服務，可於 Console 編輯器貼上後發布，或使用 CLI：
```bash
firebase deploy --only firestore:rules,storage:rules --project YOUR_PROJECT_ID
```

## 建立假資料
1. 前往 Firestore → **集合** 建立 `locations`，新增文件 `TAIPEI101`：
   ```json
   {
     "name": "Taipei 101",
     "description": "台北 101 觀景台與週邊景點",
     "myMapsUrl": "https://www.google.com/maps/d/embed?mid=...",
     "coverImagePath": "locations/TAIPEI101/cover.jpg",
     "qrKeyHash": "<SHA-256 雜湊值>",
     "isPublished": true
   }
   ```
2. 在 `locations/TAIPEI101/diaries` 新增 `DAY1` 文件，填入 `title`、`content`、`isPublished` 等欄位。
3. 在 `locations/TAIPEI101/photos` 新增數張照片資料，欄位示例：
   ```json
   {
     "title": "日落景色",
     "imagePath": "locations/TAIPEI101/photos/day1-full.jpg",
     "thumbPath": "locations/TAIPEI101/photos/day1-thumb.jpg",
     "order": 1
   }
   ```
4. 在 `locations/TAIPEI101/videos` 建立：
   ```json
   {
     "type": "youtube",
     "youtubeId": "dQw4w9WgXcQ",
     "title": "亮點導覽",
     "order": 1
   }
   ```

### Storage 路徑建議
- 地點封面：`locations/{LOC}/cover.jpg`
- 相片：`locations/{LOC}/photos/{timestamp}-{filename}`（縮圖可與原檔並存）
- 影音 MP4：`locations/{LOC}/videos/{timestamp}-{filename}.mp4`
- GPX 或補充檔：`locations/{LOC}/routes/{name}.gpx`

## YouTube 設定
- 將播放清單 ID 或單支影片 ID 填入 `videos` 文件的 `youtubeId` 欄位。
- 若使用播放清單，可搭配前端判斷顯示嵌入式播放器。
- 影音資料也支援直接上傳 MP4，會存至 Firebase Storage，頁面讀取後取得下載 URL 播放。

## QR Token 產生流程
1. 產生隨機字串（建議 8~12 碼）。
2. 於終端機或瀏覽器 DevTools 計算 SHA-256 16 進位字串，例如 Node.js：
   ```bash
   node -e "const crypto=require('crypto');const raw='YOUR_TOKEN';console.log(crypto.createHash('sha256').update(raw).digest('hex'));"
   ```
3. 將結果寫入 Firestore `locations/{LOC}.qrKeyHash`，並於 QR Code 中帶入 `k=YOUR_TOKEN`。

## 後台與權限
- 新增管理路徑 `public/admin/`：`locations.php`、`diary-edit.php`、`photos-uploader.php`、`videos-edit.php`。
- 進入頁面時會先檢查登入，再讀取 `users/{uid}.role`，僅 `admin` 可操作，否則返回首頁。
- 權限設定可在 Firebase Console → Authentication → Users → 編輯使用者 → **Add custom claims**，填入：
  ```json
  { "role": "admin" }
  ```

## 生產部署建議
### Apache (.htaccess)
確保虛擬主機的 DocumentRoot 指向 `public/`，並在 `public/.htaccess`（若需要自訂）加入：
```
RewriteEngine On
RewriteBase /
RewriteRule ^$ index.php [L]
RewriteRule ^([^.]+)$ $1.php [L]
```

### Nginx 範例
```nginx
server {
    listen 80;
    server_name travel.example.com;

    root /var/www/guide-magnets/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```
啟用 HTTPS 與自訂網域時，記得更新 Firebase Authentication 授權網域及 Storage/Firestore CORS 設定。

## 常見問題
| 問題 | 排除方式 |
| --- | --- |
| 首頁白屏或無法載入 | 檢查 `config/env.php` 是否填入正確 Firebase 設定，開啟瀏覽器主控台查看錯誤。 |
| 出現 CORS 錯誤 | 於 Firebase Console → Storage → 規則/CORS 設定允許來源；Firestore 需確認網域在授權清單。 |
| Storage 403 權限錯誤 | 確認使用者已登入且 Storage 規則允許讀寫；開發時可暫時放寬規則測試。 |
| 時區不正確 | PHP 預設時區可在 `php.ini` 或程式中設定 `date_default_timezone_set('Asia/Taipei');`；Firestore Timestamp 需於前端轉換。 |
| QR 驗證失敗 | 比對 `qrKeyHash` 與實際 token 的 SHA-256 是否相符，並確認 URL 中的 `loc`、`cat`、`k` 參數。 |

## 相關頁面與模組
- 前台：`public/index.php`、`login.php`、`diary.php`、`photos.php`、`videos.php`
- JS 模組：`assets/js/firebase-init.js`、`auth.js`、`qr.js`、`api.*.js`
- 後台：`public/admin/locations.php`、`diary-edit.php`、`photos-uploader.php`、`videos-edit.php`

完成上述設定後，即可建立專屬的旅遊磁鐵體驗，並透過 Firebase 後端同步管理內容。
