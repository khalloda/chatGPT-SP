<?php
use App\Core\Helpers;
use App\Core\I18n;
/** Purpose: Minimal login screen for first boot */
?>
<h1><?= Helpers::e(I18n::t('auth.login')) ?></h1>
<form method="post" action="/login">
  <input type="hidden" name="_token" value="<?= Helpers::csrfToken() ?>">
  <div>
    <label><?= Helpers::e(I18n::t('auth.email')) ?></label>
    <input type="email" name="email" value="<?= Helpers::e(Helpers::old('email', 'admin@example.com')) ?>" required>
  </div>
  <div>
    <label><?= Helpers::e(I18n::t('auth.password')) ?></label>
    <input type="password" name="password" value="Admin@123" required>
  </div>
  <button type="submit">Login</button>
</form>
<p>First-run hint: use admin@example.com / Admin@123 to reach dashboard (models & users arrive in Part 2).</p>

