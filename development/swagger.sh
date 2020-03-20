#!/bin/bash

../vendor/bin/openapi --bootstrap ./swagger-constants.php --output ../public/swagger/swagger.yaml ./swagger-v1.php ../app/Http/Controllers/
