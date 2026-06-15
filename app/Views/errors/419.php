<section class="card error-page">
    <p class="eyebrow">419</p>
    <h1>Session expired</h1>
    <p>The form could not be submitted safely. Refresh the page and try again.</p>
    <a class="button primary" href="<?= $currentUser ? '/dashboard' : '/login' ?>">Go back</a>
</section>
