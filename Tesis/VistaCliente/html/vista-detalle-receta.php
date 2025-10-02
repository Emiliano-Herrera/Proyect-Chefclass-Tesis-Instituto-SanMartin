<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    header("Location: ../../VistaAdmin/html/Login.php");
    exit();
}

include('conexion.php');
$receta_id = $_GET['id'];



// Obtener el número total de likes
$sql_count_likes = "SELECT COUNT(*) AS total_likes FROM likes WHERE receta_id = ?";
$stmt_count_likes = $conexion->prepare($sql_count_likes);
$stmt_count_likes->bind_param("i", $receta_id);
$stmt_count_likes->execute();
$result_count_likes = $stmt_count_likes->get_result();
$total_likes = $result_count_likes->fetch_assoc()['total_likes'];

/* //*============================================================================================================= */
// Consulta para obtener información de la receta
$sql_receta = "SELECT r.titulo, r.descripcion, r.dificultad,
    (SELECT GROUP_CONCAT(i.url_imagen SEPARATOR ',') FROM img_recetas i JOIN imagenes_recetas ir ON i.id_img = ir.img_id WHERE ir.recetas_id = r.id_receta) AS imagenes 
    FROM recetas r 
    WHERE r.id_receta = ?";
$stmt_receta = $conexion->prepare($sql_receta);
$stmt_receta->bind_param("i", $receta_id);
$stmt_receta->execute();
$result_receta = $stmt_receta->get_result();
$receta = $result_receta->fetch_assoc();

// Consulta para obtener las imágenes y videos de la receta 
$sql_medias = "
    SELECT i.url_imagen AS url, CASE 
        WHEN i.url_imagen LIKE '%.jpg' OR i.url_imagen LIKE '%.jpeg' OR i.url_imagen LIKE '%.png' OR i.url_imagen LIKE '%.gif' THEN 'imagen'
        WHEN i.url_imagen LIKE '%.mp4' OR i.url_imagen LIKE '%.webm' OR i.url_imagen LIKE '%.ogg' THEN 'video'
        ELSE 'unknown'
    END AS tipo
    FROM img_recetas i
    JOIN imagenes_recetas ir ON i.id_img = ir.img_id
    WHERE ir.recetas_id = ?
";
$stmt_medias = $conexion->prepare($sql_medias);
$stmt_medias->bind_param("i", $receta_id);
$stmt_medias->execute();
$result_medias = $stmt_medias->get_result();

$imagenes = [];
$videos = [];

while ($media = $result_medias->fetch_assoc()) {
    $url = $media['url'];
    $tipo = $media['tipo'];
    if ($tipo === 'imagen') {
        $imagenes[] = $url;
    } elseif ($tipo === 'video') {
        $videos[] = $url;
    }
}

$stmt_medias->close();

// Nueva consulta para obtener información del usuario
$sql_usuario = "SELECT u.nombre, u.apellido, u.nombre_usuario ,u.img, u.id_usuario FROM usuarios u
    JOIN recetas r ON u.id_usuario = r.usuario_id
    WHERE r.id_receta = ?";
$stmt_usuario = $conexion->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $receta_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

// Otras consultas relacionadas
$sql_ingredientes = "SELECT i.nombre, ri.cantidad 
    FROM ingredientes i 
    JOIN recetas_ingredientes ri ON i.id_ingrediente = ri.ingrediente_id 
    WHERE ri.receta_id = ?";
$stmt_ingredientes = $conexion->prepare($sql_ingredientes);
$stmt_ingredientes->bind_param("i", $receta_id);
$stmt_ingredientes->execute();
$result_ingredientes = $stmt_ingredientes->get_result();

$sql_instrucciones = "SELECT paso, descripcion 
    FROM instrucciones 
    WHERE receta_id = ? 
    ORDER BY paso";
$stmt_instrucciones = $conexion->prepare($sql_instrucciones);
$stmt_instrucciones->bind_param("i", $receta_id);
$stmt_instrucciones->execute();
$result_instrucciones = $stmt_instrucciones->get_result();

