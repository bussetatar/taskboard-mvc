# TaskBoard MVC

TaskBoard is a small task management web application written in pure PHP without a framework. Users can register, sign in, create categories, and perform full CRUD operations on their own tasks.

## Features

- User registration, login, logout, sessions, and authentication middleware
- Categories and tasks with a one-to-many database relationship
- Task create, read, update, delete, complete/reopen, filter, and statistics
- Pure PHP MVC structure with a custom router
- SQLite database through PDO and prepared statements
- CSRF tokens, escaped output, password hashing, ownership checks, secure session cookies, and security headers
- Unit and integration tests with no external testing dependency
- Docker setup for local deployment

## Quick Start With Docker

Docker Desktop is the only requirement.

```bash
docker compose up --build
```

Open `http://localhost:8000`, register an account, and start adding tasks.

Stop the server with:

```bash
docker compose down
```

The SQLite database is kept in the `taskboard_data` Docker volume. To remove all application data:

```bash
docker compose down -v
```

## Run With Local PHP

Requirements:

- PHP 8.2 or newer
- PDO SQLite extension enabled

From the project directory:

```bash
php -S localhost:8000 -t public public/router.php
```

The database file is created automatically at `storage/app.sqlite`.

## Tests

With Docker:

```bash
docker compose run --rm app php tests/run.php
```

With local PHP:

```bash
php tests/run.php
```

The tests use an in-memory SQLite database and cover validation, CSRF, password hashing, model CRUD, relationships, and user ownership.

## Project Structure

```text
app/
  Controllers/   Request handling and application logic
  Core/          Router, database, sessions, auth, CSRF, validation
  Middleware/    Authentication and guest access checks
  Models/        PDO queries and database operations
  Views/         Dynamic server-rendered HTML
public/          Front controller, development router, and CSS
storage/         Runtime SQLite database
tests/           Lightweight unit/integration test runner
```

## MVC Request Flow

1. `public/index.php` sends the request to `Router`.
2. The router matches a route and runs its middleware.
3. A controller validates input and calls a model.
4. The model uses PDO prepared statements to read or update SQLite.
5. The controller renders a view or redirects with a session message.

## Database

- `users` owns many `categories`
- `users` owns many `tasks`
- `categories` has many `tasks`
- Deleting a user cascades to their categories and tasks
- Deleting a category keeps its tasks and sets their category to `NULL`

The schema is created automatically by `App\Core\Database::migrate()`.

## Security Notes

- SQL injection: every user value is passed through a PDO prepared statement.
- XSS: dynamic output is escaped with the `e()` helper.
- CSRF: every POST form includes a random session token validated by the router.
- Passwords: stored with `password_hash()` and checked with `password_verify()`.
- Authorization: category and task queries include the signed-in user's ID.
- Sessions: IDs are regenerated at login/logout; cookies use `HttpOnly` and `SameSite=Lax`.
- Headers: CSP, frame protection, MIME sniffing protection, and a referrer policy are set.

## Version Control

The submitted folder is a Git repository. Useful commands:

```bash
git status
git log --oneline
```

To publish it, create an empty GitHub repository and add it as a remote:

```bash
git remote add origin YOUR_REPOSITORY_URL
git push -u origin main
```
