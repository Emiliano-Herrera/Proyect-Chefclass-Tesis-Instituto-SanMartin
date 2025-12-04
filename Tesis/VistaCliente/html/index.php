<?php
// Incluir archivo de conexión a la base de datos
include("conexion.php");

// Iniciar sesión para obtener datos del usuario logueado
session_start();
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
}

// ============================================================================
// CONSULTA 1: OBTENER LAS 3 ÚLTIMAS RECETAS CREADAS
// ============================================================================
// Left join permiten mostrar recetas incluso si carecen de calificaciones, evitando pérdida de contenido válido
// AVG(C.calificacion) calcula promedios en tiempo real
$sqlUltimasRecetas = "
    SELECT R.*, RI.url_imagen, AVG(C.calificacion) as promedio_calificacion, U.nombre_usuario
    FROM recetas R 
    LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id 
    LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img 
    LEFT JOIN calificaciones C ON R.id_receta = C.receta_id
    LEFT JOIN usuarios U ON R.usuario_id = U.id_usuario
    WHERE RI.url_imagen IS NOT NULL
    AND R.estado = 'habilitado'
    GROUP BY R.id_receta
    ORDER BY R.fecha_creacion DESC 
    LIMIT 3";

// Ejecutar consulta de últimas recetas
$result = $conexion->query($sqlUltimasRecetas);

