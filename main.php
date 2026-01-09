<?php

    require_once 'ConfiguracionApp.php';
    require_once 'Usuario.php';

    // Obtener la configuración (única instancia)
    $config = ConfiguracionApp::obtenerInstancia();

    // Crear varios usuarios
    $usuarios = [];
    $mensajes = [];

    try {
        // Crear Usuario 1
        $usuario1 = new Usuario('Ana García', 'ana@ejemplo.com', 'Password123');
        $usuarios[] = $usuario1;
        $mensajes[] = ['tipo' => 'exito', 'texto' => "Usuario '{$usuario1->getNombre()}' creado correctamente"];

        // Crear Usuario 2
        $usuario2 = new Usuario('Carlos Ruiz', 'carlos@ejemplo.com', 'Segura456');
        $usuarios[] = $usuario2;
        $mensajes[] = ['tipo' => 'exito', 'texto' => "Usuario '{$usuario2->getNombre()}' creado correctamente"];

        // Crear Usuario 3
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


// Preparación de datos para la visualización CLI
$resultados = [];

// 1. Resultados de creación de usuarios
$salidaUsuarios = "";
foreach ($mensajes as $msj) {
    $icono = $msj['tipo'] === 'exito' ? '✅' : '❌';
    $salidaUsuarios .= "{$icono} {$msj['texto']}\n";
}
$resultados[] = [
    'titulo' => 'CREACIÓN DE USUARIOS',
    'descripcion' => 'Se intentan crear usuarios con diferentes validaciones',
    'salida' => $salidaUsuarios
];

// 2. Resultados de Login
$salidaLogin = "";
foreach ($resultadosLogin as $res) {
    if ($res['tipo'] === 'exito') $icono = '✅';
    elseif ($res['tipo'] === 'advertencia') $icono = '⚠️';
    else $icono = '❌';
    
    $salidaLogin .= "{$icono} User: {$res['usuario']} | {$res['resultado']}\n";
}
$resultados[] = [
    'titulo' => 'SIMULACIÓN DE LOGIN',
    'descripcion' => 'Intentos de inicio de sesión y bloqueo de cuenta',
    'salida' => $salidaLogin
];

// 3. Singleton
$salidaSingleton = "";
$salidaSingleton .= ($sonLaMisma ? "✅ Las instancias son idénticas" : "❌ Las instancias son diferentes") . "\n";
$salidaSingleton .= "Valor cambiado en ref1: 'max_intentos_login' = 5\n";
$salidaSingleton .= "Valor leído en ref2: 'max_intentos_login' = " . $valorDesdeConfig2 . "\n";

$resultados[] = [
    'titulo' => 'PRUEBA DEL PATRÓN SINGLETON',
    'descripcion' => 'Verificamos que múltiples llamadas devuelven la misma instancia y comparten estado',
    'salida' => $salidaSingleton
];

// Ventajas del Singleton
$ventajas = [
    "Garantiza que una clase tenga una única instancia.",
    "Proporciona un punto de acceso global a esa instancia.",
    "Permite compartir estado (configuración, conexión DB) consistentemente."
];

// Si el archivo se ejecuta directamente (CLI) y no es un include
if (count(debug_backtrace()) === 0) {
    echo "========================================\n";
    echo "EJEMPLO SIMPLE DEL PATRÓN SINGLETON\n";
    echo "========================================\n\n";

    foreach ($resultados as $resultado) {
        echo $resultado['titulo'] . "\n";
        echo $resultado['descripcion'] . "\n";
        echo str_repeat("-", 40) . "\n";
        echo $resultado['salida'];
        echo "\n\n";
    }

    echo "============================================\n";
    echo "QUÉ VENTAJA APORTA EL PATRÓN SINGLETON:\n";
    echo "============================================\n";
    echo "Supongamos que queremos asegurar que toda la app use la misma configuración\n";
    foreach ($ventajas as $ventaja) {
        echo "✓ " . $ventaja . "\n";
    }
}