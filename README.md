# Ads Service API

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the Project](#running-the-project)
- [Seeding the Database](#seeding-the-database)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)

## Prerequisites

Before you begin, ensure you have met the following requirements:

- **PHP**: Version 8.2 or higher
- **Composer**: Latest version
- **Database**: MySQL, PostgreSQL, SQLite, etc.

## Installation

Follow these steps to get your development environment set up:

### 1. Clone the Repository

```sh
git clone git@github.com:MaxHumennui/ads-service-api.git
cd ads-service-api
```

### 2. Install Dependencies

Install PHP dependencies using Composer:

```sh
composer install
```

This will also automatically copy the `.env.example` file to `.env`.

### 3. Configure Environment Variables

Open the `.env` file and configure your environment variables:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

Run the database migrations to create the necessary tables:

```sh
php artisan migrate
```

## Running the Project

To start the development server, run:

```sh
php artisan serve
```

By default, the application will be available at `http://localhost:8000`.

## Seeding the Database

To populate the database with sample data, run the database seeders:

```sh
php artisan db:seed
```

This will create sample data for ads and visitors.

## Usage

### API Endpoints

Here are the available API endpoints:

#### Visitors

- **Track Ad Click**

  ```http
  POST /visitor/track-click
  ```

  **Request Body:**

  ```json
  {
    "ad_id": 1
  }
  ```

- **Track Ad Impression**

  ```http
  POST /visitor/track-impression
  ```

  **Request Body:**

  ```json
  {
    "ad_id": 1
  }
  ```

- **Clean Old Visitor Entries**

  ```http
  DELETE /visitor/clean-old-entries
  ```

#### Ads

- **Get All Ads**

  ```http
  GET /ads
  ```

- **Get Ad Statistics**

  ```http
  GET /ads/statistics
  ```

- **Create Ad**

  ```http
  POST /ads
  ```

  **Request Body:**

  ```json
  {
    "title": "Sample Ad",
    "description": "This is a sample ad.",
    "image": "base64-encoded-image"
  }
  ```

- **Update Ad**

  ```http
  PUT /ads/{id}
  ```

  **Request Body:**

  ```json
  {
    "title": "Updated Ad",
    "description": "This is an updated ad.",
    "image": "base64-encoded-image"
  }
  ```

- **Delete Ad**

  ```http
  DELETE /ads/{id}
  ```
