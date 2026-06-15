<section class="card error-page">
    <p class="eyebrow">404</p>
    <h1>Page not found</h1>
    <p>The requested page does not exist or you do not have access to it.</p>
    <a class="button primary" href="<?= $currentUser ? '/dashboard' : '/login' ?>">Go back</a>
</section>
