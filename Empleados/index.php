<?php
// verificamos cada campo, y validamos que la inf.llegue
// si hay algo capturamos el valor en caso contrario mandamos vacio
    $txtID = (isset($_POST["txtID"]))?$_POST["txtID"]:"";
    $txtNombre = (isset($_POST["txtNombre"]))?$_POST["txtNombre"]:"";
    $txtApellidoP = (isset($_POST["txtApellidoP"]))?$_POST["txtApellidoP"]:"";
    $txtApellidoM = (isset($_POST["txtApellidoM"]))?$_POST["txtApellidoM"]:"";
    $txtCorreo = (isset($_POST["txtCorreo"]))?$_POST["txtCorreo"]:"";
    $txtFoto = (isset($_FILES["txtFoto"]["name"]))?$_FILES["txtFoto"]["name"]:"";

    // botones que tienen accion editar,agregar,modificar,eliminar
    $accion = (isset($_POST["accion"]))?$_POST["accion"]:"";

    include("../conexion/conexion.php");
    // evaluamos con un switch lo que presiono el usuario

    switch ($accion) {
        case 'btnAgregar':
            $sentencia = $conn -> prepare("INSERT INTO empleados(Nombre,ApellidoP,ApellidoM,Correo,Foto) 
            VALUES (:Nombre,:ApellidoP,:ApellidoM,:Correo,:Foto)");

            $sentencia->bindParam(':Nombre', $txtNombre);
            $sentencia->bindParam(':ApellidoP', $txtApellidoP);
            $sentencia->bindParam(':ApellidoM', $txtApellidoM);
            $sentencia->bindParam(':Correo', $txtCorreo);
            
            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"default.png";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];

            if ($tmpFoto!="") {
               move_uploaded_file($tmpFoto,"../Imagenes/".$nombreArchivo);
            }
            
            
            $sentencia->bindParam(':Foto', $nombreArchivo);

            $sentencia->execute();
            // echo "<script> alert('presionaste agregar') </script>";
            header('Location: index.php');

            
            break;
        
        case 'btnModificar':
            $sentencia = $conn -> prepare("UPDATE empleados SET 
            Nombre=:Nombre,
            ApellidoP=:ApellidoP,
            ApellidoM=:ApellidoM,
            Correo=:Correo WHERE id=:id"); 
            
            $sentencia->bindParam(':id', $txtID);
            $sentencia->bindParam(':Nombre', $txtNombre);
            $sentencia->bindParam(':ApellidoP', $txtApellidoP);
            $sentencia->bindParam(':ApellidoM', $txtApellidoM);
            $sentencia->bindParam(':Correo', $txtCorreo);
            

            $sentencia->execute();

            // modificar foto solo si el usuario  no lo deja en blanco

            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"default.png";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            // si hay una foto seleccionada la modifica,pero antes tenemos que
            // borrar la anterior foto de nuestra carpeta Imagenes
            if ($tmpFoto!="") {
                // borrar foto antigua en dado caso que se modifique
                $sentencia = $conn -> prepare("SELECT Foto FROM empleados WHERE id=:id"); 
                $sentencia->bindParam(':id', $txtID);
                $sentencia->execute();
                $empleado=$sentencia->fetch(PDO::FETCH_LAZY);
               
                if (isset($empleado["Foto"])) {
                    if (file_exists("../Imagenes/".$empleado["Foto"])) {
                        unlink("../Imagenes/".$empleado["Foto"]);
                    }
                }
                // y luego subimos la nueva foto
               move_uploaded_file($tmpFoto,"../Imagenes/".$nombreArchivo);
               $sentencia = $conn -> prepare("UPDATE empleados SET 
        
               Foto=:Foto WHERE id=:id"); 
                $sentencia->bindParam(':id', $txtID);
                $sentencia->bindParam(':Foto', $nombreArchivo);
                $sentencia->execute();
            }
            
           header('Location: index.php');

            break;

        case 'btnEliminar':
            // elimina foto en la carpeta de imagenes cuando se elimine un registro
            $sentencia = $conn -> prepare("SELECT Foto FROM empleados WHERE id=:id"); 
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();
            $empleado=$sentencia->fetch(PDO::FETCH_LAZY);
           
            if (isset($empleado["Foto"])) {
                if (file_exists("../Imagenes/".$empleado["Foto"])) {
                    unlink("../Imagenes/".$empleado["Foto"]);
                }
            }

            // eliminamos el regitro

            $sentencia = $conn -> prepare("DELETE empleados FROM empleados WHERE id=:id"); 
            
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();
            
            header('Location: index.php');
           
            break;

        case 'btnCancelar':
           
            break;
        
        
    }
 
    $sentencia = $conn->prepare("SELECT * FROM empleados ");
    $sentencia->execute();
    $listaEmpleados = $sentencia->fetchALL(PDO::FETCH_ASSOC);

    // print_r($listaEmpleados);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>crud con php y mysql</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>

<div class="container">
    <form action="" method="post" enctype="multipart/form-data">
    
        <!-- ocultamos el id del empleado -->
        <input type="hidden" 
        name="txtID" 
        placeholder="" 
        id="txtID" 
       
        value='<?php echo $txtID; ?>'>
        <br>

        <label for="">NOMBRE:</label>
        <input type="text" 
        name="txtNombre" 
        placeholder="" 
        id="txtNombre" 
        required
        value='<?php echo $txtNombre; ?>'>
        <br>

        <label for="">APELLIDO PATERNO:</label>
        <input type="text" 
        name="txtApellidoP" 
        placeholder="" 
        id="txtApellidoP" 
        required
        value='<?php echo $txtApellidoP; ?>'>
        <br>

        <label for="">APELLIDO MATERNO:</label>
        <input type="text" 
        name="txtApellidoM" 
        placeholder="" 
        id="txtApellidoM" 
        required
        value='<?php echo $txtApellidoM; ?>'>
        <br>

        <label for="">CORREO:</label>
        <input type="text" 
        name="txtCorreo" 
        placeholder="" 
        id="txtCorreo" 
        required
        value='<?php echo $txtCorreo; ?>'>
        <br>  

        <label for="">FOTO:</label>
        <input type="file"
        accept="image/*" 
        name="txtFoto" 
        placeholder="" 
        id="txtFoto" 
        require
        value='<?php echo $txtFoto; ?>'>
        <br>
        <button value="btnAgregar" type="submit" name="accion">Agregar</button>
        <button value="btnModificar" type="submit" name="accion">Modificar</button>
        <button value="btnEliminar" type="submit" name="accion">Eliminar</button>
        <button value="btnCancelar" type="submit" name="accion">Cancelar</button>
    </form>

    <table class="table table-dark">
  <thead>
    <tr>
    
      <th scope="col">FOTO</th>
      <th scope="col">NOMBRE COMPLETO</th>
      <th scope="col">Correo</th>
      <th scope="col">ACCIONES</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($listaEmpleados as $empleado) { ?>
    <tr>
    
      <td> <img 
      src="../Imagenes/<?php echo $empleado['Foto']; ?>"
      class="img-thumbnail"
      width="100px"></td>
      <td><?php echo $empleado["Nombre"]; ?> <?php echo $empleado["ApellidoP"]; ?> <?php echo $empleado["ApellidoM"]; ?></td>
      <td><?php echo $empleado["Correo"]; ?> </td>
      <td>
      
      <form action="" method="post" >
            <input type="hidden" name="txtID" value="<?php echo $empleado['ID']; ?>">
            <input type="hidden" name="txtNombre" value="<?php echo $empleado['Nombre']; ?>">
            <input type="hidden" name="txtApellidoP" value="<?php echo $empleado['ApellidoP']; ?>">
            <input type="hidden" name="txtApellidoM" value="<?php echo $empleado['ApellidoM']; ?>">
            <input type="hidden" name="txtCorreo" value="<?php echo $empleado['Correo']; ?>">
            <input type="hidden" name="txtFoto" value="<?php echo $empleado['Foto']; ?>">

            <input type="submit" value="seleccionar" name="accion" class="btn btn-success">
            <button value="btnEliminar" type="submit" name="accion" class="btn btn-danger">Eliminar</button>
      </form>
     
      </td>
    </tr>
<?php } ?>
  </tbody>
</table>
</div>



<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
</body>
</html>