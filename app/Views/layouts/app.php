<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'TaskBoard') ?> | TaskBoard</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="<?= $currentUser ? '/dashboard' : '/login' ?>">TaskBoard</a>
            <nav aria-label="Main navigation">
                <?php if ($currentUser): ?>
                    <span class="user-name"><?= e($currentUser['name']) ?></span>
                    <form class="inline" method="post" action="/logout">
                        <?= csrf_field() ?>
                        <button class="link-button" type="submit">Sign out</button>
                    </form>
                <?php else: ?>
                    <a href="/login">Sign in</a>
                    <a href="/register">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container page">
        <?php if (is_string($flashSuccess) && $flashSuccess !== ''): ?>
            <div class="alert success" role="status"><?= e($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (is_string($flashError) && $flashError !== ''): ?>
            <div class="alert error" role="alert"><?= e($flashError) ?></div>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div class="alert error" role="alert">
                <strong>Please correct the following:</strong>
                <ul>
                    <?php foreach ($errors as $message): ?>
                        <li><?= e($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="container">TaskBoard - pure PHP MVC project</div>
    </footer>
</body>
</html>
