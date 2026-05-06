# F1 Management API

[![CI](https://github.com/jvspeed74/PHP-RESTful-API/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/jvspeed74/PHP-RESTful-API/actions/workflows/continuous-integration.yml)
[![PHP](https://img.shields.io/badge/php-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-passing-blue)](https://github.com/jvspeed74/PHP-RESTful-API/actions)
[![Docker](https://img.shields.io/badge/docker-ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/github/license/jvspeed74/PHP-RESTful-API)](https://github.com/jvspeed74/PHP-RESTful-API/blob/main/LICENSE)

A RESTful API for Formula 1 data built with PHP, Slim 4, and Eloquent ORM. 

Developed as a team project at Indiana University.

Frontend design by [Evan Deal](https://github.com/evandeal).

**[Live API Docs (Swagger UI)](https://jvspeed74.github.io/PHP-RESTful-API/)**

---
![SPA Home](docs/assets/Screenshot%202026-05-05%20204423.png)

---

![SPA Drivers](docs/assets/Screenshot%202026-05-05%20at%2020-46-21%20F1%20Center.png)

---

## Getting Started

```bash
docker-compose up
```

The API will be available at `http://localhost:8080`.

---

## Stack

| Layer           | Technology                    |
|-----------------|-------------------------------|
| Framework       | Slim 4 + PHP-DI               |
| ORM             | Eloquent                      |
| Auth            | Basic Auth, Bearer Token, JWT |
| Database        | MariaDB                       |
| Tests           | PestPHP (unit) + Newman (API) |
| Static Analysis | PHPStan                       |
| Container       | Docker                        |

---

## CI Pipeline

Every pull request runs PHPStan static analysis, PHP CS Fixer (PER-CS2.0 standard), PestPHP unit tests, and Newman integration tests against a live MariaDB service container, all via GitHub Actions.
