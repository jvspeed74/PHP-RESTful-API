# F1 Management API

## Table of Contents

1. [Project Overview](#project-overview)
2. [API Documentation](#api-documentation)
3. [Setup & Running](#setup--running)
4. [Technologies Used](#technologies-used)
5. [Technical Highlights & Design Choices](#technical-highlights--design-choices)
6. [Code Quality & Standards](#code-quality--standards)
7. [Continuous Integration & Deployment (CI/CD)](#continuous-integration--deployment-cicd)
8. [Limitations](#limitations)

## Project Overview

This project is a RESTful API designed for managing Formula 1 related data, including teams, drivers, cars, events,
tracks, and more. It was developed as a **group school project** to showcase proficiency in modern PHP development
practices, API design, database
management, and automated testing. The API provides standard CRUD operations, along with features like searching and
pagination, secured by various authentication mechanisms.

## API Documentation

Interactive API documentation, generated from the OpenAPI specification, is available via Swagger UI deployed on GitHub
Pages: [F1 Management API Documentation](https://jvspeed74.github.io/PHP-RESTful-API/).

## Setup & Running

#### Note:

Ideally this project should be run in a Docker container, but due to time constraints, it was not fully implemented. May
be added in the future.

1. **Dependencies:** Install PHP dependencies using `composer install`.
2. **Database:**
    * Set up a MariaDB instance.
    * Import the schema from `f1_db.sql`.
    * Configure database connection details (typically via environment variables or a configuration file - not detailed
      in provided context).
3. **Running the Application:**
    * The application can be served using a local PHP development server (e.g., `php -S localhost:8000 -t public`).
4. **Running Tests:**
    * Unit tests: `vendor/bin/pest`
    * Integration tests: Requires a running application and database. Configure Newman with the appropriate environment
      variables and run the Postman collection.

## Technologies Used

* **Backend:** PHP 8.2
* **Framework/Libraries:**
    * PSR-7 compliant HTTP message interfaces (Slim)
    * Eloquent ORM (for database interaction)
    * Composer (for dependency management)
* **Database:** MariaDB (as configured in CI)
* **Testing:**
    * PestPHP & PHPUnit (for unit and feature testing)
    * Mockery (for creating test doubles)
    * Newman (for running Postman collections - integration/API tests)
* **Code Quality & Analysis:**
    * PHPStan (static analysis)
    * PHP CS Fixer (for PER-CS2.0 code style adherence)
    * Redocly CLI (for OpenAPI linting)
* **CI/CD:** GitHub Actions

## Technical Highlights & Design Choices

This project demonstrates a range of technical skills and design considerations:

* **RESTful API Design:** Adherence to REST principles for creating a clean, predictable, and easy-to-use API.
* **Layered Architecture:**
    * **Controllers (`src/Controllers`, `src/Contracts/AbstractController.php`):** Handle HTTP requests, delegate
      business logic, and formulate HTTP responses. Utilizes PSR-7 interfaces for request/response handling.
    * **Repositories (`src/Repositories`, `src/Contracts/AbstractRepository.php`):** Abstract data access logic,
      providing a clean separation between controllers and data persistence.
    * **Models (`src/Models`):** Leverage the Eloquent ORM for database interaction, defining relationships, fillable
      attributes, attribute casting, and model-level data validation (e.g., `Driver::$career_points` validation).
* **PSR-7 Compliance:** Utilizes PSR-7 HTTP message interfaces for robust and interoperable request and response
  handling.
* **Eloquent ORM:** Employs Eloquent for efficient and expressive database interactions, including relationships (e.g.,
  `Driver` to `Team`, `Car` to `Team`), attribute casting, and mass assignment protection.
* **Multiple Authentication Strategies (`src/Middleware`):**
    * Implemented a flexible authentication system supporting Basic Auth, Bearer Token Auth, and JWT Auth.
    * Includes a custom header authentication (`F1-API-Authorization`) example.
    * A central `AuthMiddleware` delegates to specific authentication type handlers.
* **Dependency Injection:** Constructor injection is used throughout the application (e.g., Repositories injected into
  Controllers, Authenticators into Middleware) promoting loose coupling and testability.
* **Data Validation:**
    * Input validation for request bodies (e.g., checking for valid JSON in `AbstractController`).
    * Model-level validation (e.g., `Driver` model ensures `career_points` are non-negative).
    * Query parameter validation in controllers (e.g., `TrackController::getAllWithParams` validates pagination and
      sorting parameters).
* **Pagination and Search:**
    * Repositories provide methods for paginated results (`AbstractRepository::getAllWithParams`).
    * Search functionality is implemented in specific repositories (e.g., `DriverRepository::search`).
* **Centralized Response Handling:** A `ResponseHandler` helper class (`src/Helpers/ResponseHandler.php`, used in
  middleware) standardizes API error responses.
* **API Documentation:**
    * An OpenAPI specification (`docs/openapi.yaml`) is maintained.
    * Swagger UI is deployed to GitHub Pages for interactive API documentation (as per
      `.github/workflows/gh-pages.yml`).

## Code Quality & Standards

* **Strict Typing:** `declare(strict_types=1);` is used across PHP files to enforce type safety.
* **Coding Standards:** Adherence to PER-CS2.0 coding standards, enforced by PHP CS Fixer.
* **Static Analysis:** PHPStan is used to detect potential bugs and improve code quality.
* **Comprehensive Testing:**
    * **Unit Tests (`tests/Unit`):** Extensive unit tests for controllers and repositories, utilizing Mockery for
      isolating units. Examples include `AbstractControllerTest.php`, `AbstractRepositoryTest.php`, and specific entity
      tests like `TeamControllerTest.php`.
    * **Integration Tests:** Postman collections are run via Newman in the CI pipeline to test API endpoints against a
      live application instance with a database.
* **OpenAPI Specification:** A well-defined OpenAPI contract ensures API consistency and facilitates documentation and
  client generation.

## Continuous Integration & Deployment (CI/CD)

A robust CI pipeline is implemented using GitHub Actions (`.github/workflows/continuous-integration.yml`):

* **Triggers:** Runs on pull requests to `main` and `staging/**` branches.
* **`build` Job:**
    * Validates `composer.json` and `composer.lock`.
    * Caches Composer and Node.js dependencies.
    * Installs dependencies (Composer & Redocly CLI).
    * Installs PHP 8.2 with necessary tools and extensions.
    * Performs static analysis using PHPStan.
    * Checks code style using PHP CS Fixer against PER-CS2.0.
    * Lints the OpenAPI specification using Redocly CLI.
    * Sets up problem matchers for PHPUnit.
    * Runs PestPHP unit tests.
* **`test` Job (for Pull Requests):**
    * Depends on the successful completion of the `build` job.
    * Sets up a MariaDB service container and initializes the database schema from `f1_db.sql`.
    * Checks out code and restores cached dependencies.
    * Installs PHP with `mysqli` and `pdo_mysql` extensions.
    * Starts a PHP development server.
    * Installs Newman.
    * Runs Postman collections (integration/API tests) against the live server.
    * Outputs server and application logs for debugging.

This workflow ensures that all code changes are automatically validated for quality, correctness, and adherence to
standards before merging.

## Limitations

This project, being a school assignment developed under time constraints and with evolving requirements, has certain
limitations:

* **Rigid CI/CD Configuration:**
  The CI/CD pipeline, while functional and crucial for maintaining code quality across team members with varying skill
  levels, is somewhat rigid. It was established early in the project and saw less iteration compared to core application
  features. This was a conscious trade-off to ensure a stable development baseline from the outset, given the project's
  nature and resource limitations. Future enhancements could involve more dynamic configurations or optimized stages.

* **Lack of Comprehensive Validation in API Models:**
  While basic input validation (e.g., JSON format in `AbstractController`) and some model-specific checks (e.g.,
  `Driver::$career_points`) are in place, comprehensive data validation at the model level is not exhaustive. Rapid
  iteration and an initially undefined scope for future features made it challenging to implement thorough validation
  rules for all attributes across all models. Time constraints inherent in a school project also limited the depth of
  this aspect.

* **SPA Implementation Unfinished:**
  Any plans or initial efforts towards developing a Single Page Application (SPA) frontend for this API were not
  completed. The primary focus remained on delivering a functional backend API within the allocated timeframe.

* **Limited Scope of Integration Testing:**
  The project includes integration tests run via Newman and Postman collections, which are executed as part of the CI
  pipeline. However, the scope and depth of these tests are limited. They cover primary use cases but may not encompass
  all edge cases or complex interaction scenarios. Establishing the integration testing framework was prioritized, but
  comprehensive test case development was constrained by time and the project's rapid development cycle.

These limitations primarily stem from the project's context as a school assignment, where rapid development, learning,
and managing varying skill levels were key considerations, often taking precedence over achieving production-grade
completeness in all areas.

## Potential Ways to Break the Application (Illustrative)

Given the limitations mentioned above, here are some illustrative examples of how one might encounter issues or
unexpected behavior with the API. These are hypothetical and aim to demonstrate the implications of the known
limitations.

### 1. Exploiting Incomplete Model Validation

Due to the "Lack of Comprehensive Validation in API Models," sending carefully crafted, but technically malformed or
unexpected, data might bypass existing checks.

**Example:** Attempting to create a driver with data that might not be fully validated for all fields.

```bash
# Assumes the API is running at http://localhost:8000
# Attempting to create a driver with potentially unvalidated or problematic data
curl -X POST http://localhost:8000/api/drivers \
-H "Content-Type: application/json" \
-d '{
  "name": "Test Pilot",
  "team_id": 1,
  "nationality": "<script>alert(\"Potential XSS Attempt\")</script>",
  "date_of_birth": "9999-12-31", # A logically invalid date (future) or unexpected format
  "biography": "Imagine this is a 5MB string...", # Potential for issues if max length not enforced/validated
  "career_points": -10 # This specific field might be caught by existing validation (non-negative points)
}'
```

Potential Outcomes:

The request might succeed if fields like nationality, date_of_birth, or biography lack robust validation (e.g.,
sanitization, format checks, length limits).
This could lead to data integrity issues in the database, unexpected behavior in other parts of the application that
consume this data, or potential security vulnerabilities (like XSS if nationality is rendered elsewhere without
sanitization).

---

### 2. Uncovering Gaps in Integration Testing

The "Limited Scope of Integration Testing" means that certain complex interactions or edge-case inputs might not be
covered by the existing test suite.

Example: Attempting to update a resource to reference a non-existent related entity, an operation that might not be
explicitly tested.

```bash
# Assumes the API is running at http://localhost:8000 and driver with ID 1 exists
# Attempting to update a driver's team_id to a non-existent team
curl -X PATCH http://localhost:8000/api/drivers/1 \
    -H "Content-Type: application/json" \
    -H "Content-Type: application/json" \
    -d '{
  "team_id": 99999 # Assuming team_id 99999 does not exist
}'
```

Potential Outcomes:

- Successful Update with Dangling Reference: If there's no foreign key constraint at the database level for team_id (or
  if it's nullable and the API doesn't validate existence), the driver might be updated to reference a non-existent
  team. This could lead to errors when fetching the driver's details along with their team.
- Internal Server Error: If the application attempts to process this invalid team_id in a way that assumes its
  existence, it might result in an unhandled exception and a 500 error.
- Silent Failure or Unexpected State: The update might appear to succeed but leave the system in an inconsistent state
  that only becomes apparent later.

---

### 3. Mismatch Between Model and Database Field Definitions

The database schema (f1_db.sql) defines specific data types and constraints for each field (e.g., UNSIGNED, NOT NULL,
length of VARCHAR fields). If the API models do not strictly enforce these constraints, it can lead to unexpected
behavior or data corruption.

Example: The tracks table defines length_km as DECIMAL(5,2). If the corresponding model attribute does not enforce this
precision and scale, it might be possible to insert values that exceed these limits.

```bash
# Assumes the API is running at http://localhost:8000
# Attempting to create a track with an invalid length_km value
curl -X POST http://localhost:8000/api/tracks \
    -H "Content-Type: application/json" \
    -d '{
  "name": "Super Long Track",
  "length_km": 999.999,  # Exceeds DECIMAL(5,2) limit
  "continent": "Asia",
  "country_id": 85,
  "description": "A very long track"
}'
```

Potential Outcomes:

- Data Truncation: The database might truncate the length_km value to 999.99, leading to inaccurate data.
- Database Error: The database might reject the insertion due to a data type mismatch or constraint violation, resulting
  in an error response from the API. However, if the model doesn't validate this, the application might crash, or the
  error might not be handled gracefully.
- Inconsistent Data Representation: The value stored in the database might differ from the value intended by the user,
  leading to inconsistencies and potential issues in calculations or comparisons.
