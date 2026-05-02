# 🏭 Industrial IoT Platform

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![Node.js](https://img.shields.io/badge/Node.js-18+-green)
![MQTT](https://img.shields.io/badge/MQTT-Mosquitto-blue)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue)
![Status](https://img.shields.io/badge/status-em%20desenvolvimento-yellow)

Sistema de monitoramento industrial com simulação de máquina, mensageria
via MQTT, backend em Laravel e persistência em banco de dados.

------------------------------------------------------------------------

## 🚀 Visão Geral

Este projeto simula um ambiente industrial real com:

-   📡 Comunicação via MQTT
-   ⚙️ Simulador de máquina (Node.js)
-   🧠 Backend Laravel
-   💾 Persistência em MySQL
-   📊 Base para OEE (Overall Equipment Effectiveness)

------------------------------------------------------------------------

## 🧱 Arquitetura

    [ Node Simulator ]
            ↓ MQTT
    [ Eclipse Mosquitto ]
            ↓
    [ Laravel Consumer ]
            ↓
    [ MySQL Database ]
            ↓
    [ API / Dashboard ]

------------------------------------------------------------------------

## 📦 Tecnologias

-   PHP (Laravel)
-   Node.js
-   MQTT (Mosquitto)
-   MySQL
-   Docker
-   Vue.js (opcional)

------------------------------------------------------------------------

## 🐳 Docker

### Subir MQTT

``` bash
docker run -it -p 1883:1883 eclipse-mosquitto
```

### Subir MySQL

``` bash
docker run -d \  --name mysql-industrial \  -e MYSQL_ROOT_PASSWORD=root \  -e MYSQL_DATABASE=industrial \  -p 3306:3306 \  mysql:5.7
```

------------------------------------------------------------------------

## ⚙️ Backend Laravel

``` bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

------------------------------------------------------------------------

## 📡 MQTT Consumer

``` bash
php artisan mqtt:consume
php artisan serve
php artisan reverb:start
node index.js start
```

Tópico utilizado:

    factory/machine/events

------------------------------------------------------------------------

## 🤖 Simulador (Node.js)

``` bash
npm install mqtt
node index.js
```

------------------------------------------------------------------------

## 🌐 API

-   GET `/api/machine-events`
-   GET `/api/machine-events/latest`
-   GET `/api/machine-events/oee`

------------------------------------------------------------------------

## 🧪 Testes

``` sql
SELECT * FROM machine_events ORDER BY id DESC;
```

------------------------------------------------------------------------

## ⚠️ Problemas Comuns

**MQTT não conecta** - Verifique porta 1883 - Verifique container
Mosquitto

**Banco não conecta**

``` bash
php artisan config:clear
```

**Erro Node/Vite** - Atualizar Node.js para versão 20+

------------------------------------------------------------------------

## 📊 Roadmap

-   Dashboard em tempo real
-   WebSockets
-   Alertas industriais
-   Manutenção preditiva

------------------------------------------------------------------------

## 💼 Valor Profissional

-   IIoT
-   MQTT
-   Event-driven architecture
-   OEE

------------------------------------------------------------------------

## 👨‍💻 Autor

Projeto para estudo e evolução em automação industrial + software.

------------------------------------------------------------------------

## 🚀 Conclusão

Sistema completo simulando indústria real com mensageria, processamento
e análise de eficiência.
