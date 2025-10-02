<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pais = $_POST['pais'];
    $provincia = $_POST['provincia'];
    $departamento = $_POST['departamento'];
    $calle = $_POST['calle'];
    $barrio = $_POST['barrio'];

    // Verificar si se están pasando los datos
    echo "País: " . htmlspecialchars($pais) . "<br>";
    echo "Provincia: " . htmlspecialchars($provincia) . "<br>";
    echo "Departamento: " . htmlspecialchars($departamento) . "<br>";
    echo "Calle: " . htmlspecialchars($calle) . "<br>";
    echo "Barrio: " . htmlspecialchars($barrio) . "<br>";

    // Aquí puedes guardar los datos en una base de datos, archivo, etc.
}
?>
