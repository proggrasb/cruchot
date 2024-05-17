# General
    Purpose of application is getting currency rates from CBR

- Based on Alpine Linux.
- Symfony 6.2
- PostgreSQL
- Doctrine
- Redis
- RabbitMQ

## Build application

1) Build containers: `docker-compose up -d --build`
2) For the first time setup:
    * go into container: `docker exec -it cruchot-php bash`
    * execute setup script: `./setup.sh`
3) When you have finished performing commands check the application available on the URL address:
    * `http://localhost:9091`

## Using

To get currency rate:
```
GET http://localhots:9091/currency/current?currency=USD[&baseCurrency=RUR]
where baseCurrency is optional, RUR by default.
```

To get currency rates for last 180 days execute script: `./request_rates.sh`.
All rates will be saved into database.

Credentials for database:

    type: PostgreSQL
    host: localhost:4020
    user: cruchot
    password: cruchot
    database: cruchot
