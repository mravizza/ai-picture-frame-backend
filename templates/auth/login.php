<div class="login-page">
    <div class="login-box">
        <h1>Login</h1>
        <form method="POST" action="<?= base_url('/admin/login') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" required autofocus
                       value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Anmelden</button>
        </form>
    </div>
</div>
