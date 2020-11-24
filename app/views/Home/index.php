<ul>
    <?php foreach ($data as $post) : ?>
        <li>
            <h2>
                <?php echo $post->id . ": " . $post->title; ?>
            </h2>
            <p>
                <?php echo $post->content; ?>
            </p>
        </li>
    <?php endforeach; ?>
</ul>
