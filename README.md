# Sistema de Gestión de Usuarios

## ¿Qué hace este programa?

Este es un **sistema de gestión de usuarios** que permite:

1. **Registrar usuarios** con nombre, email y contraseña
2. **Validar contraseñas** según criterios de seguridad configurados
3. **Gestionar inicios de sesión** con control de intentos fallidos
4. **Bloquear usuarios** automáticamente tras varios intentos fallidos
5. **Controlar sesiones** con sistema de expiración por tiempo de inactividad
6. **Modo mantenimiento** para pausar el acceso de todos los usuarios

### Funcionalidad real del programa

Imagina que es el backend de una aplicación web donde los usuarios se registran e inician sesión. El sistema:

- Crea cuentas de usuario con sus datos personales
- Verifica que las contraseñas cumplan requisitos mínimos de seguridad
- Permite a los usuarios iniciar sesión con sus credenciales
- Protege las cuentas bloqueándolas tras varios intentos fallidos de login
- Cierra sesiones automáticamente si el usuario está inactivo demasiado tiempo
- Puede entrar en modo mantenimiento para actualizaciones (bloqueando todos los accesos)

**Es como un sistema de login real**, similar al que usas en cualquier web (Gmail, Facebook, tu banco, etc.).

---

## Explicación de cada clase

### 1. ConfiguracionApp.php - Configuración Global (SINGLETON)

**¿Qué es?**
Es la clase que implementa el patrón Singleton y contiene la **configuración global de la aplicación**.

**¿Qué gestiona?**
Parámetros que afectan a **TODA la aplicación**, no a usuarios específicos:

- **Nombre de la aplicación**: "Sistema de Gestión de Usuarios"
- **Versión**: "2.1.0"
- **Entorno**: desarrollo o producción
- **Modo mantenimiento**: si está activado, nadie puede acceder
- **Timeout de sesión**: minutos de inactividad antes de cerrar sesión (30 minutos)
- **Máximo intentos de login**: cuántos intentos fallidos antes de bloquear (3)
- **Longitud mínima de password**: caracteres mínimos requeridos (8)
- **Zona horaria**: "Europe/Madrid"
- **Idioma predeterminado**: "es"
- **Registros por página**: 25

**¿Por qué es Singleton?**
Porque **solo debe existir UNA configuración** para toda la aplicación. No tiene sentido que cada usuario tenga su propia configuración diferente. Todos deben seguir las mismas reglas.

**Métodos principales:**
- `obtenerInstancia()`: Devuelve la única instancia de configuración
- `obtener($clave)`: Lee un valor de configuración
- `establecer($clave, $valor)`: Modifica un valor
- `estaEnMantenimiento()`: Comprueba si la app está en mantenimiento
- `activarMantenimiento()` / `desactivarMantenimiento()`: Control del modo mantenimiento

**Características del Singleton:**
- Constructor privado (no se puede hacer `new ConfiguracionApp()`)
- Método estático `obtenerInstancia()` que controla la creación
- No se puede clonar ni deserializar

---

### 2. Usuario.php - Clase de Usuario (CLASE NORMAL, NO SINGLETON)

**¿Qué es?**
Representa a **un usuario individual** del sistema. Puedes crear **MUCHAS instancias** (muchos usuarios).

**¿Qué gestiona?**
Datos y comportamiento específicos de **cada usuario**:

- **ID**: identificador único del usuario
- **Nombre**: nombre completo
- **Email**: dirección de correo
- **Password**: contraseña hasheada (encriptada)
- **Fecha de registro**: cuándo se creó la cuenta
- **Último acceso**: última vez que inició sesión
- **Intentos fallidos**: contador de logins incorrectos
- **Bloqueado**: si está bloqueado o no

**Funcionalidades:**

1. **Crear usuario** (`__construct`)
   - Valida que la contraseña cumpla la longitud mínima (consulta el Singleton)
   - Hashea la contraseña por seguridad
   - Asigna ID único
   - Registra fecha de creación

2. **Iniciar sesión** (`iniciarSesion`)
   - Verifica si el usuario está bloqueado
   - Consulta el Singleton para ver si la app está en mantenimiento
   - Comprueba la contraseña
   - Si es incorrecta, incrementa intentos fallidos
   - Si alcanza el máximo de intentos (según Singleton), bloquea al usuario
   - Si es correcta, reinicia el contador y actualiza último acceso

3. **Control de sesión** (`sesionExpirada`)
   - Consulta el Singleton para obtener el timeout configurado
   - Calcula si han pasado más minutos que el límite
   - Devuelve true si la sesión expiró

4. **Renovar sesión** (`renovarSesion`)
   - Actualiza la marca de tiempo del último acceso
   - Mantiene la sesión activa

5. **Desbloquear** (`desbloquear`)
   - Desbloquea al usuario
   - Reinicia el contador de intentos fallidos

