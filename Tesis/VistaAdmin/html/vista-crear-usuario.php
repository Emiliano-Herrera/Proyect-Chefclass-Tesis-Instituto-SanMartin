<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Registro - Chefclass</title>
    <meta name="description" content="Sistema de registro" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Iconos -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- CSS Principal -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- CSS de Vendors -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- CSS de Página -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!-- Configuración -->
    <script src="../assets/js/config.js"></script>

    <link rel="stylesheet" href="../css/vista-crear-usuario.css">

    <!-- Agregar en el <head> de tu HTML -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>



    <div class="container-xxl wider pt-5">
        <div class="container-p-y text-center">
            <div class="authentication-inner">
                <div class="card wider">

                    <div class="card-body">

                        <?php
                        session_start();
                        // Mostrar errores si existen
                        if (isset($_SESSION['error_registro'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                            echo $_SESSION['error_registro'];
                            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            echo '</div>';
                            unset($_SESSION['error_registro']);
                        }
                        ?>

                        <!-- Logo y título -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bold">Crear cuenta</span>
                            </a>
                        </div>

                        <!-- Mensajes de error -->
                        <div id="error-messages" class="alert alert-danger d-none" role="alert"></div>

                        <!-- Indicador de pasos -->
                        <div class="step-indicator">
                            <div class="step active" id="step1">
                                <div class="step-number">1</div>
                                <div class="step-title">Información Personal</div>
                            </div>
                            <div class="step" id="step2">
                                <div class="step-number">2</div>
                                <div class="step-title">Datos de Cuenta</div>
                            </div>
                            <div class="step" id="step3">
                                <div class="step-number">3</div>
                                <div class="step-title">Ubicación</div>
                            </div>
                        </div>

                        <form id="userRegistrationForm" action="guardar-usuario-creado.php" method="POST">
                            <!-- Paso 1: Información Personal -->
                            <div class="step-content" id="step1-content">
                                <h4 class="form-section-title">Información Personal</h4>
                                <p class="form-section-subtitle">Ingresa tus datos personales</p>

                                <!-- Campos de nombre y apellido -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="firstName" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="firstName" name="nombre"
                                            placeholder="John" maxlength="50" required
                                            oninput="validateName(this)">
                                        <div class="invalid-feedback">El nombre solo puede contener letras y espacios (máx. 50 caracteres)</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName" class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" id="lastName" name="apellido"
                                            placeholder="Doe" maxlength="50" required
                                            oninput="validateName(this)">
                                        <div class="invalid-feedback">El apellido solo puede contener letras y espacios (máx. 50 caracteres)</div>
                                    </div>
                                </div>

                                <!-- Selector de género -->
                                <div class="form-group-full">
                                    <label class="form-label">Género *</label>
                                    <div class="gender-selector">
                                        <select class="form-control custom-select-gender" name="genero" id="genero" required>
                                            <option value="">Selecciona un género</option>
                                            <?php
                                            include("conexion.php");
                                            $g = mysqli_query($conexion, "SELECT * FROM generos");
                                            $genero_sesion = isset($_SESSION['registro_genero']) ? $_SESSION['registro_genero'] : '';
                                            while ($opciones = mysqli_fetch_row($g)) { ?>
                                                <option value="<?php echo $opciones[0] ?>" <?php if ($genero_sesion == $opciones[0]) echo 'selected'; ?>>
                                                    <?php echo $opciones[1] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Debe seleccionar un género</div>
                                </div>

                                <!-- reCAPTCHA -->
                                <!-- reCAPTCHA temporal para desarrollo -->
                                <div class="form-group-full">
                                    <div class="recaptcha-container">
                                        <div class="g-recaptcha" data-sitekey="6LeVg9kqAAAAAIMcjLWNuyRhU1tdZUO55BFcIN0W"></div>
                                    </div>
                                    <div class="invalid-feedback recaptcha-error">Por favor verifica que no eres un robot</div>
                                </div>

                                <!-- Botones de navegación -->
                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-outline-secondary" disabled>Anterior</button>
                                    <button type="button" class="btn btn-primary" onclick="validateStep1()">Siguiente</button>
                                </div>
                            </div>

                            <!-- Paso 2: Datos de Cuenta -->
                            <div class="step-content d-none" id="step2-content">
                                <h4 class="form-section-title">Datos de Cuenta</h4>
                                <p class="form-section-subtitle">Ingresa los detalles de tu cuenta</p>

                                <!-- Campos de email -->
                                <div class="form-group-full">
                                    <label for="email" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="john.doe@ejemplo.com" required
                                        oninput="validateEmail(this)">
                                    <div class="invalid-feedback">Por favor ingrese un email válido</div>
                                </div>


                                <!-- Teléfonos -->
                                <div class="form-group-full">
                                    <label class="form-label">Teléfonos *</label>
                                    <div id="telefonos-container">
                                        <div class="telefono-group">
                                            <input type="text" class="form-control" placeholder="Número de teléfono"
                                                name="telefonos[]" maxlength="10" required
                                                oninput="validarTelefono(this)">
                                            <select name="tipos[]" class="form-control tipo-telefono">
                                                <option value="Personal">Personal</option>
                                                <option value="Laboral">Laboral</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-danger remove-telefono mb-2" style="display: none;">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="agregar-telefono">
                                        <i class="bx bx-plus me-1"></i> Agregar otro teléfono
                                    </button>
                                    <small class="form-text text-muted">Máximo 10 dígitos. Solo números.</small>
                                </div>

                                <!-- Campo de nombre de usuario -->
                                <div class="form-group-full mt-3">
                                    <label for="username" class="form-label">Nombre de Usuario *</label>
                                    <input type="text" class="form-control" id="username" name="usuario"
                                        placeholder="johndoe" maxlength="50" required
                                        oninput="validateUsername(this)">
                                    <div class="invalid-feedback">El usuario solo puede contener letras, números y guiones bajos (máx. 50 caracteres)</div>
                                </div>

                                <!-- Campos de contraseña -->
                                <div class="form-group-full">
                                    <label for="password" class="form-label mt-3">Contraseña *</label>
                                    <div class="password-field">
                                        <input type="password" id="password" class="form-control" name="contrasena"
                                            placeholder="Ingrese su contraseña" required
                                            oninput="validatePassword(this)" />
                                        <span class="input-group-text cursor-pointer password-toggle" onclick="togglePassword('password')">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                    <!-- Mensaje de requisitos de contraseña -->
                                    <div class="password-requirements mt-2">
                                        <small class="text-muted">La contraseña debe cumplir con:</small>
                                        <ul class="list-unstyled mt-1">
                                            <li id="req-length" class="text-muted">✓ Mínimo 8 caracteres</li>
                                            <li id="req-uppercase" class="text-muted">✓ Al menos una mayúscula</li>
                                            <li id="req-lowercase" class="text-muted">✓ Al menos una minúscula</li>
                                            <li id="req-number" class="text-muted">✓ Al menos un número</li>
                                            <li id="req-special" class="text-muted">✓ Al menos un carácter especial</li>
                                            <!-- NUEVOS REQUISITOS -->
                                            <li id="req-nospaces" class="text-success">✓ Sin espacios en blanco</li>
                                            <li id="req-nosql" class="text-success">✓ Sin palabras reservadas</li>
                                            <li id="req-maxlength" class="text-success">✓ Máximo 8 caracteres</li>
                                        </ul>
                                    </div>
                                    <div class="invalid-feedback" id="password-error">
                                        La contraseña no cumple con todos los requisitos
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="progress">
                                            <div class="progress-bar" id="password-strength-bar" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted" id="password-strength-text">Seguridad de la contraseña</small>
                                    </div>
                                </div>

                                <div class="form-group-full mt-3">
                                    <label for="confirmPassword" class="form-label">Repetir Contraseña *</label>
                                    <div class="password-field">
                                        <input type="password" id="confirmPassword" class="form-control" name="confirmPassword"
                                            placeholder="Repita su contraseña" required
                                            oninput="validatePasswordConfirmation()" />
                                        <span class="input-group-text cursor-pointer password-toggle" onclick="togglePassword('confirmPassword')">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback">Las contraseñas no coinciden</div>
                                </div>

                                <!-- Botones de navegación -->
                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showStep(1)">Anterior</button>
                                    <button type="button" class="btn btn-primary" onclick="validateStep2()">Siguiente</button>
                                </div>
                            </div>

                            <!-- Paso 3: Información de Ubicación -->
                            <div class="step-content d-none" id="step3-content">
                                <h4 class="form-section-title">Información de Ubicación</h4>
                                <p class="form-section-subtitle">Selecciona tu ubicación en el mapa o usa la detección automática</p>

                                <!-- Mapa interactivo -->
                                <div class="form-group-full">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="locateUser()">
                                            <i class="bx bx-navigation me-1"></i> Detectar mi ubicación automáticamente
                                        </button>
                                        <small class="text-muted ms-2">O haz clic en el mapa para seleccionar manualmente</small>
                                    </div>

                                    <div id="map" style="height: 300px; border-radius: 8px; border: 1px solid #ddd;"></div>

                                    <!-- Estado de la ubicación -->
                                    <div class="mt-2">
                                        <small id="locationStatus" class="text-muted">
                                            <i class="bx bx-info-circle me-1"></i> No se ha seleccionado ninguna ubicación
                                        </small>
                                    </div>
                                </div>

                                <!-- Campos ocultos para enviar los datos -->
                                <input type="hidden" id="latitude" name="latitud">
                                <input type="hidden" id="longitude" name="longitud">
                                <input type="text" id="provincia" name="provincia">
                                <input type="text" id="departamento" name="departamento">
                                <input type="text" id="localidad" name="localidad">
                                <input type="text" id="barrio" name="barrio">
                                <input type="text" id="pais" name="pais">

                                <!-- Botones de navegación -->
                                <div class="nav-buttons mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showStep(2)">Anterior</button>
                                    <button type="button" class="btn btn-success" onclick="validateAndSubmit()">Crear Cuenta</button>
                                </div>
                            </div>
                        </form>

                        <!-- Enlace para iniciar sesión -->
                        <p class="text-center mt-4">
                            <span>¿Ya tienes una cuenta?</span>
                            <a href="login.php">
                                <span>Iniciar sesión</span>
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // ========== VARIABLES GLOBALES ==========
        let formData = {}; // Almacena todos los datos del formulario
        let map; // Instancia del mapa Leaflet
        let marker; // Marcador en el mapa
        let isFetching = false; // Controla si se está procesando una ubicación
        let isLocationSelected = false; // Controla si ya se seleccionó una ubicación

        // ========== MANEJO DE GÉNEROS ==========

        // Valida que se haya seleccionado un género
        function validateGenero() {
            const generoSelect = document.getElementById('genero');
            const isValid = generoSelect.value !== '';

            if (isValid) {
                generoSelect.classList.remove('is-invalid');
                generoSelect.classList.add('is-valid');
                formData['genero'] = generoSelect.value;
            } else {
                generoSelect.classList.remove('is-valid');
                generoSelect.classList.add('is-invalid');
            }

            return isValid;
        }

        // Valida que el reCAPTCHA esté completado
        function validateRecaptcha() {
            if (typeof grecaptcha === 'undefined') {
                console.error('reCAPTCHA no está cargado');
                return false;
            }

            const recaptchaResponse = grecaptcha.getResponse();
            const isValid = recaptchaResponse.length > 0;

            const recaptchaError = document.querySelector('.recaptcha-error');

            if (isValid) {
                recaptchaError.classList.remove('d-block');
                formData['g-recaptcha-response'] = recaptchaResponse;
            } else {
                recaptchaError.classList.add('d-block');
            }

            return isValid;
        }

        // Resetea el reCAPTCHA cuando se vuelve al paso 1
        function resetRecaptcha() {
            if (typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
                grecaptcha.reset();
            }
            const recaptchaError = document.querySelector('.recaptcha-error');
            recaptchaError.classList.remove('d-block');
        }

        // ========== SISTEMA DE PASOS Y DATOS ==========

        // Navega entre los pasos del formulario
        function showStep(stepNumber) {
            // Guardar datos del paso actual antes de cambiar
            saveCurrentStepData();

            // Ocultar todos los pasos
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('d-none');
            });

            // Mostrar el paso seleccionado
            document.getElementById(`step${stepNumber}-content`).classList.remove('d-none');

            // Resetear reCAPTCHA si volvemos al paso 1
            if (stepNumber === 1) {
                resetRecaptcha();
            }

            // Inicializar mapa si es el paso 3
            if (stepNumber === 3) {
                setTimeout(() => {
                    initializeMap();
                }, 100);
            }

            // Actualizar indicador de pasos visual
            updateStepIndicator(stepNumber);

            // Cargar datos guardados para el paso
            loadStepData(stepNumber);
        }

        // Actualiza la barra de progreso visual (pasos completados/activos)
        function updateStepIndicator(stepNumber) {
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });

            // Marcar pasos anteriores como completados y el actual como activo
            for (let i = 1; i <= 3; i++) {
                const stepElement = document.getElementById(`step${i}`);
                if (i < stepNumber) {
                    stepElement.classList.add('completed');
                } else if (i === stepNumber) {
                    stepElement.classList.add('active');
                }
            }
        }

        // Guarda los datos del paso actual en formData
        function saveCurrentStepData() {
            const currentStep = document.querySelector('.step-content:not(.d-none)');
            const inputs = currentStep.querySelectorAll('input, select');

            inputs.forEach(input => {
                if (input.type !== 'submit' && input.type !== 'button') {
                    formData[input.name] = input.value;
                }
            });
        }

        // Carga los datos guardados en el paso actual
        function loadStepData(stepNumber) {
            const stepContent = document.getElementById(`step${stepNumber}-content`);
            const inputs = stepContent.querySelectorAll('input, select');

            inputs.forEach(input => {
                if (formData[input.name] !== undefined) {
                    input.value = formData[input.name];

                    // Para el género, marcar la opción seleccionada
                    if (input.name === 'genero' && input.value) {
                        document.querySelectorAll('.gender-option').forEach(option => {
                            option.classList.remove('selected');
                            if (option.getAttribute('data-gender') === input.value) {
                                option.classList.add('selected');
                            }
                        });
                    }
                }
            });
        }

        // ========== SISTEMA DE TELÉFONOS ==========

        // Valida que el teléfono tenga al menos 10 dígitos
        function validarTelefono(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            const isValid = input.value.length >= 10;

            if (isValid && input.value.length > 0) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else if (input.value.length > 0) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-valid', 'is-invalid');
            }

            return isValid;
        }

        // Elimina espacios en blanco de los inputs
        function validarSinEspacios(input) {
            input.value = input.value.replace(/\s/g, '');
        }

        // Agrega un nuevo campo de teléfono (máximo 2)
        document.getElementById('agregar-telefono').addEventListener('click', function() {
            const container = document.getElementById('telefonos-container');
            const telefonoGroups = container.getElementsByClassName('telefono-group');

            if (telefonoGroups.length < 2) {
                const newGroup = telefonoGroups[0].cloneNode(true);
                const newInput = newGroup.querySelector('input');
                const removeBtn = newGroup.querySelector('.remove-telefono');

                newInput.value = '';
                newInput.classList.remove('is-valid', 'is-invalid');
                removeBtn.style.display = 'block';

                removeBtn.addEventListener('click', function() {
                    this.parentElement.remove();
                    actualizarBotonesEliminar();
                });

                container.appendChild(newGroup);
                actualizarBotonesEliminar();
            } else {
                showError('No puedes agregar más de 2 teléfonos.');
            }
        });

        // Muestra/oculta botones de eliminar según cantidad de teléfonos
        function actualizarBotonesEliminar() {
            const telefonoGroups = document.getElementsByClassName('telefono-group');
            const removeButtons = document.querySelectorAll('.remove-telefono');

            if (telefonoGroups.length > 1) {
                removeButtons.forEach(btn => btn.style.display = 'block');
            } else {
                removeButtons.forEach(btn => btn.style.display = 'none');
            }
        }

        // ========== VALIDACIONES DE FORMULARIO ==========

        // Valida nombres (solo letras y espacios, 1-50 caracteres)
        function validateName(input) {
            const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{1,50}$/;
            const isValid = regex.test(input.value.trim());

            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }

            return isValid;
        }

        // Valida formato de email
        function validateEmail(input) {
            validarSinEspacios(input);
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = regex.test(input.value.trim());

            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }

            return isValid;
        }

        // Valida nombre de usuario (solo letras, números y guiones bajos)
        function validateUsername(input) {
            validarSinEspacios(input);
            const regex = /^[A-Za-z0-9_]{3,50}$/;
            const isValid = regex.test(input.value.trim());

            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }

            return isValid;
        }

        // Valida fortaleza de la contraseña (8 caracteres exactos con requisitos)
        function validatePassword(input) {
            const password = input.value;
            let strength = 0;
            let requirements = {
                length: false,
                uppercase: false,
                lowercase: false,
                number: false,
                special: false,
                noSpaces: true,
                noSqlKeywords: true,
            };

            // Validar longitud exacta de 8 caracteres
            if (password.length === 8) {
                strength += 20;
                requirements.length = true;
                document.getElementById('req-length').className = 'text-success';
            } else {
                document.getElementById('req-length').className = 'text-muted';
            }

            // Validar mayúsculas
            if (/[A-Z]/.test(password)) {
                strength += 20;
                requirements.uppercase = true;
                document.getElementById('req-uppercase').className = 'text-success';
            } else {
                document.getElementById('req-uppercase').className = 'text-muted';
            }

            // Validar minúsculas
            if (/[a-z]/.test(password)) {
                strength += 20;
                requirements.lowercase = true;
                document.getElementById('req-lowercase').className = 'text-success';
            } else {
                document.getElementById('req-lowercase').className = 'text-muted';
            }

            // Validar números
            if (/[0-9]/.test(password)) {
                strength += 20;
                requirements.number = true;
                document.getElementById('req-number').className = 'text-success';
            } else {
                document.getElementById('req-number').className = 'text-muted';
            }

            // Validar caracteres especiales
            if (/[!@#$%^&*(),.":{}\-|]/.test(password)) {
                strength += 20;
                requirements.special = true;
                document.getElementById('req-special').className = 'text-success';
            } else {
                document.getElementById('req-special').className = 'text-muted';
            }

            // Validar seguridad adicional
            if (/\s/.test(password)) {
                requirements.noSpaces = false;
            }

            const sqlKeywords = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION', 'FROM', 'WHERE', 'OR', 'AND'];
            const upperPassword = password.toUpperCase();
            sqlKeywords.forEach(keyword => {
                if (upperPassword.includes(keyword)) {
                    requirements.noSqlKeywords = false;
                }
            });

            // Actualizar indicadores de seguridad
            document.getElementById('req-nospaces').className = requirements.noSpaces ? 'text-success' : 'text-danger';
            document.getElementById('req-nosql').className = requirements.noSqlKeywords ? 'text-success' : 'text-danger';

            // Actualizar barra de fortaleza
            updatePasswordStrengthBar(strength, requirements);

            // Validación final
            const isValid = requirements.length && requirements.uppercase &&
                requirements.lowercase && requirements.number &&
                requirements.special && requirements.noSpaces &&
                requirements.noSqlKeywords;

            showSpecificErrors(requirements, password);

            if (isValid && password.length > 0) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else if (password.length > 0) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }

            if (document.getElementById('confirmPassword').value) {
                validatePasswordConfirmation();
            }

            return isValid;
        }

        // Actualiza la barra visual de fortaleza de contraseña
        function updatePasswordStrengthBar(strength, requirements) {
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            // Penalizar problemas de seguridad
            if (!requirements.noSpaces || !requirements.noSqlKeywords) {
                strength = Math.max(0, strength - 30);
            }

            strengthBar.style.width = strength + '%';

            if (strength < 50) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Contraseña débil o no segura';
                strengthText.className = 'text-danger';
            } else if (strength < 80) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Contraseña media';
                strengthText.className = 'text-warning';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Contraseña fuerte';
                strengthText.className = 'text-success';
            }
        }

        // Muestra mensajes de error específicos para la contraseña
        function showSpecificErrors(requirements, password) {
            const errorElement = document.getElementById('password-error');

            if (!requirements.noSpaces) {
                errorElement.textContent = 'La contraseña no puede contener espacios en blanco';
            } else if (!requirements.noSqlKeywords) {
                errorElement.textContent = 'La contraseña contiene palabras reservadas no permitidas';
            } else if (!requirements.length) {
                if (password.length < 8) {
                    errorElement.textContent = 'La contraseña debe tener exactamente 8 caracteres (faltan ' + (8 - password.length) + ')';
                } else {
                    errorElement.textContent = 'La contraseña debe tener exactamente 8 caracteres';
                }
            } else if (!requirements.uppercase) {
                errorElement.textContent = 'La contraseña debe tener al menos una mayúscula';
            } else if (!requirements.lowercase) {
                errorElement.textContent = 'La contraseña debe tener al menos una minúscula';
            } else if (!requirements.number) {
                errorElement.textContent = 'La contraseña debe tener al menos un número';
            } else if (!requirements.special) {
                errorElement.textContent = 'La contraseña debe tener al menos un carácter especial (!@#$%^&*(),.":{}-|)';
            } else {
                errorElement.textContent = 'La contraseña no cumple con todos los requisitos';
            }
        }

        // Valida que la confirmación de contraseña coincida
        function validatePasswordConfirmation() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword');
            const isValid = password === confirmPassword.value;

            if (isValid && confirmPassword.value.length > 0) {
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
            } else if (confirmPassword.value.length > 0) {
                confirmPassword.classList.remove('is-valid');
                confirmPassword.classList.add('is-invalid');
            }

            return isValid;
        }

        // Alterna entre mostrar/ocultar la contraseña
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.parentNode.querySelector('.password-toggle i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }

        // ========== VALIDACIÓN DE PASOS ==========

        // Valida todos los campos del paso 1
        function validateStep1() {
            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');

            let isValid = true;

            if (!validateName(firstName)) isValid = false;
            if (!validateName(lastName)) isValid = false;
            if (!validateGenero()) isValid = false;
            if (!validateRecaptcha()) isValid = false;

            if (isValid) {
                showStep(2);
            } else {
                showError('Por favor complete correctamente todos los campos obligatorios');
            }

            return isValid;
        }

        // Valida todos los campos del paso 2
        function validateStep2() {
            const email = document.getElementById('email');
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const telefonosInputs = document.querySelectorAll('input[name="telefonos[]"]');

            let isValid = true;

            if (!validateEmail(email)) isValid = false;
            if (!validateUsername(username)) isValid = false;
            if (!validatePassword(password)) isValid = false;
            if (!validatePasswordConfirmation()) isValid = false;

            telefonosInputs.forEach(input => {
                if (!validarTelefono(input)) {
                    isValid = false;
                }
            });

            if (isValid) {
                showStep(3);
            } else {
                showError('Por favor complete correctamente todos los campos obligatorios');
            }

            return isValid;
        }

        // ========== MAPA Y UBICACIÓN - MEJORADO ==========

        // Inicializa el mapa Leaflet en el paso 3
        function initializeMap() {
            if (map) {
                map.invalidateSize();
                return;
            }

            const mapElement = document.getElementById('map');
            if (!mapElement) {
                console.error('Elemento map no encontrado');
                return;
            }

            // Usar la misma configuración del script que me pasaste
            map = L.map('map').setView([-34.603722, -58.381592], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Configurar evento de clic en el mapa (igual que en tu script)
            map.on('click', function(e) {
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;

                // Obtener detalles de la ubicación usando Nominatim (igual que en tu script)
                if (!isFetching) {
                    isFetching = true;
                    Swal.fire({
                        title: 'Cargando ubicación...',
                        text: 'Por favor espera mientras obtenemos los detalles de tu ubicación.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Datos completos de ubicación:', data); // Para debug

                            // MEJORADO: Capturar mejor el departamento con múltiples opciones
                            const departamento = data.address.county ||
                                data.address.municipality ||
                                data.address.district ||
                                data.address.state_district ||
                                '';

                            // Asignar valores a los campos específicos de tu HTML
                            document.getElementById('provincia').value = data.address.state || data.address.region || '';
                            document.getElementById('departamento').value = departamento;
                            document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || data.address.hamlet || '';
                            document.getElementById('barrio').value = data.address.suburb || data.address.neighbourhood || data.address.quarter || '';
                            document.getElementById('pais').value = data.address.country || 'Argentina';

                            // Actualizar formData
                            if (typeof formData !== 'undefined') {
                                formData['provincia'] = data.address.state || data.address.region || '';
                                formData['departamento'] = departamento;
                                formData['localidad'] = data.address.city || data.address.town || data.address.village || data.address.hamlet || '';
                                formData['barrio'] = data.address.suburb || data.address.neighbourhood || data.address.quarter || '';
                                formData['pais'] = data.address.country || 'Argentina';
                            }

                            // Actualizar estado
                            const locationName = data.address.city || data.address.town || data.address.village || 'Ubicación en mapa';
                            updateLocationStatus(true, `Ubicación seleccionada: ${locationName}`);
                        })
                        .catch(error => {
                            console.error('Error obteniendo información de ubicación:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo obtener la información de la ubicación. Inténtalo nuevamente.',
                                confirmButtonText: 'Aceptar'
                            });
                        })
                        .finally(() => {
                            isFetching = false;
                            Swal.close();
                        });
                }
            });

            // Llamar a la función para obtener la ubicación actual del usuario después de 2 segundos (igual que en tu script)
            setTimeout(() => {
                Swal.fire({
                    title: 'Usar ubicación',
                    text: 'Vamos a usar tu ubicación para completar el registro.',
                    icon: 'info',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        obtenerUbicacion();
                    }
                });
            }, 2000);

            // Redibujar el mapa cuando cambie el tamaño de la ventana
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        }

        // Función para obtener ubicación (igual que en tu script)
        function obtenerUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    mostrarPosicion,
                    mostrarError, {
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    }
                );
            } else {
                Swal.fire("La geolocalización no es soportada por este navegador.");
            }
        }

        // Función para mostrar posición (MEJORADA para capturar mejor departamento)
        function mostrarPosicion(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            var latlng = L.latLng(lat, lon);

            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }

            map.setView(latlng, 13);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;

            // Obtener detalles de la ubicación usando Nominatim
            if (!isFetching) {
                isFetching = true;
                Swal.fire({
                    title: 'Cargando ubicación...',
                    text: 'Por favor espera mientras obtenemos los detalles de tu ubicación.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Datos completos de ubicación:', data); // Para debug

                        // MEJORADO: Capturar mejor el departamento con múltiples opciones
                        const departamento = data.address.county ||
                            data.address.municipality ||
                            data.address.district ||
                            data.address.state_district ||
                            '';

                        document.getElementById('provincia').value = data.address.state || data.address.region || '';
                        document.getElementById('departamento').value = departamento;
                        document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || data.address.hamlet || '';
                        document.getElementById('barrio').value = data.address.suburb || data.address.neighbourhood || data.address.quarter || '';
                        document.getElementById('pais').value = data.address.country || 'Argentina';

                        // Actualizar formData
                        if (typeof formData !== 'undefined') {
                            formData['provincia'] = data.address.state || data.address.region || '';
                            formData['departamento'] = departamento;
                            formData['localidad'] = data.address.city || data.address.town || data.address.village || data.address.hamlet || '';
                            formData['barrio'] = data.address.suburb || data.address.neighbourhood || data.address.quarter || '';
                            formData['pais'] = data.address.country || 'Argentina';
                        }

                        updateLocationStatus(true, 'Ubicación detectada automáticamente');
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo obtener la información de la ubicación. Inténtalo nuevamente.',
                            confirmButtonText: 'Aceptar'
                        });
                    })
                    .finally(() => {
                        isFetching = false;
                        Swal.close();
                    });
            }
        }

        // Función para mostrar errores (igual que en tu script)
        function mostrarError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    Swal.fire("El usuario denegó la solicitud de geolocalización.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    Swal.fire("La información de ubicación no está disponible.");
                    break;
                case error.TIMEOUT:
                    Swal.fire("La solicitud de geolocalización ha caducado.");
                    break;
                case error.UNKNOWN_ERROR:
                    Swal.fire("Se ha producido un error desconocido.");
                    break;
            }
        }

        // Evento para buscar la ubicación nuevamente
        document.getElementById('buscar-ubicacion').addEventListener('click', function() {
            obtenerUbicacion();
        });

        // Actualiza el mensaje de estado de la ubicación
        function updateLocationStatus(isSet, message) {
            const statusElement = document.getElementById('locationStatus');
            if (!statusElement) {
                console.warn('Elemento locationStatus no encontrado');
                return;
            }

            if (isSet) {
                statusElement.innerHTML = `<i class="bx bx-check-circle text-success me-1"></i> ${message}`;
                statusElement.className = 'text-success';
            } else {
                statusElement.innerHTML = `<i class="bx bx-info-circle me-1"></i> ${message}`;
                statusElement.className = 'text-muted';
            }
        }

        // ========== FUNCIONES FINALES Y ENVÍO ==========

        // Prepara los datos de teléfonos para el envío del formulario
        function prepareFormData() {
            const telefonosInputs = document.querySelectorAll('input[name="telefonos[]"]');
            const tiposSelects = document.querySelectorAll('select[name="tipos[]"]');

            const telefonos = Array.from(telefonosInputs).map(input => input.value);
            const tipos = Array.from(tiposSelects).map(select => select.value);

            let telefonosInput = document.createElement('input');
            telefonosInput.type = 'hidden';
            telefonosInput.name = 'telefonos';
            telefonosInput.value = JSON.stringify(telefonos);
            document.getElementById('userRegistrationForm').appendChild(telefonosInput);

            let tiposInput = document.createElement('input');
            tiposInput.type = 'hidden';
            tiposInput.name = 'tipos';
            tiposInput.value = JSON.stringify(tipos);
            document.getElementById('userRegistrationForm').appendChild(tiposInput);
        }

        // Muestra mensajes de error temporales
        function showError(message) {
            const errorDiv = document.getElementById('error-messages');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('d-none');
                setTimeout(() => errorDiv.classList.add('d-none'), 5000);
            } else {
                console.error('Error:', message);
            }
        }

        // Verifica que los campos de ubicación estén completos
        function verificarCamposUbicacion() {
            const paisElement = document.getElementById('pais');
            const provinciaElement = document.getElementById('provincia');

            if (!paisElement || !provinciaElement) {
                console.error('Elementos de ubicación no encontrados');
                return false;
            }

            const pais = paisElement.value;
            const provincia = provinciaElement.value;
            return pais && provincia && pais !== '' && provincia !== '';
        }

        // Valida todo el formulario y lo envía
        function validateAndSubmit() {
            const latitud = document.getElementById('latitude').value;
            const longitud = document.getElementById('longitude').value;

            if (!latitud || !longitud) {
                const userResponse = confirm('No has seleccionado una ubicación. ¿Deseas que detectemos tu ubicación automáticamente?');
                if (userResponse) {
                    obtenerUbicacion();
                } else {
                    showError('Debes seleccionar una ubicación para crear tu cuenta. Haz clic en el mapa o usa la detección automática.');
                    return false;
                }
            } else {
                if (!verificarCamposUbicacion()) {
                    showError('Error: No se pudieron obtener todos los datos de ubicación esenciales (provincia y país).');
                    return false;
                }

                if (validateStep1() && validateStep2()) {
                    prepareFormData();
                    document.getElementById('userRegistrationForm').submit();
                } else {
                    showError('Por favor complete correctamente todos los campos obligatorios');
                }
            }
        }

        // ========== INICIALIZACIÓN ==========

        // Configuración inicial cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            formData = {};

            // Verificar que los elementos existen antes de asignar valores
            const selectedGender = document.getElementById('selectedGender');
            if (selectedGender) {
                selectedGender.value = '';
            }

            actualizarBotonesEliminar();

            // Configurar event listeners para botones de eliminar teléfonos
            document.querySelectorAll('.remove-telefono').forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.remove();
                    actualizarBotonesEliminar();
                });
            });

            // Configurar validación de género cuando cambie
            const generoSelect = document.getElementById('genero');
            if (generoSelect) {
                generoSelect.addEventListener('change', validateGenero);
                if (generoSelect.value !== '') {
                    validateGenero();
                }
            }

            console.log('Formulario de registro inicializado correctamente');
        });
    </script>


    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>