$sql_comentarios = "SELECT c.comentario, u.nombre, u.apellido, c.fecha_comentario
    FROM comentarios c 
    JOIN usuarios u ON c.usuario_id = u.id_usuario 
    WHERE c.receta_id = ? 
    ORDER BY c.fecha_comentario DESC";
$stmt_comentarios = $conexion->prepare($sql_comentarios);
$stmt_comentarios->bind_param("i", $receta_id);
$stmt_comentarios->execute();
$result_comentarios = $stmt_comentarios->get_result();


// Obtener la calificación del usuario actual para esta receta
$sql_calificacion_usuario = "SELECT calificacion FROM calificaciones WHERE receta_id = ? AND usuario_id = ?";
$stmt_calificacion_usuario = $conexion->prepare($sql_calificacion_usuario);
$stmt_calificacion_usuario->bind_param("ii", $receta_id, $ID_Usuario);
$stmt_calificacion_usuario->execute();
$result_calificacion_usuario = $stmt_calificacion_usuario->get_result();
$calificacion_usuario = $result_calificacion_usuario->fetch_assoc();
$calificacion_actual = $calificacion_usuario ? $calificacion_usuario['calificacion'] : 0;
$stmt_calificacion_usuario->close();


// ...después de obtener $usuario y antes del formulario...
$ya_sigue = false;
if ($usuario['id_usuario'] != $ID_Usuario) {
    $sql_sigue = "SELECT 1 FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt_sigue = $conexion->prepare($sql_sigue);
    $stmt_sigue->bind_param("ii", $ID_Usuario, $usuario['id_usuario']);
    $stmt_sigue->execute();
    $stmt_sigue->store_result();
    $ya_sigue = $stmt_sigue->num_rows > 0;
    $stmt_sigue->close();
}


$conexion->close();
?>



<!doctype html>
<html lang="es">

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Perfil</title>
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
    <link rel="stylesheet" href="../css/receta-detalle.css">


    <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/pages/page-profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Core CSS -->
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" /> -->
    <!-- <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/core.css" class="template-customizer-core-css"> -->
    <!-- <link rel="stylesheet" href="../css/perfil.css"> -->
    <link rel="stylesheet" href="../css/receta-detalle-usuario.css">
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" /> -->
    <!-- <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/theme-default.css"> -->
    <!-- <link rel="stylesheet" href="../../assets/css/demo.css" /> -->

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
</head>

