<?php
$section_contents = <<<EOD
<section class="section" id="section-login">
    <h2>Inicia sesión</h2>
    <p>Ingresa tu carnet y contraseña para ingresar.</p>
    <form class="section__container" id="login-form" action="index.php" method="post">
        <label for="login-carnet" class="form__label">Carnet</label>
        <input type="text" class="form__input" id="login-carnet" name="login-carnet" required pattern=".{10}" maxlength="10" placeholder="AB12345678" autofocus>
        <label for="login-clave" class="form__label">Contraseña</label>
        <input type="password" class="form__input" id="login-clave" name="login-clave" required pattern=".{1,}" maxlength="255" placeholder="********">
        <input id="btn-login-entrar" class="button" type="submit" name="login-submit" value="Entrar">
        <div id="login-error" class="error-message"></div>
    </form>
</section>
EOD;
