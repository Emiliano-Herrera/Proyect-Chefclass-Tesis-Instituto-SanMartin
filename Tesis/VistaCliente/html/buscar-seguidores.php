<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "Error: Usuario no autenticado.";
    exit();
}

$ID_Usuario = $_SESSION['id_usuario'];
$filtro = isset($_POST['filtro']) ? '%' . $_POST['filtro'] . '%' : '%';
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'seguidores';

if ($tipo === 'seguidores') {
    $sql = "
        SELECT U.id_usuario, U.nombre_usuario, U.nombre, U.apellido, U.img 
        FROM seguimiento S
        INNER JOIN usuarios U ON S.seguidor_id = U.id_usuario
        WHERE S.seguido_id = ? AND 
              (U.nombre_usuario LIKE ? OR U.nombre LIKE ? OR U.apellido LIKE ?)";
} elseif ($tipo === 'seguidos') {
    $sql = "
        SELECT U.id_usuario, U.nombre_usuario, U.nombre, U.apellido, U.img 
        FROM seguimiento S
        INNER JOIN usuarios U ON S.seguido_id = U.id_usuario
        WHERE S.seguidor_id = ? AND 
              (U.nombre_usuario LIKE ? OR U.nombre LIKE ? OR U.apellido LIKE ?)";
} else {
    echo "Error: Tipo de búsqueda no válido.";
    exit();
}

$stmt = $conexion->prepare($sql);
$stmt->bind_param("isss", $ID_Usuario, $filtro, $filtro, $filtro);
$stmt->execute();
$resultado = $stmt->get_result();

while ($usuario = $resultado->fetch_assoc()): ?>
    <li class="list-group-item d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="<?php echo '../../VistaAdmin/html/' . $usuario['img']; ?>" alt="Imagen de usuario" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
            <div>
                <strong><?php echo $usuario['nombre_usuario']; ?></strong><br>
                <small><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></small>
            </div>
        </div>
        <?php if ($tipo === 'seguidores'): ?>
            <button class="btn btn-danger btn-sm eliminar-seguidor" data-id="<?php echo $usuario['id_usuario']; ?>">Eliminar</button>
        <?php elseif ($tipo === 'seguidos'): ?>
            <button class="btn btn-danger btn-sm dejar-seguir" data-id="<?php echo $usuario['id_usuario']; ?>">Dejar de seguir</button>
        <?php endif; ?>
    </li>
<?php endwhile; ?>