<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
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

// Parámetros de paginación
$recetasPorPagina = 6;
$comentariosPorPagina = 5;

// Obtener la página actual de la URL, si no está definida, usar la página 1
$paginaActualRecetas = isset($_GET['paginaRecetas']) ? (int)$_GET['paginaRecetas'] : 1;
$paginaActualComentarios = isset($_GET['paginaComentarios']) ? (int)$_GET['paginaComentarios'] : 1;

// Calcular el offset para la consulta SQL
$offsetRecetas = ($paginaActualRecetas - 1) * $recetasPorPagina;
$offsetComentarios = ($paginaActualComentarios - 1) * $comentariosPorPagina;

// Consulta para obtener las recetas a las que el usuario ha dado "me gusta" con paginación
$sqlRecetasLikes = "
    SELECT R.id_receta, R.titulo, RI.url_imagen
    FROM recetas R
    LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id
    LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img
    LEFT JOIN likes L ON R.id_receta = L.receta_id
    WHERE L.usuario_id = ? AND RI.url_imagen IS NOT NULL
    GROUP BY R.id_receta
    ORDER BY R.fecha_creacion DESC
    LIMIT ? OFFSET ?
";
$statementRecetasLikes = $conexion->prepare($sqlRecetasLikes);
$statementRecetasLikes->bind_param("iii", $ID_Usuario, $recetasPorPagina, $offsetRecetas);
$statementRecetasLikes->execute();
$resultRecetasLikes = $statementRecetasLikes->get_result();
$recetasLikes = [];
while ($receta = $resultRecetasLikes->fetch_assoc()) {
    if (!isset($recetasLikes[$receta['id_receta']])) {
        $recetasLikes[$receta['id_receta']] = $receta;
        $recetasLikes[$receta['id_receta']]['imagenes'] = [];
    }
    $recetasLikes[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
}


// Consulta para obtener las recetas que el usuario ha comentado con paginación
$sqlRecetasComentarios = "
    SELECT R.id_receta, R.titulo, RI.url_imagen, U.nombre_usuario, U.img AS avatar_usuario, C.comentario
    FROM recetas R
    LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id
    LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img
    LEFT JOIN comentarios C ON R.id_receta = C.receta_id
    LEFT JOIN usuarios U ON R.usuario_id = U.id_usuario
    WHERE C.usuario_id = ? AND RI.url_imagen IS NOT NULL
    GROUP BY R.id_receta, C.comentario
    ORDER BY R.fecha_creacion DESC
    LIMIT ? OFFSET ?
";
$statementRecetasComentarios = $conexion->prepare($sqlRecetasComentarios);
$statementRecetasComentarios->bind_param("iii", $ID_Usuario, $comentariosPorPagina, $offsetComentarios);
$statementRecetasComentarios->execute();
$resultRecetasComentarios = $statementRecetasComentarios->get_result();
$recetasComentarios = [];
while ($receta = $resultRecetasComentarios->fetch_assoc()) {
    if (!isset($recetasComentarios[$receta['id_receta']])) {
        $recetasComentarios[$receta['id_receta']] = $receta;
        $recetasComentarios[$receta['id_receta']]['imagenes'] = [];
    }
    $recetasComentarios[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
}

// Obtener el número total de recetas y comentarios para la paginación
$sqlTotalRecetasLikes = "SELECT COUNT(DISTINCT R.id_receta) AS total FROM recetas R LEFT JOIN likes L ON R.id_receta = L.receta_id WHERE L.usuario_id = ?";
$statementTotalRecetasLikes = $conexion->prepare($sqlTotalRecetasLikes);
$statementTotalRecetasLikes->bind_param("i", $ID_Usuario);
$statementTotalRecetasLikes->execute();
$resultTotalRecetasLikes = $statementTotalRecetasLikes->get_result();
$totalRecetasLikes = $resultTotalRecetasLikes->fetch_assoc()['total'];

$sqlTotalRecetasComentarios = "SELECT COUNT(DISTINCT R.id_receta) AS total FROM recetas R LEFT JOIN comentarios C ON R.id_receta = C.receta_id WHERE C.usuario_id = ?";
$statementTotalRecetasComentarios = $conexion->prepare($sqlTotalRecetasComentarios);
$statementTotalRecetasComentarios->bind_param("i", $ID_Usuario);
$statementTotalRecetasComentarios->execute();
$resultTotalRecetasComentarios = $statementTotalRecetasComentarios->get_result();
$totalRecetasComentarios = $resultTotalRecetasComentarios->fetch_assoc()['total'];

// Calcular el número total de páginas
$totalPaginasRecetas = ceil($totalRecetasLikes / $recetasPorPagina);
$totalPaginasComentarios = ceil($totalRecetasComentarios / $comentariosPorPagina);
?>

<!doctype html>
<html lang="en">


<!-- Mirrored from technext.github.io/dingo/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:45 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Actividad</title>
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
    <link rel="stylesheet" href="../css/comentarios.css">
    <link rel="stylesheet" href="../css/slick.css">
    <link rel="stylesheet" href="../css/gijgo.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/all.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="../css/style.css">


    <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/pages/page-profile.css">

    <!-- Core CSS -->
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" /> -->
    <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <!-- <link rel="stylesheet" href="../css/perfil.css"> -->
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" /> -->
    <!-- <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/theme-default.css"> -->
    <!-- <link rel="stylesheet" href="../../assets/css/demo.css" /> -->

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
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
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-subir-receta.php' ? 'active' : '' ?>" href="<?php echo isset($_SESSION['id_usuario']) ? 'vista-subir-receta.php' : '../../VistaAdmin/html/Login.php'; ?>">Subir recetas</a>
                                </li>
                                <?php if (isset($_SESSION['id_usuario'])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' || basename($_SERVER['PHP_SELF']) == 'vista-actividad.php' ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="menu_btn">
                            <?php if (!isset($_SESSION['id_usuario'])): ?>
                                <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                            <?php else: ?>
                                <a href="cerrar_sesion.php" class="btn-naranja d-none d-sm-block">Cerrar sesión</a>
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
                            <h2>A C T I V I D A D</h2>
                            <h4>
                                <h4 class="text-white fw-light">
                                    <a class="text-white" href="vista-perfil.php" style="text-decoration: none;">Perfil </a>/ Tú actividad
                                </h4>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!-- ================ contact section start ================= -->
    <section class="food_menu gray_bg">
        <div class="container">

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
            <!-- //! NAV MENÚ ME GUSTA Y COMENTARIOS -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <ul class="nav justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" id="me-gusta-tab" data-bs-toggle="tab" href="#me-gusta" role="tab" aria-controls="me-gusta" aria-selected="true"><i class="bi bi-heart"></i> ME GUSTA</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="comentarios-tab" data-bs-toggle="tab" href="#comentarios" role="tab" aria-controls="comentarios" aria-selected="false"><i class="bi bi-chat"></i> COMENTARIOS</a>
                        </li>
                    </ul>
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

            <!-- //? ME GUSTAS ===================================================================== -->
            <!-- //? ME GUSTAS ===================================================================== -->
            <div class="tab-content" id="myTabContent">
                <!-- ME GUSTA TAB -->
                <div class="tab-pane fade show active" id="me-gusta" role="tabpanel" aria-labelledby="me-gusta-tab">
                    <div class="container mt-5">
                        <!-- Filtro de orden -->
                        <div class="d-flex justify-content-end mb-3">
                            <form method="get" class="form-inline">
                                <input type="hidden" name="paginaRecetas" value="<?= $paginaActualRecetas ?>">
                                <label for="ordenRecetas" class="me-2 fw-bold">Ordenar y filtrar</label>
                                <select name="ordenRecetas" id="ordenRecetas" class="form-select form-select-sm" style="width:auto; display:inline-block;" onchange="this.form.submit()">
                                    <option value="reciente" <?= (!isset($_GET['ordenRecetas']) || $_GET['ordenRecetas'] == 'reciente') ? 'selected' : '' ?>>Más reciente</option>
                                    <option value="antiguo" <?= (isset($_GET['ordenRecetas']) && $_GET['ordenRecetas'] == 'antiguo') ? 'selected' : '' ?>>Más antiguo</option>
                                </select>
                            </form>
                        </div>
                        <?php
                        // Ordenar me gusta según filtro
                        $ordenRecetas = isset($_GET['ordenRecetas']) && $_GET['ordenRecetas'] == 'antiguo' ? 'ASC' : 'DESC';
                        // Repetir consulta para obtener recetas ordenadas si cambia el filtro
                        $sqlRecetasLikes = "
                            SELECT R.id_receta, R.titulo, RI.url_imagen
                            FROM recetas R
                            LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id
                            LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img
                            LEFT JOIN likes L ON R.id_receta = L.receta_id
                            WHERE L.usuario_id = ? AND RI.url_imagen IS NOT NULL
                            GROUP BY R.id_receta
                            ORDER BY R.fecha_creacion $ordenRecetas
                            LIMIT ? OFFSET ?
                        ";
                        $statementRecetasLikes = $conexion->prepare($sqlRecetasLikes);
                        $statementRecetasLikes->bind_param("iii", $ID_Usuario, $recetasPorPagina, $offsetRecetas);
                        $statementRecetasLikes->execute();
                        $resultRecetasLikes = $statementRecetasLikes->get_result();
                        $recetasLikes = [];
                        while ($receta = $resultRecetasLikes->fetch_assoc()) {
                            if (!isset($recetasLikes[$receta['id_receta']])) {
                                $recetasLikes[$receta['id_receta']] = $receta;
                                $recetasLikes[$receta['id_receta']]['imagenes'] = [];
                            }
                            $recetasLikes[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
                        }
                        ?>
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php if (!empty($recetasLikes)): ?>
                                <?php foreach ($recetasLikes as $receta): ?>
                                    <div class="col mb-4">
                                        <a href="vista-detalle-receta.php?id=<?= $receta['id_receta'] ?>" class="card h-100 shadow-lg border-0 rou text-decoration-none">
                                            <img src="<?= $receta['imagenes'][0]; ?>" class="card-img-top" alt="Imagen de <?= $receta['titulo']; ?>">
                                            <div class="card-body">
                                                <h3 class="card-title text-dark font-weight-bold"><?= $receta['titulo'] ?></h3>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No hay recetas a las que hayas dado "me gusta".</p>
                            <?php endif; ?>
                        </div>
                        <!-- Paginación -->
                        <nav aria-label="...">
                            <ul class="pagination">
                                <li class="page-item <?= $paginaActualRecetas <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?paginaRecetas=<?= $paginaActualRecetas - 1 ?>&ordenRecetas=<?= $ordenRecetas == 'ASC' ? 'antiguo' : 'reciente' ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPaginasRecetas; $i++): ?>
                                    <li class="page-item <?= $i == $paginaActualRecetas ? 'active' : '' ?>">
                                        <a class="page-link" href="?paginaRecetas=<?= $i ?>&ordenRecetas=<?= $ordenRecetas == 'ASC' ? 'antiguo' : 'reciente' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $paginaActualRecetas >= $totalPaginasRecetas ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?paginaRecetas=<?= $paginaActualRecetas + 1 ?>&ordenRecetas=<?= $ordenRecetas == 'ASC' ? 'antiguo' : 'reciente' ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- COMENTARIOS TAB -->
                <div class="tab-pane fade" id="comentarios" role="tabpanel" aria-labelledby="comentarios-tab">
                    <div class="container mt-5">
                        <!-- Filtro de orden (igual que ME GUSTA) -->
                        <div class="d-flex justify-content-end mb-3">
                            <form method="get" class="form-inline">
                                <input type="hidden" name="paginaComentarios" value="<?= $paginaActualComentarios ?>">
                                <label for="ordenComentarios" class="me-2 fw-bold">Ordenar y filtrar</label>
                                <select name="ordenComentarios" id="ordenComentarios" class="form-select form-select-sm" style="width:auto; display:inline-block;" onchange="this.form.submit()">
                                    <option value="reciente" <?= (!isset($_GET['ordenComentarios']) || $_GET['ordenComentarios'] == 'reciente') ? 'selected' : '' ?>>Más reciente</option>
                                    <option value="antiguo" <?= (isset($_GET['ordenComentarios']) && $_GET['ordenComentarios'] == 'antiguo') ? 'selected' : '' ?>>Más antiguo</option>
                                </select>
                            </form>
                        </div>
                        <style>
                            /* Responsive comment card layout */
                            .comentario-header {
                                display: flex;
                                align-items: center;
                                flex-wrap: wrap;
                                gap: 0.5rem;
                            }

                            .comentario-nombre-receta {
                                font-size: 1rem;
                                color: #555;
                                word-break: break-word;
                                max-width: 100%;
                                white-space: normal;
                                display: block;
                            }

                            .comentario-nombre-usuario {
                                font-weight: bold;
                                font-size: 1.1rem;
                                margin-right: 0.5rem;
                                word-break: break-word;
                                max-width: 100%;
                            }

                            .comentario-img-receta {
                                width: 60px;
                                height: 60px;
                                object-fit: cover;
                                border-radius: 0.5rem;
                                margin-left: auto;
                            }

                            @media (max-width: 576px) {
                                .comentario-header {
                                    flex-direction: column;
                                    align-items: flex-start;
                                }

                                .comentario-img-receta {
                                    margin-left: 0;
                                    margin-top: 0.5rem;
                                    align-self: flex-end;
                                }

                                .comentario-nombre-receta {
                                    margin-top: 0.25rem;
                                }
                            }
                        </style>
                        <?php
                        // Ordenar comentarios según filtro
                        $ordenComentarios = isset($_GET['ordenComentarios']) && $_GET['ordenComentarios'] == 'antiguo' ? 'ASC' : 'DESC';
                        // Repetir consulta para obtener comentarios ordenados si cambia el filtro
                        $sqlRecetasComentarios = "
                            SELECT R.id_receta, R.titulo, RI.url_imagen, U.nombre_usuario, U.img AS avatar_usuario, C.comentario
                            FROM recetas R
                            LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id
                            LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img
                            LEFT JOIN comentarios C ON R.id_receta = C.receta_id
                            LEFT JOIN usuarios U ON R.usuario_id = U.id_usuario
                            WHERE C.usuario_id = ? AND RI.url_imagen IS NOT NULL
                            GROUP BY R.id_receta, C.comentario
                            ORDER BY R.fecha_creacion $ordenComentarios
                            LIMIT ? OFFSET ?
                        ";
                        $statementRecetasComentarios = $conexion->prepare($sqlRecetasComentarios);
                        $statementRecetasComentarios->bind_param("iii", $ID_Usuario, $comentariosPorPagina, $offsetComentarios);
                        $statementRecetasComentarios->execute();
                        $resultRecetasComentarios = $statementRecetasComentarios->get_result();
                        $recetasComentarios = [];
                        while ($receta = $resultRecetasComentarios->fetch_assoc()) {
                            if (!isset($recetasComentarios[$receta['id_receta']])) {
                                $recetasComentarios[$receta['id_receta']] = $receta;
                                $recetasComentarios[$receta['id_receta']]['imagenes'] = [];
                            }
                            $recetasComentarios[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
                        }
                        ?>
                        <?php if (!empty($recetasComentarios)): ?>
                            <?php foreach ($recetasComentarios as $receta): ?>
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-body">
                                        <div class="comentario-header">
                                            <i class="bi bi-person-circle me-2" style="font-size: 2rem;"></i>
                                            <span class="comentario-nombre-usuario"><?= htmlspecialchars($receta['nombre_usuario']); ?></span>
                                            <span class="comentario-nombre-receta"><?= htmlspecialchars($receta['titulo']); ?></span>
                                            <img src="<?= htmlspecialchars($receta['imagenes'][0]); ?>" class="comentario-img-receta" alt="Imagen de <?= htmlspecialchars($receta['titulo']); ?>">
                                        </div>
                                        <div class="bg-light rounded p-3 mt-2">
                                            <p class="mb-0"><?= htmlspecialchars($receta['comentario']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No hay recetas que hayas comentado.</p>
                        <?php endif; ?>
                        <!-- Paginación -->
                        <nav aria-label="...">
                            <ul class="pagination">
                                <li class="page-item <?= $paginaActualComentarios <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?paginaComentarios=<?= $paginaActualComentarios - 1 ?>&ordenComentarios=<?= $ordenComentarios == 'ASC' ? 'antiguo' : 'reciente' ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPaginasComentarios; $i++): ?>
                                    <li class="page-item <?= $i == $paginaActualComentarios ? 'active' : '' ?>">
                                        <a class="page-link" href="?paginaComentarios=<?= $i ?>&ordenComentarios=<?= $ordenComentarios == 'ASC' ? 'antiguo' : 'reciente' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $paginaActualComentarios >= $totalPaginasComentarios ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?paginaComentarios=<?= $paginaActualComentarios + 1 ?>&ordenComentarios=<?= $ordenComentarios == 'ASC' ? 'antiguo' : 'reciente' ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- ================ contact section end ================= -->

    <!-- footer part start-->
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
                                <li><a href="#">Inicio</a></li>
                                <li><a href="#">Nosotros</a></li>
                                <li><a href="#">Categorías</a></li>
                                <li><a href="#">Subir Recetas</a></li>
                                <li><a href="#">Perfil</a></li>
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
                        <p class="footer-text m-0">ChefClass | Proyecto realizado por <a href="#" target="_blank">Lucas Salvatierra, Emiliano Olivera.</a></p>
                    </div>
                    <div class="col-lg-4">
                        <div class="copyright_social_icon text-right">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                            <a href="#"><i class="ti-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer part end-->

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
    <!-- particles js -->
    <script src="../js/contact.js"></script>
    <!-- ajaxchimp js -->
    <script src="../js/jquery.ajaxchimp.min.js"></script>
    <!-- validate js -->
    <script src="../js/jquery.validate.min.js"></script>
    <!-- form js -->
    <script src="../js/jquery.form.js"></script>
    <!-- custom js -->
    <script src="../js/custom.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

</body>


<!-- Mirrored from technext.github.io/dingo/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:46 GMT -->

</html>