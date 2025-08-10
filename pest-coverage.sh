#!/bin/bash

echo "Running all tests with coverage..."
docker run --rm -v $(pwd):/var/www/html laravel-test-coverage ./vendor/bin/pest --coverage --coverage-html=coverage
