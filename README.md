# BOSS-BUY

**BOSS-BUY** is a dynamic e-commerce platform that provides a seamless shopping experience for users and robust management tools for administrators. Built using HTML, CSS, JavaScript, PHP, and MySQL, the project focuses on delivering a user-friendly interface and a feature-rich admin panel for managing backend operations efficiently.

## Features

### User-Focused Features
- **Product Browsing**: Users can explore a wide range of products categorized for easy navigation.
- **Shopping Cart**: Add products to the cart and proceed to checkout with a streamlined process.
- **Secure Authentication**: Login and registration system with PHP sessions for user account management.

### Admin Panel Features
- **Product Management**: Add, edit, and delete products dynamically.
- **User Management**: View and delete users.
- **Order Management**: View and update order statuses (Pending, Processing, Shipped, Delivered, Cancelled).
- **Review Management**: Monitor product reviews with detailed information.
- **Order Items**: Display specific details of items within orders.

### Technical Features
- **Session Management**: Secure session handling to differentiate admin and user access.
- **CRUD Operations**: Full Create, Read, Update, and Delete functionalities for managing database entities.
- **Form Handling and Validation**: Server-side validation using `mysqli_real_escape_string` to prevent SQL injection and other vulnerabilities.
- **Dynamic Dropdowns**: User-friendly dropdown menus for attributes like order status and product categories.
- **Database Triggers**: Automates updates to stock levels and maintains relational integrity.
- **Exception Handling**: Graceful error handling for runtime issues such as invalid operations.
- **Database Views**: Simplified data presentation with views for aggregated reports and summaries.

## Installation

### Prerequisites
- A local server environment (XAMPP).
- PHP (>=7.4 recommended).
- MySQL database.

### Steps
1. Clone this repository:
   ```bash
   git clone https://github.com/yourusername/BOSS-BUY.git

2. Move the project folder:
- Copy the project folder to your server's document root. For example:
- For XAMPP: htdocs.

3. Import the database:
- Open phpMyAdmin in your browser.
- Create a new database (e.g., projtest).
- Import the projtest.sql file provided in the repository into the new database.

4. Configure the database connection:
- Open admin.php and other necessary PHP files.
- Update the database connection details (server, username, password, dbname) to match your server configuration.

5. Start the server and access the project:
- Launch your local server environment.
- Navigate to the project in your browser:
- http://localhost/BOSS-BUY

