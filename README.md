# 1️⃣ Patrón Singleton - Guía Completa

## 🎯 Finalidad de este repositorio

Repositorio creado para explicar el patrón Singleton y su implementación mediante un ejemplo práctico en PHP.

---

## 💡 El patrón Singleton

El patrón Singleton es un **patrón de diseño creacional** que garantiza que una clase tenga una **única instancia en toda la aplicación** y proporciona un **punto de acceso global a esa instancia**.

Imagina que tienes una aplicación y necesitas un objeto de configuración. 

No tiene sentido crear 10 objetos de configuración diferentes porque todos tendrían la misma información. El Singleton asegura que solo exista uno y que todos lo compartan.

### 👉🏼 ¿Para qué se usa?

El Singleton se utiliza cuando:

- Necesitas exactamente una instancia de una clase en toda tu aplicación
- Quieres controlar el acceso a un recurso compartido (como una conexión a base de datos, un archivo de configuración, un sistema de logs, etc.)
- Necesitas un punto de acceso global a esa instancia

Ejemplos del mundo real:

- Configuración de la aplicación: Solo necesitas un objeto con la configuración
- Gestor de base de datos: Una única conexión compartida
- Sistema de logs: Un único archivo donde escribir todos los registros
- Caché: Un único espacio de almacenamiento temporal
- Gestor de sesiones: Un único controlador de las sesiones de usuario

### 👉🏼 ¿Qué características debe tener un patrón Singleton?

✅ **IMPRESCINDIBLE** (lo MÍNIMO para que sea Singleton)

Solo hay 3 cosas absolutamente necesarias:

**1. Constructor privado:**

```php
private function __construct() {}
```

¿Por qué? Sin esto, cualquiera puede hacer new MiClase() y tendrías múltiples instancias. Es OBLIGATORIO.

**2. Propiedad estática privada que guarda la instancia:**

```php
private static ?MiClase $instancia = null;
```

¿Por qué? Necesitas un lugar donde guardar la única instancia. Es OBLIGATORIO.

**3. Método estático público para obtener la instancia:**

```php
public static function obtenerInstancia(): MiClase
{
    if (self::$instancia === null) {
        self::$instancia = new self();
    }
    return self::$instancia;
}
```

¿Por qué? Es la única forma de acceder a la instancia. Es OBLIGATORIO.

Con solo estas 3 cosas ya tienes un Singleton funcional.

⚠️ **RECOMENDADO** (buenas prácticas, pero NO obligatorio)

**4. Prevención de clonación:**

```php
private function __clone() {}
```

¿Por qué recomendado?

El método __clone() es un método mágico **nativo** de PHP que se ejecuta automáticamente cuando intentas clonar un objeto con la palabra clone.

Evita que alguien haga:

```php
$instancia1 = MiClase::obtenerInstancia();
$instancia2 = clone $instancia1; // Sin __clone privado, esto crea una copia
```

Es decir, que es un método que nos permitiría, una vez creada la instancia original del singleton $instancia1, crear una copia de esta instancia original desde fuera de la clase singleton, de forma que se rompería el Singleton porque tendríamos dos instancias diferentes de la misma clase.

Si implementamos el método __clone() dentro de la propia clase singleton como método privado, entonces no se podrá clonar la instancia original del singleton desde fuera de la clase singleton:

```php
$instancia1 = MiClase::obtenerInstancia();
$instancia2 = clone $instancia1; // ❌ ERROR: Cannot access private method __clone()
```

Evidentemente, sí podrías clonar la instancia original del singleton desde dentro de la propia clase singleton, pero en este caso, estarías rompiendo el Singleton tú mismo intencionadamente. No tiene sentido hacerlo.

¿Es obligatorio prevenir la clonación? NO. El Singleton funciona sin esto, pero es una buena práctica para evitar "trampas".

**5. Prevención de serialización:**

```php
public function __wakeup()
{
    throw new \Exception("No se puede deserializar un Singleton");
}
```

¿Por qué recomendado? Evita que alguien haga:

```php
$instancia = MiClase::obtenerInstancia();
$serializado = serialize($instancia);
// ...más tarde...
$instancia2 = unserialize($serializado); // Sin __wakeup, esto crea otra instancia
```

**¿Es obligatorio?** NO. El Singleton funciona sin esto, pero es una buena práctica para casos avanzados.

### 👉🏼 ¿Qué supone usar Singleton?

Ventajas:

- Garantiza una única instancia
- Acceso controlado y global
- Ahorro de memoria (una sola instancia)
- Inicialización diferida (se crea solo cuando se necesita)

Desventajas:

