# Store Order & Inventory Mini-System

A Laravel REST API application for managing retail orders and product inventory. The system supports customer order creation, automatic tax calculation, stock management, customer order history, low-stock product monitoring, and queued order confirmation.

## Tech Stack

- PHP 8.3+
- Laravel 13
- MySQL (InnoDB)
- REST API
- Eloquent ORM
- Laravel Database Queue
- PHPUnit
- Laravel Blade, Bootstrap 5 and Vanilla JavaScript (billing interface)

## Features

- Product and customer sample data using factories and seeders.
- Create orders for existing or new customers.
- Validate product availability before creating an order.
- Automatically calculate subtotal, tax, and grand total.
- Deduct product stock when an order is successfully created.
- Prevent overselling using database transactions and row-level locking.
- Retrieve customer order history using email.
- Retrieve products below a configurable stock threshold.
- Dispatch a queued order confirmation job.
- Automated feature tests for successful order creation and insufficient stock.

## Prerequisites

Before setting up the project, ensure the following are installed:

- PHP 8.3 or later.
- Composer.
- MySQL 8.0 or a compatible MySQL installation.
- Git.

**Note for Windows users:** Laravel 13 requires PHP 8.3 or later. You can install PHP 8.3 separately and configure its executable in your Windows PATH. XAMPP can be used independently to run the MySQL database server.

The PHP version used by the command line must meet Laravel's requirements. XAMPP's bundled PHP version does not need to be used if you are using standalone PHP.

Verify the PHP version:

```bash
php -v
```

Verify Composer:

```bash
composer --version
```

## Installation and Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd store-app
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Copy `.env.example` to `.env` and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Create the database

Create a MySQL database named `store`.

If you are using XAMPP, start the MySQL service from the XAMPP Control Panel before proceeding.

### 5. Run migrations and seeders

```bash
php artisan migrate --seed
```

This creates the required database tables and inserts sample products and customers.

### 6. Start the Laravel server

```bash
php artisan serve
```

Application URL:

http://127.0.0.1:8000

### 7. Start the queue worker

Open another terminal in the project directory and run:

```bash
php artisan queue:work
```

The worker processes queued order confirmation jobs and writes simulated email details to the Laravel log.

## API Endpoints

### 1. Create Order

**POST** `/api/orders`

Creates an order for an existing or new customer, calculates totals, validates stock availability, and deducts the purchased quantity.

Request body:

```json
{
    "customer": {
        "name": "Ganesh Krishna",
        "email": "ganesh@example.com"
    },
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}
```

Successful response: `201 Created`

The response contains the created order, customer details, product items, subtotal, tax, and grand total.

### 2. Customer Order History

**GET** `/api/customers/{email}/orders`

Retrieves all orders associated with a customer's email address.

Example:

```http
GET /api/customers/ganesh@example.com/orders
```

Returns `404 Not Found` if the customer does not exist.

### 3. Low Stock Products

**GET** `/api/products/low-stock`

Retrieves products whose available stock is below the specified threshold.

Default threshold: `5`

Custom threshold example:

```http
GET /api/products/low-stock?threshold=10
```

The comparison uses `stock < threshold`.

## Order Calculation

For each order item:

- Line subtotal = Unit price × Quantity
- Line tax = Line subtotal × Tax percentage / 100
- Grand total = Subtotal + Total tax

The unit price and tax percentage are stored as snapshots in order items to preserve historical order values if product prices or tax rates change later.

## Inventory and Concurrency Handling

Order creation is performed inside a database transaction.

Product records are locked using `lockForUpdate()` while checking and deducting stock.

Products are locked in ascending ID order to reduce the possibility of deadlocks when an order contains multiple products.

If stock is insufficient, the transaction is rolled back and the API returns a validation error.

MySQL InnoDB is used to support transactions and row-level locking.

## Queued Order Confirmation

After a successful order transaction, the application dispatches `SendOrderConfirmationJob`.

The job simulates an order confirmation email by writing the following details to the Laravel log:

- Order ID
- Customer name
- Customer email
- Grand total

No SMTP configuration or external email service is required.

Check the log at:

```text
storage/logs/laravel.log
```

## Automated Testing

A separate testing database is used to protect development data.

Create a database named `store_test` and configure `.env.testing`:

```env
APP_ENV=testing
APP_KEY=your_application_key

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_test
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=sync
```

Ensure the testing database exists before running the tests.

The PHPUnit configuration is set up to use MySQL and `store_test` instead of an SQLite in-memory database.

Run the feature tests:

```bash
php artisan test
```

The current feature tests cover:

1. Successful order creation, total calculation, stock deduction, and job dispatch.
2. Insufficient stock, transaction rollback, and prevention of job dispatch.

## Assumptions and Design Decisions

- Customers are identified by their unique email address.
- If a customer already exists, their existing name is retained.
- New customers are created automatically when an order is placed.
- Each order must contain at least one product.
- Duplicate product IDs within the same order are rejected.
- Product quantities must be positive integers.
- An order cannot be created if the requested quantity exceeds available stock.
- The low-stock threshold defaults to 5 and can be configured through a query parameter.
- Low-stock filtering uses a strict less-than comparison.
- Product price and tax percentage are stored as snapshots in order items.
- Product and customer CRUD operations are outside the scope of the assignment.
- Order confirmation is simulated through Laravel logging instead of sending an actual email.
- Authentication and authorization are outside the scope of this assessment.

## License

This project was developed as part of a Laravel Developer technical assessment.