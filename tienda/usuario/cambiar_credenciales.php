<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Credenciales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../util/conexion.php');

        session_start();
        if (!isset($_SESSION["usuario"])) { 
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
    <?php
    function depurar(string $entrada) : string {
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        $salida = stripslashes($salida);
        $salida = preg_replace('!\s+!', ' ', $salida);
        return $salida;
    }
    ?>
    <div class="container">
    <h1>Cambiar credenciales</h1>
        <?php
        $usuario = $_GET["usuario"];
        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $_conexion -> query($sql);
        
        while($fila = $resultado -> fetch_assoc()) {
            $contrasena = $fila["contrasena"];
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $tmp_contrasena = ($_POST["tmp_contrasena"]);

            if($tmp_contrasena == ""){
                $err_contrasena = "La contraseña es obligatoria";
            } else {
                if( strlen($tmp_contrasena) < 8 || strlen($tmp_contrasena) > 15){
                    $err_contrasena = "La contraseña debe tener como minimo 8 y maximo 15 caracteres";
                } else {
                    $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                    if(!preg_match($patron, $tmp_contrasena)){
                        $err_contrasena = "La contraseña debe tener letras en minúsculas y mayúsculas, números y puede tener carácteres especiales";
                    } else {
                        $contrasena_cifrada = password_hash($tmp_contrasena,PASSWORD_DEFAULT);
                        $sql = "UPDATE usuarios SET contrasena = '$contrasena_cifrada' WHERE usuario = '$usuario'";
                        $_conexion -> query($sql);
                    }                    
                }
            }
        }
        ?>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario" disabled value="<?php echo $usuario ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="tmp_contrasena">
                <?php if(isset($err_contrasena)) echo "<span class='error'>$err_contrasena</span>" ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="usuario" value="<?php echo $usuario ?>">
                <input class="btn btn-primary" type="submit" value="Confirmar">
                <a href="../index.php" class="btn btn-secondary">Volver a inicio</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>