<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto</title>
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
        <h1>Nuevo Producto</h1>
        <?php
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $tmp_nombre = depurar($_POST["nombre"]);
            $tmp_precio = depurar($_POST["precio"]);
            if(isset($_POST["categoria"])) $tmp_categoria = depurar($_POST["categoria"]);
            else $tmp_categoria = "";
            $tmp_stock = depurar($_POST["stock"]);
            $tmp_descripcion = depurar($_POST["descripcion"]);

            $nombre_imagen = $_FILES["imagen"]["name"];
            $ubicacion_temporal = $_FILES["imagen"]["tmp_name"];
            $ubicacion_final = "../imagenes/$nombre_imagen";

            if($tmp_nombre == ''){
                $err_nombre = "El nombre es obligatorio";
            } else {
                if(strlen($tmp_nombre) > 50){
                    $err_nombre = "El nombre no puede ser mayor a 50 carácteres";
                } else {
                    $patron = "/^[0-9a-zA-Z áéíóúÁÉÍÓÚ]+$/";
                    if(!preg_match($patron, $tmp_nombre)){
                        $err_nombre = "El nombre solo puede tener letras, numeros y espacios";
                    } else{
                        $nombre = $tmp_nombre;
                    }                
                }
            }

            if($tmp_precio == ''){
                $err_precio = "El precio es obligatorio";
            } else {
                if(!filter_var($tmp_precio,FILTER_VALIDATE_FLOAT)){
                    $err_precio = "El precio tiene que ser un numero";
                } else {
                    $patron = "/^[0-9]{1,4}(\.[0-9]{1,2})?$/";
                    if(!preg_match($patron, $tmp_precio)) {
                        $err_precio = "El precio solo puede contener 6 dígitos (4 enteros y 2 decimales)";
                    } else {
                        $precio = $tmp_precio;
                    }
                }
            }

            if($tmp_categoria == ''){
                $err_categoria = "La categoria es obligatoria";
            } else {
                if(strlen($tmp_categoria) > 30){
                    $err_categoria = "La categoria no puede ser mayor a 30 carácteres";
                } else {
                    $sql = "SELECT * FROM categorias ORDER BY categoria";
                    $resultado = $_conexion -> query($sql);
                    $categorias = [];
            
                    while($fila = $resultado -> fetch_assoc()) {
                        array_push($categorias, $fila["categoria"]);
                    }
                    
                    if(!in_array($tmp_categoria,$categorias)){
                        $err_categoria = "La categoria no existe";
                    } else {
                        $categoria = $tmp_categoria;
                    }
                }
            }

            if($tmp_stock == ''){
                $stock = 0;
            } else {
                if(!filter_var($tmp_stock,FILTER_VALIDATE_INT)){
                    $err_stock = "El stock tiene que ser un numero entero";
                } else {
                    $stock = $tmp_stock;
                }
            }

            if($nombre_imagen == ''){
                $err_imagen = "La imagen es obligatoria";
            } else {
                if(strlen($nombre_imagen) > 60){
                    $err_imagen = "La categoria no puede ser mayor a 60 carácteres";
                } else {
                    move_uploaded_file($ubicacion_temporal, $ubicacion_final);
                    $imagen = $nombre_imagen;
                }
            }

            if($tmp_descripcion == ''){
                $err_descripcion = "La descripción es obligatoria";
            } else {
                if(strlen($tmp_descripcion) > 30){
                    $err_descripcion = "La descripción no puede ser mayor a 255 carácteres";
                } else {
                    $descripcion = $tmp_descripcion;
                }
            }

            if(isset($nombre) && isset($precio) && isset($categoria) && isset($stock) && isset($nombre_imagen) && isset($descripcion)){
            $sql = "INSERT INTO productos (nombre, precio, categoria, stock, imagen, descripcion) 
                VALUES ('$nombre', $precio, '$categoria', $stock, '$nombre_imagen', '$descripcion')";

            $_conexion -> query($sql);
                
            }
        }
 
        ?>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control" type="text" name="nombre">
                <?php if(isset($err_nombre)) echo "<span class='error'>$err_nombre</span>" ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input class="form-control" type="text" name="precio">
                <?php if(isset($err_precio)) echo "<span class='error'>$err_precio</span>" ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select class="form-select" name="categoria">
                    <option value="" selected disabled hidden>--- Elige la categoria ---</option>
                    <?php
                    foreach($categorias as $categoria) { ?>
                        <option value="<?php echo $categoria ?>">
                            <?php echo $categoria ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if(isset($err_categoria)) echo "<span class='error'>$err_categoria</span>" ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input class="form-control" type="text" name="stock">
                <?php if(isset($err_stock)) echo "<span class='error'>$err_stock</span>" ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input class="form-control" type="file" name="imagen">
                <?php if(isset($err_imagen)) echo "<span class='error'>$err_imagen</span>" ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <input class="form-control" type="text" name="descripcion">
                <?php if(isset($err_descripcion)) echo "<span class='error'>$err_descripcion</span>" ?>
            </div>
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" value="Insertar">
                <a class="btn btn-secondary" href="index.php">Volver</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>