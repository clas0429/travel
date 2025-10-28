# Guide Magnets QR Portal (MySQL Edition)

Guide Magnets 現在完全以 PHP 與 MySQL 為核心，提供旅遊地點、日誌、相片與影音素材的瀏覽與維護功能。專案內建管理後台，可直接上傳封面、相片與影音檔案，並把資料存放於 MySQL 資料庫中，適合部署在無需 Firebase 的內網或獨立伺服器環境。

## 功能總覽

- **前台導覽**：呈現地點封面、亮點摘要、旅遊日誌、相片集與影音花絮。
- **後台管理**：透過瀏覽器即可新增、更新與刪除地點、亮點、日誌、相片與影音內容。
- **檔案上傳**：支援上傳圖片與影片，檔案會存放於專案 `uploads/` 目錄，資料表記錄檔案路徑與 MIME 類型。
- **MySQL 儲存**：所有內容、排序與登入資料皆來自 MySQL，內建示範資料與預設管理員帳號。

## 系統需求

- PHP 8.0 以上（建議 8.1+）
- MySQL 5.7 / 8.0 或相容資料庫（MariaDB 10.4+）
- 可執行 `php -S` 或 Apache / Nginx 等支援 PHP 的伺服器

## 安裝步驟

1. 建立資料庫並匯入 Schema：
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lioho CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p lioho < database/schema.sql
   ```
2. 調整 `config/database.php` 內的連線資訊（資料庫、帳號與密碼）。
3. 匯入示範資料並建立預設管理員：
   ```bash
   php database/seed.php
   ```
   會建立與 `config/local-data.php` 相同的示範地點，同時新增管理員帳號 `admin` / `admin1234`（建議登入後立即更改密碼）。
4. 啟動開發伺服器：
   ```bash
   php -S 127.0.0.1:8000
   ```
5. 瀏覽 `http://127.0.0.1:8000/index.php` 觀看前台；管理後台位於 `http://127.0.0.1:8000/admin/login.php`。

## 管理後台說明

- 登入後即可看到地點列表，選擇任一地點即可新增亮點、旅遊日誌、相片或影音內容。
- 上傳檔案時，系統會自動建立 `uploads/covers/`、`uploads/photos/`、`uploads/videos/` 等資料夾並儲存檔案。
- 若已備妥外部檔案位置，也可直接輸入檔案路徑或網址，無須重新上傳。
- 影音內容支援三種類型：
  - **SVG 動畫**：貼上 SVG 原始碼即可離線播放。
  - **上傳影片檔案**：接受 MP4 / WebM / OGG，並可自訂海報圖片與 MIME 類型。
  - **外部嵌入**：填入 YouTube、Vimeo 等嵌入連結，前台會建立 iframe 播放。
- 刪除地點時，旗下亮點、日誌、相片與影音會一併移除。

## 目錄結構重點

```
assets/           # 前端樣式與圖片資源
config/           # 靜態示範資料與資料庫設定
includes/         # 共用函式、資料庫與後台工具
admin/            # 後台登入與管理介面
uploads/          # 使用者上傳的圖片與影片（已加入 .gitignore）
database/schema.sql  # MySQL 資料表定義
database/seed.php    # 匯入示範資料與預設管理員
```

## 自訂與擴充

- `config/local-data.php` 仍可作為匯入示範資料的來源，執行 `php database/seed.php` 會覆蓋資料表並重新寫入。
- 如需額外欄位，可修改 `database/schema.sql` 後再調整 `includes/functions.php` 內的 CRUD 函式與後台表單。
- 若在生產環境使用，請：
  - 變更預設管理員密碼並視需求新增其他帳號。
  - 設定 Web 伺服器對 `uploads/` 目錄的寫入權限與存取限制。
  - 考量定期備份資料庫與上傳檔案。

## 授權

原始碼依照專案所屬授權條款使用。若需商業或客製化支援，歡迎另行洽談。
