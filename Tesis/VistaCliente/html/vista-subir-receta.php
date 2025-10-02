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
            <!-- <div class="d-none d-sm-block mb-5 pb-4">
                <div id="map" style="height: 480px;"></div>
                <script>
                    function initMap() {
                        var uluru = {
                            lat: -25.363,
                            lng: 131.044
                        };
                        var grayStyles = [{
                                featureType: "all",
                                stylers: [{
                                        saturation: -90
                                    },
                                    {
                                        lightness: 50
                                    }
                                ]
                            },
                            {
                                elementType: 'labels.text.fill',
                                stylers: [{
                                    color: '#ccdee9'
                                }]
                            }
                        ];
                        var map = new google.maps.Map(document.getElementById('map'), {
                            center: {
                                lat: -31.197,
                                lng: 150.744
                            },
                            zoom: 9,
                            styles: grayStyles,
                            scrollwheel: false
                        });
                    }
                </script>
                <script
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpfS1oRGreGSBU5HHjMmQ3o5NLw7VdJ6I&amp;callback=initMap">
                </script>

            </div> -->


            <!-- <div class="row">
                <div class="col-12">
                    <h2 class="contact-title">Get in Touch</h2>
                </div>
                <div class="col-lg-15">
                    <form class="form-contact contact_form" action="https://technext.github.io/dingo/contact_process.php" method="post" id="contactForm"
                        novalidate="novalidate">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">

                                    <textarea class="form-control w-100" name="message" id="message" cols="30" rows="9"
                                        onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Message'"
                                        placeholder='Enter Message'></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control" name="name" id="name" type="text" onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Enter your name'" placeholder='Enter your name'>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control" name="email" id="email" type="email" onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Enter email address'" placeholder='Enter email address'>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Enter Subject'" placeholder='Enter Subject'>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="button button-contactForm btn_4">Send Message</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-home"></i></span>
                        <div class="media-body">
                            <h3>Buttonwood, California.</h3>
                            <p>Rosemead, CA 91770</p>
                        </div>
                    </div>
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-tablet"></i></span>
                        <div class="media-body">
                            <h3>00 (440) 9865 562</h3>
                            <p>Mon to Fri 9am to 6pm</p>
                        </div>
                    </div>
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-email"></i></span>
                        <div class="media-body">
                            <h3>support@colorlib.com</h3>
                            <p>Send us your query anytime!</p>
                        </div>
                    </div>
                </div>
            </div> -->

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
                        <h3 class="card-header">Subir una receta</h3>
                        <div class="card-body">

                            <style>
                                .error {
                                    border: 2px solid red !important;
                                }

                                .text-danger {
                                    font-size: 0.9em;
                                }
                            </style>

                            <form id="formValidationExamples" class="row g-3" method="POST" action="insert-receta.php" enctype="multipart/form-data">
                                <!-- 1. Datos de la receta -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>1. Datos de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>
                                <!-- Justo antes de <form id="formValidationExamples"... -->
                                <div class="col-12 mb-2">
                                    <small class="text-muted">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</small>
                                </div>
                                <!-- Título -->
                                <div class="col-md-6">
                                    <label class="form-label" for="titulo">Título <span class="text-danger">*</span></label>
                                    <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo" data-required="true" />
                                    <small class="text-danger d-none" id="error-titulo">Debes ingresar un título.</small>
                                </div>

                                <!-- Tiempo de preparación -->
                                <div class="col-md-6">
                                    <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM) <span class="text-danger">*</span></label>
                                    <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación" name="tiempo" data-required="true" />
                                    <small class="text-danger d-none" id="error-tiempo">Debes ingresar el tiempo de preparación.</small>
                                </div>

                                <!-- Descripción -->
                                <div class="col-md-12">
                                    <label class="form-label" for="descripcion">Descripción <span class="text-danger">*</span></label>
                                    <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta" name="descripcion" rows="3" data-required="true"></textarea>
                                    <small class="text-danger d-none" id="error-descripcion">Debes ingresar una descripción.</small>
                                </div>

                                <!-- Dificultad -->
                                <div class="col-md-6">
                                    <label class="form-label">Dificultad <span class="text-danger">*</span></label>
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-facil" name="dificultad" class="form-check-input" value="Fácil" data-required="true" />
                                        <label class="form-check-label" for="dificultad-facil">Fácil</label>
                                    </div>
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-intermedio" name="dificultad" class="form-check-input" value="Intermedio" data-required="true" />
                                        <label class="form-check-label" for="dificultad-intermedio">Intermedio</label>
                                    </div>
                                    <div class="form-check custom mb-2">
                                        <input type="radio" id="dificultad-dificil" name="dificultad" class="form-check-input" value="Difícil" data-required="true" />
                                        <label class="form-check-label" for="dificultad-dificil">Difícil</label>
                                    </div>
                                    <small class="text-danger d-none" id="error-dificultad">Debes seleccionar la dificultad.</small>
                                </div>



                                <!-- Selector de Categorías -->
                                <div class="col-md-6">
                                    <label class="form-label" for="categoria">Categorías <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <select class="form-select" id="categoria" name="categoria[]" data-required="true">
                                            <option disabled value="">Selecciona una categoría...</option>
                                            <?php
                                            include('conexion.php');
                                            $sql_categoria = "SELECT id_categoria, nombre FROM categoria WHERE estado = 'habilitado' ";
                                            $result_categoria = $conexion->query($sql_categoria);
                                            if ($result_categoria->num_rows > 0) {
                                                while ($row = $result_categoria->fetch_assoc()) {
                                                    echo "<option value='{$row['id_categoria']}'>{$row['nombre']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-success" id="add-categoria-btn">Agregar Categoría</button>
                                        <small class="text-danger d-none" id="error-categoria">Debes seleccionar al menos una categoría.</small>
                                    </div>


                                    <div id="selected-categorias"></div>


                                </div>



                                <!-- 2. Instrucciones de la receta -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>2. Instrucciones de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>
                                <!-- Pasos (solo el primer paso, los demás se agregan dinámicamente igual) -->
                                <div class="col-md-12">
                                    <label class="form-label" for="instrucciones">Pasos <span class="text-danger">*</span></label>
                                    <div id="instrucciones-container">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" value="1" readonly data-required="true" />
                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" data-required="true" />
                                            <button class="btn btn-outline-success add-instruccion-btn" type="button">Agregar paso</button>
                                        </div>

                                    </div>
                                    <small class="text-danger d-none" id="error-pasos">Debes ingresar al menos un paso.</small>
                                </div>
                                <!-- 3. Ingredientes de la receta -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>3. Ingredientes de la receta</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>
                                <!-- Para el autocompletado de los ingredientes -->

                                <!-- ...código anterior... -->

                                <!-- Para el autocompletado de los ingredientes -->
                                <?php
                                include('conexion.php');
                                $ingredientes = [];
                                $sql = "SELECT nombre FROM ingredientes";
                                $result = $conexion->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $ingredientes[] = $row['nombre'];
                                }
                                ?>
                                <script>
                                    const ingredientesBD = <?php echo json_encode($ingredientes); ?>;
                                </script>
                                <datalist id="ingredientes-list">
                                    <?php foreach ($ingredientes as $ing): ?>
                                        <option value="<?php echo htmlspecialchars($ing); ?>">
                                        <?php endforeach; ?>
                                </datalist>

                                <!-- Para el autocompletado de las cantidades -->
                                <?php
                                $cantidadesSugeridas = [
                                    "1/2",
                                    "1/5",
                                    "0/5",
                                    "2/3",
                                    "2/5",
                                    "100",
                                    "200",
                                    "250",
                                    "500",
                                    "1000",
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
                                    "25"
                                    // Agregar mas si necesitamos mas sugerencias
                                ];
                                ?>
                                <script>
                                    const cantidadesBD = <?php echo json_encode($cantidadesSugeridas); ?>;
                                </script>
                                <datalist id="cantidades-list">
                                    <?php foreach ($cantidadesSugeridas as $cant): ?>
                                        <option value="<?php echo htmlspecialchars($cant); ?>">
                                        <?php endforeach; ?>
                                </datalist>


                                <script>
                                    // Array de ingredientes desde PHP
                                    const ingredientesBD = <?php echo json_encode($ingredientes); ?>;
                                </script>
                                <datalist id="ingredientes-list">
                                    <?php foreach ($ingredientes as $ing): ?>
                                        <option value="<?php echo htmlspecialchars($ing); ?>">
                                        <?php endforeach; ?>
                                </datalist>
                                <datalist id="ingredientes-list">
                                    <?php foreach ($ingredientes as $ing): ?>
                                        <option value="<?php echo htmlspecialchars($ing); ?>">
                                        <?php endforeach; ?>
                                </datalist>
                                <!-- Ingredientes (solo el primer ingrediente, los demás se agregan dinámicamente igual) -->
                                <div class="col-md-12">
                                    <label class="form-label" for="ingredientes">Ingredientes <span class="text-danger">*</span></label>

                                    <div id="ingredientes-container">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" required />
                                            <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad (ej: 1, 1/2, 2, 1/2)" list="cantidades-list" />
                                            <input type="text" class="form-control" name="unidades[]" placeholder="Unidad (ej: Taza, Cucharada, A gusto)" list="unidades-list" />
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
                                            <button class="btn btn-outline-success add-ingrediente-btn" type="button">Agregar ingrediente</button>
                                        </div>
                                    </div>

                                    <small class="text-danger d-none" id="error-ingredientes">Debes ingresar ingredientes.</small>
                                </div>
                                <!-- 4. Sube imágenes o videos de tu receta -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <h4>4. Subir fotos o videos de la receta (Asegurate de seleccionar todas las imagenes o videos de una vez)</h4>
                                    <hr class="mt-0" style="width: 60%; min-width: 120px;">
                                </div>

                                <!-- Subir fotos o videos -->
                                <div class="col-12">
                                    <label class="form-label" for="media_files">Subir fotos o videos <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="inputGroupFile01">Subir</label>
                                        <input type="file" class="form-control" id="inputGroupFile01" name="media_files[]" accept="image/*,video/*" multiple data-required="true" />
                                    </div>
                                    <small class="text-danger d-none" id="error-media">Debes subir al menos una imagen o video.</small>
                                </div>
                                <div class="col-12 justify-content-between">
                                    <button type="submit" class="button button-contactForm btn_4 me-2">Subir receta</button>
                                    <a href="index.php" class="btn btn-label-secondary">Cancelar</a>
                                </div>
                            </form>




                        </div>
                    </div>
                </div>
            </div>



        </div>
    </section>
    <!-- ================ contact section end ================= -->


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



    <!-- footer part start-->
    <!--
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
                                    <input type="text" class="form-control" placeholder='Email Address' onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Email Address'">
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
    </footer>-->
    <!-- footer part end-->

    <!-- Scrip para poner en contorno rojo cuando se saltea algun input o select del formulario -->
    <!-- Y para los sweetAlert cuando falta algo del formulario -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LIMPIEZA DE ESPACIOS ---
            function limpiarEspaciosInput(input) {
                input.value = input.value
                    .replace(/^\s+/, '') // sin espacios al principio
                    .replace(/\s{2,}/g, ' ') // solo un espacio entre palabras
                    .replace(/(\s+)$/, function(match) {
                        return match.length > 1 ? ' ' : match; // máximo un espacio al final
                    });
            }
            // Aplica limpieza en tiempo real a todos los inputs de texto y textarea
            document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
                input.addEventListener('input', function() {
                    limpiarEspaciosInput(input);
                });
            });

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
            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" data-required="true" />
            <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
        `;
                container.appendChild(newInputGroup);

                // Limpieza y validación en el nuevo input
                newInputGroup.querySelectorAll('input[type="text"]').forEach(function(input) {
                    input.addEventListener('input', function() {
                        limpiarEspaciosInput(input);
                    });
                });
                newInputGroup.querySelectorAll('[data-required]').forEach(addValidationEvents);

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
        <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" required />
        <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad (ej: 1, 1/2, 2 1/2, a gusto)" />
        <input type="text" class="form-control" name="unidades[]" placeholder="Unidad (ej: taza, cucharada, a gusto)" />
        <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
    `;
                container.appendChild(newInputGroup);

                newInputGroup.querySelector('.remove-ingrediente-btn').addEventListener('click', function() {
                    container.removeChild(newInputGroup);
                });
            });




            // --- VALIDACIÓN DE CAMPOS DINÁMICOS ---
            function addValidationEvents(field) {
                field.addEventListener('blur', function() {
                    validateField(field);
                });
                field.addEventListener('input', function() {
                    if (field.value.trim()) {
                        field.classList.remove('error');
                    }
                    if (field.name === "pasos[]") {
                        limpiarEspaciosInput(field);
                    }
                });
            }

            // --- VALIDACIÓN VISUAL Y PALABRAS PROHIBIDAS ---
            const form = document.getElementById('formValidationExamples');
            form.addEventListener('submit', function(event) {
                let valido = true;

                // Limpia espacios antes de validar
                document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
                    input.value = input.value.replace(/^\s+|\s+$/g, '').replace(/\s{2,}/g, ' ');
                });

                // Título
                const titulo = document.getElementById('titulo');
                if (!titulo.value.trim()) {
                    mostrarError('error-titulo', 'Debes ingresar un título.');
                    titulo.classList.add('error');
                    valido = false;
                } else {
                    ocultarError('error-titulo');
                    titulo.classList.remove('error');
                }

                // Tiempo de preparación
                const tiempo = document.getElementById('tiempo');
                if (!tiempo.value.trim()) {
                    mostrarError('error-tiempo', 'Debes ingresar el tiempo de preparación.');
                    tiempo.classList.add('error');
                    valido = false;
                } else {
                    ocultarError('error-tiempo');
                    tiempo.classList.remove('error');
                }

                // Descripción
                const descripcion = document.getElementById('descripcion');
                if (!descripcion.value.trim()) {
                    mostrarError('error-descripcion', 'Debes ingresar una descripción.');
                    descripcion.classList.add('error');
                    valido = false;
                } else {
                    ocultarError('error-descripcion');
                    descripcion.classList.remove('error');
                }

                // Dificultad
                const dificultadRadios = document.querySelectorAll('input[name="dificultad"]');
                const dificultadChecked = Array.from(dificultadRadios).some(radio => radio.checked);
                if (!dificultadChecked) {
                    mostrarError('error-dificultad', 'Debes seleccionar la dificultad.');
                    dificultadRadios.forEach(radio => radio.classList.add('error'));
                    valido = false;
                } else {
                    ocultarError('error-dificultad');
                    dificultadRadios.forEach(radio => radio.classList.remove('error'));
                }

                // Categoría
                const categoria = document.getElementById('categoria');
                const selectedCategorias = document.querySelectorAll('#selected-categorias input[name="categoria[]"]');
                if ((!categoria.value.trim() && selectedCategorias.length === 0) || categoria.value === "") {
                    mostrarError('error-categoria', 'Debes seleccionar al menos una categoría.');
                    categoria.classList.add('error');
                    valido = false;
                } else {
                    ocultarError('error-categoria');
                    categoria.classList.remove('error');
                }

                // Pasos
                const pasos = document.querySelectorAll('input[name="pasos[]"]');
                let pasoVacio = false;
                pasos.forEach(paso => {
                    if (!paso.value.trim()) pasoVacio = true;
                });
                if (pasos.length === 0 || pasoVacio) {
                    mostrarError('error-pasos', 'Debes ingresar la descripcion del paso.');
                    pasos.forEach(paso => paso.classList.add('error'));
                    valido = false;
                } else {
                    ocultarError('error-pasos');
                    pasos.forEach(paso => paso.classList.remove('error'));
                }

                // Ingredientes
                const ingredientes = document.querySelectorAll('input[name="ingredientes[]"]');
                let ingredienteVacio = false;
                ingredientes.forEach(ing => {
                    if (!ing.value.trim()) ingredienteVacio = true;
                });
                if (ingredientes.length === 0 || ingredienteVacio) {
                    mostrarError('error-ingredientes', 'Debes ingresar al menos un ingrediente.');
                    ingredientes.forEach(ing => ing.classList.add('error'));
                    valido = false;
                } else {
                    ocultarError('error-ingredientes');
                    ingredientes.forEach(ing => ing.classList.remove('error'));
                }

                // Media files
                const media = document.getElementById('inputGroupFile01');
                if (!media.files || media.files.length === 0) {
                    mostrarError('error-media', 'Debes subir al menos una imagen o video.');
                    media.classList.add('error');
                    valido = false;
                } else {
                    ocultarError('error-media');
                    media.classList.remove('error');

                    // Validación de tamaño de archivos individuales
                    const MAX_SIZE_MB = 40;
                    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
                    let archivosGrandes = [];
                    for (let file of media.files) {
                        if (file.size > MAX_SIZE_BYTES) {
                            archivosGrandes.push(file.name);
                        }
                    }
                    if (archivosGrandes.length > 0) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Archivo demasiado grande',
                            text: `Los siguientes archivos superan el límite de ${MAX_SIZE_MB} MB:\n${archivosGrandes.join(', ')}`,
                            confirmButtonText: 'Aceptar'
                        });
                        media.value = '';
                        valido = false;
                    }

                    // Validación de tamaño total de archivos
                    let totalSize = 0;
                    for (let file of media.files) {
                        totalSize += file.size;
                    }
                    const MAX_TOTAL_SIZE_MB = 40; // Límite total en MB (Igual que el post_max_size de PHP)
                    const MAX_TOTAL_SIZE_BYTES = MAX_TOTAL_SIZE_MB * 1024 * 1024;
                    if (totalSize > MAX_TOTAL_SIZE_BYTES) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Demasiado peso total',
                            text: `El tamaño total de los archivos supera el límite de ${MAX_TOTAL_SIZE_MB} MB.`,
                            confirmButtonText: 'Aceptar'
                        });
                        media.value = '';
                        valido = false;
                    }
                }

                // Si hay algún error, evita el envío
                if (!valido) {
                    event.preventDefault();
                    const primerError = document.querySelector('.error');
                    if (primerError) primerError.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                    return;
                }

                // --- PALABRAS PROHIBIDAS ---
                const forbiddenWords = ["gay", "gil", "topo", "topoide"];

                function containsForbiddenWords(text) {
                    for (let word of forbiddenWords) {
                        if (text.includes(word)) {
                            return true;
                        }
                    }
                    return false;
                }
                const instrucciones = Array.from(document.querySelectorAll('input[name="pasos[]"]')).map(input => input.value);
                if (
                    containsForbiddenWords(titulo.value) ||
                    containsForbiddenWords(descripcion.value) ||
                    instrucciones.some(containsForbiddenWords) ||
                    Array.from(ingredientes).some(input => containsForbiddenWords(input.value))
                ) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Uno o más campos contienen palabras no permitidas.`,
                        confirmButtonText: 'Aceptar'
                    });
                }
            });

            // --- AGREGAR CATEGORÍA DINÁMICA ---
            const addCategoriaBtn = document.getElementById('add-categoria-btn');
            const categoriaSelect = document.getElementById('categoria');
            const selectedCategoriasContainer = document.getElementById('selected-categorias');
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor evita seleccionar categorias duplicadas .',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });


            // Función para buscar sugerencias (case-insensitive, contiene)
            function sugerenciasIngrediente(valor) {
                valor = valor.trim().toLowerCase();
                if (!valor) return [];
                return ingredientesBD.filter(ing =>
                    ing.toLowerCase().includes(valor)
                );
            }

            function corregirIngrediente(valor) {
                valor = valor.trim().toLowerCase();
                // Solo autocorrige si hay coincidencia exacta (ignorando mayúsculas/minúsculas)
                let exacta = ingredientesBD.find(ing => ing.toLowerCase() === valor);
                if (exacta) return exacta;
                return null;
            }

            // Capitaliza la primera letra de cada palabra
            function capitalizarPrimeraLetra(texto) {
                return texto.replace(/\b\w/g, l => l.toUpperCase());
            }

            // Aplica a todos los inputs de ingredientes (incluyendo los dinámicos)
            function aplicarAutocompletado(input) {
                // Solo autocorrige cuando el usuario sale del input (blur) o presiona Enter
                input.addEventListener('blur', function() {
                    let valor = input.value.trim();
                    let correcion = corregirIngrediente(valor);
                    if (correcion && valor.length >= 3) {
                        input.value = correcion;
                    } else if (valor.length > 0) {
                        input.value = capitalizarPrimeraLetra(valor);
                    }
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        let valor = input.value.trim();
                        let correcion = corregirIngrediente(valor);
                        if (correcion && valor.length >= 3) {
                            input.value = correcion;
                        } else if (valor.length > 0) {
                            input.value = capitalizarPrimeraLetra(valor);
                        }
                    }
                });
            }

            // Inicial para los inputs ya presentes
            document.querySelectorAll('input[name="ingredientes[]"]').forEach(aplicarAutocompletado);

            // Para los inputs agregados dinámicamente
            const ingredientesContainer = document.getElementById('ingredientes-container');
            ingredientesContainer.addEventListener('input', function(e) {
                if (e.target && e.target.name === "ingredientes[]") {
                    aplicarAutocompletado(e.target);
                }
            }, true);


            function corregirCantidad(valor) {
                valor = valor.trim().toLowerCase();
                // Solo autocorrige si hay coincidencia exacta
                let exacta = cantidadesBD.find(cant => cant.toLowerCase() === valor);
                if (exacta) return exacta;
                return null;
            }

            function aplicarAutocorreccionCantidad(input) {
                input.addEventListener('blur', function() {
                    const valor = input.value;
                    const correcion = corregirCantidad(valor);
                    if (correcion && valor.length >= 2) {
                        input.value = correcion;
                    }
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const valor = input.value;
                        const correcion = corregirCantidad(valor);
                        if (correcion && valor.length >= 2) {
                            input.value = correcion;
                        }
                    }
                });
            }

            // Inicial para los inputs ya presentes
            document.querySelectorAll('input[name="cantidades[]"]').forEach(aplicarAutocorreccionCantidad);

            // Para los inputs agregados dinámicamente
            ingredientesContainer.addEventListener('input', function(e) {
                if (e.target && e.target.name === "cantidades[]") {
                    aplicarAutocorreccionCantidad(e.target);
                }
            }, true);

            function soloLetras(input) {
                input.addEventListener('input', function() {
                    // Permite solo letras (mayúsculas, minúsculas, tildes y espacios)
                    input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
                });
            }

            // Aplica a todos los inputs de ingredientes actuales
            document.querySelectorAll('input[name="ingredientes[]"]').forEach(soloLetras);

            // Para los inputs agregados dinámicamente
            ingredientesContainer.addEventListener('input', function(e) {
                if (e.target && e.target.name === "ingredientes[]") {
                    soloLetras(e.target);
                }
            }, true);

            // Solo letras para el tItulo
            const tituloInput = document.getElementById('titulo');
            if (tituloInput) {
                tituloInput.addEventListener('input', function() {
                    // Permite solo letras, tildes y espacios
                    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
                });
            }
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