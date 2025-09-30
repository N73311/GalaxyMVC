# GalaxyMVC

A lightweight PHP MVC framework built from scratch with custom routing and PDO database abstraction.

[View Portfolio](https://zachayers.io) | [Live Demo](https://www.galaxymvc.zachayers.io)

## About

GalaxyMVC is a minimalist PHP framework implementing the MVC architectural pattern. Features custom routing, PDO database wrapper with prepared statements, autoloading, and simple templating without the overhead of larger frameworks.

## Built With

- PHP 7.2+
- MySQL/MariaDB
- Apache (mod_rewrite)
- PDO
- Composer (optional)

## Getting Started

### Prerequisites

- PHP 7.2 or higher
- MySQL 5.6+ or MariaDB
- Apache with mod_rewrite enabled

### Installation

```bash
git clone https://github.com/N73311/GalaxyMVC.git
cd GalaxyMVC
```

Configure database in `app/config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'galaxymvc');
define('ROOT_URL', 'http://localhost/GalaxyMVC/');
```

### Development

Point your web server document root to the `public/` directory.

Create database:
```bash
mysql -u root -p
CREATE DATABASE galaxymvc;
```

## Project Structure

```
GalaxyMVC/
├── app/
│   ├── config/        # Configuration
│   ├── controllers/   # Controllers
│   ├── libraries/     # Core framework
│   ├── models/        # Models
│   └── views/         # View templates
└── public/            # Web root
    └── index.php      # Front controller
```

## Routing

```
http://example.com/[controller]/[method]/[params]
```

Examples:
- `/` - Home controller, index method
- `/posts` - Posts controller, index method
- `/posts/show/5` - Posts controller, show method, id=5

## License

Licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for details.

## Author

Zachariah Ayers - [zachayers.io](https://zachayers.io)