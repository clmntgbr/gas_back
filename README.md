# Gas BackEnd Project

this is the backend part of the Gas Project, who collect all price from gas stations in France

## Getting Started

1. Clone https://github.com/clmntgbr/setup and run `make build start` for the setup project
2. Clone this repo
3. Run `cp .env.dist .env` and change some variable
4. Run `make build start` to start docker
5. Run `make init` to initialize the project
6. You can run `make help` to see all commands available

## Overview

Open `https://traefik.traefik.me/dashboard/#/` in your browser for traefik dashboard

Open `https://maildev.traefik.me` in your browser for maildev

Open `https://rabbitmq.traefik.me` in your browser for rabbitmq

Open `https://back.traefik.me/admin` in your browser for the admin & api part

## Commands

`make price-download` to download latest JSON gas price  
`make price-update` to update gas price  
`make status-update` to update gas station based on the status  
`make status-anomaly` to check placeId anomaly  
`make consume` to consume messages from commands  

## Skills

* PHP 8.2
* Nginx 1.20
* RabbitMQ 3-management
* MariaDB 10.4.19
* MailDev
* Traefik latest
* Symfony 6.2
* Docker

**Enjoy!**
