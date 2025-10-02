<!-- Contenedor principal de la página -->
<div class="container-xxl flex-grow-1 container-p-y">

    <?php
    // CONEXIÓN Y CONFIGURACIÓN INICIAL
    
    // Incluir el archivo que contiene la conexión a la base de datos
    include "conexion.php";

    // Verificar si la conexión a la base de datos fue exitosa
    // Si falla, mostrar error y detener la ejecución del script
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // CONFIGURACIÓN DE PAGINACIÓN
    
    // Definir cuántos usuarios mostrar por página (10 en este caso)
    $resultadosPorPagina = 10;
    
    // Obtener el número de página actual desde la URL
    // Si no se especifica página, usar la página 1 por defecto
    // (int) convierte el valor a número entero para seguridad
    $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    // Calcular desde qué registro empezar en la consulta SQL
    // Ejemplo: Página 3 → (3-1)*10 = 20 → empezar desde el registro 20
    $inicio = ($paginaActual - 1) * $resultadosPorPagina;

    // RECOLECCIÓN DE FILTROS DE BÚSQUEDA
    
    // Obtener y limpiar el filtro de nombre (elimina espacios en blanco)
    $nombreFiltro = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
    
    // Obtener y limpiar el filtro de apellido
    $apellidoFiltro = isset($_GET['apellido']) ? trim($_GET['apellido']) : '';
    
    // Obtener y limpiar el filtro de nombre de usuario
    $usuarioFiltro = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
    
    // Obtener filtro de fecha (no necesita trim porque es campo date)
    $fechaFiltro = isset($_GET['fecha']) ? $_GET['fecha'] : '';

    // CONSTRUCCIÓN DE LA CONSULTA SQL CON FILTROS
    
    // Condición base: solo traer usuarios con estado 'habilitado'
    $whereCondition = "WHERE U.estado = 'habilitado'";

    // Si hay filtro de nombre, agregarlo a la consulta
    if (!empty($nombreFiltro)) {
        // real_escape_string() previene inyección SQL escapando caracteres peligrosos
        // LIKE '%valor%' busca el texto en cualquier parte del nombre
        $whereCondition .= " AND U.nombre LIKE '%" . $conexion->real_escape_string($nombreFiltro) . "%'";
    }
    
    // Si hay filtro de apellido, agregarlo a la consulta
    if (!empty($apellidoFiltro)) {
        $whereCondition .= " AND U.apellido LIKE '%" . $conexion->real_escape_string($apellidoFiltro) . "%'";
    }
    
    // Si hay filtro de nombre de usuario, agregarlo a la consulta
    if (!empty($usuarioFiltro)) {
        $whereCondition .= " AND U.nombre_usuario LIKE '%" . $conexion->real_escape_string($usuarioFiltro) . "%'";
    }
    
    // Si hay filtro de fecha, agregarlo a la consulta
    if (!empty($fechaFiltro)) {
        // DATE() extrae solo la parte de fecha (sin hora) para comparar
        $whereCondition .= " AND DATE(U.fecha_creacion) = '" . $conexion->real_escape_string($fechaFiltro) . "'";
    }

    // CONSULTAS A LA BASE DE DATOS
    
    // Consulta principal: trae los usuarios con información de género
    // INNER JOIN une la tabla usuarios con géneros usando la clave foránea
    // ORDER BY ordena por ID de usuario ascendente
    // LIMIT limita los resultados según la paginación
    $sql = "SELECT U.*, G.* FROM usuarios U 
            INNER JOIN generos G ON G.id_genero = U.genero 
            $whereCondition
            ORDER BY U.id_usuario ASC
            LIMIT $inicio, $resultadosPorPagina";

    // Ejecutar la consulta principal
    $result = $conexion->query($sql);

    // Consulta para contar el total de usuarios (para la paginación)
    $totalQuery = "SELECT COUNT(*) as total FROM usuarios U $whereCondition";
    $totalResult = $conexion->query($totalQuery);
    
    // Obtener el número total de usuarios que coinciden con los filtros
    // Si la consulta falla, usar 0 como valor por defecto
    $totalUsuarios = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
    
    // Calcular el total de páginas necesarias
    // ceil() redondea hacia arriba: ej: 25 usuarios / 10 por página = 3 páginas
    $totalPaginas = ceil($totalUsuarios / $resultadosPorPagina);

    // Verificar si hubo error en la consulta principal
    if (!$result) {
        die("Error en la consulta: " . $conexion->error);
    }
    ?>

    <!-- TÍTULO DE LA PÁGINA -->
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Administración /</span> Usuarios Habilitados
    </h4>

    <!-- TARJETA PRINCIPAL QUE CONTIENE TODO EL CONTENIDO -->
    <div class="card">
        <!-- ENCABEZADO DE LA TARJETA -->
        <div class="card-header border-bottom">
            <!-- SECCIÓN PARA AGREGAR NUEVO USUARIO -->
            
            <!-- Título de la sección -->
            <h6 class="card-title">Añadir un usuario</h6>
            
            <!-- Botón para agregar nuevo usuario - solo visible si tiene permiso 'crear' -->
            <?php if (in_array('crear', $permisos_usuario)): ?>
                <a href="./vista-registrar-usuario.php" class="btn btn-primary">+ Nuevo usuario</a>
            <?php endif; ?>

            <!-- Línea separadora -->
            <br>
            <hr>

            <!-- FORMULARIO DE BÚSQUEDA -->
            <h6 class="card-title">Buscar usuario</h6>
            
            <!-- Formulario que envía datos por método GET (los parámetros van en la URL) -->
            <form action="" method="GET" class="row g-3">
                <!-- Campo oculto para resetear a página 1 al hacer nueva búsqueda -->
                <input type="hidden" name="page" value="1">

                <!-- CAMPO DE FILTRO POR NOMBRE -->
                <div class="col-md-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <!-- 
                    htmlspecialchars() convierte caracteres especiales en entidades HTML
                    Esto previene ataques XSS (Cross-Site Scripting)
                    value mantiene el texto buscado después de enviar el formulario
                    -->
                    <input type="text" name="nombre" id="nombre" class="form-control"
                        value="<?= htmlspecialchars($nombreFiltro) ?>" placeholder="Filtrar por nombre">
                </div>

                <!-- CAMPO DE FILTRO POR APELLIDO -->
                <div class="col-md-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control"
                        value="<?= htmlspecialchars($apellidoFiltro) ?>" placeholder="Filtrar por apellido">
                </div>

                <!-- CAMPO DE FILTRO POR NOMBRE DE USUARIO -->
                <div class="col-md-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control"
                        value="<?= htmlspecialchars($usuarioFiltro) ?>" placeholder="Filtrar por nombre de usuario">
                </div>

                <!-- CAMPO DE FILTRO POR FECHA DE CREACIÓN -->
                <div class="col-md-3">
                    <label for="fecha" class="form-label">Fecha creación</label>
                    <input type="date" name="fecha" id="fecha" class="form-control"
                        value="<?= htmlspecialchars($fechaFiltro) ?>">
                </div>

                <!-- BOTONES DE ACCIÓN DEL FORMULARIO -->
                <div class="col-md-12 d-flex justify-content-end">
                    <!-- Botón para enviar el formulario y buscar -->
                    <button type="submit" class="btn btn-primary me-2">Buscar</button>
                    
                    <!-- 
                    Botón para limpiar filtros - solo visible si hay algún filtro activo
                    Verifica si al menos uno de los filtros tiene valor
                    -->
                    <?php if ($nombreFiltro || $apellidoFiltro || $usuarioFiltro || $fechaFiltro): ?>
                        <a href="usuarios.php" class="btn btn-secondary">Limpiar filtros</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <!-- ENCABEZADO DE LA TABLA -->
                <thead>
                    <tr>
                        <th>Persona</th>
                        <th>Nombre de Usuario</th>
                        <th>Fecha de Creación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                
                <!-- CUERPO DE LA TABLA -->
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <!-- 
                        Si hay resultados en la consulta, recorrer cada fila con while
                        fetch_assoc() obtiene una fila como array asociativo
                        El bucle se ejecuta mientras haya filas disponibles
                        -->
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <!-- COLUMNA: INFORMACIÓN PERSONAL CON FOTO -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        // Obtener la ruta de la imagen del usuario
                                        $imgPath = $row['img'];
                                        
                                        // Verificar si la imagen existe y mostrar la foto
                                        // Si no existe, mostrar un ícono por defecto
                                        if (!empty($imgPath) && file_exists("../ruta/donde/guardas/las/imagenes/$imgPath")) {
                                            // Mostrar imagen del usuario
                                            echo '<img src="./img_usuario/' . $imgPath . '" alt="Avatar" class="rounded-circle me-3" width="40">';
                                        } else {
                                            // Mostrar ícono por defecto (silueta de persona)
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-fill rounded-circle me-3" viewBox="0 0 16 16">
                                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>';
                                        }
                                        ?>
                                        <div>
                                            <!-- Mostrar nombre y apellido del usuario -->
                                            <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                                            <div class="text-muted small"><?= htmlspecialchars($row['apellido']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- COLUMNA: NOMBRE DE USUARIO -->
                                <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                                
                                <!-- COLUMNA: FECHA DE CREACIÓN FORMATEADA -->
                                <td>
                                    <?php
                                    // Convertir la fecha de la base de datos a objeto DateTime
                                    $fecha = new DateTime($row['fecha_creacion']);
                                    
                                    // Configurar localización para español (aunque no se usa directamente)
                                    setlocale(LC_TIME, 'es_ES.UTF-8');
                                    
                                    // Array para traducir meses de inglés a español
                                    $meses = [
                                        'January' => 'Enero',
                                        'February' => 'Febrero',
                                        'March' => 'Marzo',
                                        'April' => 'Abril',
                                        'May' => 'Mayo',
                                        'June' => 'Junio',
                                        'July' => 'Julio',
                                        'August' => 'Agosto',
                                        'September' => 'Septiembre',
                                        'October' => 'Octubre',
                                        'November' => 'Noviembre',
                                        'December' => 'Diciembre'
                                    ];
                                    
                                    // Extraer día, mes y año de la fecha
                                    $dia = $fecha->format('d');       // Día con 2 dígitos (01-31)
                                    $mes = $meses[$fecha->format('F')]; // Mes en español
                                    $anio = $fecha->format('Y');     // Año con 4 dígitos
                                    
                                    // Mostrar fecha formateada: "15 de Enero del 2024"
                                    echo "$dia de $mes del $anio";
                                    ?>
                                </td>
                                
                                <!-- COLUMNA: ESTADO DEL USUARIO -->
                                <td>
                                    <!-- Badge verde que muestra el estado "habilitado" -->
                                    <span class="badge bg-success"><?= htmlspecialchars($row['estado']) ?></span>
                                </td>
                                
                                <!-- COLUMNA: ACCIONES DISPONIBLES -->
                                <td>
                                    <!-- Menú desplegable con las acciones posibles -->
                                    <div class="dropdown">
                                        <!-- Botón que activa el menú desplegable -->
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        
                                        <!-- Contenido del menú desplegable -->
                                        <div class="dropdown-menu">
                                            <!-- OPCIÓN: VER DETALLE -->
                                            <?php if (in_array('detalle', $permisos_usuario)): ?>
                                                <a class="dropdown-item" href="PerfilUsuario.php?id_usuario=<?= $row['id_usuario'] ?>">
                                                    <i class="bi bi-eye me-1"></i> Ver detalle
                                                </a>
                                            <?php endif; ?>

                                            <!-- 
                                            Verificar que el usuario no sea el mismo que está logueado
                                            Esto evita que un usuario se edite/elimine a sí mismo
                                            -->
                                            <?php if ($ID_Usuario != $row['id_usuario']): ?>
                                                
                                                <!-- OPCIÓN: EDITAR USUARIO -->
                                                <?php if (in_array('editar', $permisos_usuario)): ?>
                                                    <a class="dropdown-item" href="vista-editar-usuario.php?Id=<?= $row['id_usuario'] ?>">
                                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                                    </a>
                                                <?php endif; ?>

                                                <!-- OPCIÓN: ELIMINAR USUARIO -->
                                                <?php if (in_array('eliminar', $permisos_usuario)): ?>
                                                    <!-- 
                                                    onclick llama a función JavaScript con confirmación
                                                    addslashes() escapa comillas para evitar errores en JavaScript
                                                    -->
                                                    <a class="dropdown-item" href="#" onclick="confirmarEliminacion(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                        <i class="bx bx-trash me-1"></i> Eliminar
                                                    </a>
                                                <?php endif; ?>

                                                <!-- OPCIÓN: DESHABILITAR USUARIO -->
                                                <?php if (in_array('estado', $permisos_usuario)): ?>
                                                    <a class="dropdown-item" href="#" onclick="deshabilitarUsuario(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                        <i class="bi bi-box-arrow-right me-1"></i> Deshabilitar
                                                    </a>
                                                <?php endif; ?>
                                                
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- MENSAJE CUANDO NO HAY RESULTADOS -->
                        <tr>
                            <td colspan="5" class="text-center py-4">No se encontraron usuarios habilitados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- PIE DE TABLA CON INFORMACIÓN DE PAGINACIÓN -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <!-- TEXTO INFORMATIVO SOBRE LOS RESULTADOS MOSTRADOS -->
                <div class="text-muted">
                    <!-- 
                    Muestra el rango de resultados actual y el total
                    Ej: "Mostrando 1 a 10 de 25 usuarios"
                    min() evita que se muestre un número mayor al total
                    -->
                    Mostrando <?= ($inicio + 1) ?> a <?= min($inicio + $resultadosPorPagina, $totalUsuarios) ?> de <?= $totalUsuarios ?> usuarios
                </div>

                <!-- PAGINACIÓN - SOLO SE MUESTRA SI HAY MÁS DE UNA PÁGINA -->
                <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Paginación">
                        <ul class="pagination mb-0">
                            <?php
                            // PREPARAR FILTROS PARA MANTENERLOS EN LA PAGINACIÓN
                            
                            // Array con todos los filtros actuales
                            $params = [
                                'nombre' => $nombreFiltro,
                                'apellido' => $apellidoFiltro,
                                'usuario' => $usuarioFiltro,
                                'fecha' => $fechaFiltro
                            ];
                            
                            // array_filter() elimina los filtros vacíos
                            // http_build_query() convierte el array en string de URL
                            $queryString = http_build_query(array_filter($params));

                            // BOTÓN "ANTERIOR"
                            // Si estamos en página 1, el botón está deshabilitado
                            echo '<li class="page-item ' . ($paginaActual == 1 ? 'disabled' : '') . '">';
                            echo '<a class="page-link" href="?page=' . ($paginaActual - 1) . '&' . $queryString . '" aria-label="Anterior">';
                            echo '<span aria-hidden="true">&laquo;</span>';
                            echo '</a></li>';

                            // CÁLCULO DE QUÉ PÁGINAS MOSTRAR
                            // Mostrar 5 páginas centradas en la actual
                            $startPage = max(1, $paginaActual - 2);  // No menor que 1
                            $endPage = min($totalPaginas, $startPage + 4); // No mayor que el total

                            // GENERACIÓN DE LOS NÚMEROS DE PÁGINA
                            // Bucle for que crea los enlaces a cada página
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                // Marcar como activa la página actual
                                echo '<li class="page-item ' . ($i == $paginaActual ? 'active' : '') . '">';
                                echo '<a class="page-link" href="?page=' . $i . '&' . $queryString . '">' . $i . '</a>';
                                echo '</li>';
                            }

                            // BOTÓN "SIGUIENTE"
                            // Si estamos en la última página, el botón está deshabilitado
                            echo '<li class="page-item ' . ($paginaActual == $totalPaginas ? 'disabled' : '') . '">';
                            echo '<a class="page-link" href="?page=' . ($paginaActual + 1) . '&' . $queryString . '" aria-label="Siguiente">';
                            echo '<span aria-hidden="true">&raquo;</span>';
                            echo '</a></li>';
                            ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT PARA FUNCIONALIDADES ADICIONALES -->
<script>
    // FUNCIÓN PARA BUSCAR AL PRESIONAR ENTER EN CUALQUIER CAMPO DE FILTRO
    
    // Esperar a que el documento HTML esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar TODOS los campos de filtro por sus IDs
        document.querySelectorAll('#nombre, #apellido, #usuario, #fecha').forEach(input => {
            // Agregar evento que escucha cuando se presiona una tecla
            input.addEventListener('keypress', function(e) {
                // Verificar si la tecla presionada es Enter (código 13)
                if (e.key === 'Enter') {
                    // Prevenir el comportamiento por defecto del Enter
                    e.preventDefault();
                    // Enviar el formulario manualmente
                    this.form.submit();
                }
            });
        });
    });
</script>