# Rest Api Symfony Application

Requirements
------------

* PHP 7.1 or higher
* Mysql 5.7
* Mac or Linux OS

Installation
------------

Execute this command to install the project:

```bash
$ git clone https://github.com/ViraxDev/super_pokedex.git
$ cd super_pokedex
$ yes "" | make initialize 
```

These commands will : 
* Install dependencies
* Load Pokemon & User Fixtures
* Start Symfony Web Server.

You may need to change Mysql credentials in `.env` file in section `DATABASE_URL`.

To authenticate to the API, you will need to send the following information to the Header : 
* key: **X-AUTH-TOKEN**
* value: **superTokenDeTest**

Finally, execute this command which lists all the configured API routes : 

```bash
$ php bin/console debug:router
```
