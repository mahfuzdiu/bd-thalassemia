## Project and Task Listing Assignment

## Run Using Docker

1. Git pull project and go inside project-management-system directory
```angular2html
git pull git@github.com:mahfuzdiu/project-management-system.git
```
2. Setup SMTP (mailchimp preferred) for testing in .env.example
```angular2html
MAIL_USERNAME=mailtrap_username
MAIL_PASSWORD=mailtrap_password
```

3. Run below docker command. Wait a bit then visit ```http://localhost:8000/``` to check if the project is running
```angular2html
docker compose up -d
```

4. Run following command before testing email notification on ```tasks/{id}```
```angular2html
docker compose exec app php artisan queue:work
```


## Run Using Xamp

### 1. Install [Xamp 8.2](https://www.apachefriends.org/download.html) and run apache and mysql

### 2. Set php in environment file

### 3. Create database
```angular2html
create a database: pms
username: root
password:
```
4. Run the following commands
```angular2html
# git pull git@github.com:mahfuzdiu/project-management-system.git
# composer install
# php artisan key:generate
# php artisan migrate
# php artisan db:seed
# php artisan serve
```
### 9. go to ```http://localhost:8000```
### 10. To test email sending and job/queue I have used mailtrap tesing environment.

Update the .env with smtp config.
```angular2html
MAIL_USERNAME=mailtrap_username
MAIL_PASSWORD=mailtrap_password
```

RUN: ```php artisan queue:work``` from project directory

mail will be working upon sending request on ```tasks/{id}``` api  
