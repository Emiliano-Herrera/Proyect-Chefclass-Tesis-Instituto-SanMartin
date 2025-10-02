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

$sql = "SELECT U.*, G.*, R.nombre_rol, T.telefono AS user_telefono, E.email AS user_email FROM usuarios U LEFT JOIN generos G ON U.genero = G.id_genero LEFT JOIN roles R ON U.rol = R.id_rol LEFT JOIN telefonos_usuarios T ON U.id_usuario = T.id_usuario LEFT JOIN emails_usuarios E ON U.id_usuario = E.id_usuario WHERE U.id_usuario = ? ORDER BY U.id_usuario ASC";
$statement = $conexion->prepare($sql);
$statement->bind_param("i", $ID_Usuario);
$statement->execute();
$resultado = $statement->get_result();
$filas = [];
while ($fila = $resultado->fetch_assoc()) {
    $filas[] = $fila;
}
// Obtener emails, teléfonos y roles únicos 
$emails = array_unique(array_column($filas, 'user_email'));
$telefonos = array_unique(array_column($filas, 'user_telefono'));
$roles = array_unique(array_column($filas, 'nombre_rol'));


?>





<!doctype html>
<html lang="en">


<!-- Mirrored from technext.github.io/dingo/about.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:36 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dingo</title>
    <link rel="icon" href="img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <!-- themify CSS -->
    <link rel="stylesheet" href="css/themify-icons.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="css/flaticon.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="css/magnific-popup.css">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/gijgo.min.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/all.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="css/style.css">
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

                        <div class="collapse navbar-collapse main-menu-item justify-content-end"
                            id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.html">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="about.html">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="food_menu.html">Menu</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="chefs.html">Chefs</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Blog
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="blog.html">Blog</a>
                                        <a class="dropdown-item" href="single-blog.html">Single blog</a>
                                        <a class="dropdown-item" href="elements.html">Elements</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="contact.html">Contact</a>
                                </li>
                            </ul>
                        </div>
                        <div class="menu_btn">
                            <a href="#" class="single_page_btn d-none d-sm-block">book a tabel</a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Header part end-->

    <!-- breadcrumb start-->
    <!-- <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner text-center">
                        <div class="breadcrumb_iner_item">
                            <h2>USERNAME DEL CHEF</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- breadcrumb start-->


    <!--::review_part start::-->

    <section class="about_part">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-4 col-lg-5 offset-lg-1">
                    <div class="about_img">
                        <img src="<?php echo $filas[0]['img'] ?>" alt="">
                    </div>
                </div>
                <div class="col-sm-8 col-lg-4">
                    <div class="about_text">
                        <h5>Nuestra historia</h5>
                        <h2><?php echo $filas[0]['nombre'], ' ', $filas[0]['apellido'] ?></h2>
                        <h4><?php echo $filas[0]['nombre_usuario'] ?></h4>
                        <h5> Email:
                            <span>
                                <?php if (!empty($emails)): ?>
                                    <?php foreach ($emails as $email) {
                                        echo " $email<br>";
                                    } ?>
                                <?php endif; ?>
                            </span>
                        </h5>
                        <h5 class="fw-medium me-2">Nombre de usuario: <?php echo $filas[0]['nombre_usuario'] ?></h5>

                        <h5> Contacto
                            <?php if (!empty($telefonos)): ?>
                                <?php foreach ($telefonos as $telefono) {
                                    echo " $telefono<br>";
                                } ?> <?php endif; ?> </h5>
                        <h5>Genero: <?php echo $filas[0]['nombre_genero'] ?></h5>
                        <h5>Fecha de creación de usuario: <?php echo $filas[0]['fecha_creacion'] ?></h5>




                        <div class="d-flex justify-content-around flex-wrap my-4 py-3">
                            <div class="d-flex align-items-start me-4 mt-3 gap-3">
                                <span class="badge bg-label-primary p-2 rounded"><!-- <i class='bx bx-user bx-sm'></i> --><i class='bx bx-group bx-sm'></i></span>
                                <?php

                                // Consulta para contar los seguidores 
                                $sql_seguidores = "SELECT COUNT(*) AS total_seguidores FROM seguimiento WHERE seguido_id = ?";
                                $statement_seguidores = $conexion->prepare($sql_seguidores);
                                $statement_seguidores->bind_param("i", $ID_Usuario);
                                $statement_seguidores->execute();
                                $resultado_seguidores = $statement_seguidores->get_result();
                                $seguidores = $resultado_seguidores->fetch_assoc()['total_seguidores'];

                                // Consulta para contar los usuarios que sigue 
                                $sql_seguidos = "SELECT COUNT(*) AS total_seguidos FROM seguimiento WHERE seguidor_id = ?";
                                $statement_seguidos = $conexion->prepare($sql_seguidos);
                                $statement_seguidos->bind_param("i", $ID_Usuario);
                                $statement_seguidos->execute();
                                $resultado_seguidos = $statement_seguidos->get_result();
                                $seguidos = $resultado_seguidos->fetch_assoc()['total_seguidos'];
                                ?>
                                <div>
                                    <h5 class="mb-0"><?php echo number_format($seguidores); ?> </h5>
                                    <span>seguidores</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mt-3 gap-3">
                                <span class="badge bg-label-primary p-2 rounded"><!-- <i class='bx bx-customize bx-sm'></i> --><i class='bx bx-user-check bx-sm'></i></span>
                                <div>
                                    <h5 class="mb-0"><?php echo number_format($seguidos); ?></h5>
                                    <span>seguidos</span>
                                </div>
                            </div>







                        </div>
                        
                        <a href="vista-subir-receta.php" class="btn_3">Subir receta <img src="img/icon/left_2.svg" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--::review_part end::-->
    <!--::chefs_part start::-->








    <?php
    include('../vistaadmin/html/conexion.php'); // Asegúrate de que la conexión esté correctamente referenciada

    // Parámetros de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6; // Número de recetas por página
    $offset = ($page - 1) * $limit;

    // Inicializar variables
    $recetas = [];

    // Consulta para obtener las recetas con sus detalles y limitarlas según la paginación
    $sql_recetas = "SELECT r.id_receta, r.titulo, GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias, 
                    (SELECT i.url_imagen FROM img_recetas i JOIN imagenes_recetas ir ON i.id_img = ir.img_id WHERE ir.recetas_id = r.id_receta ORDER BY i.id_img ASC LIMIT 1) AS imagen
                    FROM recetas r
                    JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                    JOIN categoria c ON rc.categoria_id = c.id_categoria
                    GROUP BY r.id_receta
                    LIMIT ? OFFSET ?";
    $stmt = $conexion->prepare($sql_recetas);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($receta = $result->fetch_assoc()) {
        if ($receta['imagen']) {
            $receta['imagen'] = '../VistaAdmin/html/' . $receta['imagen']; // Construir la ruta absoluta
        } else {
            $receta['imagen'] = ''; // Imagen por defecto
        }
        $recetas[] = $receta;
    }

    // Contar el total de recetas para la paginación
    $sql_total = "SELECT COUNT(DISTINCT r.id_receta) AS total FROM recetas r";
    $result_total = $conexion->query($sql_total);
    $total = $result_total->fetch_assoc()['total'];
    $total_pages = ceil($total / $limit);

    $conexion->close();
    ?>












    <section class="chefs_part blog_item_section section_padding">
        <div class="container">


            <div class="row"> <?php foreach ($recetas as $receta): ?> <div class="col-sm-6 col-lg-4">
                        <div class="single_blog_item">
                            <div class="single_blog_img"> <img src="<?php echo $receta['imagen']; ?>" alt="Imagen de la receta" onerror="this.onerror=null; this.src='/Tesis/vistaadmin/html/uploads/default.jpg';"> </div>
                            <div class="single_blog_text text-center">
                                <h3><?php echo $receta['titulo']; ?></h3>
                                <p><?php echo $receta['categorias']; ?></p>
                                <div class="social_icon"> <a href="#"> <i class="ti-facebook"></i> </a> <a href="#"> <i class="ti-twitter-alt"></i> </a> <a href="#"> <i class="ti-instagram"></i> </a> <a href="#"> <i class="ti-skype"></i> </a> </div>
                            </div>
                        </div>
                    </div> <?php endforeach; ?> </div>
            <nav class="blog-pagination justify-content-center d-flex">
                <ul class="pagination"> <?php if ($page > 1): ?> <li class="page-item"> <a href="?page=<?php echo $page - 1; ?>" class="page-link" aria-label="Previous"> <i class="ti-angle-left"></i> </a> </li> <?php endif; ?> <?php for ($i = 1; $i <= $total_pages; $i++): ?> <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>"> <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a> </li> <?php endfor; ?> <?php if ($page < $total_pages): ?> <li class="page-item"> <a href="?page=<?php echo $page + 1; ?>" class="page-link" aria-label="Next"> <i class="ti-angle-right"></i> </a> </li> <?php endif; ?> </ul>
            </nav>


        </div>
    </section>
    <!--::chefs_part end::-->

    <!-- footer part start-->
    <footer class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_1">
                        <h4>About Us</h4>
                        <p>Heaven fruitful doesn't over for these theheaven fruitful doe over days
                            appear creeping seasons sad behold beari ath of it fly signs bearing
                            be one blessed after.</p>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Important Link</h4>
                        <div class="contact_info">
                            <ul>
                                <li><a href="#">WHMCS-bridge</a></li>
                                <li><a href="#"> Search Domain</a></li>
                                <li><a href="#">My Account</a></li>
                                <li><a href="#">Shopping Cart</a></li>
                                <li><a href="#"> Our Shop</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Contact us</h4>
                        <div class="contact_info">
                            <p><span> Address :</span>Hath of it fly signs bear be one blessed after </p>
                            <p><span> Phone :</span> +2 36 265 (8060)</p>
                            <p><span> Email : </span>info@colorlib.com </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-8 col-md-6">
                    <div class="single-footer-widget footer_3">
                        <h4>Newsletter</h4>
                        <p>Heaven fruitful doesn't over lesser in days. Appear creeping seas</p>
                        <form action="#">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder='Email Address'
                                        onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email Address'">
                                    <div class="input-group-append">
                                        <button class="btn" type="button"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright_part_text">
                <div class="row">

                    <div class="col-lg-4">
                        <div class="copyright_social_icon text-right">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="ti-dribbble"></i></a>
                            <a href="#"><i class="fab fa-behance"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer part end-->

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
    <!-- custom js -->
    <script src="js/custom.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>



</body>


<!-- Mirrored from technext.github.io/dingo/about.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:36 GMT -->

</html>