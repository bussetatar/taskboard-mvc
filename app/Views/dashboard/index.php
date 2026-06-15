<div class="page-heading">
    <div>
        <p class="eyebrow">Overview</p>
        <h1>Your dashboard</h1>
        <p class="muted">Create, organize, and complete your tasks.</p>
    </div>
    <a class="button primary" href="/tasks/create">New task</a>
</div>

<section class="stats" aria-label="Task statistics">
    <article class="stat card">
        <span>Total</span>
        <strong><?= e($stats['total']) ?></strong>
    </article>
    <article class="stat card">
        <span>Pending</span>
        <strong><?= e($stats['pending']) ?></strong>
    </article>
    <article class="stat card">
        <span>Completed</span>
        <strong><?= e($stats['completed']) ?></strong>
    </article>
</section>

<div class="dashboard-grid">
    <aside>
        <section class="card">
            <h2>Categories</h2>
            <form method="post" action="/categories">
                <?= csrf_field() ?>
                <label for="category-name">New category</label>
                <div class="input-action">
                    <input id="category-name" name="name" type="text" value="<?= e($old['name'] ?? '') ?>" maxlength="80" required>
                    <button class="button secondary" type="submit">Add</button>
                </div>
            </form>

            <?php if ($categories === []): ?>
                <p class="empty-small">No categories yet.</p>
            <?php else: ?>
                <ul class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <form method="post" action="/categories/<?= e($category['id']) ?>/update">
                                <?= csrf_field() ?>
                                <input aria-label="Category name" name="name" type="text" value="<?= e($category['name']) ?>" maxlength="80" required>
                                <button class="small-button" type="submit">Save</button>
                            </form>
                            <span><?= e($category['task_count']) ?> task<?= (int) $category['task_count'] === 1 ? '' : 's' ?></span>
                            <form method="post" action="/categories/<?= e($category['id']) ?>/delete">
                                <?= csrf_field() ?>
                                <button class="danger-link" type="submit">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </aside>

    <section>
        <div class="card task-panel">
            <div class="panel-heading">
                <h2>Tasks</h2>
                <form class="filters" method="get" action="/dashboard">
                    <label>
                        <span>Status</span>
                        <select name="status">
                            <option value="">All</option>
                            <option value="pending"<?= selected($filters['status'], 'pending') ?>>Pending</option>
                            <option value="completed"<?= selected($filters['status'], 'completed') ?>>Completed</option>
                        </select>
                    </label>
                    <label>
                        <span>Category</span>
                        <select name="category_id">
                            <option value="">All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= e($category['id']) ?>"<?= selected($filters['category_id'], $category['id']) ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button class="button secondary" type="submit">Filter</button>
                </form>
            </div>

            <?php if ($tasks === []): ?>
                <div class="empty">
                    <h3>No tasks found</h3>
                    <p>Create your first task or change the filters.</p>
                    <a class="button primary" href="/tasks/create">Create task</a>
                </div>
            <?php else: ?>
                <div class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <article class="task <?= $task['status'] === 'completed' ? 'is-complete' : '' ?>">
                            <div class="task-main">
                                <div class="task-title-row">
                                    <h3><?= e($task['title']) ?></h3>
                                    <span class="badge <?= e($task['status']) ?>"><?= e(ucfirst($task['status'])) ?></span>
                                </div>

                                <?php if ($task['description'] !== ''): ?>
                                    <p><?= nl2br(e($task['description'])) ?></p>
                                <?php endif; ?>

                                <div class="task-meta">
                                    <span>Category: <?= e($task['category_name'] ?? 'None') ?></span>
                                    <span>Due: <?= e($task['due_date'] ?? 'No date') ?></span>
                                </div>
                            </div>

                            <div class="task-actions">
                                <form method="post" action="/tasks/<?= e($task['id']) ?>/toggle">
                                    <?= csrf_field() ?>
                                    <button class="small-button" type="submit">
                                        <?= $task['status'] === 'completed' ? 'Reopen' : 'Complete' ?>
                                    </button>
                                </form>
                                <a class="small-button" href="/tasks/<?= e($task['id']) ?>/edit">Edit</a>
                                <form method="post" action="/tasks/<?= e($task['id']) ?>/delete">
                                    <?= csrf_field() ?>
                                    <button class="danger-link" type="submit">Delete</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
