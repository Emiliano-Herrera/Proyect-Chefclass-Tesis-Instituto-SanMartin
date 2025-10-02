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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass </title>
    <link rel="icon" href="../img/chefclassFinal.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- animate CSS -->
    <link rel="stylesheet" href="./css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="./css/owl.carousel.min.css">
    <!-- themify CSS -->
    <link rel="stylesheet" href="./css/themify-icons.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="./css/flaticon.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="./css/magnific-popup.css">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="./css/slick.css">
    <link rel="stylesheet" href="./css/gijgo.min.css">
    <link rel="stylesheet" href="./css/nice-select.css">
    <link rel="stylesheet" href="./css/all.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <style>
        .error {
            border: 2px solid red;
        }
    </style>

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
                                    <a class="nav-link" href="index.php">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="vista-nosotros.php">Nosotros</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="vista-categoria.php">Categorías</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="vista-equipo.php">Equipo</a>
                                </li>

                                <?php if (isset($_SESSION['id_usuario'])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="vista-perfil.php">Perfil</a>
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
                        <h5 class="card-header">Formulario para subir una receta</h5>
                        <div class="card-body">

                            <form id="formValidationExamples" class="row g-3" method="POST" action="insert-receta.php" enctype="multipart/form-data">
                                <!-- 1. Datos de la receta -->
                                <div class="col-12">
                                    <h6>1. Datos de la receta</h6>
                                    <hr class="mt-0" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="titulo">Título</label>
                                    <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo" data-required="true" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM)</label>
                                    <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación" name="tiempo" data-required="true" />
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="descripcion">Descripción</label>
                                    <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta" name="descripcion" rows="3" data-required="true"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dificultad</label>
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
                                </div>
                                <!-- Selector de Categorías -->
                                <div class="col-md-6">
                                    <label class="form-label" for="categoria">Categorías</label>
                                    <div class="input-group mb-3">
                                        <select class="form-select" id="categoria" name="categoria[]" data-required="true">
                                            <option disabled value="">Selecciona una categoría...</option>
                                            <?php
                                            include('conexion.php');
                                            $sql_categoria = "SELECT id_categoria, nombre FROM categoria";
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

                                    <script>
                                        document.getElementById('scroll-categoria-down').addEventListener('click', function() {
                                            const select = document.getElementById('categoria');
                                            const optionHeight = select.options[1] ? select.options[1].offsetHeight : 32; // fallback
                                            select.scrollBy({
                                                top: optionHeight,
                                                behavior: 'smooth'
                                            });
                                        });

                                        document.getElementById('scroll-categoria-up').addEventListener('click', function() {
                                            const select = document.getElementById('categoria');
                                            const optionHeight = select.options[1] ? select.options[1].offsetHeight : 32; // fallback
                                            select.scrollBy({
                                                top: -optionHeight,
                                                behavior: 'smooth'
                                            });
                                        });
                                    </script>
                                    <div id="selected-categorias"></div>


                                </div>
                                <!-- 2. Instrucciones de la receta -->
                                <div class="col-12 mt-5">
                                    <h6>2. Instrucciones de la receta</h6>
                                    <hr class="mt-0" />
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="instrucciones">Pasos</label>
                                    <div id="instrucciones-container">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" data-required="true" />
                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" data-required="true" />
                                            <button class="btn btn-outline-success add-instruccion-btn" type="button">Agregar paso</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- 3. Ingredientes de la receta -->
                                <div class="col-12 mt-5">
                                    <h6 class="mt-2">3. Ingredientes de la receta</h6>
                                    <hr class="mt-0" />
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="ingredientes">Ingredientes</label>
                                    <div id="ingredientes-container">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" data-required="true" />
                                            <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad" data-required="true" />
                                            <button class="btn btn-outline-success add-ingrediente-btn" type="button">Agregar ingrediente</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- 4. Sube imágenes o videos de tu receta -->
                                <div class="col-12 mt-5">
                                    <h6 class="mt-2">4. Subir fotos o videos de la receta (Asegurate de seleccionar todas las imagenes o videos de una vez)</h6>
                                    <hr class="mt-0" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="media_files">Subir fotos o videos</label>
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="inputGroupFile01">Subir</label>
                                        <input type="file" class="form-control" id="inputGroupFile01" name="media_files[]" accept="image/*,video/*" multiple data-required="true" />
                                    </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addCategoriaBtn = document.getElementById('add-categoria-btn');
            const categoriaSelect = document.getElementById('categoria');
            const selectedCategoriasContainer = document.getElementById('selected-categorias');

            // Evento para agregar una categoría
            addCategoriaBtn.addEventListener('click', function() {
                const selectedCategoriaValue = categoriaSelect.value;
                const selectedCategoriaText = categoriaSelect.options[categoriaSelect.selectedIndex].text;

                // Verificar si ya se seleccionó la categoría
                if (selectedCategoriaValue && !document.querySelector(`#selected-categorias input[value="${selectedCategoriaValue}"]`)) {
                    // Crear un nuevo elemento para la categoría seleccionada
                    const categoriaElement = document.createElement('div');
                    categoriaElement.className = 'input-group mb-2';
                    categoriaElement.innerHTML = `
                <input type="hidden" name="categoria[]" value="${selectedCategoriaValue}">
                <input type="text" class="form-control" value="${selectedCategoriaText}" readonly>
                <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
            `;

                    // Agregar el evento para eliminar la categoría
                    categoriaElement.querySelector('.remove-categoria-btn').addEventListener('click', function() {
                        categoriaElement.remove();
                    });

                    // Agregar la categoría al contenedor
                    selectedCategoriasContainer.appendChild(categoriaElement);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor selecciona una categoría válida o evita duplicados.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
        // Agregar paso de instrucción
        document.querySelector('.add-instruccion-btn').addEventListener('click', function() {
            let container = document.getElementById('instrucciones-container');
            let newInputGroup = document.createElement('div');
            newInputGroup.className = 'input-group mb-3';
            newInputGroup.innerHTML = `<input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" required />
                                                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" required />
                                                                            <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>`;
            container.appendChild(newInputGroup);

            // Agregar evento de eliminar paso
            newInputGroup.querySelector('.remove-instruccion-btn').addEventListener('click', function() {
                container.removeChild(newInputGroup);
            });
        });

        // Agregar ingrediente
        document.querySelector('.add-ingrediente-btn').addEventListener('click', function() {
            let container = document.getElementById('ingredientes-container');
            let newInputGroup = document.createElement('div');
            newInputGroup.className = 'input-group mb-3';
            newInputGroup.innerHTML = `<input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" required />
                                                                            <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad" required />
                                                                            <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>`;
            container.appendChild(newInputGroup);

            // Agregar evento de eliminar ingrediente
            newInputGroup.querySelector('.remove-ingrediente-btn').addEventListener('click', function() {
                container.removeChild(newInputGroup);
            });
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        //Definición de Palabras Prohibidas
        const forbiddenWords = ["gay", "gil", "topo", "topoide"]; // Añade aquí las palabras que quieres prohibir

        //Función para Verificar Palabras Prohibidas Esta función toma una cadena de texto (text) como argumento 
        // y verifica si contiene alguna de las palabras prohibidas. Si encuentra alguna palabra prohibida, 
        //devuelve true, de lo contrario, devuelve false.
        function containsForbiddenWords(text) {
            for (let word of forbiddenWords) {
                if (text.includes(word)) {
                    return true;
                }
            }
            return false;
        }

        // Función para Validar el Formulario
        /* Obtención de Valores: Se obtienen los valores de los campos del formulario 
        (título, descripción, instrucciones y ingredientes).

        Lista de Campos Inválidos: Se crea una lista para almacenar los nombres de los campos que 
        contienen palabras prohibidas.

        Verificación de Palabras Prohibidas: Se verifica si cada campo contiene alguna de las 
        palabras prohibidas usando la función containsForbiddenWords. 
        Si se encuentra una palabra prohibida en un campo, se añade el nombre del campo a la lista invalidFields.

        Mostrar Alerta y Prevenir Envío: Si hay campos inválidos 
        (es decir, invalidFields no está vacío), se previene el envío del formulario (event.preventDefault()) 
        y se muestra una alerta de error usando SweetAlert2, indicando los campos que contienen palabras no permitidas. */
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

        //Asignación del Evento de Validación al Formulario
        document.getElementById('formValidationExamples').addEventListener('submit', validateForm);
    </script>

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
        // ...existing code...
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formValidationExamples');
            const requiredFields = form.querySelectorAll('[data-required]');

            function validateField(field) {
                if (field.type === 'radio') {
                    const radios = document.querySelectorAll(`input[name="${field.name}"]`);
                    const isChecked = Array.from(radios).some(radio => radio.checked);
                    if (!isChecked) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Debes seleccionar una ${field.name}.`,
                            confirmButtonText: 'Aceptar'
                        });
                        radios.forEach(radio => radio.classList.add('error'));
                        return false;
                    } else {
                        radios.forEach(radio => radio.classList.remove('error'));
                    }
                } else if (field.tagName === 'SELECT') {
                    // Validar que se haya seleccionado al menos una categoría
                    if (field.selectedOptions.length === 0 || !field.value.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Debes seleccionar al menos una categoría.`,
                            confirmButtonText: 'Aceptar'
                        });
                        field.classList.add('error');
                        return false;
                    } else {
                        field.classList.remove('error');
                    }
                } else if (field.type === 'file' && field.name === 'media_files[]') {
                    if (field.files.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Debes ingresar una imagen o video.`,
                            confirmButtonText: 'Aceptar'
                        });
                        field.classList.add('error');
                        return false;
                    } else {
                        field.classList.remove('error');
                    }
                } else {
                    if (!field.value.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Debes ingresar ${field.placeholder || field.name}.`,
                            confirmButtonText: 'Aceptar'
                        });
                        field.classList.add('error');
                        return false;
                    } else {
                        field.classList.remove('error');
                    }
                }
                return true;
            }

            form.addEventListener('submit', function(event) {
                let isValid = true;
                for (let field of requiredFields) {
                    if (!validateField(field)) {
                        isValid = false;
                        event.preventDefault();
                        field.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                        field.focus();
                        break;
                    }
                }
                if (!isValid) {
                    event.preventDefault();
                }
            });

            // ...existing code...
            // ...existing code...

            // Agregar eventos de validación a los campos dinámicamente agregados
            function addValidationEvents(field) {
                field.addEventListener('blur', function() {
                    validateField(field);
                });

                field.addEventListener('input', function() {
                    if (field.value.trim()) {
                        field.classList.remove('error');
                    }
                });
            }


            // Agregar paso de instrucción
            document.querySelector('.add-instruccion-btn').addEventListener('click', function() {
                let container = document.getElementById('instrucciones-container');
                let newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-3';
                newInputGroup.innerHTML = `<input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" data-required="true" />
                                        <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" data-required="true" />
                                        <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>`;
                container.appendChild(newInputGroup);

                // Agregar eventos de validación a los nuevos campos
                newInputGroup.querySelectorAll('[data-required]').forEach(addValidationEvents);

                // Agregar evento de eliminar paso
                newInputGroup.querySelector('.remove-instruccion-btn').addEventListener('click', function() {
                    container.removeChild(newInputGroup);
                });
            });

            // Agregar ingrediente
            document.querySelector('.add-ingrediente-btn').addEventListener('click', function() {
                let container = document.getElementById('ingredientes-container');
                let newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-3';
                newInputGroup.innerHTML = `<input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" data-required="true" />
                                        <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad" data-required="true" />
                                        <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>`;
                container.appendChild(newInputGroup);

                // Agregar eventos de validación a los nuevos campos
                newInputGroup.querySelectorAll('[data-required]').forEach(addValidationEvents);

                // Agregar evento de eliminar ingrediente
                newInputGroup.querySelector('.remove-ingrediente-btn').addEventListener('click', function() {
                    container.removeChild(newInputGroup);
                });
            });
        });

       
    </script>
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
</body>


<!-- Mirrored from technext.github.io/dingo/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Nov 2024 18:26:46 GMT -->

</html>