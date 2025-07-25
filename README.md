# Freelance Time Tracker API

This is a Laravel API for tracking time spent on freelance projects.

## Setup Instructions

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/shoebHamim/freelance-time-tracker-api.git
    ```
2.  **Go to the cloned directory:**
    ```bash
    cd freelance-time-tracker-api
    ```
3.  **Install dependencies:**
    ```bash
    composer install
    ```
4.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```
5.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```
6.  **Run database migrations:**
    ```bash
    php artisan migrate:fresh
    ```
7.  **Seed the database :**
    If you want to populate the database with some sample data, run:
    ```bash
    php artisan db:seed
    ```
8.  **Start the development server:**
    ```bash
    php -S localhost:8000 -t public
    ```
    or
    ```bash
    php artisan serve
    ```
    The API will be available at `http://127.0.0.1:8000`.

## Database Structure

The database consists of the following main tables:

### `users`
-   `id` (Primary Key)
-   `name` (String)
-   `email` (String, Unique)
-   `email_verified_at` (Timestamp, Nullable)
-   `password` (String)
-   `remember_token` (String, Nullable)
-   `timestamps`

### `clients`
-   `id` (Primary Key)
-   `user_id` (Foreign Key to `users.id`, On Delete Cascade)
-   `name` (String)
-   `email` (String)
-   `contact_person` (String)
-   `timestamps`

### `projects`
-   `id` (Primary Key)
-   `client_id` (Foreign Key to `clients.id`, On Delete Cascade)
-   `title` (String)
-   `description` (Text, Nullable)
-   `status` (Enum: 'active', 'completed', Default: 'active')
-   `deadline` (Date, Nullable)
-   `timestamps`

### `time_logs`
-   `id` (Primary Key)
-   `project_id` (Foreign Key to `projects.id`, On Delete Cascade)
-   `start_time` (DateTime)
-   `end_time` (DateTime, Nullable)
-   `description` (Text, Nullable)
-   `hours` (Decimal, Default: 0)
-   `timestamps`


## API Endpoint Testing(Postman)
- Download the `Freelance Time Tracker API.postman_collection.json` file (in the root directory of the project)
- import it to Postman
- Change the variables accordingly 
- Test !
