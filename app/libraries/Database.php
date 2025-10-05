<?php

/*
 * In-Memory Mock Database Class
 * Simulates database operations without requiring MySQL
 * Stores data in PHP arrays for containerized deployment
 */

class Database
{
    private static $data = [];
    private $currentQuery = '';
    private $bindings = [];
    private $result = [];

    public function __construct()
    {
        // Initialize with sample data if empty
        if (empty(self::$data['posts'])) {
            self::$data['posts'] = [
                [
                    'id' => 1,
                    'title' => '1. Creating a Controller',
                    'content' => '<?php
// app/controllers/Posts.php
class Posts extends Controller {
    public function index() {
        $postModel = $this->model(\'Post\');
        $posts = $postModel->getPosts();

        $data = [
            \'title\' => \'Posts\',
            \'posts\' => $posts
        ];

        $this->view(\'posts/index\', $data);
    }
}',
                    'author' => 'Framework Guide',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ],
                [
                    'id' => 2,
                    'title' => '2. Building a Model',
                    'content' => '<?php
// app/models/Post.php
class Post {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getPosts() {
        $this->db->query(\'SELECT * FROM posts ORDER BY created_at DESC\');
        return $this->db->getAsResultSet();
    }

    public function addPost($data) {
        $this->db->query(\'INSERT INTO posts (title, content, author)
                         VALUES (:title, :content, :author)\');
        $this->db->bind(\':title\', $data[\'title\']);
        $this->db->bind(\':content\', $data[\'content\']);
        $this->db->bind(\':author\', $data[\'author\']);

        return $this->db->execute();
    }
}',
                    'author' => 'Framework Guide',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
                ],
                [
                    'id' => 3,
                    'title' => '3. Rendering Views',
                    'content' => '<!-- app/views/posts/index.php -->
<div class="posts">
    <h1><?php echo $data[\'title\']; ?></h1>

    <?php foreach($data[\'posts\'] as $post): ?>
        <article class="post">
            <h2><?php echo $post->title; ?></h2>
            <p><?php echo $post->content; ?></p>
            <span>By <?php echo $post->author; ?></span>
        </article>
    <?php endforeach; ?>
</div>

// Views automatically receive $data variable
// Header and footer included automatically',
                    'author' => 'Framework Guide',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'id' => 4,
                    'title' => '4. Custom Routing System',
                    'content' => '// URL Structure: /controller/method/params

// Examples:
/posts              → Posts::index()
/posts/show/5       → Posts::show(5)
/users/edit/3       → Users::edit(3)

// Core.php handles routing automatically:
$url = explode(\'/\', filter_var(rtrim($_GET[\'url\'], \'/\'), FILTER_SANITIZE_URL));

$controller = ucwords($url[0]);
$method = $url[1] ?? \'index\';
$params = array_slice($url, 2);

// Instantiate controller and call method
$controller = new $controller;
call_user_func_array([$controller, $method], $params);',
                    'author' => 'Framework Guide',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'id' => 5,
                    'title' => '5. Database Abstraction Layer',
                    'content' => '// PDO wrapper with prepared statements
$db = new Database;

// Query with parameter binding
$db->query(\'SELECT * FROM users WHERE email = :email\');
$db->bind(\':email\', $email);
$user = $db->getAsSingleRecord();

// Prevents SQL injection
// Supports transactions
// Simple, intuitive API

// Available methods:
- query($sql)           // Prepare query
- bind($param, $value)  // Bind parameters
- execute()             // Execute query
- getAsResultSet()      // Get all rows as objects
- getAsSingleRecord()   // Get single row as object
- getRowCount()         // Get affected row count',
                    'author' => 'Framework Guide',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]
            ];

            self::$data['users'] = [
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'email' => 'admin@galaxymvc.com',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }
    }

    // Create base query
    public function query($sql)
    {
        $this->currentQuery = $sql;
        $this->bindings = [];
        $this->result = [];
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        $this->bindings[$param] = $value;
    }

    // Get result set as array of objects
    public function getAsResultSet()
    {
        $this->execute();
        return array_map(function($item) {
            return (object)$item;
        }, $this->result);
    }

    // Execute the prepared statement
    public function execute()
    {
        // Parse the query to determine what operation to perform
        $query = strtolower(trim($this->currentQuery));

        if (strpos($query, 'select * from posts') !== false) {
            // Return all posts
            $this->result = self::$data['posts'];
        } elseif (strpos($query, 'select * from users') !== false) {
            // Return all users
            $this->result = self::$data['users'];
        } elseif (strpos($query, 'insert into posts') !== false) {
            // Add a new post
            $newPost = [
                'id' => count(self::$data['posts']) + 1,
                'title' => $this->bindings[':title'] ?? 'New Post',
                'content' => $this->bindings[':content'] ?? $this->bindings[':body'] ?? 'Post content',
                'author' => $this->bindings[':author'] ?? 'User',
                'created_at' => date('Y-m-d H:i:s')
            ];
            self::$data['posts'][] = $newPost;
            $this->result = [$newPost];
        } elseif (strpos($query, 'update posts') !== false) {
            // Update a post
            foreach (self::$data['posts'] as &$post) {
                if (isset($this->bindings[':id']) && $post['id'] == $this->bindings[':id']) {
                    if (isset($this->bindings[':title'])) $post['title'] = $this->bindings[':title'];
                    if (isset($this->bindings[':content'])) $post['content'] = $this->bindings[':content'];
                    if (isset($this->bindings[':body'])) $post['content'] = $this->bindings[':body'];
                    $this->result = [$post];
                    break;
                }
            }
        } elseif (strpos($query, 'delete from posts') !== false) {
            // Delete a post
            self::$data['posts'] = array_filter(self::$data['posts'], function($post) {
                return !isset($this->bindings[':id']) || $post['id'] != $this->bindings[':id'];
            });
            self::$data['posts'] = array_values(self::$data['posts']);
        } else {
            // Default empty result
            $this->result = [];
        }

        return true;
    }

    // Get a single record as an object
    public function getAsSingleRecord()
    {
        $this->execute();
        return !empty($this->result) ? (object)$this->result[0] : false;
    }

    // Get the database row count
    public function getRowCount()
    {
        return count($this->result);
    }

}