# Table of Contents

- [Project Set Up](#requirements-before-initilizating-project)
- [Troubleshooting](#troubleshooting)
- [Updating Repository](#updating-repository)
- [Admin Account Credentials](#admin-account-credentials)

## Initial Project Set Up

### Requirements Before Initilizating Project

- [Laragon](https://laragon.org/download)
Underr laragon/bin/php/{php-ver}, duplicate the .ini file for development and rename it to php.ini. Then,
    - Enable TLS/SSL extension
    - Enable zip extension
    - Enable extension_dir for windows
    - Make sure the .ini file your PHP is using is the one you edited by running:
    ```
    php --ini
    ```
    and double-check the file path is the same as the one in Laragon. (laragon/bin/php/(php_ver))
- [TablePlus](https://tableplus.com/download/)
- [NodeJS](https://nodejs.org/en/download) using Windows Installer
- Import [VSCode Profile](https://drive.google.com/file/d/121iSh4aGY5EKfOgKekKpoUPEsWchIKe_/view?usp=drive_link) in VSCode

### Steps

#### 1. Initialize this repository in **VSCode**. (Required)

#### 2. After successful initialization, open terminal **(View->Terminal or ctrl+`)** and run the following:

```
composer install
npm install
cp .env.example .env
php artisan key:generate
```

#### 3. Open Laragon and enable Apache and MySQL.

#### 4. Double-check .env (NOT .env.example) file for database configuration. By default, it should have the following:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fairall
DB_USERNAME=root
DB_PASSWORD=
```

If yours are different, update the values to match your configurations. **Leave the DB_DATABASE as is.**

#### 5. Run the following in the terminal:

```
php artisan migrate --seed
```

This creates all the necessary tables in the database and inserts dummy data.

#### 6. Every time you need to run the web application, you need to type this command in the terminal:

```
npm run dev
```

Without this, the website may not render or function as intended.

#### (Optional) 7. If you need to test email and file upload features,

you need to configure your .env file for **mail** like this:

```
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME={your email here}
MAIL_PASSWORD{=your app password here. Make sure to wrap in double quotes if there are spaces in between}
MAIL_FROM_ADDRESS={your email here}
MAIL_FROM_NAME="${APP_NAME}"
```

for **file upload**:

##### A. Create B2 Bucket
1. Go to Backblaze B2 Console and log in to your account
2. Click "Buckets" in the left menu
3. Click "Create a Bucket"
4. Configure:
Bucket Name: fairall-documents (must be globally unique)  
Files in Bucket are: Public (so files can be downloaded/previewed)  
Click Create Bucket  
Copy the Bucket ID (you'll need this later)  
##### B. Create Application Key
1. Go to Account → App Keys (or Account Settings → Application Keys)
2. Click Create Application Key
3. Configure:
Key Name: Laravel FAIRALL  
Capabilities: Check all.  
Bucket: Select fairall-documents  
File Name Prefix: Leave empty  
Click Create Key  
Copy and save these values (you won't see them again):  
Application Key ID  
Application Key (the secret)  

*Also note the S3 Endpoint URL from the bucket details page (it looks like: https://s3.us-west-004.backblazeb2.com or something similar based on your region)*

##### C. Update your .env file to look something like this:
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_application_key_id
AWS_SECRET_ACCESS_KEY=your_application_key_secret
AWS_DEFAULT_REGION=us-west-004
AWS_BUCKET=fairall-documents
AWS_URL=https://your-bucket-name.s3.your-region.backblazeb2.com
AWS_ENDPOINT=https://s3.your-region.backblazeb2.com
```

## Troubleshooting

If at any point you encounter an error of _"...cannot be loaded because running scripts is disabled on this system..."_

Run this in the terminal using administrator mode:

```
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
```

and retry whatever command you were doing before.

For other errors you encounter, double-check if you wrote the right command.

## Updating Repository

Every time you update your repository from the **main** branch, **always run** this in the terminal (VSCode):

```
composer install
npm install
php artisan migrate:fresh --seed
```

This command automatically **drops all the existing tables** in your current database and **creates new tables** while populating it with dummy data. This allows us to have the **same database structure** while also having some data to work with. Additionally, it also installs all new dependencies, if any.

## Admin Account Credentials

email: admin@example.com  
password: Password123!

If you want to test the system using other roles, take a look at the users tab when you're signed in as the administrator for other account details. All account password is Password123!
