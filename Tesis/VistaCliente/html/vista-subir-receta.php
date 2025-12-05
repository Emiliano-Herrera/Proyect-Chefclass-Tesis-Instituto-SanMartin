<?php

session_start();

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    // Redirigir a la página de inicio de sesión si no hay una sesión activa
    header("Location: Login.php");
    exit();
}

?>
<!doctype html>
<html lang="en">


<!-- Mirrored from technext.github.io/dingo/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:45 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <!-- Link para cambiar las letras a  Roboto -->
    <link rel="stylesheet" href="../css/cambio_de_letra.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Subir Receta</title>
    <link rel="icon" href="../img/chefclassFinal.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../css/boton-naranja.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/themify-icons.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/slick.css">
    <link rel="stylesheet" href="../css/gijgo.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/all.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/cambio_de_letra.css">

    <style>
        /* Solo para este archivo: fuerza negrita y color negro al enlace activo */
        .navbar-nav .nav-link.active {
            color: #212529 !important;
            /* color negro Bootstrap */
            font-weight: bold !important;
        }

        .error {
            border: 2px solid red;
        }
    </style>

</head>

<body>


    <!--::header part start::-->
    <header class="main_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.html"> <img src="../img/chefclassFinal.png" alt="logo" width="140" height="auto"> </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse main-menu-item justify-content-end" id="navbarSupportedContent">

                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-nosotros.php' ? 'active' : '' ?>" href="vista-nosotros.php">Nosotros</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-categoria.php' ? 'active' : '' ?>" href="vista-categoria.php">Categorías</a>
                                </li>
                                <li class="nav-item">
                                    <?php if (!isset($_SESSION['id_usuario'])): ?>
                                        <a class="nav-link subir-receta-no-logeado" href="#">Subir recetas</a>
                                    <?php else: ?>
                                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-subir-receta.php' ? 'active' : '' ?>" href="vista-subir-receta.php">Subir recetas</a>
                                    <?php endif; ?>
                                </li>
                                <?php if (isset($_SESSION['id_usuario'])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="menu_btn d-flex align-items-center">
                            <?php if (!isset($_SESSION['id_usuario'])): ?>
                                <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                            <?php else: ?>


                                <span class="d-none d-sm-inline align-middle" style="font-weight: 500; margin-right: 2rem;">
                                    <i class="bi bi-person-circle" style="font-size: 1.3em; vertical-align: middle;"></i>
                                    <?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>
                                </span>

                                <a href="cerrar_sesion.php" class="btn-naranja d-none d-sm-block ms-1">Cerrar sesión</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Header part end-->

    <!-- breadcrumb start-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner text-center">
                        <div class="breadcrumb_iner_item">
                            <h2>Subir mis recetas</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!-- ================ contact section start ================= -->
    <section class="contact-section section_padding">
        <div class="container">


            <?php if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo "<script> Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'La receta se ha subido correctamente.', confirmButtonText: 'Aceptar' }); </script>";
                } elseif ($_GET['status'] == 'error') {
                    echo "<script> Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al subir la receta. Por favor, inténtalo de nuevo.', confirmButtonText: 'Aceptar' }); </script>";
                }
            } ?>

            <div class="row">
                <div class="col-12">
                    <!-- Contenedor principal de la tarjeta -->
                    <div class="card shadow-lg">
                        <!-- Encabezado de la tarjeta -->
                        <h3 class="card-header">Subir una receta</h3>
                        <div class="card-body">

                            <!-- Estilos CSS para validación -->
                            <style>
                                .error {
                                    border: 2px solid red !important;
                                    /* Borde rojo para campos con error */
                                }

                                .text-danger {
                                    font-size: 0.9em;
                                    /* Tamaño más pequeño para mensajes de error */
                                }
                            </style>

                            <!-- ========== FORMULARIO PRINCIPAL ========== -->
                            <form id="formValidationExamples" class="row g-3" method="POST" action="insert-receta.php" enctype="multipart/form-data">

                                <!-- ========== SECCIÓN 1: DATOS DE LA RECETA ========== -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>1. Datos de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>

                                <!-- Indicador de campos obligatorios -->
                                <div class="col-12 mb-2">
                                    <small class="text-muted">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</small>
                                </div>

                                <!-- Campo: Título de la receta -->
                                <div class="col-md-6">
                                    <!-- Atributo personalizado para validación JS -->
                                    <!-- Regex: solo letras, números y caracteres básicos -->
                                    <!-- Límite de caracteres -->
                                    <label class="form-label" for="titulo">Título <span class="text-danger">*</span></label>
                                    <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo"
                                        data-required="true"
                                        pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-]{1,100}"
                                        maxlength="100"
                                        title="Solo letras, números y espacios (máximo 100 caracteres)" /> <!-- Mensaje tooltip -->
                                    <small class="text-danger d-none" id="error-titulo">Debes ingresar un título.</small> <!-- Mensaje de error oculto -->
                                </div>

                                <!-- Campo: Tiempo de preparación -->
                                <div class="col-md-6">
                                    <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM) <span class="text-danger">*</span></label>
                                    <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación"
                                        name="tiempo" data-required="true" /> <!-- Input tipo time para formato HH:MM -->
                                    <small class="text-danger d-none" id="error-tiempo">Debes ingresar el tiempo de preparación.</small>
                                </div>

                                <!-- Campo: Descripción -->
                                <div class="col-md-12">
                                    <label class="form-label" for="descripcion">Descripción <span class="text-danger">*</span></label>
                                    <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta"
                                        name="descripcion" rows="3" data-required="true"
                                        maxlength="500"
                                        title="Máximo 500 caracteres"></textarea>
                                    <small class="text-danger d-none" id="error-descripcion">Debes ingresar una descripción.</small>
                                </div>

                                <!-- Campo: Dificultad (Radio Buttons) -->
                                <div class="col-md-6">
                                    <label class="form-label">Dificultad <span class="text-danger">*</span></label>
                                    <!-- Opción Fácil -->
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-facil" name="dificultad" class="form-check-input"
                                            value="Fácil" data-required="true" />
                                        <label class="form-check-label" for="dificultad-facil">Fácil</label>
                                    </div>
                                    <!-- Opción Intermedio -->
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-intermedio" name="dificultad" class="form-check-input"
                                            value="Intermedio" data-required="true" />
                                        <label class="form-check-label" for="dificultad-intermedio">Intermedio</label>
                                    </div>
                                    <!-- Opción Difícil -->
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-dificil" name="dificultad" class="form-check-input"
                                            value="Difícil" data-required="true" />
                                        <label class="form-check-label" for="dificultad-dificil">Difícil</label>
                                    </div>
                                    <small class="text-danger d-none" id="error-dificultad">Debes seleccionar la dificultad.</small>
                                </div>

                                <!-- Campo: Categorías (Select Dinámico) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="categoria">Categorías <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <!-- Select que carga categorías desde la base de datos -->
                                        <select class="form-select" id="categoria" name="categoria[]" data-required="true">
                                            <option disabled value="">Selecciona una categoría...</option>
                                            <?php
                                            // CONEXIÓN Y CONSULTA A LA BASE DE DATOS
                                            include('conexion.php');
                                            // Consulta para obtener categorías habilitadas
                                            $sql_categoria = "SELECT id_categoria, nombre FROM categoria WHERE estado = 'habilitado' ";
                                            $result_categoria = $conexion->query($sql_categoria);
                                            // Generar opciones dinámicamente
                                            if ($result_categoria->num_rows > 0) {
                                                while ($row = $result_categoria->fetch_assoc()) {
                                                    // Crear option por cada categoría
                                                    echo "<option value='{$row['id_categoria']}'>{$row['nombre']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <!-- Botón para agregar múltiples categorías -->
                                        <button type="button" class="btn btn-outline-success" id="add-categoria-btn">Agregar Categoría</button>
                                        <small class="text-danger d-none" id="error-categoria">Debes seleccionar al menos una categoría.</small>
                                    </div>
                                    <!-- Contenedor para mostrar categorías seleccionadas -->
                                    <div id="selected-categorias"></div>
                                </div>

                                <!-- ========== SECCIÓN 2: INSTRUCCIONES DE LA RECETA ========== -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>2. Instrucciones de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>

                                <!-- Contenedor de pasos dinámicos -->
                                <div class="col-md-12">
                                    <label class="form-label" for="instrucciones">Pasos <span class="text-danger">*</span></label>
                                    <div id="instrucciones-container">
                                        <!-- Paso inicial (los demás se agregan dinámicamente) -->
                                        <div class="input-group mb-3">
                                            <!-- Número de paso (automático y de solo lectura) -->
                                            <input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso"
                                                value="1" readonly data-required="true" />
                                            <!-- Descripción del paso -->
                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso"
                                                data-required="true"
                                                pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-(){}\[\]]{1,255}"
                                                maxlength="255" /> <!-- Límite de caracteres -->
                                            <!-- Botón para agregar más pasos -->
                                            <button class="btn btn-outline-success add-instruccion-btn" type="button">Agregar paso</button>
                                        </div>
                                    </div>
                                    <small class="text-danger d-none" id="error-pasos">Debes ingresar al menos un paso.</small>
                                </div>

                                <!-- ========== SECCIÓN 3: INGREDIENTES DE LA RECETA ========== -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>3. Ingredientes de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>

                                <!-- ========== SISTEMA DE AUTOCOMPLETADO ========== -->

                                <!-- Autocompletado para ingredientes (desde base de datos) -->
                                <?php
                                include('conexion.php');
                                $ingredientes = [];
                                // Consulta todos los ingredientes de la base de datos
                                $sql = "SELECT nombre FROM ingredientes";
                                $result = $conexion->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $ingredientes[] = $row['nombre'];
                                }
                                ?>
                                <!-- Pasar array de ingredientes a JavaScript -->
                                <script>
                                    const ingredientesBD = <?php echo json_encode($ingredientes); ?>;
                                </script>
                                <!-- Datalist para autocompletar ingredientes -->
                                <datalist id="ingredientes-list">
                                    <?php foreach ($ingredientes as $ing): ?>
                                        <option value="<?php echo htmlspecialchars($ing); ?>">
                                        <?php endforeach; ?>
                                </datalist>

                                <!-- Autocompletado para cantidades (valores predefinidos) -->
                                <?php
                                // Array de cantidades sugeridas comúnmente usadas en recetas
                                $cantidadesSugeridas = [
                                    "1/2",
                                    "1/5",
                                    "0/5",
                                    "2/3",
                                    "2/5",  // Fracciones
                                    "100",
                                    "200",
                                    "250",
                                    "500",
                                    "1000",  // Cantidades en gramos/ml
                                    "1",
                                    "2",
                                    "3",
                                    "4",
                                    "5",
                                    "6",
                                    "7",
                                    "8",
                                    "9",
                                    "10",
                                    "18",
                                    "25"  // Números enteros
                                ];
                                ?>
                                <!-- Pasar array de cantidades a JavaScript -->
                                <script>
                                    const cantidadesBD = <?php echo json_encode($cantidadesSugeridas); ?>;
                                </script>
                                <!-- Datalist para autocompletar cantidades -->
                                <datalist id="cantidades-list">
                                    <?php foreach ($cantidadesSugeridas as $cant): ?>
                                        <option value="<?php echo htmlspecialchars($cant); ?>">
                                        <?php endforeach; ?>
                                </datalist>

                                <!-- Contenedor de ingredientes dinámicos -->
                                <div class="col-md-12">
                                    <label class="form-label" for="ingredientes">Ingredientes <span class="text-danger">*</span></label>
                                    <div id="ingredientes-container">
                                        <!-- Ingrediente inicial (los demás se agregan dinámicamente) -->
                                        <div class="input-group mb-3">
                                            <!-- Campo: Nombre del ingrediente -->
                                            <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente"
                                                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s\-]{1,100}"
                                                maxlength="100"
                                                title="Solo letras y espacios (máximo 100 caracteres)" />

                                            <!-- Campo: Cantidad del ingrediente -->
                                            <input type="text" class="form-control" name="cantidades[]"
                                                placeholder="Cantidad (ej: 1, 1/2, 2 1/2, a gusto)"
                                                pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s\/\.\-]{1,50}"
                                                maxlength="50"
                                                title="Solo letras, números y caracteres básicos (máximo 50 caracteres)" />

                                            <!-- Campo: Unidad de medida -->
                                            <input type="text" class="form-control" name="unidades[]"
                                                placeholder="Unidad (ej: taza, cucharada, a gusto)"
                                                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{1,20}"
                                                maxlength="20"
                                                title="Solo letras y espacios (máximo 20 caracteres)"
                                                list="unidades-list" /> <!-- Vinculado al datalist de unidades -->

                                            <!-- Datalist de unidades de medida predefinidas -->
                                            <datalist id="unidades-list">
                                                <option value="Taza">
                                                <option value="Tazas">
                                                <option value="Cucharada">
                                                <option value="Cucharadas">
                                                <option value="Cucharadita">
                                                <option value="Gramos">
                                                <option value="Gramo">
                                                <option value="Pizca">
                                                <option value="Pizcas">
                                                <option value="Mililitros">
                                                <option value="Mililitro">
                                                <option value="A gusto">
                                                <option value="Unidad">
                                                <option value="Unidades">
                                            </datalist>

                                            <!-- Botón para agregar más ingredientes -->
                                            <button class="btn btn-outline-success add-ingrediente-btn" type="button">Agregar ingrediente</button>
                                        </div>
                                    </div>
                                    <small class="text-danger d-none" id="error-ingredientes">Debes ingresar ingredientes.</small>
                                </div>

                                <!-- ========== SECCIÓN 4: MULTIMEDIA ========== -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>4. Subir fotos o videos de la receta (Asegurate de seleccionar todas las imagenes o videos de una vez)</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>

                                <!-- Campo: Subida de archivos multimedia -->
                                <div class="col-12">
                                    <label class="form-label" for="media_files">Subir fotos o videos <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="inputGroupFile01">Subir</label>
                                        <!-- Input file que acepta imágenes y videos, múltiples archivos -->
                                        <input type="file" class="form-control" id="inputGroupFile01" name="media_files[]"
                                            accept="image/*,video/*"
                                            multiple
                                            data-required="true" />
                                    </div>
                                    <small class="text-danger d-none" id="error-media">Debes subir al menos una imagen o video.</small>
                                </div>

                                <!-- ========== BOTONES DE ACCIÓN ========== -->
                                <div class="col-12 justify-content-between">
                                    <!-- Botón de enviar formulario -->
                                    <button type="submit" class="button button-contactForm btn_4 me-2">Subir receta</button>
                                    <!-- Botón de cancelar (vuelve al index) -->
                                    <a href="index.php" class="btn btn-label-secondary">Cancelar</a>
                                </div>
                            </form>
                            <!-- ========== FIN DEL FORMULARIO ========== -->
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </section>
    <!-- ================ contact section end ================= -->


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Scrip para poner en contorno rojo cuando se saltea algun input o select del formulario -->
    <!-- Y para los sweetAlert cuando falta algo del formulario -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- LIMPIEZA DE ESPACIOS ---
            function limpiarEspaciosInput(input) {
                input.value = input.value
                    .replace(/^\s+/, '')
                    .replace(/\s{2,}/g, ' ')
                    .replace(/(\s+)$/, function(match) {
                        return match.length > 1 ? ' ' : match;
                    });
            }

            // Aplica limpieza en tiempo real
            document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
                input.addEventListener('input', function() {
                    limpiarEspaciosInput(input);
                });
            });

            // --- VALIDACIÓN SQL - SANITIZACIÓN DE CAMPOS ---
            function sanitizeSQLInput(text) {
                return text.replace(/[;'"\\\-\-]/g, '');
            }

            function addSQLValidationEvents(field) {
                field.addEventListener('input', function() {
                    this.value = sanitizeSQLInput(this.value);
                });
            }

            // Aplicar a todos los campos existentes
            document.querySelectorAll('input[type="text"], textarea').forEach(addSQLValidationEvents);

            // --- FUNCIONES DE ERROR VISUAL ---
            function mostrarError(id, mensaje) {
                const error = document.getElementById(id);
                if (error) {
                    error.textContent = mensaje;
                    error.classList.remove('d-none');
                }
            }

            function ocultarError(id) {
                const error = document.getElementById(id);
                if (error) {
                    error.textContent = '';
                    error.classList.add('d-none');
                }
            }

            // --- VALIDACIÓN DINÁMICA AL CORREGIR ---
            document.querySelectorAll('input, textarea, select').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (input.classList.contains('error') && input.value.trim()) {
                        input.classList.remove('error');
                        const id = input.id ? 'error-' + input.id : '';
                        ocultarError(id);
                    }
                });
            });

            // --- AGREGAR PASOS DINÁMICOS ---
            document.querySelector('.add-instruccion-btn').addEventListener('click', function() {
                let container = document.getElementById('instrucciones-container');
                let pasoNumber = container.querySelectorAll('.input-group').length + 1;
                let newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-3';
                newInputGroup.innerHTML = `
            <input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" value="${pasoNumber}" readonly data-required="true" />
            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" 
                data-required="true"
                pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-(){}\[\]]{1,255}"
                maxlength="255" />
            <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
        `;
                container.appendChild(newInputGroup);

                // Validación SQL para el nuevo campo
                newInputGroup.querySelectorAll('input[type="text"]').forEach(function(input) {
                    addSQLValidationEvents(input);
                });

                // Limpieza en el nuevo input
                newInputGroup.querySelectorAll('input[type="text"]').forEach(function(input) {
                    input.addEventListener('input', function() {
                        limpiarEspaciosInput(input);
                    });
                });

                // Eliminar paso y renumerar
                newInputGroup.querySelector('.remove-instruccion-btn').addEventListener('click', function() {
                    container.removeChild(newInputGroup);
                    Array.from(container.querySelectorAll('.input-group')).forEach((group, idx) => {
                        let numInput = group.querySelector('input[name="num_pasos[]"]');
                        if (numInput) numInput.value = idx + 1;
                    });
                });
            });

            // --- AGREGAR INGREDIENTES DINÁMICOS ---
            document.querySelector('.add-ingrediente-btn').addEventListener('click', function() {
                let container = document.getElementById('ingredientes-container');
                let newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-3';
                newInputGroup.innerHTML = `
            <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" 
                required
                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s\-]{1,100}"
                maxlength="100" />
            <input type="text" class="form-control" name="cantidades[]" 
                placeholder="Cantidad (ej: 1, 1/2, 2 1/2, a gusto)" 
                pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s\/\.\-]{1,50}"
                maxlength="50" />
            <input type="text" class="form-control" name="unidades[]" 
                placeholder="Unidad (ej: taza, cucharada, a gusto)" 
                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{1,20}"
                maxlength="20" />
            <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
        `;
                container.appendChild(newInputGroup);

                // Validación SQL para nuevos campos
                newInputGroup.querySelectorAll('input[type="text"]').forEach(function(input) {
                    addSQLValidationEvents(input);
                });

                // Eliminar ingrediente
                newInputGroup.querySelector('.remove-ingrediente-btn').addEventListener('click', function() {
                    container.removeChild(newInputGroup);
                });
            });

            // --- AGREGAR CATEGORÍA DINÁMICA ---
            const addCategoriaBtn = document.getElementById('add-categoria-btn');
            const categoriaSelect = document.getElementById('categoria');
            const selectedCategoriasContainer = document.getElementById('selected-categorias');

            if (addCategoriaBtn) {
                addCategoriaBtn.addEventListener('click', function() {
                    const selectedCategoriaValue = categoriaSelect.value;
                    const selectedCategoriaText = categoriaSelect.options[categoriaSelect.selectedIndex].text;

                    if (selectedCategoriaValue && !document.querySelector(`#selected-categorias input[value="${selectedCategoriaValue}"]`)) {
                        const categoriaElement = document.createElement('div');
                        categoriaElement.className = 'input-group mb-2';
                        categoriaElement.innerHTML = `
                    <input type="hidden" name="categoria[]" value="${selectedCategoriaValue}">
                    <input type="text" class="form-control" value="${selectedCategoriaText}" readonly>
                    <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
                `;
                        categoriaElement.querySelector('.remove-categoria-btn').addEventListener('click', function() {
                            categoriaElement.remove();
                        });
                        selectedCategoriasContainer.appendChild(categoriaElement);

                        // Limpiar el select después de agregar
                        categoriaSelect.value = "";

                        // Remover error de categoría si había uno
                        ocultarError('error-categoria');
                        categoriaSelect.classList.remove('error');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Por favor evita seleccionar categorías duplicadas.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }

            // --- VALIDACIÓN COMPLETA DEL FORMULARIO ---
            const form = document.getElementById('formValidationExamples');

            if (form) {
                form.addEventListener('submit', function(event) {
                    console.log('Validando formulario...'); // Para debug

                    let valido = true;

                    // Limpia espacios antes de validar
                    document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
                        input.value = input.value.replace(/^\s+|\s+$/g, '').replace(/\s{2,}/g, ' ');
                    });

                    // VALIDACIÓN DE TÍTULO
                    const titulo = document.getElementById('titulo');
                    if (!titulo || !titulo.value.trim()) {
                        mostrarError('error-titulo', 'Debes ingresar un título.');
                        if (titulo) titulo.classList.add('error');
                        valido = false;
                        console.log('Error en título');
                    } else {
                        ocultarError('error-titulo');
                        titulo.classList.remove('error');
                    }

                    // VALIDACIÓN DE TIEMPO DE PREPARACIÓN
                    const tiempo = document.getElementById('tiempo');
                    if (!tiempo || !tiempo.value.trim()) {
                        mostrarError('error-tiempo', 'Debes ingresar el tiempo de preparación.');
                        if (tiempo) tiempo.classList.add('error');
                        valido = false;
                        console.log('Error en tiempo');
                    } else {
                        ocultarError('error-tiempo');
                        tiempo.classList.remove('error');
                    }

                    // VALIDACIÓN DE DESCRIPCIÓN
                    const descripcion = document.getElementById('descripcion');
                    if (!descripcion || !descripcion.value.trim()) {
                        mostrarError('error-descripcion', 'Debes ingresar una descripción.');
                        if (descripcion) descripcion.classList.add('error');
                        valido = false;
                        console.log('Error en descripción');
                    } else {
                        ocultarError('error-descripcion');
                        descripcion.classList.remove('error');
                    }

                    // VALIDACIÓN DE DIFICULTAD
                    const dificultadRadios = document.querySelectorAll('input[name="dificultad"]');
                    const dificultadChecked = Array.from(dificultadRadios).some(radio => radio.checked);
                    if (!dificultadChecked) {
                        mostrarError('error-dificultad', 'Debes seleccionar la dificultad.');
                        dificultadRadios.forEach(radio => radio.classList.add('error'));
                        valido = false;
                        console.log('Error en dificultad');
                    } else {
                        ocultarError('error-dificultad');
                        dificultadRadios.forEach(radio => radio.classList.remove('error'));
                    }

                    // VALIDACIÓN DE CATEGORÍA - CORREGIDA
                    const categoria = document.getElementById('categoria');
                    const selectedCategorias = document.querySelectorAll('#selected-categorias input[name="categoria[]"]');

                    // SOLO verifica si hay categorías seleccionadas, no el valor del select
                    if (selectedCategorias.length === 0) {
                        mostrarError('error-categoria', 'Debes seleccionar al menos una categoría.');
                        categoria.classList.add('error');
                        valido = false;
                        console.log('Error en categoría - No hay categorías seleccionadas');
                    } else {
                        ocultarError('error-categoria');
                        categoria.classList.remove('error');
                        console.log('Categorías seleccionadas:', selectedCategorias.length);
                    }

                    // VALIDACIÓN DE PASOS
                    const pasos = document.querySelectorAll('input[name="pasos[]"]');
                    let pasoVacio = false;
                    pasos.forEach(paso => {
                        if (!paso.value.trim()) pasoVacio = true;
                    });
                    if (pasos.length === 0 || pasoVacio) {
                        mostrarError('error-pasos', 'Debes ingresar la descripción del paso.');
                        pasos.forEach(paso => paso.classList.add('error'));
                        valido = false;
                        console.log('Error en pasos');
                    } else {
                        ocultarError('error-pasos');
                        pasos.forEach(paso => paso.classList.remove('error'));
                    }

                    // VALIDACIÓN DE INGREDIENTES
                    const ingredientes = document.querySelectorAll('input[name="ingredientes[]"]');
                    let ingredienteVacio = false;
                    ingredientes.forEach(ing => {
                        if (!ing.value.trim()) ingredienteVacio = true;
                    });
                    if (ingredientes.length === 0 || ingredienteVacio) {
                        mostrarError('error-ingredientes', 'Debes ingresar al menos un ingrediente.');
                        ingredientes.forEach(ing => ing.classList.add('error'));
                        valido = false;
                        console.log('Error en ingredientes');
                    } else {
                        ocultarError('error-ingredientes');
                        ingredientes.forEach(ing => ing.classList.remove('error'));
                    }

                    // VALIDACIÓN DE ARCHIVOS MULTIMEDIA
                    const media = document.getElementById('inputGroupFile01');
                    if (!media || !media.files || media.files.length === 0) {
                        mostrarError('error-media', 'Debes subir al menos una imagen o video.');
                        if (media) media.classList.add('error');
                        valido = false;
                        console.log('Error en media files');
                    } else {
                        ocultarError('error-media');
                        media.classList.remove('error');
                    }

                    console.log('Formulario válido:', valido);

                    // Si hay algún error, evita el envío
                    if (!valido) {
                        event.preventDefault();
                        const primerError = document.querySelector('.error');
                        if (primerError) {
                            primerError.scrollIntoView({
                                behavior: "smooth",
                                block: "center"
                            });
                        }
                        console.log('Envío del formulario prevenido');
                        return;
                    }

                    // Si todo está bien, el formulario se envía normalmente
                    console.log('Formulario enviado correctamente');
                });
            }

            // --- AUTOCOMPLETADO Y VALIDACIONES ADICIONALES ---

            // Capitaliza la primera letra de cada palabra
            function capitalizarPrimeraLetra(texto) {
                return texto.replace(/\b\w/g, l => l.toUpperCase());
            }

            // Solo letras para ingredientes
            function soloLetras(input) {
                input.addEventListener('input', function() {
                    input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
                });
            }

            // Aplica validación de solo letras a ingredientes
            document.querySelectorAll('input[name="ingredientes[]"]').forEach(soloLetras);

            // Solo letras para el título
            const tituloInput = document.getElementById('titulo');
            if (tituloInput) {
                tituloInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
                });
            }

            // Para campos dinámicos
            const ingredientesContainer = document.getElementById('ingredientes-container');
            if (ingredientesContainer) {
                ingredientesContainer.addEventListener('input', function(e) {
                    if (e.target && e.target.name === "ingredientes[]") {
                        soloLetras(e.target);
                    }
                }, true);
            }

            console.log('Script de validación cargado correctamente');
        });
    </script>
    <!--Scrip para validar la extension de los archivos -->
    <script>
        document.getElementById('inputGroupFile01').addEventListener('change', function(event) {
            const archivos = event.target.files;
            const formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
            let archivosInvalidos = [];
            for (let file of archivos) {
                let extension = file.name.split('.').pop().toLowerCase();
                if (!formatosPermitidos.includes(extension)) {
                    archivosInvalidos.push(file.name);
                }
            }
            if (archivosInvalidos.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no permitido',
                    text: `Los siguientes archivos tienen un formato no permitido:\n${archivosInvalidos.join(', ')}\n\nSolo se permiten: ${formatosPermitidos.join(', ')}`,
                    confirmButtonText: 'Aceptar'
                });
                event.target.value = '';
            }
        });
    </script>

    <?php
    if (isset($_GET['status']) && $_GET['status'] === 'error_webp'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Formato no permitido',
                text: 'No se permiten imágenes en formato .webp. Por favor, selecciona otra imagen.',
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>

    <!-- jquery plugins here-->
    <!-- jquery -->
    <script src="js/jquery-1.12.1.min.js"></script>
    <!-- popper js -->
    <script src="js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- easing js -->
    <script src="js/jquery.magnific-popup.js"></script>
    <!-- swiper js -->
    <script src="js/swiper.min.js"></script>
    <!-- swiper js -->
    <script src="js/masonry.pkgd.js"></script>
    <!-- particles js -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- swiper js -->
    <script src="js/slick.min.js"></script>
    <script src="js/gijgo.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <!-- particles js -->
    <script src="js/contact.js"></script>
    <!-- ajaxchimp js -->
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <!-- validate js -->
    <script src="js/jquery.validate.min.js"></script>
    <!-- form js -->
    <script src="js/jquery.form.js"></script>
    <!-- custom js -->
    <script src="js/custom.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

    <!-- Footer igual al de vista-perfil.php -->
    <footer class="footer-area mt-5">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_1">
                        <h4>Sobre nosotros</h4>
                        <p>Bienvenidos a ChefClass, tu comunidad culinaria en línea.
                            Somos la plataforma perfecta para los amantes de la cocina que desean compartir,
                            descubrir y disfrutar de recetas únicas y deliciosas.
                            Inspirados por la pasión de cocinar y la conexión que se genera al compartir nuestras creaciones,
                            ChefClass se ha convertido en el lugar ideal para encontrar inspiración diaria..</p>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-7 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Enlaces</h4>
                        <div class="contact_info">

                            <ul>
                                <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Inicio</a></li>
                                <li><a href="vista-nosotros.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vista-nosotros.php' ? 'active' : '' ?>">Nosotros</a></li>
                                <li><a href="vista-categoria.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vista-categoria.php' ? 'active' : '' ?>">Categorías</a></li>

                                <?php if (!isset($_SESSION['id_usuario'])): ?>
                                    <li><a href="#" class="subir-receta-no-logeado <?= basename($_SERVER['PHP_SELF']) == 'vista-subir-receta.php' ? 'active' : '' ?>">Subir Recetas</a></li>
                                <?php else: ?>
                                    <li><a href="vista-subir-receta.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vista-subir-receta.php' ? 'active' : '' ?>">Subir Recetas</a></li>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['id_usuario'])) : ?>
                                    <li><a href="vista-perfil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' ? 'active' : '' ?>">Perfil</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Contáctenos</h4>
                        <div class="contact_info">
                            <p><span> Ubicación :</span>San Martín 311, K4751 San Fernando del Valle de Catamarca, Catamarca </p>
                            <p><span> Celular :</span> +2 36 265 (8060)</p>
                            <p><span> Email : </span>tesisdesarrollodesoftware@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_3">
                        <h4>Iniciar sesión en ChefClass</h4>
                        <form action="#">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <?php if (!isset($_SESSION['id_usuario'])): ?>
                                            <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                                        <?php else: ?>
                                            <a href="cerrar_sesion.php" class="btn-naranja d-none d-sm-block">Cerrar sesión</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright_part_text">
                <div class="row">
                    <div class="col-lg-8">
                        <p class="footer-text m-0">
                            ChefClass | Proyecto realizado por
                            <a href="#" target="_blank" id="creditos-link">Lucas Salvatierra, Emiliano Olivera.</a>
                        </p>
                        <script>
                            document.getElementById('creditos-link').addEventListener('click', function(e) {
                                e.preventDefault();
                            });
                        </script>
                    </div>

                </div>
            </div>
        </div>
    </footer>
</body>


<!-- Mirrored from technext.github.io/dingo/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:46 GMT -->

</html>