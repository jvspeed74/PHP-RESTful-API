# F1 Management API

[![CI](https://github.com/jvspeed74/PHP-RESTful-API/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/jvspeed74/PHP-RESTful-API/actions/workflows/continuous-integration.yml)
[![PHP](https://img.shields.io/badge/php-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-passing-blue)](https://github.com/jvspeed74/PHP-RESTful-API/actions)
[![Docker](https://img.shields.io/badge/docker-ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/github/license/jvspeed74/PHP-RESTful-API)](https://github.com/jvspeed74/PHP-RESTful-API/blob/main/LICENSE)

A RESTful API for Formula 1 data built with PHP, Slim 4, and Eloquent ORM. Features include authentication, pagination, testing, and Docker containerization.

Developed as a team project at Indiana University.

**[Live API Docs (Swagger UI) via Github Pages](https://jvspeed74.github.io/PHP-RESTful-API/)**

### Table of Contents

* [Gallery](#gallery)
* [Contributors](#contributors)
* [How to Run Locally](#how-to-run-locally)
* [Stack](#stack)
* [CI Pipeline](#ci-pipeline)

---

## Gallery

---

![SPA Home](docs/assets/Screenshot%202026-05-05%20204423.png)

> Main banner of the Single Page Application (SPA)

---

![SPA Home Cards](docs/assets/Screenshot%202026-05-09%20114444.png)

> Home page cards for Tracks and Drivers, including baseline information and links to their respective pages.

---

![SPA Drivers Pagination](docs/assets/Drivers%20Pagination.gif)

> Demonstrating pagination functionality on the Drivers page, allowing users to navigate through multiple pages of driver data.

---

![SPA Drivers Search](docs/assets/Drivers%20Search.gif)

> Demonstrating the search functionality on the Drivers page, enabling users to filter drivers by name in real-time.

---

## Contributors

- [Jalen Vaughn](https://github.com/jvspeed74) — Lead developer; API architecture, authentication, Docker, CI/CD, and
  documentation
- [Evan Deal](https://github.com/evandeal) — Frontend SPA design and implementation.
- [Matt Jobe](https://github.com/mjobe2) — Car and Driver models, repositories, and controllers

Developed as a team project at Indiana University.

---

## How to Run Locally

### Prerequisites

- Docker
- Web browser

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

Every pull request runs:

- PHPStan static analysis
- PHP CS Fixer (PER-CS2.0 standard),
- PestPHP unit tests,
- Newman (Postman) integration tests against a live MariaDB service container,

Utilizing GitHub Actions for automation.

---

