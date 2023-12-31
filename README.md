## Installation Guide - Backend Shortlink App

This guide provides instructions on how to install and set up the backend for the Shortlink App. The backend is built with PHP 8.2 and utilizes MySQL as the database. Follow the steps below to install and configure the backend.

### Prerequisites
- PHP 8.2
- MySQL

### Installation Steps

1. Clone the Backend Repository:

```
git clone https://github.com/Zadelkhair/shorter-be.git
```

2. Create the Environment File:
- Copy the provided `.env.example` file and rename it to `.env`.
- Open the `.env` file in a text editor.
- Set the database connection details such as `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` according to your MySQL database configuration.

3. Create the Database:
- Access your MySQL database management tool (e.g., phpMyAdmin, MySQL command-line interface).
- Create a new database with the name specified in the `DB_DATABASE` variable of the `.env` file.

4. Migrate the Database:
- In the command-line interface, navigate to the root directory of the backend project.
- Run the following command to migrate the database tables:

```
php artisan migrate
```

5. Seed the Database:
- Run the following command to seed the database with initial data:

```
php artisan db:seed
```

This command will create an administrator account with the email `admin@email.com` and password `admin@password`.

6. Run the Backend Server:
- Start the backend server by running the following command:
```
php artisan serve
```

The backend server will be running at `http://localhost:8000`.

7. Install the Frontend:
- Follow the instructions provided in the [Frontend Shortlink App Repository](https://github.com/Zadelkhair/shorter-fe) to install and set up the frontend.

Congratulations! You have successfully installed and set up the Shortlink App backend. You can now access the app and use the administrator credentials to log in and manage the shortlinks.

If you have any further questions or issues, please let me know.
