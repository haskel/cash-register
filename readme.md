
## Development
There is a prepared Docker environment for you.
Configuration is in `docker-compose.yml`.

All useful commands are in the `Makefile` placed into root of the project.

#### How to start the server.
```bash
$ make up env=dev http_port=8001
```

#### How to run console
```bash
$ make php-console-bash
```
or zsh if you like
```bash
$ make php-console-zsh
```

#### How to down containers
```bash
$ make down env=dev
```

### Generate keys for JWT
#### Run the command 
```bash
$ bin/console lexik:jwt:generate-keypair
```

### Run migrations
```bash
$ make migrate
```

### Users
There are two predefined users: `admin`, `user`. 
Do not hesitate to add more users if you need more to the file `config/packages/security.yaml` section `providers.backend_users.memory.users`.
To hash password use `bin/console security:hash-password` command.

### Create new cash register
```bin/console app:cash-register:create serial123123 user --name=7-Eleven```
The first argument is a serial number of device.
The second argument is a user of the device. The username from the `Users` section above. (Yes, not API tokens, because the implementation is a bit longer)
The third argument is an optional name.

### HTTP requests
There is a `doc/requests.http` file which contains all requests you need.
File `doc/http-client.env.json` contains environment variables. 
Open it in PhpStorm (https://www.jetbrains.com/help/phpstorm/http-client-in-product-code-editor.html).

Use Auth section of requests file to obtain admin and cash register tokens. 
Then put these tokens into `http-client.env.json` file. Try send requests.

### Add products
Add products using particular routes from requests file.
Notice that VAT class is a number. Get it from `App\Enum\VatClass`. 
The amount of specific VAT class is in `app.vat_class` parameter in `config/services.yaml` file.

### Play with receipts
Create a receipt then add or remove some products by barcodes.
Use requests from `RECEIPT` section of `requests.http` file.
Try to finish.

### Get a report
Use request from `REPORT` section of `requests.http` file.
It summarizes the receipts finished in the current hour.

### Tests
#### To run tests use the following command:
```
make test
```

### Static analysis
#### Run psalm
```
make psalm
```
#### or inside the container
```
vendor/bin/psalm
```

#### Run phpstan
```
make phpstan
```
#### or inside the container
```
vendor/bin/pstan
```