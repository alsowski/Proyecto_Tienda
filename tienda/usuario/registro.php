<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../util/conexion.php');
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

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $tmp_usuario = depurar($_POST["usuario"]);
        $tmp_contrasena = $_POST["contrasena"];

        if($tmp_usuario == ""){
            $err_usuario = "El usuario es obligatorio";
        } else {
            $sql="SELECT * FROM usuarios WHERE usuario ='$tmp_usuario'";
            $resultado = $_conexion -> query($sql);

            if($resultado -> num_rows == 1){
                $err_usuario = "El usuario ya ha sido registrado";
            } else {
                if(strlen($tmp_usuario) > 15 || strlen($tmp_usuario) < 3){
                    $err_usuario = "El usuario debe tener como mínimo 3 y máximo 15 carácteres";
                } else {
                    $patron = "/^[0-9a-zA-ZáéíóúÁÉÍÓÚ]+$/";
                    if(!preg_match($patron, $tmp_usuario)){
                        $err_usuario = "El usuario únicamente puede tener letras y números";
                    } else {
                        $usuario = $tmp_usuario;
                    }
                }
            }
        }

        if($tmp_contrasena == ""){
            $err_contrasena = "La contraseña es obligatoria";
        } else {
            if(strlen($tmp_contrasena) > 15 || strlen($tmp_contrasena) < 8){
                $err_contrasena = "La contraseña debe tener como mínimo 8 y máximo 15 carácteres";
            } else {
                $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                if(!preg_match($patron, $tmp_contrasena)){
                    $err_contrasena = "La contraseña debe tener letras en minúsculas y mayúsculas, números y puede tener carácteres especiales";
                } else {
                    $contrasena_cifrada = password_hash($tmp_contrasena,PASSWORD_DEFAULT);
                }                    
            }
        }

        if(isset($usuario) && isset($contrasena_cifrada)){
            $sql = "INSERT INTO usuarios VALUES ('$usuario','$contrasena_cifrada')";
            $_conexion -> query($sql);
            header("location: iniciar_sesion.php");
            exit;
        }
            
    } 
    ?>
    <div class="container">
        <h1>Registro</h1>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($err_usuario)) echo "<span class='error'>$err_usuario</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) echo "<span class='error'>$err_contrasena</span>"; ?>
            </div>
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" value="Registrarse">
            </div>
        </form>
        <div class="mb-3">
            <h3>Si ya tienes cuenta, inicia sesión</h3>
            <a class="btn btn-warning" href="iniciar_sesion.php">Iniciar sesión</a>
            <a href="../index.php" class="btn btn-secondary">Volver a inicio</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>