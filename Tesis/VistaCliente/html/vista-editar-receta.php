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

include("conexion.php");

// Obtener el ID de la receta desde la URL
$receta_id = $_GET['id_receta'];

// Obtener los datos de la receta
$sql = "SELECT * FROM recetas WHERE id_receta = ?";
$statement = $conexion->prepare($sql);
$statement->bind_param("i", $receta_id);
$statement->execute();
$resultado = $statement->get_result();
$receta = $resultado->fetch_assoc();

// Obtener las categorías de la receta
$sql_categorias = "SELECT categoria_id FROM recetas_categorias WHERE receta_id = ?";
$statement_categorias = $conexion->prepare($sql_categorias);
$statement_categorias->bind_param("i", $receta_id);
$statement_categorias->execute();
$resultado_categorias = $statement_categorias->get_result();
$categorias_receta = [];
while ($fila = $resultado_categorias->fetch_assoc()) {
    $categorias_receta[] = $fila['categoria_id'];
}

// Obtener las instrucciones de la receta
$sql_instrucciones = "SELECT id_instruccion, paso, descripcion FROM instrucciones WHERE receta_id = ?";
$statement_instrucciones = $conexion->prepare($sql_instrucciones);
$statement_instrucciones->bind_param("i", $receta_id);
$statement_instrucciones->execute();
$resultado_instrucciones = $statement_instrucciones->get_result();
$instrucciones = [];
while ($fila = $resultado_instrucciones->fetch_assoc()) {
    $instrucciones[] = $fila;
}



// Obtener los ingredientes de la receta
$sql_ingredientes = "SELECT I.id_ingrediente AS id, I.nombre, RI.cantidad FROM ingredientes I JOIN recetas_ingredientes RI ON I.id_ingrediente = RI.ingrediente_id WHERE RI.receta_id = ?";
$statement_ingredientes = $conexion->prepare($sql_ingredientes);
$statement_ingredientes->bind_param("i", $receta_id);
$statement_ingredientes->execute();
$resultado_ingredientes = $statement_ingredientes->get_result();
$ingredientes = [];
while ($fila = $resultado_ingredientes->fetch_assoc()) {
    $ingredientes[] = $fila;
}




