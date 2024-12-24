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

今回管理者を作成するので`MAIL_FROM_ADDRESS=admin@example.com`  
任意のメールアドレスでも構いませんが今回はこちらの設定で進めます。

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

## 管理者の作成

1. `docker compose exec app bash`

2. `cd laravel-project`

3. `php artisan tinker`

4. `use App\Models\User;`

5. `use Illuminate\Support\Facades\Hash;`

6. `$user = User::create(['name' => 'admin','email' => 'admin@example.com','password' => Hash::make('yourpassword'),
]);`

7. `use Spatie\Permission\Models\Role;`

8. `$user->assignRole('admin');`

9. `exit`

管理者として作成したアカウントでログインして下さい。

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
