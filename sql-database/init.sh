#!/bin/bash

cd /setup

# Run the SQL script
sleep 20
php sql-install.php

/run.sh
