<?php

include 'funciones.php';
$centinela=False;
csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El alumno ' . escapar($_POST['nombre']) . ' ha sido agregado con éxito'
  ];

  $config = include 'config.php';

  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $alumno = [
      "nombre"   => $_POST['nombre'],
      "apellido" => $_POST['apellido'],
      "email"    => $_POST['email'],
      "edad"     => $_POST['edad'],
    ];

    $consultaSQL = "INSERT INTO alumnos (nombre, apellido, email, edad)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($alumno)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($alumno);

  
    /////////////////// MODIFICADO
   
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                      
      function get_data() {
          $name = $_POST['nombre'];
          $file_name='datosForm'. '.json';
      
          if(file_exists("$file_name")) { 
              $current_data=file_get_contents("$file_name");
              $array_data=json_decode($current_data);
                                  
              $extra=array(
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'edad' => $_POST['edad'],
              );
              $array_data[]=$extra;
              echo "Archivo existe<br/>";
              
              return json_encode($array_data);

              
              
          }
          else {
              $datae=array();
              $datae[]=array(
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'edad' => $_POST['edad'],
              );
              echo "Archivo NO existe, creando<br/>";
              return json_encode($datae);   
          }
      }

      $file_name='datosForm'. '.json';
      
      
      if(file_put_contents("$file_name", get_data())) { //pone contenido de la funcion "get data" que es el arreglo, dentro del archivo.
          // echo 'Archivo existe';

          //MOSTRAR ARREGLO
          $centinela=True;
      }                
      else {
          echo 'Hay un error!';                
      }
    }
   
    ////////////////////
    
    // $nombre=$_POST['nombre'];
    // $apellido=$_POST['apellido'];
    // $edad=$_POST['edad'];
    // $carrera=$_POST['carrera'];
    // $localidad=$_POST['localidad'];
    // $correo=$_POST['correo'];
    // $telefono=$_POST['telefono'];
    // $numcontrol=$_POST['numcontrol'];

    
    // //crear_archivo_json.php
    // $arreglo_form = array('nombre'=> $nombre, 'apellido'=> $apellido,
    // 'edad'=> $edad, 'carrera'=> $carrera, 'localidad'=>
    // $localidad, 'correo'=> $correo, 'telefono'=>$telefono,'numcontrol'=>$numcontrol);

    // //Creamos el JSON
    // $json_string = json_encode($arreglo_form);
    // $archivo_json = 'formDatos.json';
    // file_put_contents($archivo_json, $json_string);

    


  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-4">Crea un alumno</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control">
        </div>
        <div class="form-group">
          <label for="apellido">Apellido</label>
          <input type="text" name="apellido" id="apellido" class="form-control">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="form-group">
          <label for="edad">Edad</label>
          <input type="text" name="edad" id="edad" class="form-control">
        </div>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <input type="submit" name="submit" class="btn btn-primary" value="Enviar">
          <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
  //MOSTRAR ARREGLO
  if($centinela==True){
    $datos_json = file_get_contents("datosForm.json");

    // print_r(json_decode($datos_json, true)); //otra forma de mostrar el array
  
    $usuarios_json = json_decode($datos_json); //decodificar
    echo "Alumnos: <br><br>";
    foreach ($usuarios_json as $datosJson) { //cilco leer array of objects json   [] array     {} object
      
      echo "Nombre: ".$datosJson->nombre."<br>"; //EL . despues de ->nombre es para poder usar HTML despues
      echo "Apellido: ".$datosJson->apellido."<br>";
      echo "Email: ".$datosJson->email."<br>";
      echo "Edad: ".$datosJson->edad."<br><br>";  
      // if (property_exists($datosJson, 'datosJson')){ //PARA COMPRAR QUE SI EXISTE VARIABLE
      
    // }
  
    }
  }
 
?>

<?php include 'templates/footer.php'; ?>