// Obtener las imágenes de la receta
$sql_imagenes = "SELECT IR.url_imagen FROM img_recetas IR JOIN imagenes_recetas I ON IR.id_img = I.img_id WHERE I.recetas_id = ?";
$statement_imagenes = $conexion->prepare($sql_imagenes);
$statement_imagenes->bind_param("i", $receta_id);
$statement_imagenes->execute();
$resultado_imagenes = $statement_imagenes->get_result();
$imagenes = [];
while ($fila = $resultado_imagenes->fetch_assoc()) {
    $imagenes[] = $fila['url_imagen'];
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
    <title>ChefClass - Editar Receta</title>
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
                                        <a class="nav-link subir-receta-no-logeado" href="#">Editar receta</a>
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
                            <h2>Editar mi receta</h2>
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
                    <div class="card">
                        <h5 class="card-header">Formulario para editar una receta</h5>
                        <div class="card-body">
                            <form class="mb-3" id="editar-receta-form" action="editar-receta.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $receta_id; ?>">
                                <div class="row">
                                    <!--//! 1. Datos de la receta -->
                                    <!-- Parte 1: Datos de la Receta -->
                                    <div class="col-12">
                                        <h6>1. Datos de la receta</h6>
                                        <hr class="mt-0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="titulo">Título</label>
                                        <!-- <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo" value="<?php echo htmlspecialchars($receta['titulo']); ?>" required /> -->
                                        <!-- Título - Validación de longitud y caracteres -->
                                        <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo"
                                            value="<?php echo htmlspecialchars($receta['titulo']); ?>"
                                            pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-]{1,100}"
                                            title="Solo letras, números y espacios (máximo 100 caracteres)"
                                            maxlength="100" required />
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM)</label>
                                        <!-- <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación" name="tiempo" value="<?php echo htmlspecialchars($receta['tiempo_preparacion']); ?>" required /> -->
                                        <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación"
                                            name="tiempo" value="<?php echo htmlspecialchars($receta['tiempo_preparacion']); ?>"
                                            required />
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label" for="descripcion">Descripción</label>
                                        <!-- <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta" name="descripcion" rows="3" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea> -->
                                        <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta"
                                            name="descripcion" rows="3" maxlength="500"
                                            title="Máximo 500 caracteres" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dificultad</label>
                                        <div class="form-check custom mb-2">
                                            <input type="radio" id="dificultad-facil" name="dificultad" class="form-check-input" value="Fácil" <?php echo ($receta['dificultad'] == 'Fácil') ? 'checked' : ''; ?> />
                                            <label class="form-check-label" for="dificultad-facil">Fácil</label>
                                        </div>
                                        <div class="form-check custom mb-2">
                                            <input type="radio" id="dificultad-intermedio" name="dificultad" class="form-check-input" value="Intermedio" <?php echo ($receta['dificultad'] == 'Intermedio') ? 'checked' : ''; ?> />
                                            <label class="form-check-label" for="dificultad-intermedio">Intermedio</label>
                                        </div>
                                        <div class="form-check custom mb-2">
                                            <input type="radio" id="dificultad-dificil" name="dificultad" class="form-check-input" value="Difícil" <?php echo ($receta['dificultad'] == 'Difícil') ? 'checked' : ''; ?> />
                                            <label class="form-check-label" for="dificultad-dificil">Difícil</label>
                                        </div>
                                    </div>

                                    <!--//! Parte 2: Selector de Categorías -->
                                    <!-- Parte del formulario de edición: Selector de Categorías -->
                                    <!-- Selector de Categorías -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="categoria">Categorías</label>
                                        <div class="input-group mb-3">
                                            <select class="form-select" id="categoria">
                                                <option selected disabled>Selecciona una categoría...</option>
                                                <?php
                                                $receta_id = $_GET['id_receta'];

                                                // Subconsulta para obtener las categorías ya asociadas con la receta
                                                $sql_categoria_asociada = "SELECT categoria_id FROM recetas_categorias WHERE receta_id = ?";
                                                $stmt_categoria_asociada = $conexion->prepare($sql_categoria_asociada);
                                                $stmt_categoria_asociada->bind_param("i", $receta_id);
                                                $stmt_categoria_asociada->execute();
                                                $result_categoria_asociada = $stmt_categoria_asociada->get_result();
                                                $categorias_asociadas = [];
                                                while ($row_asociada = $result_categoria_asociada->fetch_assoc()) {
                                                    $categorias_asociadas[] = $row_asociada['categoria_id'];
                                                }

                                                // Consulta para obtener todas las categorías que no están asociadas con la receta
                                                if (count($categorias_asociadas) > 0) {
                                                    $categorias_asociadas_list = implode(",", $categorias_asociadas);
                                                    $sql_categoria = "SELECT id_categoria, nombre FROM categoria WHERE id_categoria NOT IN ($categorias_asociadas_list)";
                                                } else {
                                                    $sql_categoria = "SELECT id_categoria, nombre FROM categoria";
                                                }

                                                $result_categoria = $conexion->query($sql_categoria);
                                                if ($result_categoria->num_rows > 0) {
                                                    while ($row = $result_categoria->fetch_assoc()) {
                                                        echo "<option value='{$row['id_categoria']}'>{$row['nombre']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <button type="button" class="btn btn-outline-success" id="add-categoria-btn">Agregar Categoría</button>
                                        </div>

                                        <div id="selected-categorias">
                                            <?php foreach ($categorias_receta as $cat_id): ?>
                                                <?php
                                                $sql_cat_nombre = "SELECT nombre FROM categoria WHERE id_categoria = ?";
                                                $stmt_cat_nombre = $conexion->prepare($sql_cat_nombre);
                                                $stmt_cat_nombre->bind_param("i", $cat_id);
                                                $stmt_cat_nombre->execute();
                                                $cat_nombre = $stmt_cat_nombre->get_result()->fetch_assoc()['nombre'];
                                                ?>
                                                <div class="input-group mb-2">
                                                    <input type="hidden" name="categoria_existente[]" value="<?php echo $cat_id; ?>">
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($cat_nombre); ?>" readonly>
                                                    <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
                                                    <input type="hidden" name="categorias_eliminar[]" value="false">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" name="categorias_eliminar[]">
                                    </div>


                                    <!-- 2. Instrucciones de la receta -->
                                    <div class="col-12 mt-5">
                                        <h6>2. Instrucciones de la receta</h6>
                                        <hr class="mt-0" />
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="instrucciones">Pasos</label>
                                        <div id="instrucciones-container">
                                            <?php foreach ($instrucciones as $instruccion): ?>
                                                <div class="input-group mb-3">
                                                    <input type="hidden" name="instruccion_existente_id[]" value="<?php echo htmlspecialchars($instruccion['id_instruccion']); ?>">
                                                    <!-- <input type="number" class="form-control" name="num_pasos_existentes[]" placeholder="Número de paso" value="<?php echo htmlspecialchars($instruccion['paso']); ?>" required /> -->
                                                    <input type="number" class="form-control" name="num_pasos_existentes[]"
                                                        placeholder="Número de paso" min="1" max="50"
                                                        value="<?php echo htmlspecialchars($instruccion['paso']); ?>" required />
                                                    <!-- <input type="text" class="form-control" name="descripcion_pasos_existentes[]" placeholder="Descripción del paso" value="<?php echo htmlspecialchars($instruccion['descripcion']); ?>" required /> -->
                                                    <input type="text" class="form-control" name="descripcion_pasos_existentes[]"
                                                        placeholder="Descripción del paso"
                                                        pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-(){}\[\]]{1,255}"
                                                        maxlength="255"
                                                        value="<?php echo htmlspecialchars($instruccion['descripcion']); ?>" required />
                                                    <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
                                                    <input type="hidden" name="instrucciones_eliminar[]" value="false">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-outline-success add-instruccion-btn">Añadir Paso</button>
                                        <input type="hidden" name="instrucciones_eliminar[]">
                                    </div>


                                    <!-- 3. Ingredientes de la receta -->
                                    <div class="col-12 mt-5">
                                        <h6 class="mt-2">3. Ingredientes de la receta</h6>
                                        <hr class="mt-0" />
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="ingredientes">Ingredientes</label>
                                        <div id="ingredientes-container">
                                            <?php foreach ($ingredientes as $ingrediente): ?>
                                                <div class="input-group mb-3">
                                                    <input type="hidden" name="ingrediente_existente_id[]" value="<?php echo htmlspecialchars($ingrediente['id']); ?>">
                                                    <!-- <input type="text" class="form-control" name="nombre_ingredientes_existentes[]" placeholder="Ingrediente" value="<?php echo htmlspecialchars($ingrediente['nombre']); ?>" required /> -->
                                                    <input type="text" class="form-control" name="nombre_ingredientes_existentes[]"
                                                        placeholder="Ingrediente"
                                                        pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s\-]{1,100}"
                                                        title="Solo letras y espacios (máximo 100 caracteres)"
                                                        maxlength="100"
                                                        value="<?php echo htmlspecialchars($ingrediente['nombre']); ?>" required />
                                                    <!-- <input type="text" class="form-control" name="cantidades_existentes[]" placeholder="Cantidad" value="<?php echo htmlspecialchars($ingrediente['cantidad']); ?>" required /> -->
                                                    <input type="text" class="form-control" name="cantidades_existentes[]"
                                                        placeholder="Cantidad"
                                                        pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s\/\.\-]{1,50}"
                                                        title="Solo letras, números y caracteres básicos (máximo 50 caracteres)"
                                                        maxlength="50"
                                                        value="<?php echo htmlspecialchars($ingrediente['cantidad']); ?>" required />
                                                    <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
                                                    <input type="hidden" name="ingredientes_eliminar[]" value="false">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-outline-success add-ingrediente-btn">Añadir Ingrediente</button>
                                        <input type="hidden" name="ingredientes_eliminar[]">
                                    </div>




                                    <!-- 4. Sube imágenes o videos de tu receta -->
                                    <div class="col-12 mt-5">
                                        <h6 class="mt-2">4. Subir fotos o videos de la receta</h6>
                                        <hr class="mt-0" />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="media_files">Subir fotos o videos</label>
                                        <div id="archivos-subidos">
                                            <?php foreach ($imagenes as $imagen): ?>
                                                <div class="archivo mb-2">
                                                    <?php $file_path = "../../uploads" . htmlspecialchars($imagen); ?>
                                                    <?php if (file_exists($file_path)): ?>
                                                        <?php if (preg_match('/\.(mp4|webm)$/i', $imagen)): ?>
                                                            <video src="<?php echo $file_path; ?>" class="img-thumbnail" width="200" controls></video>
                                                        <?php elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $imagen)): ?>
                                                            <img src="<?php echo $file_path; ?>" class="img-thumbnail" width="200">
                                                        <?php endif; ?>
                                                        <input type="file" name="cambiar_archivo[]" data-url="<?php echo $file_path; ?>" class="form-control mt-2">
                                                        <button type="button" class="btn btn-outline-danger remove-archivo-btn mt-2">Eliminar</button>
                                                        <input type="hidden" name="archivos_eliminar[]" value="false">
                                                        <input type="hidden" name="archivo_url[]" value="<?php echo $file_path; ?>">
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-outline-success add-archivo-btn">Añadir Archivo</button>
                                        <input type="hidden" name="archivos_eliminar[]">
                                    </div>

                                    <div class="col-12 justify-content-between mt-2">
                                        <button type="submit" class="btn btn-primary me-2">Guardar Cambios</button>
                                        <a href="vista-perfil.php" class="btn btn-label-secondary">Cancelar</a>
                                    </div>
                                </div>
                            </form>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ================ contact section end ================= -->



    <!-- Agregar más de una categoria, instruccion, ingrediente. -->
    <script>
        $(document).ready(function() {
            const recetaId = $('#receta-id').val(); // Asumiendo que tienes un campo oculto con el ID de la receta

            $('#add-categoria-btn').click(function() {
                const selectedCategoria = $('#categoria').val();
                const selectedCategoriaText = $('#categoria option:selected').text();
                if (selectedCategoria && !$(`#selected-categorias input[value="${selectedCategoria}"]`).length) {
                    $('#selected-categorias').append(
                        `<div class="input-group mb-2">
                                        <input type="hidden" name="categoria_nueva[]" value="${selectedCategoria}">
                                        <input type="hidden" name="receta_id[]" value="${recetaId}">
                                        <input type="text" class="form-control" value="${selectedCategoriaText}" readonly>
                                        <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
                                        <input type="hidden" name="categorias_eliminar[]" value="false">
                                    </div>`
                    );
                }
            });

            $(document).on('click', '.remove-categoria-btn', function() {
                const hiddenInput = $(this).siblings('input[name="categorias_eliminar[]"]');
                hiddenInput.val('true');
                const categoriaId = $(this).siblings('input[name="categoria_existente[]"]').val();
                console.log(`Eliminando categoría con ID: ${categoriaId}`);
                $(this).closest('.input-group').hide();
            });
        });


        /* //?INSTRUCCIONES============================================================================================================================ */
        $(document).ready(function() {
            $('.add-instruccion-btn').click(function() {
                let container = $('#instrucciones-container');
                let newInputGroup = $('<div class="input-group mb-3">');
                newInputGroup.html(`
                                    <input type="number" class="form-control" name="num_pasos_nuevos[]" placeholder="Número de paso" required />
                                    <input type="text" class="form-control" name="descripcion_pasos_nuevos[]" placeholder="Descripción del paso" required />
                                    <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
                                    <input type="hidden" name="instrucciones_nuevas_eliminar[]" value="false">
                                `);
                container.append(newInputGroup);

                newInputGroup.find('.remove-instruccion-btn').click(function() {
                    console.log(`Eliminando instrucción nueva`);
                    newInputGroup.find('input[name="instrucciones_nuevas_eliminar[]"]').val('true');
                    newInputGroup.hide();
                });
            });

            $(document).on('click', '.remove-instruccion-btn', function() {
                const hiddenInput = $(this).siblings('input[name="instrucciones_eliminar[]"], input[name="instrucciones_nuevas_eliminar[]"]');
                hiddenInput.val('true');
                const instruccionId = $(this).siblings('input[name="instruccion_existente_id[]"]').val();
                console.log(`Eliminando instrucción con ID: ${instruccionId}`);
                $(this).closest('.input-group').hide();
            });
        });


        /* //?INGREDIENTES============================================================================================================================ */
        $(document).ready(function() {
            $('.add-ingrediente-btn').click(function() {
                let container = $('#ingredientes-container');
                let newInputGroup = $('<div class="input-group mb-3">');
                newInputGroup.html(`
                                        <input type="text" class="form-control" name="nombre_ingredientes_nuevos[]" placeholder="Ingrediente" required />
                                        <input type="text" class="form-control" name="cantidades_nuevas[]" placeholder="Cantidad" required />
                                        <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
                                        <input type="hidden" name="ingredientes_nuevos_eliminar[]" value="false">
                                    `);
                container.append(newInputGroup);

                newInputGroup.find('.remove-ingrediente-btn').click(function() {
                    console.log(`Eliminando ingrediente nuevo`);
                    newInputGroup.find('input[name="ingredientes_nuevos_eliminar[]"]').val('true');
                    newInputGroup.hide();
                });
            });

            $(document).on('click', '.remove-ingrediente-btn', function() {
                const hiddenInput = $(this).siblings('input[name="ingredientes_eliminar[]"], input[name="ingredientes_nuevos_eliminar[]"]');
                hiddenInput.val('true');
                const ingredienteId = $(this).siblings('input[name="ingrediente_existente_id[]"]').val();
                console.log(`Eliminando ingrediente con ID: ${ingredienteId}`);
                $(this).closest('.input-group').hide();
            });
        });





        $(document).ready(function() {
            $('.add-archivo-btn').click(function() {
                let container = $('#archivos-subidos');
                let newInputGroup = $('<div class="archivo mb-2">');
                newInputGroup.html(`
                                    <input type="file" class="form-control mt-2" name="archivos[]" required />
                                    <button class="btn btn-outline-danger remove-archivo-btn" type="button">Eliminar</button>
                                    <input type="hidden" name="archivos_eliminar[]" value="false">
                                    <input type="hidden" name="archivo_url[]" value="">
                                `);
                container.append(newInputGroup);

                newInputGroup.find('.remove-archivo-btn').click(function() {
                    console.log(`Eliminando archivo`);
                    newInputGroup.find('input[name="archivos_eliminar[]"]').val('true');
                    newInputGroup.hide();
                });
            });

            $(document).on('click', '.remove-archivo-btn', function() {
                const hiddenInput = $(this).siblings('input[name="archivos_eliminar[]"]');
                hiddenInput.val('true');
                const archivoUrl = $(this).siblings('input[name="archivo_url[]"]').val();
                console.log(`Eliminando archivo con URL: ${archivoUrl}`);
                $(this).closest('.archivo').hide();
            });
        });
    </script>

    <!-- Validaciones para inputs -->
    <script>
        // Definición de Palabras Prohibidas
        const forbiddenWords = ["gay", "gil", "topo", "topoide"];

        // Función para Verificar Palabras Prohibidas
        function containsForbiddenWords(text) {
            for (let word of forbiddenWords) {
                if (text.toLowerCase().includes(word.toLowerCase())) {
                    return true;
                }
            }
            return false;
        }

        // Validación de dificultad
        function validateDificultad(value) {
            const allowedValues = ['Fácil', 'Intermedio', 'Difícil'];
            if (!allowedValues.includes(value)) {
                document.querySelector('input[name="dificultad"]:checked').checked = false;
                Swal.fire('Error', 'Valor de dificultad no válido', 'error');
                return false;
            }
            return true;
        }

        // Validación de archivos
        function validateFiles() {
            const files = document.querySelectorAll('input[type="file"]');
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];

            for (let file of files) {
                if (file.files[0]) {
                    if (file.files[0].size > maxSize) {
                        Swal.fire('Error', 'El archivo excede el tamaño máximo de 10MB', 'error');
                        file.value = '';
                        return false;
                    }
                    if (!allowedTypes.includes(file.files[0].type)) {
                        Swal.fire('Error', 'Tipo de archivo no permitido', 'error');
                        file.value = '';
                        return false;
                    }
                }
            }
            return true;
        }

        // Función Principal para Validar el Formulario Completo
        function validateCompleteForm(event) {
            // 1. Validar archivos primero
            if (!validateFiles()) {
                event.preventDefault();
                return;
            }

            // 2. Validar dificultad
            const dificultadSelected = document.querySelector('input[name="dificultad"]:checked');
            if (dificultadSelected && !validateDificultad(dificultadSelected.value)) {
                event.preventDefault();
                return;
            }

            // 3. Validar palabras prohibidas
            const titulo = document.getElementById('titulo').value;
            const descripcion = document.getElementById('descripcion').value;

            // Obtener todos los campos de instrucciones (existentes y nuevos)
            const instruccionesExistentes = Array.from(document.querySelectorAll('input[name="descripcion_pasos_existentes[]"]')).map(input => input.value);
            const instruccionesNuevas = Array.from(document.querySelectorAll('input[name="descripcion_pasos_nuevos[]"]')).map(input => input.value);
            const todasLasInstrucciones = [...instruccionesExistentes, ...instruccionesNuevas];

            // Obtener todos los campos de ingredientes (existentes y nuevos)
            const ingredientesExistentes = Array.from(document.querySelectorAll('input[name="nombre_ingredientes_existentes[]"]')).map(input => input.value);
            const ingredientesNuevos = Array.from(document.querySelectorAll('input[name="nombre_ingredientes_nuevos[]"]')).map(input => input.value);
            const todosLosIngredientes = [...ingredientesExistentes, ...ingredientesNuevos];

            let invalidFields = [];

            if (containsForbiddenWords(titulo)) invalidFields.push('Título');
            if (containsForbiddenWords(descripcion)) invalidFields.push('Descripción');
            if (todasLasInstrucciones.some(containsForbiddenWords)) invalidFields.push('Instrucciones');
            if (todosLosIngredientes.some(containsForbiddenWords)) invalidFields.push('Ingredientes');

            if (invalidFields.length > 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Los siguientes campos contienen palabras no permitidas: ${invalidFields.join(', ')}.`,
                    confirmButtonText: 'Aceptar'
                });
            }
        }

        // Asignación del Evento de Validación al Formulario
        document.getElementById('editar-receta-form').addEventListener('submit', validateCompleteForm);

        // Validación en tiempo real para nuevos campos dinámicos
        $(document).on('input', 'input[name="nombre_ingredientes_nuevos[]"], input[name="nombre_ingredientes_existentes[]"]', function() {
            this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s\-]/g, '');
        });

        $(document).on('input', 'input[name="descripcion_pasos_nuevos[]"], input[name="descripcion_pasos_existentes[]"]', function() {
            this.value = this.value.replace(/[^A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,!?\-(){}\[\]]/g, '');
        });

        // Validación en tiempo real para palabras prohibidas
        $(document).on('input', '#titulo, #descripcion', function() {
            if (containsForbiddenWords(this.value)) {
                this.style.borderColor = 'red';
            } else {
                this.style.borderColor = '';
            }
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Validaciones para evitar malas palbras, validaciones para el completado del formulario. -->
    <!-- <script>
        // Definición de Palabras Prohibidas
        const forbiddenWords = ["gay", "gil", "topo", "topoide"]; // Añade aquí las palabras que quieres prohibir

        // Función para Verificar Palabras Prohibidas
        function containsForbiddenWords(text) {
            for (let word of forbiddenWords) {
                if (text.includes(word)) {
                    return true;
                }
            }
            return false;
        }

        // Función para Validar el Formulario
        function validateForm(event) {
            const titulo = document.getElementById('titulo').value;
            const descripcion = document.getElementById('descripcion').value;
            const instrucciones = Array.from(document.querySelectorAll('input[name="pasos[]"]')).map(input => input.value);
            const ingredientes = Array.from(document.querySelectorAll('input[name="ingredientes[]"]')).map(input => input.value);

            let invalidFields = [];

            if (containsForbiddenWords(titulo)) invalidFields.push('Título');
            if (containsForbiddenWords(descripcion)) invalidFields.push('Descripción');
            if (instrucciones.some(containsForbiddenWords)) invalidFields.push('Instrucciones');
            if (ingredientes.some(containsForbiddenWords)) invalidFields.push('Ingredientes');

            if (invalidFields.length > 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Los siguientes campos contienen palabras no permitidas: ${invalidFields.join(', ')}.`,
                    confirmButtonText: 'Aceptar'
                });
            }
        }

        
    </script> -->
    <div class="content-backdrop fade"></div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>






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