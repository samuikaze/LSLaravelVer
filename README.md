# 洛嬉遊戲 Laravel ver.
這個專案原為在勞動部發展署雲嘉南分署數位設計班第 108-1 期上課期間所製作的 PHP 專題作品，為了驗證自學 Laravel 框架的成果，因而將此作品修改為 Laravel Framework 版，如果要檢視原始版本可以[按此檢視](https://github.com/samuikaze/IndependentStudyfForPHP)。

# 開發環境
- XAMPP Portable 3.2.3：
    - PHP 7.3.3
    - MySQL 10.1.38-MariaDB
    - phpMyAdmin 4.8.5
- ngrok （測試金流回傳資料用）
- composer
- Laravel Framework 6.0.3

# 在本地機器檢視專案
如要在本地機器上檢視專案請先執行下列動作：
1. 先以 composer 安裝 Laravel Framework 6.0.3 並建立一個新專案資料夾。
2. `git clone` 到剛剛建立的專案資料夾內。
3. 將 `.env.example` 重新命名為 `.env` 並修改網站資料與資料庫相關設定。
4. 在專案根目錄執行指令 `php artisan migrate:refresh --seed` 將資料庫及資料建立好。
5. 將以下資料夾權限設定為 `755` 或 `777`：
    - public/images/bbs/noard
    - public/images/carousel
    - public/images/goods
    - public/images/products
    - public/images/userAvator
- 金流設定皆為測試環境的設定。

# 線上展示
* 線上展示可以[按此瀏覽](https://sksklvl10801.000webhostapp.com/)。
