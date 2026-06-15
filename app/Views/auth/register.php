<section class="auth-card card">
    <p class="eyebrow">Get started</p>
    <h1>Create account</h1>
    <p class="muted">A password must contain at least eight characters.</p>

    <form method="post" action="/register">
        <?= csrf_field() ?>

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="<?= e($old['name'] ?? '') ?>" maxlength="80" required autocomplete="name">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" maxlength="190" required autocomplete="email">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password">

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">

        <button class="button primary full" type="submit">Create account</button>
    </form>

    <p class="auth-switch">Already registered? <a href="/login">Sign in</a>.</p>
</section>
