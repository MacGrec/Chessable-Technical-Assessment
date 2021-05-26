# How to run

- **up containers:** docker-compose up -d --build
- **Build dataBase:**  docker-compose exec app php bin/console doctrine:migrations:migrate
- You can see the database scheme at database-scheme.png

# Launch tests
- **Unit Tests:** docker-compose exec app php .vendor/bin/phpunit tests/Unit
- **Functional Tests:** docker-compose exec app php .vendor/bin/phpunit tests/Functional

# Postman
Import using postman to use the api:
- **Branches:** Branches.postman_collection.json
- **Customers:** Customers.postman_collection.json
- **Reports** Reports.postman_collection.json

# Time invested
- I think that invested around 20 hours to finish it.

# How improve it
- **Add security:** Install the security bundle of symfony https://symfony.com/doc/current/security.html to use the api
- **More complex database:** Add, for example, a coin table related with the balance table, table country with specific kind of taxes...
- **Balance between coins:** make the change between different coins with you move money a balance with different coins
- **External connections:** Check the change between different coins using a third party 
- **Chance to ask money:** A customer can to ask for money to the bank generating a debt and a monthly payment 

# Notes
- I didn't use the ORM to access to the Database. All the querys are raws.
- I didn't all the test, I did a couple of them to show you how I do.
    This is not a full set of test, trustworthy to upload to Live. 
    You must to check all the Endpoints (every posible response) and Services (every possible differece at the business logic)