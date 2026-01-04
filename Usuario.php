<?php

use DateTime;

require_once 'ConfiguracionApp.php';

/**
 * Clase Usuario - Representa a un usuario del sistema
 * Puede haber MUCHAS instancias (muchos usuarios)
 */
class Usuario
{
    private int $id;
    private string $nombre;
    private string $email;
    private string $password;
    private DateTime $fechaRegistro;
    private DateTime $ultimoAcceso;
    private int $intentosFallidos = 0;
    private bool $bloqueado = false;

    public function __construct(string $nombre, string $email, string $password)
    {
        // Acceder a configuración global para validar password
        $config = ConfiguracionApp::obtenerInstancia();
        $longitudMinima = $config->obtener('longitud_minima_password');

        if (strlen($password) < $longitudMinima) {
            throw new \Exception("La contraseña debe tener al menos {$longitudMinima} caracteres");
        }

        $this->id = random_int(10000, 99999);
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->fechaRegistro = new DateTime();
        $this->ultimoAcceso = new DateTime();
    }

    /**
     * Intento de inicio de sesión
     */
    public function iniciarSesion(string $passwordIntentada): bool
    {
        $config = ConfiguracionApp::obtenerInstancia();

        // Verificar si está bloqueado
        if ($this->bloqueado) {
            throw new \Exception("Usuario bloqueado por exceso de intentos fallidos");
        }

        // Verificar si la aplicación está en mantenimiento
        if ($config->estaEnMantenimiento()) {
            throw new \Exception("La aplicación está en modo mantenimiento");
        }

        // Verificar contraseña
        if (password_verify($passwordIntentada, $this->password)) {
            $this->intentosFallidos = 0;
            $this->ultimoAcceso = new DateTime();
            return true;
        }

        // Si la contraseña es incorrecta (el anterior condicional no se cumple)
        $this->intentosFallidos++;
        $maxIntentos = $config->obtener('max_intentos_login');

        if ($this->intentosFallidos >= $maxIntentos) {
            $this->bloqueado = true;
            throw new \Exception("Usuario bloqueado tras {$maxIntentos} intentos fallidos");
        }

        return false;
    }

    /**
     * Verificar si la sesión ha expirado
     */
    public function sesionExpirada(): bool
    {
        $config = ConfiguracionApp::obtenerInstancia();
        $timeoutMinutos = $config->obtener('sesion_timeout_minutos');
        
        $ahora = new DateTime();
        $diferencia = $ahora->getTimestamp() - $this->ultimoAcceso->getTimestamp();
        $minutosPasados = $diferencia / 60;

        return $minutosPasados > $timeoutMinutos;
    }

    /**
     * Renovar actividad de sesión
     */
    public function renovarSesion(): void
    {
        $this->ultimoAcceso = new DateTime();
    }

    /**
     * Desbloquear usuario
     */
    public function desbloquear(): void
    {
        $this->bloqueado = false;
        $this->intentosFallidos = 0;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getFechaRegistro(): \DateTime { return $this->fechaRegistro; }
    public function getUltimoAcceso(): \DateTime { return $this->ultimoAcceso; }
    public function getIntentosFallidos(): int { return $this->intentosFallidos; }
    public function estaBloqueado(): bool { return $this->bloqueado; }

    public function obtenerInfo(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'fecha_registro' => $this->fechaRegistro->format('d/m/Y H:i:s'),
            'ultimo_acceso' => $this->ultimoAcceso->format('d/m/Y H:i:s'),
            'intentos_fallidos' => $this->intentosFallidos,
            'bloqueado' => $this->bloqueado,
            'sesion_expirada' => $this->sesionExpirada()
        ];
    }
}