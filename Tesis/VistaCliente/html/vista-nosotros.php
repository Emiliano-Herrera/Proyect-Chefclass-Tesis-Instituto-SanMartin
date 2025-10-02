<?php
include("conexion.php");
session_start();
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
}
?>

<!doctype html>
<html lang="en">


<!-- Mirrored from technext.github.io/dingo/about.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:36 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Nosotros</title>
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
                                    <a class="dropdown-item <?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
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
                            <h2>Sobre Nosotros</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!-- about part start-->
    <section class="about_part about_bg">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-4 col-lg-5 offset-lg-1">
                    <div class="about_img">
                        <img src="../img/about.png" alt="">
                    </div>
                </div>
                <div class="col-sm-8 col-lg-4">
                    <div class="about_text">
                        <h2>ChefClass nuestra historia.</h2>
                        <h4>Un lugar para quienes aman cocinar, compartir y disfrutar de la buena comida.</h4>
                        <p>
                            ChefClass nació del deseo de unir a personas apasionadas por la cocina, sin importar su experiencia. Aquí, cada receta cuenta una historia y cada usuario es parte de una gran familia culinaria. 
                            <br><br>
                            Nos encanta ver cómo la creatividad y el entusiasmo de nuestra comunidad llenan de sabor cada rincón del sitio. En ChefClass, no solo compartimos recetas: compartimos momentos, recuerdos y la alegría de cocinar juntos.
                            <br><br>
                            Si te apasiona la cocina, este es tu lugar. Descubrí nuevas ideas, inspirate con otros amantes de la gastronomía y animate a compartir tus propias creaciones. ¡Bienvenido a la comunidad donde la comida une y emociona!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="alternadas my-5">
        <div class="container mb-5">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h1 class="mb-3" style="font-weight: 300;">Novedades</h1>
                    <div class="texto-expandible">
                        Descubrí las últimas novedades, historias y logros de nuestra comunidad. ¡Cada día somos más los que compartimos la pasión por la cocina!
                        <span class="texto-mas d-none">
                            <br>
                            Nos enorgullece ver cómo crece ChefClass gracias a personas como vos, que aportan sus recetas, consejos y buena onda. Seguimos sumando nuevas funciones y espacios para que puedas inspirarte, aprender y conectar con otros amantes de la gastronomía. ¡Animate a ser parte de nuestras novedades y a dejar tu huella en la comunidad!
                        </span>
                    </div>
                    <button class="btn-naranja mt-3 btn-ver-mas">Ver más</button>
                </div>
                <div class="col-md-6 text-center">
                    <img src="../img/2.jpg" alt="Novedades" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>

        <!-- Sección 2: Imagen izquierda, texto derecha -->
        <div class="container mb-5">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0 order-md-1 order-2">
                    <img src="../img/img_1.jpg" alt="Compromiso" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6 order-md-2 order-1">
                    <h1 class="mb-3" style="font-weight: 300;">Nuestro compromiso</h1>
                    <div class="texto-expandible">
                        En ChefClass creemos en el poder de la comida para unir personas y crear recuerdos inolvidables. Nos comprometemos a mantener un espacio cálido, inclusivo y lleno de inspiración.
                        <span class="texto-mas d-none">
                            <br>
                            Trabajamos cada día para que encuentres recetas auténticas, consejos útiles y una comunidad que te motive a seguir creciendo en tu pasión. Valoramos la diversidad de sabores, culturas y experiencias, porque sabemos que cada plato tiene una historia única. ¡Gracias por ser parte de este viaje culinario con nosotros!
                        </span>
                    </div>
                    <button class="btn-naranja mt-3 btn-ver-mas">Ver más</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-ver-mas').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var textoExpandible = btn.previousElementSibling;
                    var textoMas = textoExpandible.querySelector('.texto-mas');
                    if (textoMas.classList.contains('d-none')) {
                        textoMas.classList.remove('d-none');
                        btn.textContent = 'Ver menos';
                    } else {
                        textoMas.classList.add('d-none');
                        btn.textContent = 'Ver más';
                        textoExpandible.scrollIntoView({
                            behavior: "smooth"
                        });
                    }
                });
            });
        });
    </script>

    <!--::chefs_part start::-->
    <section class="chefs_part blog_item_section section_padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="section_tittle">
                        <h1>Fundadores de ChefClass</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="single_blog_item">
                        <div class="single_blog_img">
                            <img src="../img/team/chefs_1.png" alt="">
                        </div>
                        <div class="single_blog_text text-center">
                            <h3>Lucas Salvatierra</h3>
                            <p>Chef Master</p>
                            <div class="social_icon">
                                <a href="#"> <i class="ti-facebook"></i> </a>
                                <a href="#"> <i class="ti-twitter-alt"></i> </a>
                                <a href="#"> <i class="ti-instagram"></i> </a>
                                <a href="#"> <i class="ti-skype"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="single_blog_item">
                        <div class="single_blog_img">
                            <img src="../img/team/chefs_2.png" alt="">
                        </div>
                        <div class="single_blog_text text-center">
                            <h3>Emiliano Olivera</h3>
                            <p>Desarrollador web</p>
                            <div class="social_icon">
                                <a href="#"> <i class="ti-facebook"></i> </a>
                                <a href="#"> <i class="ti-twitter-alt"></i> </a>
                                <a href="#"> <i class="ti-instagram"></i> </a>
                                <a href="#"> <i class="ti-skype"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--::chefs_part end::-->

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

    <script>
        document.querySelectorAll('.subir-receta-no-logeado').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Debes iniciar sesión',
                    text: 'Por favor, inicia sesión para poder subir una receta.',
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>


<!-- Mirrored from technext.github.io/dingo/about.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:36 GMT -->

</html>