// Procesar resultados: organizar recetas con sus imágenes
$recetas = [];
while ($receta = $result->fetch_assoc()) {
    // Si es la primera vez que encontramos esta receta, inicializamos su estructura
    if (!isset($recetas[$receta['id_receta']])) {
        $recetas[$receta['id_receta']] = $receta;
        $recetas[$receta['id_receta']]['imagenes'] = [];
    }
    // Agregar la imagen al array de imágenes de la receta
    $recetas[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
}

// ============================================================================
// FUNCIÓN: LIMITAR LONGITUD DE DESCRIPCIÓN
// ============================================================================
function limitar_descripcion($descripcion, $limite = 15)
{
    // Dividir la descripción en palabras individuales
    $palabras = explode(' ', $descripcion);

    // Si hay más palabras que el límite, recortar y agregar "..."
    if (count($palabras) > $limite) {
        return implode(' ', array_slice($palabras, 0, $limite)) . '...';
    } else {
        // Si está dentro del límite, devolver la descripción completa
        return $descripcion;
    }
}

// ============================================================================
// CONSULTA 2: OBTENER LOS 3 ÚLTIMOS COMENTARIOS
// ============================================================================
$sqlComentarios = "
    SELECT C.comentario, C.fecha_comentario, U.nombre_usuario, R.titulo AS receta_titulo
    FROM comentarios C
    JOIN usuarios U ON C.usuario_id = U.id_usuario
    JOIN recetas R ON C.receta_id = R.id_receta
    ORDER BY C.fecha_comentario DESC
    LIMIT 3
";

// Ejecutar y procesar comentarios
$resultComentarios = $conexion->query($sqlComentarios);
$comentarios = [];
while ($comentario = $resultComentarios->fetch_assoc()) {
    $comentarios[] = $comentario;
}

// ============================================================================
// CONSULTA 3: OBTENER TODAS LAS CATEGORÍAS HABILITADAS
// ============================================================================
$sql_categorias = "SELECT * FROM categoria WHERE estado = 'habilitado'";
$result_categorias = $conexion->query($sql_categorias);

$categorias = [];
if ($result_categorias->num_rows > 0) {
    while ($row = $result_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// ============================================================================
// CONSULTA 4: OBTENER RECETAS POR CATEGORÍA (3 POR CADA CATEGORÍA)
// ============================================================================
$recetas_por_categoria = [];

// Recorrer cada categoría para obtener sus recetas
foreach ($categorias as $categoria) {
    $categoria_id = $categoria['id_categoria'];

    // Consulta preparada para seguridad (evita inyección SQL)
    $sql_recetas = "
        SELECT R.id_receta, R.titulo, R.descripcion, R.dificultad, RI.url_imagen, U.nombre_usuario, R.fecha_creacion, AVG(C.calificacion) as promedio_calificacion
        FROM recetas R 
        JOIN recetas_categorias RC ON R.id_receta = RC.receta_id 
        JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id 
        JOIN img_recetas RI ON IR.img_id = RI.id_img 
        JOIN usuarios U ON R.usuario_id = U.id_usuario
        LEFT JOIN calificaciones C ON R.id_receta = C.receta_id
        WHERE RC.categoria_id = ? 
        AND  R.estado = 'habilitado'
        AND (RI.url_imagen LIKE '%.jpg' OR RI.url_imagen LIKE '%.jpeg' OR RI.url_imagen LIKE '%.png' OR RI.url_imagen LIKE '%.gif')
        GROUP BY R.id_receta
        LIMIT 3
    ";

    // Preparar y ejecutar consulta con parámetro seguro
    $stmt = $conexion->prepare($sql_recetas);
    $stmt->bind_param("i", $categoria_id);  // "i" indica que es un parámetro integer
    $stmt->execute();
    $result_recetas = $stmt->get_result();

    // Procesar recetas de esta categoría
    if ($result_recetas->num_rows > 0) {
        while ($row = $result_recetas->fetch_assoc()) {
            // Organizar recetas por categoría y por ID de receta
            if (!isset($recetas_por_categoria[$categoria_id][$row['id_receta']])) {
                $recetas_por_categoria[$categoria_id][$row['id_receta']] = $row;
                $recetas_por_categoria[$categoria_id][$row['id_receta']]['imagenes'] = [];
            }
            $recetas_por_categoria[$categoria_id][$row['id_receta']]['imagenes'][] = $row['url_imagen'];
        }
    }
}

// ============================================================================
// CONSULTA 5: OBTENER LAS 3 MEJORES RECETAS CALIFICADAS
// ============================================================================
$sql_mejores_recetas = "
    SELECT r.id_receta,r.estado, r.titulo, r.descripcion, i.url_imagen, AVG(c.calificacion) as promedio_calificacion, COUNT(c.id_calificacion) as total_calificaciones, u.nombre_usuario, r.fecha_creacion
    FROM recetas r
    JOIN calificaciones c ON r.id_receta = c.receta_id
    JOIN imagenes_recetas ir ON r.id_receta = ir.recetas_id
    JOIN img_recetas i ON ir.img_id = i.id_img
    JOIN usuarios u ON r.usuario_id = u.id_usuario
    WHERE r.estado = 'habilitado'
    GROUP BY r.id_receta
    ORDER BY promedio_calificacion DESC, total_calificaciones DESC
    LIMIT 3
";

// Ejecutar y procesar mejores recetas
$result_mejores_recetas = $conexion->query($sql_mejores_recetas);
$mejores_recetas = [];
if ($result_mejores_recetas->num_rows > 0) {
    while ($row = $result_mejores_recetas->fetch_assoc()) {
        $mejores_recetas[] = $row;
    }
}

// ============================================================================
// FUNCIÓN: GENERAR ESTRELLAS DE CALIFICACIÓN VISUAL
// ============================================================================
function generar_estrellas($promedio)
{
    // Calcular estrellas completas (parte entera del promedio)
    $estrellas_completas = floor($promedio);

    // Determinar si necesita media estrella
    $mitad_estrella = ($promedio - $estrellas_completas >= 0.5) ? true : false;

    $estrellas_html = '';

    // Generar 5 estrellas (máxima calificación)
    for ($i = 0; $i < 5; $i++) {
        if ($i < $estrellas_completas) {
            // Estrella completa
            $estrellas_html .= '<span class="fa fa-star checked"></span>';
        } elseif ($mitad_estrella && $i == $estrellas_completas) {
            // Media estrella
            $estrellas_html .= '<span class="fa fa-star-half-alt checked"></span>';
            $mitad_estrella = false; // Solo una media estrella permitida
        } else {
            // Estrella vacía
            $estrellas_html .= '<span class="fa fa-star"></span>';
        }
    }
    return $estrellas_html;
}

// Cerrar conexión a la base de datos
$conexion->close();
?>



<!doctype html>
<html lang="en">



<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <link rel="stylesheet" href="../css/themify-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Inicio</title>
    <link rel="icon" href="../img/chefclassFinal.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <!-- themify CSS -->
    <link rel="stylesheet" href="../css/themify-icons.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="../css/flaticon.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="../css/slick.css">
    <link rel="stylesheet" href="../css/gijgo.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/all.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
</head>

<body>
    <!--::header part start::-->
    <header class="main_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.php">
                            <img src="../img/chefclassFinal.png" alt="logo" width="140" height="auto">
                        </a>

                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <style>
                            .navbar-nav .nav-link {
                                font-size: 1.2rem;
                                color: black;
                                transition: color 0.3s ease, transform 0.3s ease;
                            }

                            .navbar-nav .nav-link:hover {
                                color: #ff6600;
                                transform: scale(1.1);
                            }

                            .navbar-nav .nav-link.active {
                                color: #ff6600;
                                font-weight: bold;
                            }
                        </style>

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

    <!-- banner part start-->
    <!-- //!INICIO ------------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="banner_part">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="banner_text">
                        <div class="banner_text_iner">
                            <h1 style="font-size: 2.7rem; font-weight: 700; color: #222;">
                                ¡Bienvenido a ChefClass!
                                <br>
                                <span>
                                    <h2 style="color: #ff6600;">Comparte, descubre y disfruta el sabor de la comunidad</h2>
                                </span>
                            </h1>
                            <p style="font-size: 1.25rem; color: #444; margin-top: 1.2rem;">
                                Nos alegra tenerte aquí. ChefClass es tu espacio para inspirarte, aprender y conectar con personas apasionadas por la cocina.<br>
                                <span style="color: #ff6600; font-weight: 500;">¿Tienes una receta especial?</span> ¡Compártela!<br>
                                ¿Buscas nuevas ideas? <span style="color: #ff6600; font-weight: 500;">Explora las creaciones de otros usuarios.</span><br>
                                <span style="font-weight: 500;">Juntos hacemos que cada plato cuente una historia.</span>
                            </p>
                            <div class="banner_btn">
                                <?php if (!isset($_SESSION['id_usuario'])): ?>
                                    <div class="banner_btn_iner">
                                        <a href="../../VistaAdmin/html/Login.php" class="btn_2">Inciar sesión <img src="../img/icon/left_1.svg" alt=""></a>
                                    </div>

                                <?php else: ?>
                                    <div class="banner_btn_iner">
                                        <a href="cerrar_sesion.php" class="btn_2">Cerrar sesion <img src="../img/icon/left_1.svg" alt=""></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner part start-->
    <style>
        .fa-star,
        .fa-star-half-alt {
            color: #ffeb3b;
            /* Color para las estrellas amarillas */
        }

        .fa-star.checked,
        .fa-star-half-alt.checked {
            color: #ffeb3b;
        }

        .fa-star {
            color: #ccc;
            /* Color para las estrellas grises */
        }
    </style>

    <!--Ajuste para tener todas las imagenes y toda la section de igual tamaño-->
    <style>
        .single_blog_img img {
            width: 100%;
            height: 200px;
            /* Ajusta la altura según sea necesario */
            object-fit: cover;
            /* Asegura que la imagen cubra el contenedor sin distorsionarse */
        }

        .single_blog_item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .single_blog_text {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .single_blog_text h3 {
            margin-top: 10px;
            margin-bottom: 10px;
            text-transform: uppercase;
            /* Asegura que los títulos se muestren en mayúsculas */
        }

        .single_blog_text p {
            margin-bottom: 10px;
        }

        .single_blog_text a {
            align-self: flex-start;
        }
    </style>
    <!-- Section de las mejores recetas -->
    <br>
    <!-- //! MEJORES -------------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="exclusive_item_part blog_item_section mt-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-xl-5">
                    <div class="section_tittle">
                        <h1><strong>Las mejores recetas</strong></h1>
                    </div>
                </div>
            </div>
            <style>
                .card-img-top {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-top-left-radius: 15px;
                    border-top-right-radius: 15px;
                }

                .card {
                    transition: transform 0.3s ease;
                }

                .card:hover {
                    transform: translateY(-10px);
                }

                .fa-star,
                .fa-star-half-alt {
                    color: #ffeb3b;
                }

                .fa-star.checked,
                .fa-star-half-alt.checked {
                    color: #ffeb3b;
                }

                .fa-star {
                    color: #ccc;
                }

                .rou {
                    border-radius: 15px;
                }

                /* Responsive: tarjetas una debajo de otra en pantallas pequeñas */
                @media (max-width: 767.98px) {
                    .mejores-recetas-col {
                        flex: 0 0 100%;
                        max-width: 100%;
                        margin-bottom: 1.5rem;
                    }

                    .row.row-cols-1.row-cols-md-3.g-4 {
                        flex-direction: column;
                    }
                }
            </style>

            <div class="row row-cols-1 row-cols-md-3 g-4 d-flex flex-wrap">
                <?php foreach ($mejores_recetas as $receta): ?>
                    <div class="col mb-4 mejores-recetas-col">
                        <div class="card h-100 shadow-lg border-0 rou">
                            <img src="<?= htmlspecialchars($receta['url_imagen']) ?>" class="card-img-top" alt="Imagen de <?= htmlspecialchars($receta['titulo']) ?>">
                            <div class="card-body">
                                <h3 class="card-title text-dark font-weight-bold"><?= htmlspecialchars($receta['titulo']) ?></h3>
                                <div class="d-flex justify-content-between mt-3">
                                    <h6 class="text-muted"><i class="bi bi-person-fill"></i> <?= htmlspecialchars($receta['nombre_usuario']) ?></h6>
                                    <h6 class="text-muted"><i class="bi bi-calendar-fill"></i><?= strftime(' %e de %b, %Y', strtotime($receta['fecha_creacion'])) ?></h6>
                                </div>
                                <p class=""><?= generar_estrellas($receta['promedio_calificacion']) ?></p>
                                <p class="card-text mt-3"><?= htmlspecialchars(limitar_descripcion($receta['descripcion'])) ?></p>
                            </div>
                            <div class="card-footer">
                                <?php if (!isset($_SESSION['id_usuario'])): ?>
                                    <a href="#" class="btn_3 ver-detalle-no-logeado">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                <?php else: ?>
                                    <a href="vista-detalle-receta.php?id=<?= $receta['id_receta'] ?>" class="btn_3">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!--::exclusive_item_part end::-->

    <!-- about part start-->
    <!-- //! SUBIR RECETA ---------------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="about_part">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-4 col-lg-5 offset-lg-1">
                    <div class="about_img">
                        <img src="../img/about.png" alt="">
                    </div>
                </div>
                <div class="col-sm-8 col-lg-4">
                    <div class="about_text">
                        <h2>¿Tienes una receta especial? ¡Publícala y hazla famosa!</h2>
                        <h4>Inspira a otros, comparte tus secretos culinarios y sé parte de nuestra comunidad</h4>
                        <p>En ChefClass, cada receta cuenta una historia. Anímate a compartir tus mejores platos, ayuda a otros a descubrir nuevos sabores y deja tu huella en nuestra comunidad. ¡Tu receta puede ser la próxima favorita de todos!</p>

                    </div>
                </div>
            </div>
    </section>
    <!-- about part end-->

    <!-- intro_video_bg start-->
    <!-- //! VIDEO ---------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="intro_video_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="intro_video_iner text-center">
                        <h2>Comida para todos - El futuro de la alimentación</h2>
                        <div class="intro_video_icon">
                            <a id="play-video_1" class="video-play-button popup-youtube"
                                href="https://www.youtube.com/watch?v=oOn_rTintBk">
                                <span class="ti-control-play"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- intro_video_bg part start-->

    <!-- //! CATEGORIA =====================================================================================================================================================-->
    <section class="food_menu gray_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="section_tittle">
                        <h1><strong>Categorías de las Recetas</strong></h1>
                    </div>
                </div>
            </div>
            <style>
                .nav-link {
                    font-size: 1.2rem;
                    /* Agranda las letras */
                    color: black;
                    /* Cambia el color del texto a negro */
                    transition: color 0.3s ease, transform 0.3s ease;
                    /* Agrega una transición suave */
                }

                .nav-link:hover {
                    color: #ff6600;
                    /* Cambia el color del texto al pasar el cursor */
                    transform: scale(1.1);
                    /* Agrega un efecto de agrandamiento al pasar el cursor */
                }

                .nav-link.active {
                    color: #ff6600;
                    /* Cambia el color del texto activo */
                }
            </style>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <ul class="nav justify-content-center">
                        <!-- menú de pestañas con los nombres de las categorías -->
                        <?php foreach ($categorias as $categoria): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $categoria['id_categoria'] == 1 ? 'active' : '' ?>" id="categoria-<?= $categoria['id_categoria'] ?>-tab" data-toggle="tab" href="#categoria-<?= $categoria['id_categoria'] ?>" role="tab" aria-controls="categoria-<?= $categoria['id_categoria'] ?>" aria-selected="<?= $categoria['id_categoria'] == 1 ? 'true' : 'false' ?>">
                                    <?= $categoria['nombre'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>


            <style>
                .card-img-top {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-top-left-radius: 15px;
                    border-top-right-radius: 15px;
                }

                .card {
                    transition: transform 0.3s ease;
                    height: 100%;
                    min-height: 500px;
                    /* Aumenté esta altura para acomodar mejor el contenido */
                    display: flex;
                    flex-direction: column;
                }

                .card:hover {
                    transform: translateY(-10px);
                }

                .fa-star,
                .fa-star-half-alt {
                    color: #ffeb3b;
                }

                .fa-star.checked,
                .fa-star-half-alt.checked {
                    color: #ffeb3b;
                }

                .fa-star {
                    color: #ccc;
                }

                .rou {
                    border-radius: 15px;
                }

                /* SOLUCIÓN PRINCIPAL - Obligar tamaño uniforme */
                .recipes-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                    gap: 1.5rem;
                }

                @media (min-width: 768px) {
                    .recipes-grid {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }

                /* Asegurar que todas las cards sean iguales */
                .recipe-card {
                    height: 100%;
                    display: flex;
                }

                .recipe-card .card {
                    width: 100%;
                }

                /* Control preciso del contenido interno */
                .card-body {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }

                .card-title {
                    min-height: 60px;
                    /* Altura fija para el título */
                    display: flex;
                    align-items: center;
                }

                .card-text {
                    flex: 1;
                    overflow: hidden;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    /* Limita a 3 líneas */
                    -webkit-box-orient: vertical;
                    margin-bottom: 1rem;
                }
            </style>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="tab-content" id="myTabContent">
                        <?php foreach ($categorias as $categoria): ?>
                            <div class="tab-pane fade <?= $categoria['id_categoria'] == 1 ? 'show active' : '' ?>" id="categoria-<?= $categoria['id_categoria'] ?>" role="tabpanel" aria-labelledby="categoria-<?= $categoria['id_categoria'] ?>-tab">

                                <!-- CAMBIO PRINCIPAL: Reemplazar row de Bootstrap por grid CSS -->
                                <div class="recipes-grid">
                                    <?php if (isset($recetas_por_categoria[$categoria['id_categoria']])): ?>
                                        <?php
                                        $contador = 0;
                                        foreach ($recetas_por_categoria[$categoria['id_categoria']] as $receta):
                                            if ($contador >= 3) break;
                                            $contador++;
                                        ?>
                                            <div class="recipe-card">
                                                <div class="card h-100 shadow-lg border-0 rou">
                                                    <img src="<?= $receta['imagenes'][0]; ?>" class="card-img-top" alt="Imagen de <?= $receta['titulo']; ?>">
                                                    <div class="card-body">
                                                        <!-- Título con altura fija -->
                                                        <h3 class="card-title text-dark font-weight-bold mb-3"><?= $receta['titulo'] ?></h3>

                                                        <!-- Información del usuario y fecha -->
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <h6 class="text-muted m-0"><i class="bi bi-person-fill"></i> <?= $receta['nombre_usuario'] ?></h6>
                                                            <h6 class="text-muted m-0"><i class="bi bi-calendar-fill"></i><?= strftime(' %e de %b, %Y', strtotime($receta['fecha_creacion'])) ?></h6>
                                                        </div>

                                                        <!-- Estrellas -->
                                                        <div class="mb-3">
                                                            <?= generar_estrellas($receta['promedio_calificacion']) ?>
                                                        </div>

                                                        <!-- Descripción limitada y altura fija -->
                                                        <p class="card-text text-muted"><?= limitar_descripcion($receta['descripcion']) ?></p>
                                                    </div>

                                                    <!-- Footer siempre al fondo -->
                                                    <div class="card-footer bg-transparent border-top-0 pt-0">
                                                        <?php if (!isset($_SESSION['id_usuario'])): ?>
                                                            <a href="#" class="btn_3 ver-detalle-no-logeado">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                                        <?php else: ?>
                                                            <a href="vista-detalle-receta.php?id=<?= $receta['id_receta'] ?>" class="btn_3">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (count($recetas_por_categoria[$categoria['id_categoria']]) > 3): ?>
                                            <div class="col-12 text-center mt-4" style="grid-column: 1 / -1;">
                                                <a href="vista-categoria.php?id=<?= $categoria['id_categoria'] ?>" class="btn_3">Ver más</a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p class="text-center py-4">No hay recetas en esta categoría.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>





        </div>
    </section>

    <!-- Fin de Style-->
    <!-- //! EQUIPO ------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="chefs_part blog_item_section section_padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="section_tittle">
                        <h1><strong>Nuestro Equipo</strong></h1>
                    </div>
                </div>
            </div>

            <style>
                .single_blog_img img {
                    width: 100%;
                    height: 250px;
                    /* Ajusta la altura según sea necesario */
                    object-fit: cover;
                    /* Asegura que la imagen cubra el contenedor sin distorsionarse */
                    border-top-left-radius: 15px;
                    /* Redondea las esquinas superiores */
                    border-top-right-radius: 15px;
                    /* Redondea las esquinas superiores */
                }

                .single_blog_item {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    height: 100%;
                }

                .single_blog_text {
                    flex-grow: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .single_blog_text h3 {
                    margin-top: 10px;
                    margin-bottom: 10px;
                    text-transform: uppercase;
                    /* Asegura que los títulos se muestren en mayúsculas */
                }

                .single_blog_text p {
                    margin-bottom: 10px;
                }

                .single_blog_text a {
                    align-self: flex-start;
                }
            </style>

            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="single_blog_item">
                        <div class="single_blog_img">
                            <img src="../img/LucasSalvatierra.jpg" alt="">
                        </div>
                        <div class="single_blog_text text-center">
                            <h3>Lucas Salvatierra</h3>
                            <p>Desarrollador de Software</p>

                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="single_blog_item">
                        <div class="single_blog_img">
                            <img src="../img/Elemy.jpg
                            " alt="">
                        </div>
                        <div class="single_blog_text text-center">
                            <h3>Emiliano Olivera</h3>
                            <p>Desarrollador de Software</p>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- //! COMENTARIOS ---------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="review_part gray_bg section_padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="section_tittle">
                        <h1><strong>Comentarios</strong></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-11">
                    <div class="client_review_part owl-carousel">
                        <?php foreach ($comentarios as $comentario): ?>
                            <div class="client_review_single media" style="min-height: 180px;">
                                <div class="client_img align-self-center">
                                    <img src="img/client/client_1.png" alt="">
                                </div>
                                <div class="client_review_text media-body">
                                    <h4 style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.2rem;">
                                        👤 <?php echo htmlspecialchars($comentario['nombre_usuario']); ?>
                                    </h4>
                                    <span style="font-size: 1rem; color: #ff6600; font-weight: 500; display: block; margin-bottom: 0.7rem;">
                                        🍽️ <?php echo htmlspecialchars($comentario['receta_titulo']); ?>
                                    </span>
                                    <p style="font-size: 1.25rem; font-weight: 600; color: #222; background: #fffbe8; border-radius: 8px; padding: 1rem 1.2rem; margin-bottom: 0;">
                                        💬 <?php echo htmlspecialchars($comentario['comentario']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--::review_part end::-->

    <!--::exclusive_item_part start::-->
    <!-- //! ULTIMAS ---------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="blog_item_section blog_section section_padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="section_tittle">
                        <p></p>
                        <h1><strong>Últimas recetas subidas</strong></h1>
                    </div>
                </div>
            </div>
            <style>
                .card-img-top {
                    width: 100%;
                    height: 200px;
                    /* Ajusta la altura según sea necesario */
                    object-fit: cover;
                    /* Asegura que la imagen cubra el contenedor sin distorsionarse */
                    border-top-left-radius: 15px;
                    border-top-right-radius: 15px;
                }

                .card {
                    transition: transform 0.3s ease;
                }

                .card:hover {
                    transform: translateY(-10px);
                }
            </style>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($recetas as $receta): ?>
                    <div class="col mb-4">
                        <div class="card h-100 shadow-lg border-0 rou">
                            <img src="<?php echo $receta['imagenes'][0]; ?>" class="card-img-top" alt="Imagen de <?php echo $receta['titulo']; ?>">
                            <div class="card-body">
                                <h3 class="card-title text-dark font-weight-bold"><?php echo $receta['titulo']; ?></h3>
                                <div class="d-flex justify-content-between mt-3">
                                    <h6 class="text-muted"><i class="bi bi-person-fill"></i> <?php echo $receta['nombre_usuario']; ?></h6>
                                    <h6 class="text-muted"><i class="bi bi-calendar-fill"></i></i><?php echo strftime(' %e de %b, %Y', strtotime($receta['fecha_creacion'])); ?></h6>
                                </div>
                                <p class=""><?= generar_estrellas($receta['promedio_calificacion']) ?></p>
                                <p class="card-text mt-3"><?php echo limitar_descripcion($receta['descripcion']); ?></p>
                            </div>
                            <div class="card-footer">
                                <?php if (!isset($_SESSION['id_usuario'])): ?>
                                    <a href="#" class="btn_3 ver-detalle-no-logeado">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                <?php else: ?>
                                    <a href="vista-detalle-receta.php?id=<?= $receta['id_receta'] ?>" class="btn_3">Ver detalle <i class="bi bi-chevron-compact-right"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <style>
                .fa-star,
                .fa-star-half-alt {
                    color: #ffeb3b;
                    /* Color para las estrellas amarillas */
                }

                .fa-star.checked,
                .fa-star-half-alt.checked {
                    color: #ffeb3b;
                }

                .fa-star {
                    color: #ccc;
                    /* Color para las estrellas grises */
                }

                .rou {
                    border-radius: 15px;
                    /* Ajusta este valor según sea necesario */
                }
            </style>


        </div>
    </section>



    <!-- footer part start-->
    <!-- //! FOOTER ---------------------------------------------------------------------------------------------------------------------------------------------------->
    <footer class="footer-area">
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
                                <li><a href="index.php">Inicio</a></li>
                                <li><a href="vista-nosotros.php">Nosotros</a></li>
                                <li><a href="vista-categoria.php">Categorías</a></li>


                                <?php if (!isset($_SESSION['id_usuario'])): ?>
                                    <li><a href="#" class="subir-receta-no-logeado">Subir Recetas</a></li>
                                <?php else: ?>
                                    <li><a href="vista-subir-receta.php">Subir Recetas</a></li>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['id_usuario'])) : ?>
                                    <li><a href="vista-perfil.php">Perfil</a></li>
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
                                        <!-- <a href="#" class="single_page_btn d-none d-sm-block">Iniciar Sesión</a> -->
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
    <!-- footer part end-->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert para "Ver detalle"
            document.querySelectorAll('.ver-detalle-no-logeado').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Debes iniciar sesión',
                        text: 'Por favor, inicia sesión para continuar.',
                        confirmButtonText: 'Iniciar sesión',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../../VistaAdmin/html/Login.php';
                        }
                    });
                });
            });
            // SweetAlert para "Subir recetas"
            document.querySelectorAll('.subir-receta-no-logeado').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Debes iniciar sesión',
                        text: 'Por favor, inicia sesión para poder subir una receta.',
                        confirmButtonText: 'Iniciar sesión',
                        showCancelButton: true,
                        confirmButtonColor: '#007bff', // Azul 
                        cancelButtonColor: '#d33', // Rojo
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../../VistaAdmin/html/Login.php';
                        }
                    });
                });
            });
        });
    </script>
    <!-- jquery plugins here-->
    <!-- jquery -->
    <script src="../js/jquery-1.12.1.min.js"></script>
    <!-- popper js -->
    <script src="../js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- easing js -->
    <script src="../js/jquery.magnific-popup.js"></script>
    <!-- swiper js -->
    <script src="../js/swiper.min.js"></script>
    <!-- swiper js -->
    <script src="../js/masonry.pkgd.js"></script>
    <!-- particles js -->
    <script src="../js/owl.carousel.min.js"></script>
    <!-- swiper js -->
    <script src="../js/slick.min.js"></script>
    <script src="../js/gijgo.min.js"></script>
    <script src="../js/jquery.nice-select.min.js"></script>
    <!-- custom js -->
    <script src="../js/custom.js"></script>
    <!-- scripts de Bootstrap para el carrucel  -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>


<!-- Mirrored from technext.github.io/dingo/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:24 GMT -->

</html>