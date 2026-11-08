REGLAS GENERALES PARA ANTIGRAVITY:
- El proyecto utiliza Laravel 12, `laravel/ui`, Bootstrap 5 y Vite.
- Toda la estructura base de Laravel reside dentro del subdirectorio `Application/`.
- No crear bases de datos nuevas; usar la existente manteniendo la nomenclatura.
- Usar el modelo `Role` nativo; no hay paquetes externos como Spatie.
- No avanzar al siguiente hito hasta que el usuario confirme que el código del hito actual fue probado y aprobado.

HITO 1: ESTRUCTURA DE BASE DE DATOS Y MODELOS
- Alterar tabla `users` para añadir `induction_status` ('pending', 'completed').
- Crear migraciones para: `document_categories`, `document_subcategories`, `corporate_contents`, `user_content_progress`, `user_induction_evidences`.
- Relacionar `corporate_contents` con el modelo `Role` nativo.
- Crear Modelos Eloquent con relaciones y fillables dentro de `Application/app/Models/`.

HITO 2: INTERCEPTOR DE ACCESO (MIDDLEWARE)
- Crear un Middleware `CheckInductionStatus` en `Application/app/Http/Middleware/`.
- Lógica: Si el usuario autenticado tiene `induction_status === 'pending'`, redirigir forzosamente a la ruta de inducción. Excluir las rutas de logout y las propias de la inducción para evitar bucles.
- Registrar el middleware en la configuración de la aplicación.

HITO 3: BACKOFFICE - GESTIÓN DE CONTENIDO (ADMINISTRACIÓN)
- Crear Controladores para gestionar categorías, subcategorías y subir contenido corporativo.
- Crear rutas agrupadas bajo un middleware de administrador.
- Crear vistas Blade usando Bootstrap 5 para el CRUD de contenidos, manteniendo el diseño del dashboard actual.

HITO 4: FRONTEND - PANTALLA DE INDUCCIÓN Y DASHBOARD
- Crear Controlador y Vista Blade (Bootstrap 5) para la inducción del usuario.
- Interfaz limpia, sin distracciones, donde el usuario consuma el material.
- Lógica backend para requerir el consumo secuencial de los contenidos.

HITO 5: MOTOR DE VALIDACIÓN Y CAPTURA DE EVIDENCIA
- Implementar JavaScript nativo o con Axios en la vista de inducción para el control de tiempo (`required_time_seconds`).
- Integrar la API MediaDevices del navegador para capturar fotografía al finalizar.
- Crear endpoints para recibir confirmación de lectura, guardar la foto, registrar IP y cambiar `induction_status` a 'completed'.

HITO 6: BACKOFFICE - PANEL DE AUDITORÍA
- Crear Controlador y Vista Blade (Bootstrap 5) en el backoffice para auditoría gerencial.
- Mostrar tabla inmutable con: Empleado, Fecha/Hora, IP y miniatura de la evidencia.