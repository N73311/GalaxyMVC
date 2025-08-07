# GalaxyMVC - Lightweight PHP MVC Framework

A clean, lightweight MVC (Model-View-Controller) framework built from scratch in PHP. GalaxyMVC provides a simple yet powerful foundation for building web applications with proper separation of concerns, routing, and database abstraction.

## Overview

GalaxyMVC is a minimalist PHP framework that implements the MVC architectural pattern without the overhead of larger frameworks. It features a custom routing system, PDO database wrapper, autoloading, and a simple templating system. Perfect for learning MVC concepts or building small to medium-sized applications where full frameworks like Laravel would be overkill.

## Features

- **Clean MVC Architecture** - Proper separation of models, views, and controllers
- **Custom Routing System** - SEO-friendly URLs with parameter support
- **PDO Database Wrapper** - Secure database operations with prepared statements
- **Autoloading** - PSR-4 compatible autoloader for classes
- **Simple Templating** - PHP-based views with layout support
- **Configuration Management** - Centralized configuration system
- **Security Features** - URL sanitization and SQL injection protection
- **Lightweight** - No dependencies, pure PHP implementation

## Requirements

- PHP 7.2 or higher
- MySQL 5.6+ or MariaDB
- Apache with mod_rewrite enabled
- Composer (optional, for dependency management)

## Installation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/N73311/GalaxyMVC.git
cd GalaxyMVC

# Set up your web server document root to point to the public folder
# Configure your database settings in app/config/config.php

# Create your database
mysql -u root -p
CREATE DATABASE galaxymvc;
USE galaxymvc;

# Import any SQL schema if provided
# mysql -u root -p galaxymvc < schema.sql
```

### Apache Configuration

Ensure your `.htaccess` file in the public directory contains:

```apache
Options -MultiViews
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
```

## Project Structure

```
GalaxyMVC/
├── app/
│   ├── bootstrapper.php    # Application bootstrapper
│   ├── config/
│   │   └── config.php      # Configuration constants
│   ├── controllers/        # Controller classes
│   │   └── Home.php        # Default home controller
│   ├── libraries/          # Core framework classes
│   │   ├── Controller.php  # Base controller class
│   │   ├── Core.php        # Router and dispatcher
│   │   └── Database.php    # PDO database wrapper
│   ├── models/             # Model classes
│   │   └── Post.php        # Example model
│   └── views/              # View templates
│       ├── Home/           # Home controller views
│       └── inc/            # Shared view components
└── public/                 # Public web root
    ├── index.php           # Front controller
    ├── css/                # Stylesheets
    └── js/                 # JavaScript files
```

## Configuration

### Database Configuration

Edit `app/config/config.php`:

```php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'galaxymvc');

// Application URL
define('ROOT_URL', 'http://localhost/GalaxyMVC/');
define('SITE_NAME', 'My Application');
```

## Usage Guide

### Creating a Controller

```php
// app/controllers/Posts.php
class Posts extends Controller
{
    private $postModel;
    
    public function __construct()
    {
        // Load model
        $this->postModel = $this->model('Post');
    }
    
    public function index()
    {
        // Get posts from model
        $posts = $this->postModel->getAllPosts();
        
        // Load view with data
        $data = [
            'title' => 'All Posts',
            'posts' => $posts
        ];
        
        $this->view('posts/index', $data);
    }
    
    public function show($id)
    {
        $post = $this->postModel->getPostById($id);
        $this->view('posts/show', ['post' => $post]);
    }
}
```

### Creating a Model

```php
// app/models/Post.php
class Post
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database;
    }
    
    public function getAllPosts()
    {
        $this->db->query('SELECT * FROM posts ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    public function getPostById($id)
    {
        $this->db->query('SELECT * FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createPost($data)
    {
        $this->db->query('INSERT INTO posts (title, body) VALUES (:title, :body)');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        return $this->db->execute();
    }
}
```

### Creating a View

```php
// app/views/posts/index.php
<?php require HEADER_PATH; ?>

<h1><?php echo $data['title']; ?></h1>

<div class="posts">
    <?php foreach($data['posts'] as $post) : ?>
        <div class="post">
            <h2><?php echo $post->title; ?></h2>
            <p><?php echo $post->body; ?></p>
            <a href="<?php echo ROOT_URL; ?>posts/show/<?php echo $post->id; ?>">
                Read More
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php require FOOTER_PATH; ?>
```

## Routing

GalaxyMVC uses a simple routing pattern:

```
http://example.com/[controller]/[method]/[params]
```

Examples:
- `http://example.com/` → Home controller, index method
- `http://example.com/posts` → Posts controller, index method
- `http://example.com/posts/show/5` → Posts controller, show method, id=5
- `http://example.com/users/edit/john` → Users controller, edit method, username=john

## Database Usage

The Database class provides a PDO wrapper with prepared statements:

```php
// Query with parameters
$db = new Database();
$db->query('SELECT * FROM users WHERE email = :email AND active = :active');
$db->bind(':email', $email);
$db->bind(':active', 1);

// Get single record
$user = $db->single();

// Get all records
$users = $db->resultSet();

// Get row count
$count = $db->rowCount();

// Execute insert/update/delete
$db->execute();
```

## Best Practices

### Security
- Always use prepared statements (built into Database class)
- Sanitize user input
- Validate data in models before database operations
- Keep sensitive configuration out of version control

### Architecture
- Keep controllers thin - business logic belongs in models
- Use meaningful names for controllers, models, and methods
- Follow PSR standards for code style
- Separate concerns - views should only display data

## Extending the Framework

### Adding Middleware

```php
// app/libraries/Middleware.php
class Middleware
{
    public static function authenticate()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }
    }
}
```

### Helper Functions

```php
// app/helpers/functions.php
function redirect($page)
{
    header('Location: ' . ROOT_URL . $page);
    exit;
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}
```

## Troubleshooting

### Common Issues

1. **404 Errors**: Check Apache mod_rewrite is enabled
2. **Database Connection**: Verify credentials in config.php
3. **Class Not Found**: Check file/class naming conventions
4. **Blank Pages**: Enable PHP error reporting for debugging

### Debug Mode

```php
// Add to config.php for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Future Enhancements

- Session management wrapper
- Form validation library
- CSRF protection
- File upload handling
- Caching system
- CLI tools for scaffolding
- Unit testing setup

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Inspired by CodeIgniter and Laravel routing concepts
- PDO best practices from PHP documentation
- MVC pattern implementation guidelines
