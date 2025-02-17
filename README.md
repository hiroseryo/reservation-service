# Rese(飲食店予約サービス)

![image](/images/Rese.png)

## 作成した目的

Laravel に搭載された色々な機能に触れてどのようなパッケージが存在するのかを確かめ、今後のプロジェクトごとに臨機応変に対応できるために作成しました。

## テーブル設計

![image](/images/first.png)
![image](/images/second.png)
![image](/images/third.png)

## 機能一覧

![image](/images/function.png)

## ER 図

![image](/images/ER.png)

## 使用技術(実行環境)

-   Mysql8.0.36
-   PHP8.3.14
-   Laravel11.35.1

## 環境構築

### Docker ビルド

1. `git@github.com:hiroseryo/reservation-service.git`

2. DockerDesktop 　アプリの立ち上げ

3. `docker compose up -d --build`

### Laravel 環境構築

1. `docker compose exec app bash`

2. `cd laravel-project`

3. `composer install`

4. .env に以下の環境変数を追加

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. アプリケーションの作成

```
php artisan key:generate
```

6. マイグレーションとシーディングの実行

```
php artisan migrate:fresh --seed
```

## メール認証実装

Mailtrap を使用しての実装です。ログイン->Email Testing->SMTP の画面下の PHP/Laravel 9+ を選んで下さい。  
そして発行された PASSWORD と USERNAME をクリックして.env に貼り付けて下さい。

今回管理者での操作がありますので`MAIL_FROM_ADDRESS=admin@example.com`  
こちらを使用します。

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=yourusername
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 管理者の情報

`database/seeders/AdminUserSeeder.php` に情報が載っています。

## Laravel Task Scheduling 実装

今回 cron を使用してリマインドの実装を行いましたので、cron での実装アプローチを行いますが、各々の環境に適した手順で実装して下さい。

1. `crontab -e`

2. `* * * * * cd /path/to/your/laravel/project && /usr/local/bin/docker compose exec -T app php /var/www/laravel-project/artisan schedule:run > /dev/null 2>&1`

-   補足  
    パスの確認`pwd`  
    pwd で出力されたコマンドを cd /ここに貼り付けて下さい。

3. 保存して、`crontab -l`で確認して下さい。

4. ScheduleServiceProviders.php ファイルの時間を予約を行った数分後に設定して下さい。

5. 最後に Mailtrap でメールの確認して下さい。

## Stripe 機能

Stripe 公式のサイトでログイン->開発者->API キーの画面に公開可能キーとシークレットキーをコピーして.env に貼り付けて下さい。

```
STRIPE_KEY=public key
STRIPE_SECRET=secret key
```

## URL

-   開発環境： http://localhost/8000
-   phpMyAdmin : http://localhost:8080/

# 更新と追加(飲食店予約サービス)

## ホーム画面

![image](/images/update.png)

## ER 図

![image](/images/updateER.png)

### 今回 3 つの機能の実装を行いました

口コミ機能の変更　ソート機能　 csv インポート機能

いくつか注意点がありますので共有します。

1. 今回レビューのテーブルに画像のカラムを追加しました。
   それと店舗来店予定日時が終了した際に、口コミの投稿が可能な仕様にいたしました。現在予約できるのは翌日以降となっていますので、ReservationController 内で利用している RequestForm を外す、または投稿可能日時の判定を変更するなどして、仕様に沿った調整をお願いいたします。

2. インポート機能のファイルは 2 つ用意しました。laravel-project/shops-error.csv はインポートする際に全体のエラーメッセージや成功したレコードのメッセージを拝見できます。laravel-project/shops.csv は全て正常に機能します。

    `注意点`
  
    - 前提として/public ディレクトリに画像を保存するため必ずlaravel-projectで`php artisan storage:link`をコマンド入力してください。

    - 一番上の行は必ずそれぞれのカラムを書いて下さい。shops.csv を参考にしてインポートしてください。

    - 管理者のみの実装ですので`database/seeders/AdminUserSeeder.php`に情報が記載されているのでご確認ください。
