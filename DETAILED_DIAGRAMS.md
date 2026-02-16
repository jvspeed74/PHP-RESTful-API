# F1 Management API - Detailed System Diagrams

## 1. Request Flow Diagram

```mermaid
graph LR
    Start([HTTP Request]) -->|" GET /drivers "| Entry["Entry Point:<br/>public/index.php"]
    Entry -->|Bootstrap| Config["Load Configuration"]
    Config -->|Container Setup| DI["Dependency Injection<br/>Container"]
    DI -->|Route Matching| Router["Slim Router"]
    Router -->|Middleware Stack| Auth["Authentication<br/>Middleware"]
    Auth -->|Token Validation| JWT["JWT Verification"]
    JWT -->|✓ Valid Token| Controller["Route Handler<br/>Controller"]
    JWT -->|✗ Invalid| Error401["401 Unauthorized"]
    Controller -->|Execute Logic| Business["Business Logic"]
    Business -->|Data Access| Repository["Repository Pattern"]
    Repository -->|ORM Query| Eloquent["Eloquent ORM"]
    Eloquent -->|SQL| Database["MariaDB"]
    Database -->|Result Set| Eloquent
    Eloquent -->|Collection| Repository
    Repository -->|Domain Objects| Business
    Business -->|Format| Presenter["Response Presenter"]
    Presenter -->|JSON| Response["HTTP Response"]
    Response -->|200/201/404/500| Client([Client])
    Error401 -->|Error Response| Client
```

## 2. Authentication Architecture

```mermaid
graph TD
    subgraph "Authentication Methods"
        Basic["Basic Auth<br/>username:password"]
        Bearer["Bearer Token<br/>Authorization: Bearer"]
        JWT["JWT Token<br/>Claims + Signature"]
    end

    subgraph "Authentication Layer"
        BasicAuth["BasicAuthenticator"]
        BearerAuth["BearerAuthenticator"]
        JWTAuth["JWTAuthenticator"]
    end

    subgraph "Validation"
        ValidUser["User Lookup"]
        VerifyToken["Token Signature<br/>Verification"]
        CheckExpiry["Check Token<br/>Expiration"]
    end

    subgraph "Storage"
        Users[("Users Table")]
        Tokens[("Tokens Table")]
    end

    Basic -->|Validate| BasicAuth
    Bearer -->|Extract| BearerAuth
    JWT -->|Decode| JWTAuth
    BasicAuth -->|Lookup| ValidUser
    BearerAuth -->|Retrieve| VerifyToken
    JWTAuth -->|Verify| VerifyToken
    ValidUser -->|Query| Users
    VerifyToken -->|Verify Signature| Tokens
    CheckExpiry -->|Compare| Tokens
    VerifyToken -->|Valid| CheckExpiry
    CheckExpiry -->|Authorized ✓| Success["Grant Access<br/>Set User Context"]
    CheckExpiry -->|Expired| Reject["403 Forbidden"]
    BasicAuth -->|Invalid| Reject
```

