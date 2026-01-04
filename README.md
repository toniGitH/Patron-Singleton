# 1️⃣ El patrón Singleton - Guía Completa


Repositorio creado para explicar el patrón Singleton y su implementación mediante un ejemplo práctico en PHP.

<details>
  <summary><h2 style="display: inline-block; margin: 0; padding: 0; border: none;">📑 Índice de contenidos</h2></summary>
  <ul>
    <li>💡 <a href="#-el-patrón-singleton">El patrón Singleton</a>
      <ul>
        <li>👉🏼 <a href="#-por-qué-nos-puede-interesar-tener-una-sola-instancia-de-una-clase">¿Por qué nos puede interesar tener una sola instancia de una clase?</a></li>
        <li>👉🏼 <a href="#-para-qué-se-usa">¿Para qué se usa?</a></li>
        <li>👉🏼 <a href="#-qué-características-debe-tener-un-patrón-singleton">¿Qué características debe tener un patrón Singleton?</a></li>
        <li>👉🏼 <a href="#-qué-supone-usar-singleton">¿Qué supone usar Singleton?</a></li>
      </ul>
    </li>
    <li>🧪 <a href="#-ejemplo-de-implementación-sistema-de-gestión-de-usuarios">Ejemplo de implementación: Sistema de Gestión de Usuarios</a>
      <ul>
        <li>🔧 <a href="#-qué-hace-esta-aplicación-de-ejemplo">¿Qué hace esta aplicación de ejemplo?</a></li>
        <li>🔄 <a href="#-funcionamiento-de-la-aplicación-flujo-completo">Funcionamiento de la aplicación (flujo completo)</a></li>
        <li>🎖️ <a href="#-el-papel-del-singleton">El papel del Singleton</a></li>
        <li>🤼 <a href="#-comparación-con-singleton-vs-sin-singleton">Comparación: Con Singleton vs Sin Singleton</a></li>
        <li>📄 <a href="#-explicación-de-cada-archivo-del-ejemplo">Explicación de cada archivo del ejemplo</a></li>
      </ul>
    </li>
    <li>🚀 <a href="#-cómo-ejecutar-la-aplicación">Cómo ejecutar la aplicación</a></li>
  </ul>
</details>

---

<br>

## 💡 El patrón Singleton

El patrón Singleton es un **patrón de diseño creacional** que garantiza que una clase tenga una **única instancia en toda la aplicación** y proporciona un **punto de acceso global a esa instancia**.

### 👉🏼 ¿Por qué nos puede interesar tener una sola instancia de una clase?

Imagina que tienes una aplicación, con unos usuarios, y estos usuarios están sujetos a una única configuración de la aplicación común para todos los usuarios (número máximo de intentos de login, longitud de password, modo mantenimiento, etc...).

Podrías representar esa configuración como una clase que se encargara de gestionarla.

Como esa configuración DEBE ser común a todos los usuarios:

- no tendría sentido crear un objeto de configuración diferente para cada usuario que se creara, porque todos esos objetos de configuración tendrían la misma información (si tuviéramos 20 usuarios, tendríamos 20 objetos de configuración, cuando en realidad, con uno solo sería suficiente).
- si tuvieramos 20 usuarios, cada uno con su objeto de configuración, si quisiéramos, por ejemplo, poner la aplicación en modo mantenimiento, ¿cuál de esos 20 objetos de configuración tendríamos que modificar?. Si modificáramos sólo uno, el resto de los usuarios no tendrían la configuración actualizada. Por tanto, tendríamos que modificar la configuración de cada uno de esos 20 objetos de configuración, lo que sería absurdo.

Debido a la naturaleza dinámica de una aplicación (parámetros de configuración, conexión a una base de datos, ...), que implica que ésta puede cambiar dinámicamente durante la ejecución de la aplicación, hace que tengamos que asegurarnos de que cuando haya cambios en esos parámetros, todos los elementos de la aplicación puedan ver esos cambios.

El Singleton asegura que solo exista uno y que todos lo compartan, con todas las ventajas que eso conlleva.

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

**1. Constructor privado - ✅ IMPRESCINDIBLE**

```php
private function __construct() {}
```

¿Por qué? Sin esto, cualquiera puede hacer new MiClase() y tendrías múltiples instancias. Es OBLIGATORIO.

**2. Propiedad estática privada que guarda la instancia - ✅ IMPRESCINDIBLE**

```php
private static ?MiClase $instancia = null;
```

¿Por qué? Necesitas un lugar donde guardar la única instancia. Es OBLIGATORIO.

**3. Método estático público para obtener la instancia - ✅ IMPRESCINDIBLE**

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


**4. Prevención de clonación - ⚠️ RECOMENDADO**, pero NO obligatorio (buenas prácticas)

¿Por qué se recomienda evitar la clonación?

Evita que alguien haga:

```php
$instancia1 = MiClase::obtenerInstancia();
$instancia2 = clone $instancia1; // Sin prevenir la clonación, esto crearía una copia de la instancia original
```

El método **__clone()** es un **método mágico nativo de PHP** que se ejecuta automáticamente cuando intentas clonar un objeto con la palabra `clone`.

Es decir, que es un método que nos permitiría, una vez creada la instancia original del singleton `$instancia1` (de la que sólo queremos tener una para toda la aplicación) crear una copia de esta instancia original desde fuera de la clase singleton, de forma que **se rompería el Singleton porque tendríamos dos instancias diferentes de la misma clase**.

