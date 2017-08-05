# Castle

Castle is a platform for storing project information, credentials, and documentation for client and internal projects, as well as a place for general discussion within the company.

## Setup

### Dependencies

- PHP >= 5.5.9
  - mcrypt
  - sqlite
- Composer
- npm

### Setup

    git clone
    composer install
    echo > database/database.sqlite
    php artisan migrate --seed   # see also: seeding
    php artisan app:superuser --name="" --email=""

Before opening the site, compile its assets:

    npm install
    ./node_modules/.bin/gulp

(You can run just `gulp` if you have it installed globally. To install it globally, run `sudo npm install -g gulp`.)

Finally, you can simply:

    php artisan serve

...and log in to <http://localhost:8000>, and you should be up and running.

To have Gulp automatically recompile scripts, assets, and stylesheets while you're making changes locally, you can run:

    ./node_modules/.bin/gulp watch

#### Seeding

The seeder takes the current app environment (`APP_ENV`) into account when deciding what to seed. It will seed every table it can if you're developing Castle locally; otherwise it will just seed what's necessary.

Set `APP_ENV` to 'local' or 'dev' to have Castle seed every table with random fake data. Setting it to 'live' or 'production' will have it seed only permissions, discussion statuses, and resource types.

### Tagging

You should tag every merge to `live` with a proper version tag, following [Semantic Versioning](http://semver.org) standards.

## Artisan commands

### `app:superuser`

Running `php artisan app:superuser` will (try to) create a new user account with:

- name: **Administrator**
- email: **root@localhost**
- password: entered at console

The created user will also be given *all* available permissions (but not new ones if they are created later).

It will exit with an error if there is already a user with the specified email address.