- Puede dificultar las pruebas unitarias
- Viola el principio de responsabilidad única (gestiona su propia creación)
- Puede introducir dependencias ocultas
- En aplicaciones multihilo puede requerir sincronización

---

## 🧪 Ejemplo de implementación: Sistema de Gestión de Usuarios

### 🔧 ¿Qué hace esta aplicación de ejemplo?

Es un **sistema de gestión de usuarios** que:

1. **Permite registrar usuarios** con nombre, email y contraseña.
2. **Verifica contraseñas** para asegurarse de que cumplan con los requisitos de seguridad configurados.
3. **Permite iniciar sesión** a los usuarios con sus credenciales, con control de intentos fallidos.
4. **Protege las cuentas** bloqueándolas tras varios intentos fallidos de login.
5. **Cierra sesiones automáticamente** si el usuario está inactivo demasiado tiempo.
6. **Puede entrar en modo mantenimiento** para actualizaciones (bloqueando todos los accesos).

**Es como un sistema de login real**, similar al que usas en cualquier web (Gmail, Facebook, tu banco, etc.).

>⚠️ **IMPORTANTE**
>
> No es un sistema de login y registro completo. No es un frontend con un formulario de registro y login, sino sólo una parte de la lógica de dicha implementación para ejemplificar cómo, con el patrón Singleton, aunque haya varias instancias de usuarios que dependen de una configuración global, todas ellas comparten esa misma configuración, una misma y única instancia de configuración.

### 🔄 Funcionamiento de la aplicación (flujo completo)

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

### 🎖️ El papel del Singleton:

- **ConfiguracionApp es única**: Solo existe una configuración para todos
- **Todos los usuarios la comparten**: No importa cuántos usuarios crees, todos leen la misma configuración
- **Cambios globales**: Si cambias algo en la configuración (ej: máximo de intentos de 3 a 5), el cambio afecta a **todos** los usuarios instantáneamente
- **Consistencia**: No hay riesgo de que usuarios diferentes tengan reglas diferentes

### 🤼 Comparación: Con Singleton vs Sin Singleton

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

### 📄 Explicación de cada archivo del ejemplo

#### ⚙️ ConfiguracionApp.php - Configuración Global (SINGLETON)

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

#### 👤 Usuario.php - Clase de Usuario (CLASE NORMAL, NO SINGLETON)

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

#### 📌 index.php - Archivo Principal (DEMOSTRACIÓN)

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

#### 4. 🎨 estilos.css - Presentación Visual

**¿Qué hace?**
Proporciona estilos CSS para que la página se vea profesional y sea fácil de leer.

**Características:**
- Diseño responsive (se adapta a diferentes pantallas)
- Código de colores para estados (éxito: verde, error: rojo, advertencia: amarillo)
- Cards para usuarios con efecto hover
- Layout en grid para organización
- Destacados visuales para elementos importantes

---

## 🚀 Cómo ejecutar la aplicación

1. Crea la carpeta del proyecto (por ejemplo, patrones/singleton) dentro de la carpeta htdocs (o equivalente según la versión de XAMPP y sistema operativo que uses).
2. Guarda en esa carpeta los archivos PHP y CSS.

#### 📍 Para ejecutarlo mediante XAMPP:

3. Arranca XAMPP.
4. Accede a index.php desde tu navegador (por ejemplo: http://localhost/patrones/singleton/index.php)

#### 📍 Para ejecutarlo usando el servidor web interno de PHP

PHP trae un servidor web ligero que sirve para desarrollo. No necesitas instalar Apache ni XAMPP.

3. Abre la terminal y navega a la carpeta de tu proyecto:

```bash
cd ~/Documentos/htdocs/patrones/singleton
```
4. Dentro de esa ubicación, ejecuta:

```bash
php -S localhost:8000
```

Con esto, lo que estás haciendo es crear un servidor web php, que está escuchando en el puerto 8000 (o en el que hayas elegido) cuya carpeta raíz es la carpeta seleccionada.
   
>💡 **TIP **
>
> No es obligatorio usar el puerto 8000, puedes usar el que desees, por ejemplo, el 8001.

5. Ahora, abre tu navegador y accede a http://localhost:8000

Ya podrás visualizar el documento index.php con toda la información del ememplo.

>💡 **TIP*
>
> No es necesario indicar `http://localhost:8000/index.php` porque el servidor va a buscar dentro de la carpeta raíz (Documentos/htdocs/patrones/singleton, en este caso), un archivo index.php o index.html de forma automática. Si existe, lo sirve como página principal.
>
> Por eso, estas dos URLs funcionan igual:
>
> http://localhost:8000
> http://localhost:8000/index.php