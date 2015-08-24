<?php

define("MYSQL_HOST", getenv('DOMAIN_IP'));
define("MYSQL_PORT", 3307);
define("MYSQL_DATABASE", getenv("MYSQL_ENV_MYSQL_DATABASE"));
define("MYSQL_USER", getenv("MYSQL_ENV_MYSQL_USER"));
define("MYSQL_PASSWORD", getenv("MYSQL_ENV_MYSQL_PASSWORD"));
