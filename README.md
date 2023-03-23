# Hero Local Docker Setup

> Make sure you have docker installed in your local machine.

1. Clone the repository and go to the root folder of the project.

> Configure environment variables of docker and laravel env

2. Go to root folder, then create `.env` file and copy `.env.docker` content to `.env` file, this will allow to change PORTS based on local ports availability.

3. Go to `src` folder, then create `.env` file and copy `.env.example` content to `.env` file.

> Go to the root folder of the project

4. On terminal run command `docker-compose up` to build and run the containers or `docker-compose up -d` to run the containers in the background.

> If the containers successfully build and already running:

5. Go to `src` folder, then run `composer install` and `npm install` in the terminal to install PHP modules and Javascript libraries.

6. Go to `src` folder, then run `npm run prepare` in the terminal to install husky for pre-commit operation.

7. Go to root folder, then run `docker-compose exec apache php artisan migrate:fresh --seed` in the terminal to migrate database tables and schema and seed tables dummy contents.
#
> ### Access project sites in the browser using these url's:

> ## NOTES:
> #### PORTS depends on what is declared in docker environment variables  

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`http://localhost:8080` - Project Site  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`http://localhost:8081` - phpMyAdmin Site  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`http://localhost:8025` - Mailhog Site


