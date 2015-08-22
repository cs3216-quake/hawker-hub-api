
# Hawker Hub (App)

# Container Setup
The stack contains `N + 1 + 1` docker containers where N is the number of serving APIs.
It comprises

  1. An NGINX container serving the static files, with all requests to `/api/` reverse proxied to one of N Apache+PHP containers. 
  2. N Apache+PHP containers linked to the mySQL container
  3. A MySQL container.


# Prerequisites

1. PHP (for installing Composer, OSX has it out of the box,
  for other OS you will need to install)
2. Docker with docker-compose run the app

# Preparation

Download and build the front end repository

```
$ cd ..
$ git clone https://github.com/cs3216-quake/hawker-hub
$ cd hawker-hub
$ gulp
```
Make sure that the front end repository is located on the same directory
as the server, like this.

```
/hawker-hub
/hawker-hub-api
```
Install Composer and PHP library dependencies.

```
$ curl -sS https://getcomposer.org/installer | php
$ ./composer.phar install
```

Make sure you have a working `docker-compose` command.

```
$ docker-compose
```

# Running the App

  -  Development
    
    `$ docker-compose up`

  -  Run all API Unit Tests
    
    `$ docker-compose -f testing.yml up`

  -  Production
    
    `$ docker-compose -f production.yml up`

The site is located on `http://[DOCKER_HOST_IP]/`

Test the API using `curl http://[DOCKER_HOST_IP]/api/v1`.
You should get a JSON response with `status: "Running"`

# License

The MIT License (MIT)

Copyright (c) 2015 cs3216-quake

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
