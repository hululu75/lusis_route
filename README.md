# Lusis Route Management Platform

**Version:** 1.0
**Framework:** Laravel 11
**Database:** SQLite
**PHP:** 8.2+

## Overview

The **Lusis Route Management Platform** (LRMP) is a web-based tool for designing, storing, and generating **Tango-compatible routing configurations**. It provides a visual and database-backed management interface for routing logic, replacing manual XML editing with structured UI.

## Key Features

- **Visual Route Management** - Edit routes through a web interface instead of raw XML
- **Structured Data Storage** - Store routing configurations in a relational database
- **Import/Export** - Import legacy `routing_*.xml` files and export to XML
- **Traceability** - Full audit trail and version control for routing changes
- **Relationship Management** - Manage Projects, Route Files, Routes, Matches, Rules, Deltas, and Services

## Core Concepts

### Routing Components

| Component | Description |
|-----------|-------------|
| **Project** | Top-level container for organizing routing configurations |
| **Route File** | Represents a `routing_*.xml` file containing multiple routes |
| **Service** | Tango service definition (source/destination) |
| **Route** | Entry point defining message paths for a service |
| **Match** | Logical condition group with multiple conditions |
| **Rule** | Defines destination service, type, and optional deltas |
| **Delta** | Message transformation applied during routing |

### Message Types

- **REQ** - Request (waits for response)
- **NOT** - Notification (no reply expected)
- **SAME** - Same process (forward internally)
- **PUB** - Publish (broadcast to multiple)
- **END** - End (terminate routing)

## Architecture

```
┌─────────────┐
│   Project   │
└──────┬──────┘
       │ 1:N
       ▼
┌─────────────┐
│ Route File  │
└──────┬──────┘
       │ 1:N
       ▼
┌─────────────┐      ┌──────────┐
│    Route    │─────▶│ Service  │
└──────┬──────┘      └──────────┘
       │
       ├──────▶ Match ──────▶ Conditions
       │
       └──────▶ Rule  ──────▶ Delta
```

## Database Schema

The application uses 8 core tables:

1. **projects** - Project containers
2. **services** - Tango service definitions
3. **route_files** - Routing configuration files
4. **matches** - Conditional matching rules
5. **match_conditions** - Individual conditions
6. **deltas** - Message transformations
7. **rules** - Routing rules and destinations
8. **routes** - Complete route definitions

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- **PHP SQLite extension** (pdo_sqlite)

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lusis_route
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Install PHP SQLite extension** (if not already installed)
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php8.4-sqlite3

   # Verify installation
   php -m | grep sqlite
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

7. **Access the application**
   ```
   http://localhost:8000
   ```

## Project Structure

```
app/
├── Models/              # Eloquent ORM models
│   ├── Project.php
│   ├── Service.php
│   ├── RouteFile.php
│   ├── RouteMatch.php
│   ├── MatchCondition.php
│   ├── Delta.php
│   ├── Rule.php
│   └── Route.php
│
database/
├── migrations/          # Database schema migrations
│   ├── *_create_projects_table.php
│   ├── *_create_services_table.php
│   ├── *_create_route_files_table.php
│   ├── *_create_matches_table.php
│   ├── *_create_match_conditions_table.php
│   ├── *_create_deltas_table.php
│   ├── *_create_rules_table.php
│   └── *_create_routes_table.php
│
└── database.sqlite      # SQLite database file
```

## Models and Relationships

### Project
- Has many RouteFiles

### RouteFile
- Belongs to Project
- Has many Routes

### Route
- Belongs to RouteFile
- Belongs to Service (source)
- Belongs to Match (optional)
- Belongs to Rule (optional)

### RouteMatch
- Has many MatchConditions
- Has many Routes

### Rule
- Belongs to Delta (optional)
- Has many Routes

### Delta
- Has many Rules

## Documentation

- **[SETUP.md](SETUP.md)** - Detailed setup and troubleshooting guide
- **[Functional Specification](docs/specification.md)** - Complete system specification

## Development Status

### ✅ Completed
- Laravel 11 installation and configuration
- SQLite database configuration
- Database migrations for all tables
- Eloquent models with relationships
- Core schema implementation

### 🚧 In Progress
- Web UI for route management
- XML import/export functionality
- Controllers and views

### 📋 Planned
- Route validation logic
- XML generation
- User authentication
- Audit logging

## Requirements from Specification

This implementation follows the functional specification:
- Entity Relationship Model as per ERD diagram
- Full referential integrity with foreign keys
- Support for all message types (REQ/NOT/SAME/PUB/END)
- Proper cascade/restrict delete behavior
- Timestamps for audit trail

## Contributing

This is an internal project for managing Tango routing configurations. For questions or issues, contact the development team.

## License

Proprietary - All rights reserved.