<body>
    <!-- Inicio del Header -->
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
                                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
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
    <!-- Fin del Header -->
    <div class="container mt-5">

        <div class="container d-flex justify-content-center" style="max-width: 700px;">


            <div class="container instagram-post">

                <!-- //?TITULO DE LA RECETA ====================================================================== -->
                <div class="post-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="user-info d-flex align-items-center flex-wrap">
                        <div class="usuario-img-container" style="border-radius: 50%; overflow: hidden; width: 50px; height: 40px;">
                            <?php $userImage = !empty($usuario['img']) ? "../../VistaAdmin/html/{$usuario['img']}" : "../img/login-persona.png"; ?>
                            <img src="<?php echo $userImage; ?>" alt="Foto del usuario" class="usuario-img" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="username-container ms-1">
                            <div class="username d-flex align-items-center" style="font-size: 1.2rem; font-weight: bold;">
                                <a href="vista-perfil-usuario.php?id=<?php echo $usuario['id_usuario']; ?>&receta_id=<?php echo $receta_id; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo $usuario['nombre_usuario']; ?>
                                </a>
                            </div>
                        </div>

                        <form class="ms-3 mt-2 mt-sm-0" id="seguir-form" action="seguir-usuario.php" method="post">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id_usuario']; ?>">
                            <?php if ($ya_sigue): ?>
                                <button type="button" class="btn btn-success btn-sm" id="seguir-button" disabled>Ya lo sigues</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-outline-primary btn-sm" id="seguir-button">Seguir</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- //!CARRUSEL ===================================================================== -->
                <div class="post-image-container mt-3">
                    <div id="customCarousel" class="carousel slide" data-bs-ride="carousel" style="position:relative;">
                        <div class="carousel-inner">
                            <?php if (!empty($imagenes) || !empty($videos)): ?>
                                <?php
                                $index = 0;
                                foreach ($imagenes as $imagen): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?= $imagen; ?>" class="d-block mx-auto img-fluid" alt="Imagen de la receta" onerror="this.onerror=null; this.src='/uploads/default.jpg';">
                                    </div>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                                <?php foreach ($videos as $video): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>" style="position:relative;">
                                        <div class="video-container position-relative" style="width:100%;height:100%;">
                                            <video class="d-block mx-auto static-video" onclick="expandVideo(this)">
                                                <source src="<?= $video; ?>" type="video/mp4">
                                                Tu navegador no soporta el elemento de video.
                                            </video>
                                            <!-- Play icon overlay centrado respecto al carrusel -->
                                            <span class="play-icon-overlay">
                                                <i class="bi bi-play-btn"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="carousel-control-prev custom-control" type="button" data-bs-target="#customCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next custom-control" type="button" data-bs-target="#customCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <style>
                    /* Estilo para el contenedor del carrusel */
                    #customCarousel .carousel-inner {
                        width: 100%;
                        max-width: 650px;
                        height: auto;
                        aspect-ratio: 16 / 9;
                        margin: 0 auto;
                        overflow: hidden;
                        background-color: transparent;
                        border-radius: 10px;
                        position: relative;
                    }

                    /* Estilo para las imágenes dentro del carrusel */
                    #customCarousel .carousel-inner img {
                        width: 100%;
                        height: auto;
                        object-fit: cover;
                        display: block;
                    }

                    /* Estilo para los videos dentro del carrusel */
                    #customCarousel .carousel-inner .video-container {
                        width: 100%;
                        height: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background-color: transparent;
                        position: relative;
                    }

                    #customCarousel .carousel-inner video.static-video {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        cursor: pointer;
                    }

                    /* Play icon overlay centrado respecto al carrusel */
                    .play-icon-overlay {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 10;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        width: 70px;
                        height: 70px;
                        background: rgba(0, 0, 0, 0.2);
                        border-radius: 50%;
                        pointer-events: none;
                    }

                    .play-icon-overlay i {
                        font-size: 3rem;
                        color: #fff;
                        text-shadow: 0 0 10px #000;
                        pointer-events: none;
                    }

                    /* Estilo para los botones de navegación */
                    .custom-control {
                        width: 40px;
                        height: 40px;
                        top: 50%;
                        transform: translateY(-50%);
                        background-color: rgba(0, 0, 0, 0.2);
                        border-radius: 50%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    .carousel-control-prev-icon,
                    .carousel-control-next-icon {
                        width: 20px;
                        height: 20px;
                    }

                    /* Ajustes para pantallas pequeñas */
                    @media (max-width: 768px) {
                        #customCarousel .carousel-inner {
                            max-width: 100%;
                            aspect-ratio: 4 / 3;
                        }

                        .play-icon-overlay {
                            width: 50px;
                            height: 50px;
                        }

                        .play-icon-overlay i {
                            font-size: 2rem;
                        }
                    }

                    @media (max-width: 576px) {
                        #customCarousel .carousel-inner {
                            aspect-ratio: 1 / 1;
                        }

                        .play-icon-overlay {
                            width: 40px;
                            height: 40px;
                        }

                        .play-icon-overlay i {
                            font-size: 1.5rem;
                        }
                    }
                </style>

                <!-- //!ACCIONES DE LA RECETA ======================================================== -->

                <style>
                    .action-btn.active i {
                        color: #ff6426;
                    }
                </style>

                <div class="post-actions d-flex justify-content-between align-items-center mt-3">
                    <div class="left-actions d-flex">
                        <button type="button" class="action-btn btn p-0 me-2" id="like-button">
                            <i class="bi bi-heart" style="font-size: 1.5rem;"></i>
                        </button>
                    </div>
                    <div class="right-actions">
                        <button type="button" class="action-btn btn p-0" id="guardar-button">
                            <i class="bi bi-bookmark" style="font-size: 1.5rem;"></i>
                        </button>
                    </div>
                </div>


                <!-- //?DESCRIPCION DE LA RECETA ================================================================== -->
                <div class="post-info mt-3">
                    <div class="likes">
                        <span id="like-count-number">0</span> Likes
                        <h4><strong><?php echo $receta['titulo']; ?></strong></h4>
                    </div>
                    <div class="caption">
                        <div class="rating">
                            <form id="ratingForm" method="POST" action="procesar_calificacion.php">
                                <input type="hidden" name="receta_id" value="<?= $receta_id ?>">
                                <input type="hidden" name="usuario_id" value="<?= $ID_Usuario ?>">
                                <div class="stars">
                                    <input type="radio" id="star5" name="calificacion" value="5" <?= $calificacion_actual == 5 ? 'checked' : '' ?>><label for="star5" title="5 estrellas">&#9733;</label>
                                    <input type="radio" id="star4" name="calificacion" value="4" <?= $calificacion_actual == 4 ? 'checked' : '' ?>><label for="star4" title="4 estrellas">&#9733;</label>
                                    <input type="radio" id="star3" name="calificacion" value="3" <?= $calificacion_actual == 3 ? 'checked' : '' ?>><label for="star3" title="3 estrellas">&#9733;</label>
                                    <input type="radio" id="star2" name="calificacion" value="2" <?= $calificacion_actual == 2 ? 'checked' : '' ?>><label for="star2" title="2 estrellas">&#9733;</label>
                                    <input type="radio" id="star1" name="calificacion" value="1" <?= $calificacion_actual == 1 ? 'checked' : '' ?>><label for="star1" title="1 estrella">&#9733;</label>
                                </div>
                            </form>
                            <style>
                                .rating {
                                    margin-top: -10px;
                                }

                                .stars {
                                    display: flex;
                                    flex-direction: row-reverse;
                                    justify-content: left;
                                }

                                .stars input[type="radio"] {
                                    display: none;
                                }

                                .stars label {
                                    font-size: 2em;
                                    color: #ccc;
                                    cursor: pointer;
                                    transition: color 0.2s;
                                }

                                .stars input[type="radio"]:checked~label,
                                .stars input[type="radio"]:checked~label~label {
                                    color: #ffeb3b;
                                }
                            </style>


                        </div>
                    </div>
                    <div class="additional-info mt-1 d-flex align-items-center">
                        <span class="info-text"><?php echo $receta['descripcion']; ?></span>
                    </div>
                </div>


                <!-- //! DETALLES DE LA RECETA - PREPARACION -->

                <div class="mt-5">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ingredientes-tab" data-toggle="tab" href="#ingredientes" role="tab" aria-controls="ingredientes" aria-selected="true"><i class="bi bi-egg-fill"></i> Ingredientes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="instrucciones-tab" data-toggle="tab" href="#instrucciones" role="tab" aria-controls="instrucciones" aria-selected="false"><i class="bi bi-clipboard-fill"></i> Instrucciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="comentarios-tab" data-toggle="tab" href="#comentarios" role="tab" aria-controls="comentarios" aria-selected="false"><i class="bi bi-chat-fill"></i> Comentarios</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active m-2" id="ingredientes" role="tabpanel" aria-labelledby="ingredientes-tab">
                            <div class="row recipe-section">
                                <div class="col-md-12">
                                    <table class="table table-bordered ingredients-table">
                                        <thead>
                                            <tr>
                                                <th>Ingrediente</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($ingrediente = $result_ingredientes->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $ingrediente['nombre']; ?></td>
                                                    <td><?php echo $ingrediente['cantidad']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="instrucciones" role="tabpanel" aria-labelledby="instrucciones-tab">
                            <div class="row recipe-section">
                                <div class="col-md-12">
                                    <h2 class="section-title">Instrucciones</h2>
                                    <table class="table table-bordered ingredients-table">
                                        <thead>
                                            <tr>
                                                <th>Paso</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($instruccion = $result_instrucciones->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $instruccion['paso']; ?></td>
                                                    <td><?php echo $instruccion['descripcion']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="comentarios" role="tabpanel" aria-labelledby="comentarios-tab">
                            <div class="comment-section">
                                <h2 class="section-title">Comentarios</h2>
                                <?php while ($comentario = $result_comentarios->fetch_assoc()): ?>
                                    <div class="comment">
                                        <p><strong><?php echo $comentario['nombre'] . ' ' . $comentario['apellido']; ?>:</strong> <?php echo $comentario['comentario']; ?></p>
                                        <p class="text-muted"><small><?php echo $comentario['fecha_comentario']; ?></small></p>
                                    </div>
                                <?php endwhile; ?>
                                <div class="comment-form mt-4">
                                    <h4>Deja un comentario</h4>
                                    <form action="enviar_comentario.php" method="post">
                                        <div class="form-group">
                                            <label for="comentario">Comentario</label>
                                            <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                                        </div>
                                        <input type="hidden" name="receta_id" value="<?php echo $receta_id; ?>">
                                        <button type="submit" class="btn btn-primary">Enviar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="instrucciones" role="tabpanel" aria-labelledby="instrucciones-tab">
                        <div class="row recipe-section">
                            <div class="col-md-12">
                                <h2 class="section-title">Instrucciones</h2>
                                <table class="table table-bordered ingredients-table">
                                    <thead>
                                        <tr>
                                            <th>Paso</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($instruccion = $result_instrucciones->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $instruccion['paso']; ?></td>
                                                <td><?php echo $instruccion['descripcion']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>


            </div>

        </div>




        <!-- Nueva Sección: Datos del Usuario -->
        <!-- <div class="usuario-container mt-5">
            <div class="recipe-header">
                <h2>Datos del Usuario</h2>
                <div class="usuario-info">
                    
                    <div class="usuario-img-container">
                        <?php $userImage = !empty($usuario['img']) ? "../../VistaAdmin/html/{$usuario['img']}" : "../img/login-persona.png"; ?>
                        <img src="<?php echo $userImage; ?>" alt="Foto del usuario" class="usuario-img">
                    </div>
                    <div class="usuario-detalles">
                        <p><strong>Nombre de Usuario:</strong> <?php echo $usuario['nombre_usuario']; ?></p>
                        <p><strong>Nombre:</strong> <?php echo $usuario['nombre']; ?></p>
                        <p><strong>Apellido:</strong> <?php echo $usuario['apellido']; ?></p>
                        
                        <form id="seguir-form" action="seguir-usuario.php" method="post">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id_usuario']; ?>">
                            <button type="submit" class="btn btn-primary">Seguir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->

    </div> <!-- Cierre del container principal -->
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
    <!-- Fin del Footer -->

    <!-- Agregar los scripts de Bootstrap necesarios -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
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
    <!-- custom js -->
    <script src="../js/custom.js"></script>

    <!-- scripts de Bootstrap para el carrusel  -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Incluir SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- //! SCRIPT DE LIKES, GUARDAR Y SEGUIR USUARIO please no touch -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const guardarButton = document.getElementById('guardar-button');
            const likeButton = document.getElementById('like-button');
            const likeCountNumber = document.getElementById('like-count-number');
            const receta_id = document.querySelector('input[name="receta_id"]').value;

            // Inicializar el estado de los botones
            fetch(`get-button-states.php?receta_id=${receta_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_saved) {
                        guardarButton.classList.add('active');
                        guardarButton.querySelector('i').classList.replace('bi-bookmark', 'bi-bookmark-fill');
                    }
                    if (data.is_liked) {
                        likeButton.classList.add('active');
                        likeButton.querySelector('i').classList.replace('bi-heart', 'bi-heart-fill');
                    }
                    likeCountNumber.textContent = data.likes;
                });

            guardarButton.addEventListener('click', function() {
                fetch('like-save-receta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            receta_id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            guardarButton.classList.toggle('active');
                            const icon = guardarButton.querySelector('i');
                            if (icon.classList.contains('bi-bookmark')) {
                                icon.classList.replace('bi-bookmark', 'bi-bookmark-fill');
                            } else {
                                icon.classList.replace('bi-bookmark-fill', 'bi-bookmark');
                            }
                            Swal.fire('¡Hecho!', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            });

            likeButton.addEventListener('click', function() {
                fetch('like-receta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            receta_id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            likeButton.classList.toggle('active');
                            const icon = likeButton.querySelector('i');
                            if (icon.classList.contains('bi-heart')) {
                                icon.classList.replace('bi-heart', 'bi-heart-fill');
                            } else {
                                icon.classList.replace('bi-heart-fill', 'bi-heart');
                            }
                            likeCountNumber.textContent = data.likes;
                            Swal.fire('¡Hecho!', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            });
        });
    </script>

    <!-- Funcion para expandir el video en grande -->
    <script>
        //Funcion para poder ver el video en grande

        function expandVideo(videoElement) {
            if (videoElement.requestFullscreen) {
                videoElement.requestFullscreen();
            } else if (videoElement.mozRequestFullScreen) { // Firefox
                videoElement.mozRequestFullScreen();
            } else if (videoElement.webkitRequestFullscreen) { // Chrome, Safari and Opera
                videoElement.webkitRequestFullscreen();
            } else if (videoElement.msRequestFullscreen) { // IE/Edge
                videoElement.msRequestFullscreen();
            }
        }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- //! FUNCION PARA CALIFICAR RECETA "ESTRELLA" -->
    <script>
        // Seleccionar todas las estrellas
        document.querySelectorAll('.stars input[type="radio"]').forEach(star => {
            // Añadir evento 'change' a cada estrella
            star.addEventListener('change', function() {
                document.getElementById('ratingForm').submit();
            });
        });

        function updateStars() {
            // Seleccionar todas las estrellas
            const stars = document.querySelectorAll('.stars input[type="radio"]');
            let checked = false;
            stars.forEach(star => {
                // Cambiar el color de las estrellas seleccionadas y las anteriores
                if (star.checked) {
                    checked = true;
                }
                star.nextElementSibling.style.color = checked ? '#ffeb3b' : '#ccc';
            });
        }

        // Actualizar estrellas al hacer clic
        document.querySelectorAll('.stars input[type="radio"]').forEach(star => {
            star.addEventListener('change', updateStars);
        });

        // Llamar a la función para actualizar las estrellas al cargar la página
        updateStars();

        <?php if (isset($_GET['calificado']) && $_GET['calificado'] == 'true'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Gracias por tu calificación',
                text: '¡Tu calificación ha sido registrada exitosamente!',
                confirmButtonText: 'Aceptar'
            });
        <?php endif; ?>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seguirForm = document.getElementById('seguir-form');
            const seguirButton = document.getElementById('seguir-button');

            seguirForm.addEventListener('submit', function(e) {
                e.preventDefault();
                seguirButton.disabled = true; // Opcional: deshabilita el botón mientras procesa

                const formData = new FormData(seguirForm);

                fetch('seguir-usuario.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? '¡Listo!' : 'Error',
                            text: data.message,
                            confirmButtonText: 'Aceptar'
                        });
                        if (data.success) {
                            seguirButton.textContent = 'Siguiendo';
                            seguirButton.disabled = true;
                        } else {
                            seguirButton.disabled = false;
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo procesar la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });
                        seguirButton.disabled = false;
                    });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
</body>

</html>