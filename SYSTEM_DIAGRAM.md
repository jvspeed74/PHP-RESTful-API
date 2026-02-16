# F1 Management API - System Diagram

## Overall Architecture

```mermaid
graph TB
    subgraph "Client Layer"
        Browser["🌐 Web Browser"]
        Mobile["📱 Mobile Client"]
        CLI["⚙️ CLI Tools"]
    end

    subgraph "Docker Host"
        subgraph "Bridge Network: f1_network"
            subgraph "PHP App Container"
                Router["Slim Router"]
                Auth["Authentication Layer"]
                AuthC["AuthController"]
                CarC["CarController"]
                DriverC["DriverController"]
                TeamC["TeamController"]
                EventC["EventController"]
                TrackC["TrackController"]
                Middleware["Middleware Stack"]
            end

            subgraph "Data Access Layer"
                ORM["Eloquent ORM<br/>Capsule"]
                CarModel["Car Model"]
                DriverModel["Driver Model"]
                TeamModel["Team Model"]
                EventModel["Event Model"]
                TrackModel["Track Model"]
                UserModel["User Model"]
                TokenModel["Token Model"]
            end

            subgraph "Database Service"
                MariaDB[("🗄️ MariaDB<br/>f1_db")]
            end

            subgraph "Admin Interface"
                PhpMyAdmin["phpMyAdmin<br/>Port: 8081"]
            end
        end

        subgraph "Volumes"
            SourceBind["📁 Project Files<br/>/var/www/html"]
            LogsBind["📝 Logs Directory<br/>logs/"]
            VendorVol["📦 Vendor Cache<br/>named volume"]
            DBVol["💾 DB Data<br/>f1_db_data"]
        end
    end

    subgraph "Host Machine"
        HostFS["Host Filesystem"]
        Docker["Docker Engine"]
    end

    Browser -->|HTTP Requests| Router
    Mobile -->|API Calls| Router
    CLI -->|Commands| Docker

    Router -->|Route Matching| Middleware
    Middleware -->|Authentication| Auth
    Auth -->|Token Validation| AuthC
    
    Router -->|Resource Requests| CarC
    Router -->|Resource Requests| DriverC
    Router -->|Resource Requests| TeamC
    Router -->|Resource Requests| EventC
    Router -->|Resource Requests| TrackC

    CarC -->|Query| ORM
    DriverC -->|Query| ORM
    TeamC -->|Query| ORM
    EventC -->|Query| ORM
    TrackC -->|Query| ORM
    AuthC -->|Query| ORM

    ORM -->|Model Mapping| CarModel
    ORM -->|Model Mapping| DriverModel
    ORM -->|Model Mapping| TeamModel
    ORM -->|Model Mapping| EventModel
    ORM -->|Model Mapping| TrackModel
    ORM -->|Model Mapping| UserModel
    ORM -->|Model Mapping| TokenModel

    CarModel -->|SQL Queries| MariaDB
    DriverModel -->|SQL Queries| MariaDB
    TeamModel -->|SQL Queries| MariaDB
    EventModel -->|SQL Queries| MariaDB
    TrackModel -->|SQL Queries| MariaDB
    UserModel -->|SQL Queries| MariaDB
    TokenModel -->|SQL Queries| MariaDB

    PhpMyAdmin -->|Database Management| MariaDB

    SourceBind -.->|Hot Reload| Router
    LogsBind -->|Application Logs| HostFS
    VendorVol -->|Dependencies| ORM
    DBVol -->|Persistent Data| MariaDB

    Docker -->|Manages| SourceBind
    Docker -->|Manages| LogsBind
    Docker -->|Manages| VendorVol
    Docker -->|Manages| DBVol

