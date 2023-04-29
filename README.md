### Commission calculator (test task)
## Requirements
- php 8.0+
- composer

_or just_
- docker

**Don't forget to add your ApiLayer API key to .env!**

_BTW you can use mine: 5tMiIDxfwW1WW3VViiAbskpp0dAREtET_
## How to run
# Docker-compose
```bash
cp .env.example .env
docker-compose up app
```
# PHP
```bash
cp .env.example .env
composer install
php app commission:calc input.txt
```