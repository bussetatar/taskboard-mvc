<section class="auth-card card">
    <p class="eyebrow">Welcome back</p>
    <h1>Sign in</h1>
    <p class="muted">Manage your tasks and categories in one place.</p>

    <form method="post" action="/login">
        <?= csrf_field() ?>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <button class="button primary full" type="submit">Sign in</button>
    </form>

    <p class="auth-switch">No account yet? <a href="/register">Create one</a>.</p>
</section>
