<div class="hero">
    <div class="container">
        <h1 class="hero-title">GalaxyMVC</h1>
        <p class="hero-subtitle">A Lightweight PHP MVC Framework Built From Scratch</p>
        <p class="hero-description">Learn the fundamentals of MVC architecture with a custom-built framework featuring routing, database abstraction, and clean separation of concerns.</p>
    </div>
</div>

<div class="architecture-section">
    <div class="container">
        <h2 class="section-title">MVC Architecture Pattern</h2>
        <div class="mvc-diagram">
            <div class="mvc-box model">
                <h3>Model</h3>
                <p>Database abstraction with PDO</p>
                <code>app/models/</code>
            </div>
            <div class="mvc-box view">
                <h3>View</h3>
                <p>PHP templating system</p>
                <code>app/views/</code>
            </div>
            <div class="mvc-box controller">
                <h3>Controller</h3>
                <p>Business logic layer</p>
                <code>app/controllers/</code>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="section-title">Framework Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>🚀 Custom Routing</h3>
                <p>URL pattern: <code>/controller/method/params</code></p>
                <p>Clean URLs with automatic controller/method resolution</p>
            </div>
            <div class="feature-card">
                <h3>💾 Database Abstraction</h3>
                <p>PDO wrapper with prepared statements</p>
                <p>Protection against SQL injection built-in</p>
            </div>
            <div class="feature-card">
                <h3>🎯 Autoloading</h3>
                <p>PSR-4 compatible class autoloading</p>
                <p>No manual require statements needed</p>
            </div>
            <div class="feature-card">
                <h3>📦 Lightweight</h3>
                <p>Zero dependencies, pure PHP</p>
                <p>Perfect for learning MVC concepts</p>
            </div>
        </div>
    </div>
</div>

<div class="code-examples">
    <div class="container">
        <h2 class="section-title">Code Examples</h2>

        <?php foreach ($data as $post) : ?>
            <div class="example-card">
                <h3><?php echo $post->title; ?></h3>
                <p class="example-author">By <?php echo $post->author; ?> • <?php echo date('M d, Y', strtotime($post->created_at)); ?></p>
                <div class="example-content">
                    <?php echo $post->content; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="footer-section">
    <div class="container">
        <p>Built by <a href="https://zachayers.io" target="_blank">Zachariah Ayers</a> to demonstrate custom PHP MVC framework development</p>
    </div>
</div>
