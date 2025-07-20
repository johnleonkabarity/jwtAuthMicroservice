<<<<<<< HEAD
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
=======
**Authentication and Permissions System for Laravel 12 API**
============================================================

This repository contains a robust, secure, and scalable backend system for a REST API, built with Laravel 12. The system is designed to be 100% backend, utilizing JSON Web Token (JWT) based authentication and a granular role-based access control system managed by a superuser.

**✨ Key Features**
------------------

*   **JWT Authentication:** Secure login and registration system that returns a JWT to manage API sessions.
    
*   **Social Login:** Integration with **Google** for third-party authentication, using Laravel Socialite in stateless mode.
    
*   **Role-Based Access Control (RBAC):**
    
*   **Superuser (is\_system\_admin):** A user type with full control over the permissions system.
    
*   **Dynamic Roles & Permissions:** Administrators can dynamically create roles (editor, moderator) and permissions (create-post, delete-user) through the API.
    
*   **Flexible Assignments:** Assign roles to users, permissions to roles, and direct permissions to users.
    
*   **Object-Level Permissions:** Ability to assign permissions on a specific model instance (e.g., "User X can edit the Post with ID 5").
    
*   **RESTful API:** Clear and well-defined endpoints for managing users, authentication, and access control.
    
*   **Security:** Configured with best practices, including route protection middleware, token invalidation (blacklisting), and password hashing.
    
*   **Production-Ready:** A solid, tested system designed to be deployed in a real-world environment.
    

**🚀 Tech Stack**
-----------------

*   **Framework:** Laravel 12
    
*   **JWT Authentication:** tymon/jwt-auth
    
*   **Social Login:** laravel/socialite
    
*   **Database:** Compatible with MySQL, PostgreSQL, SQLite.
    
*   **Testing:** PHPUnit
    

**⚙️ Installation & Setup**
---------------------------

Follow these steps to get the project up and running on your local environment.

### **1\. Clone the Repository**

git clone https://your-repository.com/project.gitcd project

### **2\. Install Dependencies**

Ensure you have Composer installed.

composer install

### **3\. Configure the Environment**

Copy the example .env.example file to create your own configuration file.

cp .env.example .env

Generate the application key.

php artisan key:generate

Generate the secret for JWTs.

php artisan jwt:secret

### **4\. Configure the .env File**

Open the .env file and configure the following variables:

*   **Database:**DB\_CONNECTION=mysqlDB\_HOST=127.0.0.1DB\_PORT=3306DB\_DATABASE=your\_database\_nameDB\_USERNAME=your\_userDB\_PASSWORD=your\_password
    
*   **Application URL:**APP\_URL=http://localhost:8000
    
*   **Google Socialite (Optional):**GOOGLE\_CLIENT\_ID=your-google-client-idGOOGLE\_CLIENT\_SECRET=your-google-client-secretGOOGLE\_REDIRECT\_URI=${APP\_URL}/api/auth/google/callback
    

### **5\. Run Migrations**

This command will create all the necessary tables in your database.

php artisan migrate

### **6\. Create the First Admin User**

1.  Run the application: php artisan serve.
    
2.  Use an API client (like Postman) to register a new user at the POST /api/auth/register endpoint.
    
3.  Access your database and, in the users table, change the value of the is\_system\_admin column to 1 (or true) for the user you just created.
    

Your environment is now ready!

**API Endpoints**
-----------------

The base URL for all endpoints is {{baseUrl}}/api. All protected routes require a Bearer Token in the Authorization header.

### **Authentication (/auth)**

*   POST /register: Registers a new user.
    
*   POST /login: Logs in a user and returns a JWT.
    
*   GET /google/redirect: Gets the redirect URL for Google authentication.
    
*   GET /google/callback: Google's callback to finalize the authentication.
    
*   POST /logout (Protected): Invalidates the current token.
    
*   POST /refresh (Protected): Refreshes an expired token.
    
*   GET /me (Protected): Returns the authenticated user's data.
    

### **Admin Actions (/admin/access-control)**

_(Requires authentication and for the user to be an is\_system\_admin)_

*   POST /roles: Creates a new role.
    
*   POST /permissions: Creates a new permission.
    
*   POST /assign/permission-to-role: Assigns a permission to a role.
    
*   POST /assign/role-to-user: Assigns a role to a user.
    
*   POST /assign/direct-permission-to-user: Assigns a direct permission to a user.
    
*   POST /assign/object-permission: Assigns a permission on a specific object to a user.
    
*   POST /revoke/all-user-access: Revokes all roles and permissions from a user.
    
*   GET /roles: Lists all roles.
    
*   GET /permissions: Lists all permissions.
    
*   GET /users/{user\_uuid}/access: Shows all permissions (direct, role-based, and object-level) for a specific user.
    

### **Regular User Actions (/me/access)**

_(Requires authentication)_

*   GET /: Shows the authenticated user's roles and permissions.
    
*   POST /check-object-permission: Checks if the user has a specific permission for a specific object.
    

**✅ Testing**
-------------

The project includes a suite of unit and feature tests to ensure quality and correct functionality.

To run all tests, use the following command:

php artisan test

**📦 Postman Collection**
-------------------------

A Postman collection has been prepared to facilitate API testing.

1.  **Import:** Import the postman\_collection.json file into your Postman client.
    
2.  **Configure Environment Variables:** In Postman, set up the following variables for the collection:
    

*   baseUrl: Your API's URL (e.g., http://localhost:8000).
    
*   jwt\_token\_admin: Automatically populated upon logging in as an admin.
    
*   jwt\_token\_user: Automatically populated upon logging in as a regular user.
    
*   adminUserId: The UUID of your admin user.
    
*   regularUserId: The UUID of a regular user.
    
*   Other variables like roleName, permissionName, objectId.
    

1.  **Workflow:**
    

*   Register and configure an admin user.
    
*   Register a regular user.
    
*   Log in with both to obtain their tokens.
    
*   Start testing the endpoints!
>>>>>>> 5fd41a39bcdd8c0d15a668129e97b3de93d03106
