<?php

require_once __DIR__ . "/../helpers/auth.php";

logoutUsuario();

header('Location: /index.php?vista=login&msg=sesion_cerrada');
exit;