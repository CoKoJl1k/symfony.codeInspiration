
Attention ports used:
3306  - db
80    - webserver 
9000  - app

/*  Run  APP */
docker-compose build app
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php bin/console doctrine:migrations:migrate

http://localhost:80

/*for laravel */
docker-compose exec app php artisan key:generate

/*rebuild containers*/
docker-compose up -d --build --force-recreate

/*remove all*/
docker-compose down --volumes --rmi all
docker system prune -a --volumes -f
