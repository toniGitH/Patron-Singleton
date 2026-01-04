<?php
    require_once 'logica.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config->obtener('nombre_aplicacion')) ?></title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?= htmlspecialchars($config->obtener('nombre_aplicacion')) ?></h1>
            <h2 class="patron">Ejemplo de Patrón <?= htmlspecialchars($config->obtener('patron')) ?></h2>
            <p class="version">Versión <?= htmlspecialchars($config->obtener('version')) ?></p>
        </header>

        <div class="seccion">
            <h2>📋 Configuración Global de la Aplicación</h2>
            <div class="config-grid">
                <?php foreach ($config->obtenerTodo() as $clave => $valor): ?>
                    <div class="config-item">
                        <strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $clave))) ?>:</strong>
                        <span><?= is_bool($valor) ? ($valor ? 'Sí' : 'No') : htmlspecialchars($valor) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="seccion">
            <h2>👥 Usuarios Registrados</h2>
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="mensaje <?= $mensaje['tipo'] ?>">
                    <?= htmlspecialchars($mensaje['texto']) ?>
                </div>
            <?php endforeach; ?>
            
            <div class="usuarios-grid">
                <?php foreach ($usuarios as $usuario): ?>
                    <?php $info = $usuario->obtenerInfo(); ?>
                    <div class="usuario-card <?= $info['bloqueado'] ? 'bloqueado' : '' ?>">
                        <h3><?= htmlspecialchars($info['nombre']) ?></h3>
                        <p><strong>ID:</strong> <?= $info['id'] ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($info['email']) ?></p>
                        <p><strong>Registrado:</strong> <?= $info['fecha_registro'] ?></p>
                        <p><strong>Último acceso:</strong> <?= $info['ultimo_acceso'] ?></p>
                        <p><strong>Intentos fallidos:</strong> <?= $info['intentos_fallidos'] ?></p>
                        <p class="estado <?= $info['bloqueado'] ? 'bloqueado' : 'activo' ?>">
                            <?= $info['bloqueado'] ? '🔒 BLOQUEADO' : '✓ ACTIVO' ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="seccion">
            <h2>🔐 Resultados de Inicio de Sesión</h2>
            <div class="login-resultados">
                <?php foreach ($resultadosLogin as $resultado): ?>
                    <div class="login-item <?= $resultado['tipo'] ?>">
                        <strong><?= htmlspecialchars($resultado['usuario']) ?>:</strong>
                        <?= htmlspecialchars($resultado['resultado']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="seccion destacado">
            <h2>🔄 Demostración del Patrón Singleton</h2>
            <div class="demo-singleton">
                <p class="<?= $sonLaMisma ? 'exito' : 'error' ?>">
                    <strong>¿$config1 y $config2 son la misma instancia?</strong> 
                    <?= $sonLaMisma ? 'SÍ ✓' : 'NO ✗' ?>
                </p>
                <p class="info">
                    Se modificó 'max_intentos_login' a 5 desde $config1.<br>
                    Al consultar desde $config2, el valor es: <strong><?= $valorDesdeConfig2 ?></strong>
                </p>
                <p class="explicacion">
                    Esto demuestra que ambas variables apuntan al mismo objeto en memoria. 
                    Cualquier cambio en la configuración se refleja globalmente.
                </p>
            </div>
        </div>
    </div>
</body>
</html>