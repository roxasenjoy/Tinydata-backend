# TinyData's API

v1.2.1
## Get Started

- Pull the project
- Generate key pair and put them on /app/config/jwt
- update docker-compose.yml environement variables (take a look to DATABASE_URL and JWT_* variables)
- Run 
```
docker-compose build
```
- Then run
```
docker-compose up -d
```
- Connect to your container :
```
#Find your container name
docker ps

#Then run
docker exec -it DOCKERNAME bash
```
- Run
```
composer install
```
- Try to connect on localhost:8081 (or the port you decided to use) in your favorite web browser

## Manage database
Migration et exécution pour Tinycoaching 
( :warning: : penser à répercuter ces modifications sur le back de TinyCoaching)
```
php bin/console doctrine:migrations:diff --em=tinycoaching
php bin/console doctrine:migrations:migrate --em=tinycoaching
```
Migration et exécution pour Tinydata
```
php bin/console doctrine:migrations:diff --em=tinydata
php bin/console doctrine:migrations:migrate --em=tinydata
```