La solución está en implementar, **DENTRO** de la propia clase singleton, el método **__clone()** , como **método privado**:

```php
private function __clone() {}
```

De esta forma, **no se podrá clonar la instancia original del singleton desde fuera de la clase singleton**:


```php
$instancia1 = MiClase::obtenerInstancia();
$instancia2 = clone $instancia1; // ❌ ERROR: Cannot access private method __clone()
```

Evidentemente, SÍ podrías clonar la instancia original del singleton desde dentro de la propia clase singleton, pero en este caso, estarías rompiendo el Singleton tú mismo intencionadamente. No tiene sentido hacerlo.

**5. Prevención de deserialización - ⚠️ RECOMENDADO**, pero NO obligatorio (buenas prácticas)

¿Por qué se recomienda evitar la deserialización?

Evita que alguien haga:

```php
$instancia = MiClase::obtenerInstancia();
$serializado = serialize($instancia);

// ...y más tarde...
$instancia2 = unserialize($serializado); // Sin prevenir la deserialización, esto crearía una copia de la instancia original
```

La **serialización** es el proceso de convertir un objeto (o una estructura de datos) en una cadena de texto (string), con el objetivo de poder:

- Guardarlo (en un archivo, base de datos, caché, sesión…)
- Enviarlo (por red, entre procesos…)
- Reconstruirlo más adelante

En PHP, el ejemplo típico es:

```php
$cadena = serialize($objeto);
```

Y la **deserialización** es el proceso contrario:

```php
$objeto = unserialize($cadena);
```

No se puede serializar cualquier objeto ni se puede deserializar cualquier cadena de texto (existen unos límites). 

En la **serialización** interviene el método mágico **__sleep()**, y en la **deserialización** interviene el método mágico **__wakeup()**.

Para mantener la integridad de un Singleton, **la serialización no es un problema**, porque sólo convierte nuestra instancia en una cadena de texto, pero no crea una nueva instancia. Sin embargo, **la deserialización SÍ es un problema**, porque al deserializar la cadena de texto, **SÍ se crearía una nueva instancia de la clase singleton**, lo que rompería el Singleton.

Por eso, si queremos proteger un Singleton ante este problema, debemos actuar sobre la **deserialización**, es decir, sobre el método **__wakeup()**, que es el que se ejecuta cuando se deserializa un objeto. Debemos definirlo en la clase Singleton para que sobreescriba el método mágico **__wakeup()** que viene por defecto en PHP.

Ahora bien, el método **__wakeup()** IGNORA la visibilidad privada, lo que significa que aunque hagamos `private function __wakeup() {}`, PHP lo ignorará y lo seguirá ejecutando como `public function __wakeup() {}`.

Lo que tenemos que hacer entonces es mantenerlo como `public function __wakeup() {}`, y en su interior, lanzar una excepción:

```php
public function __wakeup()
{
    throw new \Exception("No se puede deserializar un Singleton");
}
```

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

<br>

---

<br>

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

#### 🧠 logica.php - Lógica Principal (demostración del Singleton)

**¿Qué hace?**
Es el archivo de ejecución que demuestra el funcionamiento del sistema.

**¿Cuándo se ejecuta?**
Se ejecuta automáticamente al cargar la página web, cuando se llama al archivo index.php.

**¿Qué gestiona?**
Gestiona la creación de usuarios y los inicios de sesión.

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

#### ▶️ index.php - Archivo de entrada (html + logica.php)

**¿Qué hace?**
Es el archivo de entrada que muestra, en HTML, toda la información ejecutada por `logica.php`.

**Acciones que realiza:**

1. **Incluye el archivo de logica.php**: 
   - Este archivo contiene la lógica principal del sistema, la que va a testear nuestro Singleton.

2. **Muestra toda la información** en HTML:
   - Configuración global
   - Usuarios registrados con sus estados
   - Resultados de los intentos de login
   - Demostración del Singleton

#### 🎨 estilos.css - Presentación Visual (estilos css)

**¿Qué hace?**
Proporciona estilos CSS para que la página se vea profesional y sea fácil de leer.

**Características:**
- Diseño responsive (se adapta a diferentes pantallas)
- Código de colores para estados (éxito: verde, error: rojo, advertencia: amarillo)
- Cards para usuarios con efecto hover
- Layout en grid para organización
- Destacados visuales para elementos importantes

<br>

---

<br>

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

>💡
>
> No es obligatorio usar el puerto 8000, puedes usar el que desees, por ejemplo, el 8001.

Con esto, lo que estás haciendo es crear un servidor web php (cuya carpeta raíz es la carpeta seleccionada), que está escuchando en el puerto 8000 (o en el que hayas elegido).

>💡
>
> Si quisieras, podrías crear simultáneamente tantos servidores como proyectos tengas en tu ordenador, siempre y cuando cada uno estuviera escuchando en un puerto diferente (8001, 8002, ...).

5. Ahora, abre tu navegador y accede a http://localhost:8000

Ya podrás visualizar el documento index.php con toda la información del ejemplo.

>💡
>
> No es necesario indicar `http://localhost:8000/index.php` porque el servidor va a buscar dentro de la carpeta raíz (en este caso, en Documentos/htdocs/patrones/singleton), un archivo index.php o index.html de forma automática. Si existe, lo sirve como página principal.
>
> Por eso, estas dos URLs funcionan igual:
>
> http://localhost:8000
>
> http://localhost:8000/index.php