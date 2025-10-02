<?php

session_start();
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
}

include('conexion.php');

// Obtener todas las categorías
$sql_categorias = "SELECT * FROM categoria";
$result_categorias = $conexion->query($sql_categorias);

$categorias = [];
if ($result_categorias->num_rows > 0) {
    while ($row = $result_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Obtener la categoría seleccionada
$categoria_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Obtener recetas e imágenes para cada categoría
$recetas_por_categoria = [];
foreach ($categorias as $categoria) {
    $categoria_id = $categoria['id_categoria'];
    $sql_recetas = "
        SELECT R.id_receta, R.titulo, R.descripcion, R.dificultad, RI.url_imagen, U.nombre_usuario, R.fecha_creacion, AVG(C.calificacion) as promedio_calificacion
        FROM recetas R 
        JOIN recetas_categorias RC ON R.id_receta = RC.receta_id 
        JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id 
        JOIN img_recetas RI ON IR.img_id = RI.id_img 
        JOIN usuarios U ON R.usuario_id = U.id_usuario
        LEFT JOIN calificaciones C ON R.id_receta = C.receta_id
        WHERE RC.categoria_id = ? 
        AND R.estado = 'habilitado' 
        AND (RI.url_imagen LIKE '%.jpg' OR RI.url_imagen LIKE '%.jpeg' OR RI.url_imagen LIKE '%.png' OR RI.url_imagen LIKE '%.gif')
        GROUP BY R.id_receta
    ";
    $stmt = $conexion->prepare($sql_recetas);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $result_recetas = $stmt->get_result();

    if ($result_recetas->num_rows > 0) {
        while ($row = $result_recetas->fetch_assoc()) {
            if (!isset($recetas_por_categoria[$categoria_id][$row['id_receta']])) {
                $recetas_por_categoria[$categoria_id][$row['id_receta']] = $row;
                $recetas_por_categoria[$categoria_id][$row['id_receta']]['imagenes'] = [];
            }
            $recetas_por_categoria[$categoria_id][$row['id_receta']]['imagenes'][] = $row['url_imagen'];
        }
    }
}

function formatear_fecha($fecha)
{
    $meses = [
        1 => 'Ene',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dic'
    ];

    $fecha_obj = new DateTime($fecha);
    $dia = $fecha_obj->format('d');
    $mes = $meses[(int)$fecha_obj->format('m')];
    $anio = $fecha_obj->format('Y');

    return "$dia de $mes, $anio";
}

function limitar_descripcion($descripcion, $limite = 15)
{
    $palabras = explode(' ', $descripcion);
    if (count($palabras) > $limite) {
        return implode(' ', array_slice($palabras, 0, $limite)) . '...';
    } else {
        return $descripcion;
    }
}

function generar_estrellas($promedio)
{
    $estrellas_completas = floor($promedio);
    $mitad_estrella = ($promedio - $estrellas_completas >= 0.5) ? true : false;
    $estrellas_html = '';

    for ($i = 0; $i < 5; $i++) {
        if ($i < $estrellas_completas) {
            $estrellas_html .= '<span class="fa fa-star checked"></span>';
        } elseif ($mitad_estrella && $i == $estrellas_completas) {
            $estrellas_html .= '<span class="fa fa-star-half-alt checked"></span>';
            $mitad_estrella = false; // Para que solo una estrella pueda ser media
        } else {
            $estrellas_html .= '<span class="fa fa-star"></span>';
        }
    }
    return $estrellas_html;
}

?>




<!doctype html>
<html lang="en">


<!-- Mirrored from technext.github.io/dingo/food_menu.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:36 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
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

    <!-- breadcrumb start-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner text-center">
                        <div class="breadcrumb_iner_item">
                            <h2>C A T E G O R Í A</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- //!CATEGORIAS -------------------------------------------------------------------------------- -->


    <section class="food_menu gray_bg">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="section_tittle">
                        <h1><strong>Categorías de las Recetas</strong></h1>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="section_tittle">
                        <input type="text" id="search-input" class="form-control" placeholder="Buscar recetas...">
                    </div>
                </div>
            </div>

            <style>
                .nav-link {
                    font-size: 1.2rem;
                    color: black;
                    transition: color 0.3s ease, transform 0.3s ease;
                }

                .nav-link:hover {
                    color: #ff6600;
                    transform: scale(1.1);
                }

                .nav-link.active {
                    color: #ff6600;
                }
            </style>
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <ul class="nav justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link <?= $categoria_filtro == 0 ? 'active' : '' ?>" id="categoria-todas-tab" href="vista-categoria.php?categoria=0" role="tab" aria-controls="categoria-todas" aria-selected="<?= $categoria_filtro == 0 ? 'true' : 'false' ?>">
                                Todas las recetas
                            </a>
                        </li>
                        <?php foreach ($categorias as $categoria): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $categoria['id_categoria'] == $categoria_filtro ? 'active' : '' ?>" id="categoria-<?= $categoria['id_categoria'] ?>-tab" href="vista-categoria.php?categoria=<?= $categoria['id_categoria'] ?>" role="tab" aria-controls="categoria-<?= $categoria['id_categoria'] ?>" aria-selected="<?= $categoria['id_categoria'] == $categoria_filtro ? 'true' : 'false' ?>">
                                    <?= $categoria['nombre'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- //? RECETAS------------------------------------------------------------------------ -->
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
                }

                .card:hover {
                    transform: translateY(-10px);
                }

                .card-body {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .card-footer {
                    text-align: center;
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
            </style>

            <div class="container mt-5">
                <div class="row" id="recetas-container">
                    <?php
                    $recetas = [];

                    if ($categoria_filtro == 0) { // Todas las recetas
                        foreach ($recetas_por_categoria as $categoria_recetas) {
                            foreach ($categoria_recetas as $id_receta => $receta) {
                                $recetas[$id_receta] = $receta; // Usar el id_receta como índice para evitar duplicados
                            }
                        }
                    } else { // Recetas de una categoría específica
                        $recetas = isset($recetas_por_categoria[$categoria_filtro]) ? $recetas_por_categoria[$categoria_filtro] : [];
                    }
                    ?>
                    <?php if (!empty($recetas)): ?>
                        <?php foreach ($recetas as $receta): ?>
                            <div class="col-md-6 mb-3 receta-item">
                                <a href="vista-detalle-receta.php?id=<?= $receta['id_receta']; ?>" class="card-link">
                                    <div class="card mb-3 h-100 shadow-lg border-0 rou">
                                        <div class="row g-0">
                                            <div class="col-md-5">
                                                <img src="<?= $receta['imagenes'][0] ?>" class="img-fluid rounded-start custom-img" alt="...">
                                            </div>
                                            <div class="col-md-7">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <h5 class="card-title"><?= $receta['titulo'] ?></h5>
                                                    <p class="card-text"><?= generar_estrellas($receta['promedio_calificacion']) ?></p>
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text"><small class="text-muted"><i class="bi bi-person-fill"></i> <?= $receta['nombre_usuario'] ?></small></p>
                                                        <p class="card-text"><small class="text-muted"><i class="bi bi-calendar-fill"></i> <?= formatear_fecha($receta['fecha_creacion']) ?></small></p>
                                                    </div>
                                                    <p class="card-text mt-3"><?= limitar_descripcion($receta['descripcion']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay recetas disponibles en esta categoría.</p>
                    <?php endif; ?>
                </div>
                <br>
                <br>
                <nav aria-label="Page navigation example mt-5">
                    <ul class="pagination justify-content-center" id="pagination-container">

                    </ul>
                </nav>
            </div>

            <style>
                .card-link {
                    text-decoration: none;
                    color: inherit;
                }

                .card-link:hover .card {
                    transform: translateY(-10px);
                }
            </style>

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
                }

                .card:hover {
                    transform: translateY(-10px);
                }

                .card-body {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .card-footer {
                    text-align: center;
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

                .custom-img {
                    height: 100%;
                    width: 100%;
                    object-fit: cover;
                    border-top-left-radius: 20px;
                    border-bottom-left-radius: 20px;
                }

                .card .row.g-0 {
                    height: 100%;
                }

                .card .col-md-5 {
                    display: flex;
                    align-items: stretch;
                }

                .card .col-md-5 img {
                    border-top-left-radius: 15px;
                    border-bottom-left-radius: 15px;
                }

                .pagination .page-link {
                    color: #ff6426;

                    /* Color primary */
                }

                .pagination .page-item.active .page-link {
                    background-color: #ff6426;
                    /* Color primary */
                    border-color: #ff6426;
                    /* Color primary */
                }
            </style>


        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const recetasContainer = document.getElementById('recetas-container');
            const paginationContainer = document.getElementById('pagination-container');
            const recetasPorPagina = 6;
            let currentPage = 1;

            function mostrarRecetasPagina(pagina) {
                const recetas = Array.from(recetasContainer.querySelectorAll('.receta-item'));
                const inicio = (pagina - 1) * recetasPorPagina;
                const fin = inicio + recetasPorPagina;

                recetas.forEach((receta, index) => {
                    if (index >= inicio && index < fin) {
                        receta.style.display = '';
                    } else {
                        receta.style.display = 'none';
                    }
                });

                generarPaginacion(recetas.length, pagina);
            }

            function generarPaginacion(totalRecetas, paginaActual) {
                const totalPaginas = Math.ceil(totalRecetas / recetasPorPagina);
                paginationContainer.innerHTML = '';

                for (let i = 1; i <= totalPaginas; i++) {
                    const li = document.createElement('li');
                    li.classList.add('page-item');
                    if (i === paginaActual) {
                        li.classList.add('active');
                    }
                    const a = document.createElement('a');
                    a.classList.add('page-link', 'text-dark');
                    a.href = '#';
                    a.textContent = i;
                    a.addEventListener('click', function(event) {
                        event.preventDefault();
                        currentPage = i;
                        mostrarRecetasPagina(currentPage);
                    });
                    li.appendChild(a);
                    paginationContainer.appendChild(li);
                }
            }

            searchInput.addEventListener('input', function() {
                const query = searchInput.value.toLowerCase();
                const recetas = recetasContainer.querySelectorAll('.receta-item');

                recetas.forEach(function(receta) {
                    const title = receta.querySelector('.card-title').textContent.toLowerCase();
                    if (title.includes(query)) {
                        receta.style.display = '';
                    } else {
                        receta.style.display = 'none';
                    }
                });

                // Si el campo de búsqueda está vacío, mostrar todas las recetas
                if (query === '') {
                    mostrarRecetasPagina(currentPage);
                } else {
                    paginationContainer.innerHTML = ''; // Clear pagination when searching
                }
            });

            mostrarRecetasPagina(currentPage); // Initial call to display the first page
        });
    </script>


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
    <!-- custom js -->
    <script src="../js/custom.js"></script>


</body>



</html>