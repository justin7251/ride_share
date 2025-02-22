# RideShare Project

RideShare is a modern ride-sharing platform that connects drivers with passengers, facilitating easy, quick, and safe rides across the city. This project is split into two main components: the backend, built with Laravel, and the frontend, developed using Laravel andVue.js.

## Features

- User authentication and authorization
- Real-time ride tracking
- Ride booking and cancellation
- Driver location updates
- webhook notification for driver

## Prerequisites

Before you begin, ensure you have met the following requirements:
- PHP >= 8.1
- Composer
- Node.js and npm
- MySQL
- A Pusher account for real-time capabilities

## Installation

Follow these steps to get your development environment running:

### Backend Setup

1. Clone the backend repository:
   ```bash
   git clone https://your-backend-repo-url.git
   cd backend-directory
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the example env file and make the necessary configuration changes in the `.env` file:
   ```bash
   cp .env.example .env
   ```

4. Generate an app encryption key:
   ```bash
   php artisan key:generate
   ```

5. Run the database migrations (**Set the database connection in .env before migrating**):
   ```bash
   php artisan migrate
   ```

6. Start the local development server:
   ```bash
   php artisan serve
   ```

### Frontend Setup

1. Clone the frontend repository:
   ```bash
   git clone https://your-frontend-repo-url.git
   cd frontend-directory
   ```

2. Install npm packages:
   ```bash
   npm install
   ```

3. Compile and hot-reload for development:
   ```bash
   npm run serve
   ```

4. Build for production:
   ```bash
   npm run build
   ```

## Usage

Describe how to use the application with examples of functionality.



## Acknowledgements

- [Vue.js](https://vuejs.org/)
- [Laravel](https://laravel.com/)
- [Pusher](https://pusher.com/)
- [MySQL](https://www.mysql.com/)

# clone_ride_share

# Laravel
http://localhost:9001/

# Vue
http://localhost:8082/

# MySQL
http://localhost:8080/

# Mailpit
http://localhost:8025/


docker-compose down

docker-compose build --no-cache frontend

docker-compose up -d

docker-compose exec app php artisan migrate:fresh