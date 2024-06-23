# Sinai Manuscripts Data Portal

As the centralized data store for the Sinai Manuscripts Digital Library website, the Portal will house records for all Sinai-related data and provide open-access data for research.

## How to Set Up

### Prerequisites
- Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Run the Application Locally
- Clone this repository: `git clone git@github.com:UCLALibrary/sinai_portal.git`
- Change into the project directory: `cd sinai_portal`
- Copy `.env.example` to `.env` and update it with application-specific configuration variables.
- Run the following command to install Laravel Sail: 
  ```
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
  ```
- Start the application using Laravel Sail: `sail up`
  - ***Note:** If you do not have one already, setup an alias in your shell configuration file:* `alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'`. *See [Configuring A Shell Alias](https://laravel.com/docs/11.x/sail#configuring-a-shell-alias).*

  - ***Note:** If you encounter database role errors (e.g. FATAL:  role "sinai" does not exist), run* `sail down --rmi all -v` *to remove all images and volumes followed by* `sail up`.
- Run the database migrations using Laravel Sail: `sail artisan migrate`
  - ***Note:** If you encounter database connection errors or password authentication errors (e.g. FATAL:  password authentication failed for user "sail"), run* `sail down --rmi all -v` *to remove all images and volumes followed by* `sail up` *before running the database migrations.*
- Install dependencies with Composer using Laravel Sail: `sail composer install`
- Install frontend dependencies using Laravel Sail: `sail npm install`
- Go to http://localhost:8005 and verify that the application is running.

---

## Local Development

### Building Javascript and CSS for Local Development
- Watch and live reload local changes to JavaScript and CSS assets: `sail npm run dev`

### Building Javascript and CSS Files for Production
- Build JavaScript and CSS asset bundle for production: `sail npm run build`
  - ***Note:** This is not necessary for development but might be useful if you want to inspect the generated/compiled JavaScript and CSS output files. You will find them under* `public/build/assets`*. The* `public/build` *folder is git ignored, therefore the generated files do not show up in git when they are rebuilt.*

### Running npm Commands
- `sail npm <command>`
  - e.g. `sail npm install`

### Running artisan Commands
- `sail artisan <command>`
  - e.g. `sail artisan migrate`

### Running Composer Commands
- `sail composer <command>`
  - e.g. `sail composer install`

### Mailpit Dashboard
- To test sending and receiving mails from the application locally, go to http://localhost:8028.

### Updates to docker-compose.yml
- For your changes in `docker-compose.yml` to take effect, run `sail up --build`.
