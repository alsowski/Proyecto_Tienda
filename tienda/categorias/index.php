<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index de Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../util/conexion.php');

        session_start();
        if (isset($_SESSION["usuario"])) { ?>
            <h2>Bienvenid@ <?php echo $_SESSION["usuario"] ?> </h2>
            <a class="btn btn-warning" href="../usuario/cambiar_credenciales.php?usuario=<?php echo $_SESSION["usuario"] ?>">Cambiar contraseña</a>
            <a class="btn btn-danger" href="../usuario/cerrar_sesion.php">Cerrar sesion</a>
        <?php } else {
            header("location: ../usuario/iniciar_sesion.php");
            exit;
        }
    ?>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tabla de Categorias</h1>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $categoria = $_POST["categoria"];
                $sql = "SELECT * FROM productos WHERE categoria ='$categoria'";
                $resultado = $_conexion -> query($sql);

                if($resultado -> num_rows >= 1){
                    $err_borrar = "Para borrar la categoria, primero tienes que borrar los productos asociados a esta";
                } else {
                    $sql = "DELETE FROM categorias WHERE categoria = '$categoria'";
                    $_conexion -> query($sql);
                }
            }

            $sql = "SELECT * FROM categorias";
            $resultado = $_conexion -> query($sql);

        ?>
        <a class="btn btn-outline-primary" href="nueva_categoria.php">Crear nueva categoria</a>
        <a class="btn btn-outline-secondary" href="../productos/index.php">Tabla Productos</a><br><br>
        <?php if(isset($err_borrar)) echo "<span class='error'>$err_borrar</span><br><br>"; ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while($fila = $resultado -> fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila["categoria"] . "</td>";
                        echo "<td>" . $fila["descripcion"] . "</td>";
                        ?>
                        <td>
                            <a class="btn btn-primary" 
                               href="editar_categoria.php?categoria=<?php echo $fila["categoria"] ?>">Editar</a>
                        </td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="categoria" value="<?php echo $fila["categoria"] ?>">
                                <input class="btn btn-danger" type="submit" value="Borrar">
                            </form>
                        </td>
                        <?php
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <a href="../index.php" class="btn btn-success">Volver a inicio</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>