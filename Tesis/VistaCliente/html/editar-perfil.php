<?php
session_start();
require_once '../../VistaAdmin/html/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
// Agrega aquí el debug:
echo "<!-- id_usuario: $id_usuario -->";

if (!$id_usuario) {
    header('Location: login.php');
    exit;
}

// Traer datos del usuario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

// Traer emails
$sql_emails = "SELECT id_email, email, tipo FROM emails_usuarios WHERE id_usuario = ?";
$stmt_emails = $conexion->prepare($sql_emails);
$stmt_emails->bind_param("i", $id_usuario);
$stmt_emails->execute();
$res_emails = $stmt_emails->get_result();
$emails = [];
while ($row = $res_emails->fetch_assoc()) {
    $emails[] = $row;
}

// Traer teléfonos
$sql_telefonos = "SELECT id_telefono, telefono, tipo FROM telefonos_usuarios WHERE id_usuario = ?";
$stmt_telefonos = $conexion->prepare($sql_telefonos);
$stmt_telefonos->bind_param("i", $id_usuario);
$stmt_telefonos->execute();
$res_telefonos = $stmt_telefonos->get_result();
$telefonos = [];
while ($row = $res_telefonos->fetch_assoc()) {
    $telefonos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title> ChefClass - Editar Perfil</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .perfil-img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            margin-bottom: 10px;
        }

        .navbar-nav .nav-link {
            font-size: 1.2rem;
            color: black;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        /* Quitar subrayado solo al enlace de créditos en el footer */
        .footer-text a {
            text-decoration: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <!-- Link para cambiar las letras a  Roboto -->
    <link rel="stylesheet" href="../css/cambio_de_letra.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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

                            /* Mantener el color blanco del texto en el botón Cancelar al pasar el mouse */
                            /* Botón Cancelar más oscuro al pasar el mouse */
                            .btn-danger:hover,
                            .btn-danger:focus {
                                color: #fff !important;
                                background-color: #bb2d3b !important;
                                /* rojo más oscuro */
                                border-color: #b02a37 !important;
                                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
                            }

                            /* Quitar subrayado a los enlaces del footer */
                            .footer-area .single-footer-widget.footer_2 ul li a {
                                text-decoration: none !important;
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

                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' || basename($_SERVER['PHP_SELF']) == 'editar-perfil.php') ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
                                </li>
                            <?php endif; ?>
                            </ul>
                        </div>

                        <div class="menu_btn d-flex align-items-center">
                            <?php if (!isset($_SESSION['id_usuario'])): ?>
                                <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                            <?php else: ?>



                                <span class="d-none d-sm-inline align-middle" style="font-weight: 500; margin-right: 2rem; color: #212529;">
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
    <!--::header part end::-->

    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner text-center">
                        <div class="breadcrumb_iner_item">
                            <h2>T Ú - P E R F I L</h2>
                            <h4>
                                <h4 class="text-white fw-light">
                                    <a class="text-white" href="vista-perfil.php" style="text-decoration: none;">Perfil </a>/ Editar Perfil
                                </h4>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-5 mb-5">



        <!-- Agrega esto dentro de tu <body> donde quieras mostrar el formulario -->
        <div class="container my-5">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">

                    <div class="card shadow">

                        <!--Esta parte es para mostrar la localidad si es que tiene localidad-->
                        <div class="card-body">

                            <?php
                            $latitud = '';
                            $longitud = '';
                            $provincia = '';
                            $departamento = '';
                            $localidad = '';
                            $barrio = '';
                            $pais = '';

                            if (!empty($usuario['id_localidad'])) {
                                $sql = "SELECT * FROM localidades WHERE id_localidad = ?";
                                $stmt = $conexion->prepare($sql);
                                $stmt->bind_param("i", $usuario['id_localidad']);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                if ($loc = $res->fetch_assoc()) {
                                    $latitud = $loc['latitud'];
                                    $longitud = $loc['longitud'];
                                    $provincia = $loc['provincia'];
                                    $departamento = $loc['departamento'];
                                    $localidad = $loc['localidad'];
                                    $barrio = $loc['barrio'];
                                    $pais = $loc['pais'];
                                }
                                $stmt->close();
                            }
                            ?>
                            <form method="POST" action="guardar-perfil.php" enctype="multipart/form-data" class="row g-3">
                                <!-- Imagen de perfil -->
                                <div class="col-12 text-center d-flex flex-column align-items-center">
                                    <label class="form-label">Imagen de perfil actual</label>
                                    <img id="imgPreview" src="<?= !empty($usuario['img']) ? '../../VistaAdmin/html/' . $usuario['img'] : '../img/default-user.png' ?>" class="perfil-img-preview mb-2" alt="Imagen de perfil">
                                    <input type="file" name="img" accept="image/*" onchange="previewImage(event)" class="form-control w-auto">
                                </div>
                                <hr class="mt-0" style="width: 60%; min-width: 120px;">

                                <!-- Nombre y Apellido -->

                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?: [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$" maxlength="40">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" id="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?: [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$" maxlength="40">
                                </div>

                                <!-- Usuario y Género -->
                                <div class="col-md-6">
                                    <label class="form-label">Usuario</label>
                                    <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?: [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$" maxlength="30">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Género</label>
                                    <select class="form-select" name="genero" required>
                                        <?php
                                        $sql_generos = "SELECT * FROM generos";
                                        $res_generos = $conexion->query($sql_generos);
                                        while ($gen = $res_generos->fetch_assoc()):
                                        ?>
                                            <option value="<?= $gen['id_genero'] ?>" <?= $usuario['genero'] == $gen['id_genero'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($gen['nombre_genero']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Mapa de ubicación -->

                                <!-- Ubicación -->
                                <div class="col-12">
                                    <label class="form-label">Ubicación</label>
                                    <div id="map" style="height: 300px; border-radius: 10px; margin-bottom: 15px;"></div>
                                    <button type="button" class="btn btn-outline-primary mb-3" id="btn-geo">Buscar mi ubicación automáticamente</button>
                                    <input type="hidden" id="latitud" name="latitud" value="<?= htmlspecialchars($latitud) ?>">
                                    <input type="hidden" id="longitud" name="longitud" value="<?= htmlspecialchars($longitud) ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Provincia</label>
                                            <input type="text" class="form-control" id="provincia" name="provincia" value="<?= htmlspecialchars($provincia) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Departamento</label>
                                            <input type="text" class="form-control" id="departamento" name="departamento" value="<?= htmlspecialchars($departamento) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Localidad</label>
                                            <input type="text" class="form-control" id="localidad" name="localidad" value="<?= htmlspecialchars($localidad) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Barrio</label>
                                            <input type="text" class="form-control" id="barrio" name="barrio" value="<?= htmlspecialchars($barrio) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">País</label>
                                            <input type="text" class="form-control" id="pais" name="pais" value="<?= htmlspecialchars($pais) ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emails -->
                                <div class="col-12">
                                    <label class="form-label">Emails</label>
                                    <div id="emails-container">
                                        <?php foreach ($emails as $i => $email): ?>
                                            <div class="input-group mb-2">
                                                <input type="hidden" name="email_ids[]" value="<?= $email['id_email'] ?>">
                                                <input type="email" class="form-control email-input" name="emails[]" value="<?= htmlspecialchars($email['email']) ?>" required maxlength="60">
                                                <select class="form-select" name="tipo_email[]">
                                                    <option value="Personal" <?= $email['tipo'] == 'Personal' ? 'selected' : '' ?>>Personal</option>
                                                    <option value="Laboral" <?= $email['tipo'] == 'Laboral' ? 'selected' : '' ?>>Laboral</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-danger btn-remove-email">Eliminar</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-add-email" <?= count($emails) >= 2 ? 'disabled' : '' ?>>Agregar Email</button>
                                </div>

                                <!-- Teléfonos -->
                                <div class="col-12">
                                    <label class="form-label">Teléfonos</label>
                                    <div id="telefonos-container">
                                        <?php foreach ($telefonos as $i => $telefono): ?>
                                            <div class="input-group mb-2">
                                                <!-- ID oculto para distinguir entre teléfonos existentes y nuevos -->
                                                <input type="hidden" name="telefono_ids[]" value="<?= htmlspecialchars($telefono['id_telefono']) ?>">
                                                <!-- Solo números, sin espacios, longitud máxima 13, mínima 10 -->
                                                <input
                                                    type="text"
                                                    class="form-control telefono-input"
                                                    name="telefonos[]"
                                                    value="<?= htmlspecialchars($telefono['telefono']) ?>"
                                                    required
                                                    pattern="^[0-9]{10,13}$"
                                                    maxlength="13"
                                                    minlength="10"
                                                    autocomplete="off"
                                                    title="El teléfono debe tener entre 10 y 13 dígitos numéricos">
                                                <select class="form-select" name="tipo_telefono[]">
                                                    <option value="Personal" <?= $telefono['tipo'] == 'Personal' ? 'selected' : '' ?>>Personal</option>
                                                    <option value="Laboral" <?= $telefono['tipo'] == 'Laboral' ? 'selected' : '' ?>>Laboral</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-danger btn-remove-telefono">Eliminar</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-add-telefono" <?= count($telefonos) >= 2 ? 'disabled' : '' ?>>Agregar Teléfono</button>
                                </div>


                                <!-- Botones -->
                                <div class="col-12 d-flex justify-content-between mt-4">
                                    <button type="submit" class="btn btn-success px-4">Guardar cambios</button>
                                    <a href="vista-perfil.php" class="btn btn-danger px-4">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estilos adicionales -->
        <style>
            .perfil-img-preview {
                width: 120px;
                height: 120px;
                object-fit: cover;
                border-radius: 50%;
                border: 2px solid #ccc;
                margin-bottom: 10px;
            }

            .input-group .btn {
                min-width: 90px;
            }

            .btn-add-email,
            .btn-add-telefono {
                margin-top: 5px;
            }
        </style>
    </div>

    <script>
        function previewImage(event) {
            const imgPreview = document.getElementById('imgPreview');
            imgPreview.src = URL.createObjectURL(event.target.files[0]);
        }

        // Emails
        document.addEventListener('DOMContentLoaded', function() {
            const emailsContainer = document.getElementById('emails-container');
            const btnAddEmail = document.querySelector('.btn-add-email');
            btnAddEmail.addEventListener('click', function() {
                if (emailsContainer.querySelectorAll('.input-group').length < 2) {
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
                <input type="hidden" name="email_ids[]" value="">
    <input type="email" class="form-control email-input" name="emails[]" required maxlength="60">
                <select class="form-select" name="tipo_email[]">
                    <option value="Personal">Personal</option>
                    <option value="Laboral">Laboral</option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-remove-email">Eliminar</button>
            `;
                    emailsContainer.appendChild(div);
                    if (emailsContainer.querySelectorAll('.input-group').length >= 2) btnAddEmail.disabled = true;
                }
            });
            emailsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-email')) {
                    e.target.closest('.input-group').remove();
                    btnAddEmail.disabled = false;
                }
            });

            // Teléfonos
            const telefonosContainer = document.getElementById('telefonos-container');
            const btnAddTelefono = document.querySelector('.btn-add-telefono');
            btnAddTelefono.addEventListener('click', function() {
                if (telefonosContainer.querySelectorAll('.input-group').length < 2) {
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
                <input type="hidden" name="telefono_ids[]" value="">
    <input type="text" class="form-control telefono-input" name="telefonos[]" required pattern="^[0-9]+$" maxlength="20">
                <select class="form-select" name="tipo_telefono[]">
                    <option value="Personal">Personal</option>
                    <option value="Laboral">Laboral</option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-remove-telefono">Eliminar</button>
            `;
                    telefonosContainer.appendChild(div);
                    if (telefonosContainer.querySelectorAll('.input-group').length >= 2) btnAddTelefono.disabled = true;
                }
            });
            telefonosContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-telefono')) {
                    e.target.closest('.input-group').remove();
                    btnAddTelefono.disabled = false;
                }
            });
        });
    </script>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function limpiarTexto(texto) {
                return texto
                    .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '') // solo letras y espacios
                    .replace(/\s{2,}/g, ' ') // solo un espacio entre palabras
                    .replace(/^\s+|\s+$/g, ''); // sin espacios al inicio/final
            }

            ['nombre', 'apellido', 'nombre_usuario'].forEach(function(id) {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function(e) {
                        // Permite letras y espacios, pero NO permite dos espacios seguidos
                        let val = this.value;
                        // Elimina caracteres no permitidos
                        val = val.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '');
                        // Elimina espacios al inicio
                        val = val.replace(/^\s+/, '');
                        // Elimina espacios dobles en tiempo real
                        val = val.replace(/\s{2,}/g, ' ');
                        this.value = val;
                    });
                    input.addEventListener('blur', function() {
                        this.value = limpiarTexto(this.value);
                    });
                }
            });

            // Teléfonos: solo números, sin espacios
            document.querySelectorAll('.telefono-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                });
            });

            // Emails: sin espacios
            document.querySelectorAll('.email-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\s/g, '');
                });
            });

            // Para los inputs dinámicos (agregados por JS)
            document.body.addEventListener('input', function(e) {
                if (e.target.matches('.telefono-input')) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                }
                if (e.target.matches('.email-input')) {
                    e.target.value = e.target.value.replace(/\s/g, '');
                }
                if (['nombre', 'apellido', 'nombre_usuario'].includes(e.target.id)) {
                    // Igual que arriba: solo letras y espacios, no más de un espacio seguido
                    let val = e.target.value;
                    val = val.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '');
                    val = val.replace(/^\s+/, '');
                    val = val.replace(/\s{2,}/g, ' ');
                    e.target.value = val;
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let lat = <?= $latitud !== '' ? $latitud : '-27.4689' ?>;
            let lng = <?= $longitud !== '' ? $longitud : '-58.8341' ?>;

            let map = L.map('map').setView([lat, lng], 13);
            let marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            function updateInputs(lat, lng) {
                document.getElementById('latitud').value = lat;
                document.getElementById('longitud').value = lng;
                // Llama a la función de geocodificación inversa
                reverseGeocode(lat, lng);
            }

            function reverseGeocode(lat, lng) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        // Limpia los campos antes de rellenar
                        document.getElementById('provincia').value = '';
                        document.getElementById('departamento').value = '';
                        document.getElementById('localidad').value = '';
                        document.getElementById('barrio').value = '';
                        document.getElementById('pais').value = '';

                        if (data.address) {
                            document.getElementById('provincia').value = data.address.state || '';
                            document.getElementById('departamento').value = data.address.county || '';
                            document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || data.address.hamlet || '';
                            document.getElementById('barrio').value = data.address.suburb || data.address.neighbourhood || '';
                            document.getElementById('pais').value = data.address.country || '';
                        }
                    })
                    .catch(error => {
                        console.log('Error en geocodificación inversa:', error);
                    });
            }

            // Inicializa los campos si ya hay lat/lng
            updateInputs(lat, lng);

            marker.on('moveend', function(e) {
                let coords = e.target.getLatLng();
                updateInputs(coords.lat, coords.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            document.getElementById('btn-geo').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let lat = position.coords.latitude;
                        let lng = position.coords.longitude;
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                        updateInputs(lat, lng);
                    });
                } else {
                    alert('La geolocalización no está soportada por este navegador.');
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación de emails
            function validarEmailFormato(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function emailsUnicos() {
                const emails = Array.from(document.querySelectorAll('.email-input')).map(e => e.value.trim());
                return (new Set(emails)).size === emails.length;
            }

            function validarEmails() {
                let valid = true;
                let emails = document.querySelectorAll('.email-input');
                emails.forEach(input => {
                    input.value = input.value.replace(/\s/g, '');
                    if (!validarEmailFormato(input.value)) {
                        input.classList.add('is-invalid');
                        valid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                // Verifica que no haya emails repetidos
                if (!emailsUnicos()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No puedes repetir emails.',
                    });
                    valid = false;
                }
                return valid;
            }

            // Validación de teléfonos
            function telefonosUnicos() {
                const tels = Array.from(document.querySelectorAll('.telefono-input')).map(e => e.value.trim());
                return (new Set(tels)).size === tels.length;
            }

            function validarTelefonos() {
                let valid = true;
                let telefonos = document.querySelectorAll('.telefono-input');
                telefonos.forEach(input => {
                    input.value = input.value.replace(/\D/g, '');
                    if (input.value.length < 10 || input.value.length > 13) {
                        input.classList.add('is-invalid');
                        valid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                // Verifica que no haya teléfonos repetidos
                if (!telefonosUnicos()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No puedes repetir teléfonos.',
                    });
                    valid = false;
                }
                return valid;
            }

            // Validar al enviar el formulario
            document.querySelector('form').addEventListener('submit', function(e) {
                let emailsOk = validarEmails();
                let telefonosOk = validarTelefonos();
                if (!emailsOk || !telefonosOk) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Corrige los errores en emails y teléfonos antes de guardar.',
                    });
                }
            });

            // Emails: sin espacios en tiempo real
            document.body.addEventListener('input', function(e) {
                if (e.target.matches('.email-input')) {
                    e.target.value = e.target.value.replace(/\s/g, '');
                }
            });

            // Teléfonos: solo números en tiempo real
            document.body.addEventListener('input', function(e) {
                if (e.target.matches('.telefono-input')) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                }
            });
        });
    </script>


    <?php if (isset($_SESSION['perfil_error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?php echo json_encode($_SESSION['perfil_error']); ?>,
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php unset($_SESSION['perfil_error']);
    endif; ?>


    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Perfil actualizado!',
                text: 'Tus datos se guardaron correctamente.',
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>
</body>

</html>