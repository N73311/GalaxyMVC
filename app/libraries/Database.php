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
                    'title' => 'Welcome to GalaxyMVC',
                    'content' => 'This is a lightweight PHP MVC framework running with an in-memory database.',
                    'author' => 'Admin',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'id' => 2,
                    'title' => 'MVC Architecture',
                    'content' => 'GalaxyMVC implements the Model-View-Controller pattern for clean code organization.',
                    'author' => 'Developer',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'id' => 3,
                    'title' => 'Containerized Deployment',
                    'content' => 'This application runs in Docker with PHP-FPM and Nginx, using in-memory data storage.',
                    'author' => 'DevOps',
                    'created_at' => date('Y-m-d H:i:s')
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