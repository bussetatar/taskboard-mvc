<?php
$source = $old !== [] ? $old : ($task ?? []);
?>

<div class="form-shell">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Task details</p>
            <h1><?= e($heading) ?></h1>
        </div>
        <a class="button secondary" href="/dashboard">Back</a>
    </div>

    <section class="card">
        <form method="post" action="<?= e($action) ?>">
            <?= csrf_field() ?>

            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="<?= e($source['title'] ?? '') ?>" maxlength="120" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" maxlength="1000"><?= e($source['description'] ?? '') ?></textarea>

            <div class="form-grid">
                <label>
                    <span>Category</span>
                    <select name="category_id">
                        <option value="">No category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['id']) ?>"<?= selected($source['category_id'] ?? '', $category['id']) ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Due date</span>
                    <input name="due_date" type="date" value="<?= e($source['due_date'] ?? '') ?>">
                </label>
            </div>

            <label>
                <span>Status</span>
                <select name="status">
                    <option value="pending"<?= selected($source['status'] ?? 'pending', 'pending') ?>>Pending</option>
                    <option value="completed"<?= selected($source['status'] ?? '', 'completed') ?>>Completed</option>
                </select>
            </label>

            <div class="form-actions">
                <button class="button primary" type="submit">Save task</button>
                <a class="button secondary" href="/dashboard">Cancel</a>
            </div>
        </form>
    </section>
</div>
