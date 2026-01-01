<?php

require_once 'ConfiguracionApp.php';
require_once 'Usuario.php';

// Obtener la configuración (única instancia)
$config = ConfiguracionApp::obtenerInstancia();

// Crear varios usuarios
$usuarios = [];
$mensajes = [];

try {
    // Usuario 1
    $usuario1 = new Usuario('Ana García', 'ana@ejemplo.com', 'Password123');
    $usuarios[] = $usuario1;
    $mensajes[] = ['tipo' => 'exito', 'texto' => "Usuario '{$usuario1->getNombre()}' creado correctamente"];

    // Usuario 2
    $usuario2 = new Usuario('Carlos Ruiz', 'carlos@ejemplo.com', 'Segura456');
    $usuarios[] = $usuario2;
    $mensajes[] = ['tipo' => 'exito', 'texto' => "Usuario '{$usuario2->getNombre()}' creado correctamente"];

    // Usuario 3
    $usuario3 = new Usuario('Laura Pérez', 'laura@ejemplo.com', 'MiClave789');
    $usuarios[] = $usuario3;
    $mensajes[] = ['tipo' => 'exito', 'texto' => "Usuario '{$usuario3->getNombre()}' creado correctamente"];

    // Intentar crear usuario con contraseña corta (falla según configuración)
    try {
        $usuarioInvalido = new Usuario('Pedro López', 'pedro@ejemplo.com', '123'); // Muy corta
    } catch (\Exception $e) {
        $mensajes[] = ['tipo' => 'error', 'texto' => "Error al crear usuario: " . $e->getMessage()];
    }

} catch (\Exception $e) {
    $mensajes[] = ['tipo' => 'error', 'texto' => $e->getMessage()];
}

// Simulación de inicios de sesión
$resultadosLogin = [];

// Login exitoso de Ana
try {
    if ($usuario1->iniciarSesion('Password123')) {
        $resultadosLogin[] = ['usuario' => $usuario1->getNombre(), 'resultado' => 'Sesión iniciada correctamente', 'tipo' => 'exito'];
    }
} catch (\Exception $e) {
    $resultadosLogin[] = ['usuario' => $usuario1->getNombre(), 'resultado' => $e->getMessage(), 'tipo' => 'error'];
}

// Intentos fallidos de Carlos (3 intentos incorrectos para bloquearlo)
for ($i = 1; $i <= 3; $i++) {
    try {
        if (!$usuario2->iniciarSesion('passwordIncorrecta')) {
            $resultadosLogin[] = [
                'usuario' => $usuario2->getNombre(),
                'resultado' => "Intento {$i} fallido - Contraseña incorrecta",
                'tipo' => 'advertencia'
            ];
        }
    } catch (\Exception $e) {
        $resultadosLogin[] = ['usuario' => $usuario2->getNombre(), 'resultado' => $e->getMessage(), 'tipo' => 'error'];
        break;
    }
}

// Login exitoso de Laura
try {
    if ($usuario3->iniciarSesion('MiClave789')) {
        $resultadosLogin[] = ['usuario' => $usuario3->getNombre(), 'resultado' => 'Sesión iniciada correctamente', 'tipo' => 'exito'];
    }
} catch (\Exception $e) {
    $resultadosLogin[] = ['usuario' => $usuario3->getNombre(), 'resultado' => $e->getMessage(), 'tipo' => 'error'];
}

// Demostración del Singleton: dos referencias a la misma instancia
$config1 = ConfiguracionApp::obtenerInstancia();
$config2 = ConfiguracionApp::obtenerInstancia();
$sonLaMisma = $config1 === $config2;

// Modificar configuración desde una referencia
$config1->establecer('max_intentos_login', 5);
$valorDesdeConfig2 = $config2->obtener('max_intentos_login');

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