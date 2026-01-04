<?php

/**
 * Singleton - Configuración global de la aplicación
 * Gestiona parámetros que afectan a TODA la aplicación
 */
class ConfiguracionApp
{
    private static ?ConfiguracionApp $instancia = null; // CARACTERÍSTICA OBLIGATORIA EN UN SINGLETON

    private array $configuracion = [
        'nombre_aplicacion' => 'Sistema de Gestión de Usuarios',
        'patron' => 'Singleton',
        'version' => '1.0.0',
        'entorno' => 'desarrollo', // desarrollo | produccion
        'modo_mantenimiento' => false,
        'sesion_timeout_minutos' => 30,
        'max_intentos_login' => 3,
        'longitud_minima_password' => 8,
        'zona_horaria' => 'Europe/Madrid',
        'idioma_predeterminado' => 'es',
        'registros_por_pagina' => 25
    ];

    /**
     * Constructor privado - Se ejecuta solo una vez
     */
    private function __construct() // CARACTERÍSTICA OBLIGATORIA EN UN SINGLETON
    {
        // Configuración global de la aplicación (toma los valores establecidos en el array)
        
        // Aplicar zona horaria
        date_default_timezone_set($this->configuracion['zona_horaria']);
    }

    /**
     * Obtener la única instancia de configuración
     */
    public static function obtenerInstancia(): ConfiguracionApp // CARACTERÍSTICA OBLIGATORIA EN UN SINGLETON
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /**
     * Obtener un valor de configuración
     */
    public function obtener(string $clave): mixed
    {
        return $this->configuracion[$clave] ?? null;
    }

    /**
     * Establecer un valor de configuración
     */
    public function establecer(string $clave, mixed $valor): void
    {
        $this->configuracion[$clave] = $valor;
    }

    /**
     * Verificar si la aplicación está en mantenimiento
     */
    public function estaEnMantenimiento(): bool
    {
        return $this->configuracion['modo_mantenimiento'] === true;
    }

    /**
     * Activar modo mantenimiento
     */
    public function activarMantenimiento(): void
    {
        $this->configuracion['modo_mantenimiento'] = true;
    }

    /**
     * Desactivar modo mantenimiento
     */
    public function desactivarMantenimiento(): void
    {
        $this->configuracion['modo_mantenimiento'] = false;
    }

    /**
     * Obtener toda la configuración
     */
    public function obtenerTodo(): array
    {
        return $this->configuracion;
    }

    /**
     * Evita la clonación
     */
    private function __clone() {} // CARACTERÍSTICA RECOMENDADA EN UN SINGLETON

    /**
     * Evita la deserialización
     */
    public function __wakeup() // CARACTERÍSTICA RECOMENDADA EN UN SINGLETON
    {
        throw new \Exception("No se puede deserializar un Singleton");
    }
}