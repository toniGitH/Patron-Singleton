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