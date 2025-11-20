### Ecommerce project (Run Using Docker)

##### 1. Git pull project and go inside project-management-system directory
```angular2html
git pull git@github.com:mahfuzdiu/bd-thalassemia.git
```
##### 2. Setup SMTP (mailchimp preferred) for testing in .env.example
```angular2html
MAIL_USERNAME=mailtrap_username
MAIL_PASSWORD=mailtrap_password
```


##### 3. Run below docker command. Wait a bit then visit ```http://localhost:8000/``` to check if the project is running
```angular2html
docker compose up -d
```

##### 4. Once project is up run below command to a terminal to import product data from csv
```
docker exec -it laravel-app php artisan app:import-products
```

##### 5. Auth flow

```
1. Register
2. Login (user stays loggedin for 1 hour)
3. Use /me api to check user
4. Hit /refresh api if token expires to get a new valid token
```

##### 6. Run following command before testing email notification 
```angular2html
docker compose exec app php artisan queue:work
```
```
orders/status/{order_id} -> Sends email to customer
orders/confirm/{order_id} -> Generates invoice of order
orders/confirm/{order_id} -> Upon order confirmation stock is deducted
orders/cancel/{order_id} -> Upon order cacellation product is restocked
/orders/status/{{order_id}} -> Checnges status from processing -> shipped -> delivered
```

##### 6. Elastic Search: Text Search

Products are inserted into elastic search during csv upload. Even though data in elastic search can be updated upon the change of there state though functionality have not beeen implemented for this project due to time constraint.

Api
```
products/search?description=Lightweight -> returns data based on query from elastic search
```

##### 7. Access Control

Role based and policy based access control has been implemented

##### 8. Added  couple of feature test and unit test  
