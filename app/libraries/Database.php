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
                    'title' => 'Building Type-Safe Controllers with Dependency Injection',
                    'content' => '<?php
/**
 * Modern Controller with Type Hints and Constructor Injection
 * Demonstrates SOLID principles and clean architecture
 */
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use App\Services\ValidationService;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class PostController extends Controller
{
    private Post $postModel;
    private ValidationService $validator;

    public function __construct(Post $postModel, ValidationService $validator)
    {
        $this->postModel = $postModel;
        $this->validator = $validator;
    }

    /**
     * Display paginated list of posts with eager loading
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = (int) ($request->query(\'page\') ?? 1);
        $perPage = 15;

        $posts = $this->postModel
            ->with([\'author\', \'comments\'])
            ->published()
            ->orderBy(\'created_at\', \'DESC\')
            ->paginate($page, $perPage);

        return $this->view(\'posts/index\', [
            \'posts\' => $posts,
            \'pagination\' => $posts->getPaginationData()
        ]);
    }

    /**
     * Create new post with validation and error handling
     */
    public function store(Request $request): Response
    {
        $rules = [
            \'title\' => \'required|max:255|unique:posts,title\',
            \'content\' => \'required|min:50\',
            \'category_id\' => \'required|exists:categories,id\'
        ];

        if (!$this->validator->validate($request->all(), $rules)) {
            return $this->json([
                \'errors\' => $this->validator->errors()
            ], 422);
        }

        $post = $this->postModel->create($request->only([
            \'title\', \'content\', \'category_id\'
        ]));

        return $this->json([
            \'message\' => \'Post created successfully\',
            \'data\' => $post
        ], 201);
    }
}',
                    'author' => 'Senior PHP Architect',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
                ],
                [
                    'id' => 2,
                    'title' => 'Eloquent-Style Model with Query Builder & Relationships',
                    'content' => '<?php
/**
 * Active Record Pattern Implementation
 * Fluent query builder with relationship support
 */
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\QueryBuilder;

class Post extends Model
{
    protected string $table = \'posts\';
    protected array $fillable = [\'title\', \'content\', \'author_id\', \'category_id\'];
    protected array $hidden = [\'deleted_at\'];
    protected array $casts = [
        \'published_at\' => \'datetime\',
        \'is_featured\' => \'boolean\'
    ];

    /**
     * Relationship: Post belongs to an Author
     */
    public function author(): QueryBuilder
    {
        return $this->belongsTo(User::class, \'author_id\');
    }

    /**
     * Relationship: Post has many Comments
     */
    public function comments(): QueryBuilder
    {
        return $this->hasMany(Comment::class, \'post_id\')
            ->where(\'approved\', true)
            ->orderBy(\'created_at\', \'DESC\');
    }

    /**
     * Scope: Only published posts
     */
    public function scopePublished(QueryBuilder $query): QueryBuilder
    {
        return $query->whereNotNull(\'published_at\')
            ->where(\'published_at\', \'<=\', date(\'Y-m-d H:i:s\'));
    }

    /**
     * Accessor: Get formatted excerpt
     */
    public function getExcerptAttribute(): string
    {
        return substr(strip_tags($this->content), 0, 200) . \'...\';
    }

    /**
     * Business Logic: Publish post with timestamp
     */
    public function publish(): bool
    {
        $this->published_at = date(\'Y-m-d H:i:s\');
        $this->is_featured = true;

        return $this->save();
    }

    /**
     * Static Factory: Create with validation
     */
    public static function createValidated(array $data): self
    {
        $validator = new ValidationService();

        if (!$validator->validate($data, self::validationRules())) {
            throw new ValidationException($validator->errors());
        }

        return self::create($data);
    }
}',
                    'author' => 'Senior PHP Architect',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ],
                [
                    'id' => 3,
                    'title' => 'Advanced Query Builder with Method Chaining',
                    'content' => '<?php
/**
 * Fluent Query Builder with Expression Trees
 * Supports complex queries with elegant syntax
 */
declare(strict_types=1);

namespace App\Core;

class QueryBuilder
{
    private Database $db;
    private array $wheres = [];
    private array $joins = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(Database $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Add WHERE clause with operator support
     */
    public function where(string $column, string $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = \'=\';
        }

        $this->wheres[] = [
            \'type\' => \'basic\',
            \'column\' => $column,
            \'operator\' => $operator,
            \'value\' => $value,
            \'boolean\' => \'AND\'
        ];

        return $this;
    }

    /**
     * Eager load relationships to prevent N+1 queries
     */
    public function with(array $relations): self
    {
        foreach ($relations as $relation) {
            $this->eagerLoad[] = $relation;
        }
        return $this;
    }

    /**
     * Complex join with multiple conditions
     */
    public function joinWhere(string $table, callable $callback): self
    {
        $joinClause = new JoinClause($table);
        $callback($joinClause);

        $this->joins[] = $joinClause;
        return $this;
    }

    /**
     * Execute and return collection of models
     */
    public function get(): Collection
    {
        $sql = $this->toSql();
        $bindings = $this->getBindings();

        $this->db->query($sql);
        foreach ($bindings as $key => $value) {
            $this->db->bind($key, $value);
        }

        $results = $this->db->getAsResultSet();

        return new Collection(
            array_map(fn($item) => $this->hydrate($item), $results)
        );
    }

    /**
     * Usage Example:
     */
    public static function example(): void
    {
        $posts = Post::query()
            ->with([\'author\', \'comments\', \'tags\'])
            ->where(\'status\', \'published\')
            ->where(\'views\', \'>\', 1000)
            ->joinWhere(\'categories\', function($join) {
                $join->on(\'posts.category_id\', \'=\', \'categories.id\')
                     ->where(\'categories.active\', true);
            })
            ->orderBy(\'created_at\', \'DESC\')
            ->limit(10)
            ->get();

        // Result: Collection of Post models with eager-loaded relationships
        // Zero N+1 query problems, fully type-safe
    }
}',
                    'author' => 'Senior PHP Architect',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
                ],
                [
                    'id' => 4,
                    'title' => 'Service Layer Pattern with Business Logic Isolation',
                    'content' => '<?php
/**
 * Service Layer separates business logic from controllers
 * Promotes reusability, testability, and single responsibility
 */
declare(strict_types=1);

namespace App\Services;

use App\Models\{Post, User, Category};
use App\Events\PostPublished;
use App\Exceptions\{UnauthorizedException, ValidationException};
use App\Core\{EventDispatcher, Cache};

class PostPublishingService
{
    private Post $postModel;
    private EventDispatcher $events;
    private Cache $cache;

    public function __construct(
        Post $postModel,
        EventDispatcher $events,
        Cache $cache
    ) {
        $this->postModel = $postModel;
        $this->events = $events;
        $this->cache = $cache;
    }

    /**
     * Publish a post with authorization, validation, and side effects
     *
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function publish(int $postId, User $user): Post
    {
        $post = $this->postModel->findOrFail($postId);

        // Authorization check
        if (!$user->can(\'publish\', $post)) {
            throw new UnauthorizedException(
                "User {$user->id} cannot publish post {$postId}"
            );
        }

        // Business rule: Featured posts require review
        if ($post->is_featured && !$post->reviewed_at) {
            throw new ValidationException(
                \'Featured posts must be reviewed before publishing\'
            );
        }

        // Database transaction for data consistency
        $this->postModel->transaction(function() use ($post) {
            $post->published_at = now();
            $post->status = \'published\';
            $post->save();

            // Update denormalized counters
            $post->author->increment(\'posts_count\');
            $post->category->increment(\'posts_count\');
        });

        // Fire domain event for async processing
        $this->events->dispatch(new PostPublished($post));

        // Invalidate relevant caches
        $this->cache->tags([\'posts\', "author:{$post->author_id}"])
            ->flush();

        // Return published post with relationships
        return $post->fresh([\'author\', \'category\']);
    }

    /**
     * Schedule post for future publishing
     */
    public function schedulePublishing(Post $post, \DateTime $publishAt): void
    {
        $post->scheduled_at = $publishAt->format(\'Y-m-d H:i:s\');
        $post->status = \'scheduled\';
        $post->save();

        Queue::dispatch(new PublishScheduledPost($post))
            ->delay($publishAt);
    }
}',
                    'author' => 'Senior PHP Architect',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'id' => 5,
                    'title' => 'Repository Pattern for Data Access Abstraction',
                    'content' => '<?php
/**
 * Repository Pattern decouples business logic from data access
 * Enables easy testing with mock repositories
 */
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use App\Core\{Cache, Collection};

interface PostRepositoryInterface
{
    public function findById(int $id): ?Post;
    public function getFeatured(int $limit = 5): Collection;
    public function searchByTitle(string $query): Collection;
}

class PostRepository implements PostRepositoryInterface
{
    private Post $model;
    private Cache $cache;
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(Post $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * Find post by ID with automatic caching
     */
    public function findById(int $id): ?Post
    {
        return $this->cache->remember(
            "posts:{$id}",
            self::CACHE_TTL,
            fn() => $this->model->with([\'author\', \'tags\'])
                ->find($id)
        );
    }

    /**
     * Get featured posts with cache-aside pattern
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->cache->remember(
            "posts:featured:{$limit}",
            self::CACHE_TTL,
            fn() => $this->model
                ->where(\'is_featured\', true)
                ->where(\'status\', \'published\')
                ->with(\'author\')
                ->orderBy(\'views\', \'DESC\')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Full-text search with relevance scoring
     */
    public function searchByTitle(string $query): Collection
    {
        $sanitized = $this->sanitizeSearchQuery($query);

        return $this->model
            ->whereRaw(
                "MATCH(title, content) AGAINST(? IN BOOLEAN MODE)",
                [$sanitized]
            )
            ->selectRaw(
                "*, MATCH(title, content) AGAINST(?) as relevance",
                [$sanitized]
            )
            ->where(\'status\', \'published\')
            ->orderBy(\'relevance\', \'DESC\')
            ->limit(50)
            ->get();
    }

    /**
     * Batch update with optimistic locking
     */
    public function batchUpdateStatus(array $ids, string $status): int
    {
        return $this->model
            ->whereIn(\'id\', $ids)
            ->where(\'updated_at\', \'<\', now()->subMinutes(5))
            ->update([
                \'status\' => $status,
                \'updated_at\' => now()
            ]);
    }

    private function sanitizeSearchQuery(string $query): string
    {
        return addslashes(trim($query));
    }
}',
                    'author' => 'Senior PHP Architect',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'id' => 6,
                    'title' => 'Middleware Pipeline for Request Processing',
                    'content' => '<?php
/**
 * Middleware Pipeline Implementation
 * Chain of responsibility pattern for request/response handling
 */
declare(strict_types=1);

namespace App\Core;

class Pipeline
{
    private array $middleware = [];
    private $destination;

    public function send($passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function then(callable $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    private function carry(): callable
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    return $pipe($passable, $stack);
                }

                // Resolve middleware from container
                $middleware = $this->container->make($pipe);
                return $middleware->handle($passable, $stack);
            };
        };
    }
}

/**
 * Example Middleware: Rate Limiting
 */
class RateLimitMiddleware
{
    private Cache $cache;
    private int $maxAttempts = 60;
    private int $decayMinutes = 1;

    public function handle(Request $request, callable $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $attempts = $this->cache->get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            throw new TooManyRequestsException(
                \'Rate limit exceeded. Try again in 1 minute.\'
            );
        }

        $this->cache->increment($key);
        $this->cache->expire($key, $this->decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        return $response->withHeaders([
            \'X-RateLimit-Limit\' => $this->maxAttempts,
            \'X-RateLimit-Remaining\' => max(0, $this->maxAttempts - $attempts - 1)
        ]);
    }

    private function resolveRequestSignature(Request $request): string
    {
        return sprintf(
            \'rate_limit:%s:%s\',
            $request->ip(),
            $request->path()
        );
    }
}

// Usage in Router:
$router->group([\'middleware\' => [RateLimitMiddleware::class]], function($router) {
    $router->post(\'/api/posts\', [PostController::class, \'store\']);
});',
                    'author' => 'Senior PHP Architect',
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