**¿Por qué NO es Singleton?**
Porque necesitas **MUCHOS usuarios**, no solo uno. Cada persona que se registra es un objeto Usuario diferente. Si fuera Singleton, solo podrías tener un usuario en todo el sistema, lo cual no tiene sentido.

**Relación con el Singleton:**
El usuario **consulta** la configuración global (Singleton) para:
- Validar longitud de contraseña al registrarse
- Saber cuántos intentos fallidos se permiten
- Verificar si la app está en mantenimiento
- Calcular si la sesión ha expirado

Pero el usuario **NO modifica** la configuración. Solo la lee para ajustarse a las reglas globales.

---

### 3. index.php - Archivo Principal (DEMOSTRACIÓN)

**¿Qué hace?**
Es el archivo de ejecución que demuestra el funcionamiento del sistema.

**Acciones que realiza:**

1. **Obtiene la configuración** (la única instancia del Singleton)

2. **Crea varios usuarios**:
   - Ana García (contraseña válida)
   - Carlos Ruiz (contraseña válida)
   - Laura Pérez (contraseña válida)
   - Intenta crear Pedro López con contraseña muy corta → FALLA

3. **Simula inicios de sesión**:
   - Ana inicia sesión correctamente
   - Carlos falla 3 veces seguidas → se bloquea automáticamente
   - Laura inicia sesión correctamente

4. **Demuestra el Singleton**:
   - Obtiene la configuración dos veces (`$config1` y `$config2`)
   - Verifica que son el mismo objeto (`===`)
   - Modifica un valor desde `$config1`
   - Lee ese valor desde `$config2` → muestra que el cambio se ve en ambas

5. **Muestra toda la información** en HTML:
   - Configuración global
   - Usuarios registrados con sus estados
   - Resultados de los intentos de login
   - Demostración del Singleton

---

### 4. estilos.css - Presentación Visual

**¿Qué hace?**
Proporciona estilos CSS para que la página se vea profesional y sea fácil de leer.

**Características:**
- Diseño responsive (se adapta a diferentes pantallas)
- Código de colores para estados (éxito: verde, error: rojo, advertencia: amarillo)
- Cards para usuarios con efecto hover
- Layout en grid para organización
- Destacados visuales para elementos importantes

---

## ¿Cómo funciona todo junto?

### Flujo completo:

1. **Al cargar la página**, se crea la instancia única de ConfiguracionApp
2. **Se crean varios usuarios**, cada uno:
   - Consulta la configuración para validar su contraseña
   - Se registra con sus datos únicos
3. **Cada usuario intenta iniciar sesión**:
   - Consulta la configuración para saber cuántos intentos tiene
   - Consulta si la app está en mantenimiento
   - Valida su contraseña
   - Se bloquea si falla demasiadas veces (según la configuración global)
4. **La demostración muestra** que todos los usuarios comparten la misma configuración

### El papel del Singleton:

- **ConfiguracionApp es única**: Solo existe una configuración para todos
- **Todos los usuarios la comparten**: No importa cuántos usuarios crees, todos leen la misma configuración
- **Cambios globales**: Si cambias algo en la configuración (ej: máximo de intentos de 3 a 5), el cambio afecta a **todos** los usuarios instantáneamente
- **Consistencia**: No hay riesgo de que usuarios diferentes tengan reglas diferentes

### Comparación: Con Singleton vs Sin Singleton

**SIN Singleton (problema):**
```
Usuario1 → ConfiguracionApp #1 (max_intentos = 3)
Usuario2 → ConfiguracionApp #2 (max_intentos = 3)
Usuario3 → ConfiguracionApp #3 (max_intentos = 3)

Cambias max_intentos a 5 en #1
Usuario1 → max_intentos = 5
Usuario2 → max_intentos = 3 (no se enteró del cambio)
Usuario3 → max_intentos = 3 (no se enteró del cambio)
```
**Resultado: CAOS e inconsistencia**

**CON Singleton (solución):**
```
Usuario1 → ConfiguracionApp (única)
Usuario2 → ConfiguracionApp (única)  
Usuario3 → ConfiguracionApp (única)

Cambias max_intentos a 5
TODOS los usuarios ven el cambio inmediatamente
```
**Resultado: Consistencia total**

---

## Resumen

**Función del programa**: Sistema de gestión de usuarios con registro, login, bloqueos automáticos y control de sesiones.

**ConfiguracionApp (Singleton)**: Configuración global única que todos los usuarios consultan y comparten.

**Usuario (clase normal)**: Representa a cada usuario individual. Puede haber muchos. Consulta la configuración global para ajustarse a las reglas.

**Relación**: Los usuarios **dependen** de la configuración para funcionar correctamente, pero **no son parte** de la configuración. La configuración es transversal a toda la aplicación, los usuarios son entidades individuales del dominio.