## 3. Database Schema Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ TOKENS: "has many"
    USERS ||--o{ DRIVERS: "can be"
    TEAMS ||--o{ DRIVERS: "has many"
    DRIVERS ||--o{ EVENTS: "participates in"
    TRACKS ||--o{ EVENTS: "hosts"
    EVENTS ||--o{ CARS: "uses"
    TEAMS ||--o{ CARS: "builds"
    COUNTRIES ||--o{ DRIVERS: "from"
    COUNTRIES ||--o{ TEAMS: "from"

    USERS {
        int id PK
        string email UK
        string password
        string name
        string role
        timestamp created_at
        timestamp updated_at
    }

    TOKENS {
        int id PK
        int user_id FK
        string token UK
        timestamp expires_at
        timestamp created_at
    }

    DRIVERS {
        int id PK
        string name
        int country_id FK
        int team_id FK
        int number UK
        string status
        timestamp created_at
    }

    TEAMS {
        int id PK
        string name UK
        int country_id FK
        string principal
        string status
        timestamp created_at
    }

    CARS {
        int id PK
        int team_id FK
        string chassis
        string power_unit
        string year
        timestamp created_at
    }

    TRACKS {
        int id PK
        string name UK
        int country_id FK
        float length
        int laps
        timestamp created_at
    }

    EVENTS {
        int id PK
        int track_id FK
        string name
        string season
        int round
        timestamp date
        timestamp created_at
    }

    COUNTRIES {
        int id PK
        string name UK
        string code
        timestamp created_at
    }
```

## 4. Container & Volume Architecture

```mermaid
graph TB
    subgraph "Host Machine"
        LocalFS["Host Filesystem"]
        ProjectDir["Project Directory<br/>./"]
    end

    subgraph "Docker Daemon"
        ComposeFile["docker-compose.yml"]
        Dockerfile["Dockerfile"]
    end

    subgraph "Docker Containers"
        subgraph "PHP App Container<br/>f1_api_app"
            AppCode["Application Code<br/>Slim Framework"]
            PHPServer["PHP Dev Server<br/>:8080"]
        end

        subgraph "MariaDB Container<br/>f1_api_db"
            DBServer["Database Server<br/>:3306"]
            DBInit["Initialization<br/>f1_db.sql"]
        end

        subgraph "phpMyAdmin Container"
            AdminUI["Web UI<br/>:8081"]
        end
    end

    subgraph "Volumes"
        BindProjectCode["Bind Mount<br/>./ → /var/www/html"]
        BindLogs["Bind Mount<br/>./logs → logs/"]
        NamedVendor["Named Volume<br/>vendor cache"]
        NamedDB["Named Volume<br/>f1_db_data"]
    end

    LocalFS -->|Compose Reads| ComposeFile
    ComposeFile -->|Build| Dockerfile
    ProjectDir -->|Bind| BindProjectCode
    ProjectDir -->|Bind| BindLogs
    BindProjectCode -->|Mount| AppCode
    BindLogs -->|Mount| AppCode
    NamedVendor -->|Mount| AppCode
    NamedDB -->|Mount| DBServer
    DBInit -->|Initialize| DBServer
    AppCode -->|Serve| PHPServer
    PHPServer -->|Port 8080| LocalFS
    DBServer -->|Port 3306| LocalFS
    AdminUI -->|Port 8081| LocalFS
    PHPServer -->|Connect| DBServer
    AdminUI -->|Manage| DBServer
```

## 5. Dependency Injection Container

```mermaid
graph LR
    subgraph "Container Setup"
        DI["PHP-DI Container"]
    end

    subgraph "Service Definitions"
        DB["Database Connection"]
        Logger["Monolog Logger"]
        JWTHandler["JWT Handler"]
        Repos["Repositories"]
        Controllers["Controllers"]
    end

    subgraph "Configuration Files"
        Config["config/dependencies.php"]
        DBConfig["config/DatabaseConfig.php"]
        LogConfig["config/LogConfig.php"]
    end

    subgraph "External Services"
        MySQL[("MariaDB")]
        FileSystem["Log Files"]
    end

    Config -->|Define| DI
    DBConfig -->|Configure| DB
    LogConfig -->|Configure| Logger
    DI -->|Create| DB
    DI -->|Create| Logger
    DI -->|Create| JWTHandler
    DI -->|Create| Repos
    DI -->|Create| Controllers
    DB -->|Connect to| MySQL
    Logger -->|Write to| FileSystem
    JWTHandler -->|Verify| Repos
    Repos -->|Use| DB
    Controllers -->|Inject| Repos
    Controllers -->|Inject| Logger
```

## 6. API Endpoint Architecture

```mermaid
graph TD
    API["F1 Management API<br/>Base: /"]
    API -->|/auth| AuthEndpoints["Authentication"]
    API -->|/cars| CarEndpoints["Cars Resource"]
    API -->|/drivers| DriverEndpoints["Drivers Resource"]
    API -->|/teams| TeamEndpoints["Teams Resource"]
    API -->|/events| EventEndpoints["Events Resource"]
    API -->|/tracks| TrackEndpoints["Tracks Resource"]
    AuthEndpoints -->|POST /login| Login["User Login"]
    AuthEndpoints -->|POST /register| Register["User Registration"]
    AuthEndpoints -->|POST /refresh| Refresh["Token Refresh"]
    AuthEndpoints -->|POST /logout| Logout["User Logout"]
    CarEndpoints -->|GET /| ListCars["List All Cars"]
    CarEndpoints -->|POST /| CreateCar["Create Car"]
    CarEndpoints -->|GET /:id| GetCar["Get Car Details"]
    CarEndpoints -->|PUT /:id| UpdateCar["Update Car"]
    CarEndpoints -->|DELETE /:id| DeleteCar["Delete Car"]
    DriverEndpoints -->|GET /| ListDrivers["List All Drivers"]
    DriverEndpoints -->|POST /| CreateDriver["Create Driver"]
    DriverEndpoints -->|GET /:id| GetDriver["Get Driver Details"]
    DriverEndpoints -->|PUT /:id| UpdateDriver["Update Driver"]
    DriverEndpoints -->|DELETE /:id| DeleteDriver["Delete Driver"]
    TeamEndpoints -->|GET /| ListTeams["List All Teams"]
    TeamEndpoints -->|POST /| CreateTeam["Create Team"]
    TeamEndpoints -->|GET /:id| GetTeam["Get Team Details"]
    TeamEndpoints -->|PUT /:id| UpdateTeam["Update Team"]
    TeamEndpoints -->|DELETE /:id| DeleteTeam["Delete Team"]
    EventEndpoints -->|GET /| ListEvents["List All Events"]
    EventEndpoints -->|POST /| CreateEvent["Create Event"]
    EventEndpoints -->|GET /:id| GetEvent["Get Event Details"]
    EventEndpoints -->|PUT /:id| UpdateEvent["Update Event"]
    EventEndpoints -->|DELETE /:id| DeleteEvent["Delete Event"]
    TrackEndpoints -->|GET /| ListTracks["List All Tracks"]
    TrackEndpoints -->|POST /| CreateTrack["Create Track"]
    TrackEndpoints -->|GET /:id| GetTrack["Get Track Details"]
    TrackEndpoints -->|PUT /:id| UpdateTrack["Update Track"]
    TrackEndpoints -->|DELETE /:id| DeleteTrack["Delete Track"]
    Login -->|Controller| AuthC["AuthController"]
    Register -->|Controller| AuthC
    Refresh -->|Controller| AuthC
    Logout -->|Controller| AuthC
    ListCars -->|Controller| CarC["CarController"]
    CreateCar -->|Controller| CarC
    GetCar -->|Controller| CarC
    UpdateCar -->|Controller| CarC
    DeleteCar -->|Controller| CarC
    ListDrivers -->|Controller| DriverC["DriverController"]
    CreateDriver -->|Controller| DriverC
    GetDriver -->|Controller| DriverC
    UpdateDriver -->|Controller| DriverC
    DeleteDriver -->|Controller| DriverC
    ListTeams -->|Controller| TeamC["TeamController"]
    CreateTeam -->|Controller| TeamC
    GetTeam -->|Controller| TeamC
    UpdateTeam -->|Controller| TeamC
    DeleteTeam -->|Controller| TeamC
    ListEvents -->|Controller| EventC["EventController"]
    CreateEvent -->|Controller| EventC
    GetEvent -->|Controller| EventC
    UpdateEvent -->|Controller| EventC
    DeleteEvent -->|Controller| EventC
    ListTracks -->|Controller| TrackC["TrackController"]
    CreateTrack -->|Controller| TrackC
    GetTrack -->|Controller| TrackC
    UpdateTrack -->|Controller| TrackC
    DeleteTrack -->|Controller| TrackC
```

## 7. Development & Deployment Stack

```mermaid
graph TB
    subgraph "Development Tools"
        IDE["IDE<br/>JetBrains/VSCode"]
        Composer["Composer<br/>Dependency Manager"]
        Docker["Docker<br/>Containerization"]
    end

    subgraph "Code Quality"
        PHPStan["PHPStan<br/>Static Analysis"]
        PhpCSFixer["PHP-CS-Fixer<br/>Code Formatting"]
        Pest["Pest<br/>Testing Framework"]
    end

    subgraph "Source Control"
        Git["Git Repository"]
        GitHub["GitHub"]
    end

    subgraph "Runtime"
        PHP["PHP 8.2"]
        MariaDB["MariaDB 11.4"]
        Slim["Slim Framework 4.14"]
    end

    subgraph "Logging & Debugging"
        Monolog["Monolog Logger"]
        Xdebug["Xdebug Debugger"]
        AppLog["app.log"]
    end

    IDE -->|Edits| Composer
    Composer -->|Installs| Runtime
    Docker -->|Orchestrates| Runtime
    PHPStan -->|Analyzes| IDE
    PhpCSFixer -->|Formats| IDE
    Pest -->|Tests| Runtime
    IDE -->|Commits| Git
    Git -->|Syncs| GitHub
    PHP -->|Runs| Slim
    Slim -->|Communicates| MariaDB
    Xdebug -->|Debugs| IDE
    Monolog -->|Logs to| AppLog
```

## 8. Docker Startup Sequence

```mermaid
sequenceDiagram
    participant User
    participant Docker
    participant MariaDB
    participant PHP as PHP App
    participant phpMyAdmin
    User ->> Docker: docker-compose up -d
    Docker ->> MariaDB: Start container
    MariaDB ->> MariaDB: Initialize database
    MariaDB ->> MariaDB: Health check (10s intervals)
    Docker ->> Docker: Wait for MariaDB healthy
    MariaDB -->> Docker: ✓ Healthy
    Docker ->> PHP: Start container (depends_on healthy)
    PHP ->> PHP: Execute docker-entrypoint.sh
    PHP ->> MariaDB: Test connection
    MariaDB -->> PHP: ✓ Connected
    PHP ->> PHP: composer install
    PHP ->> PHP: Create logs directory
    PHP ->> PHP: Start PHP dev server :8080
    PHP -->> Docker: ✓ Ready
    Docker ->> phpMyAdmin: Start container
    phpMyAdmin ->> MariaDB: Connect to database
    phpMyAdmin -->> Docker: ✓ Ready
    Docker -->> User: ✓ All services running
    User ->> PHP: http://localhost:8080
    User ->> phpMyAdmin: http://localhost:8081
```

## Legend

```
🌐 = Web/Network
📁 = File System
📝 = Logs
📦 = Packages/Dependencies
💾 = Database Storage
📱 = Mobile/Client
⚙️ = Configuration/Tools
🗄️ = Database
```

