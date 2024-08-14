<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Esmefis\Gradebook\verifyAuth;

if(isset($_COOKIE['LoSessionToken'])){
    $verifyLocalSession = verifyAuth::LocalSession($_COOKIE['LoSessionToken']);
} else if (isset($_SESSION["adnanhussainturki/microsoft"]["accessToken"])) {
    $verifyMicrosoftSession = verifyAuth::MicrosoftSession($_SESSION["adnanhussainturki/microsoft"]["accessToken"]);
} else if (!isset($_SESSION["adnanhussainturki/microsoft"]["accessToken"])) {
    header('Location: login.php?sessionMicrosoft=expired');
    exit;
} else {
    header('Location: login.php?session=expired');
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Gradebook - Horarios</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/esmefis_icon.ico" />
        <!-- Custom Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body class="d-flex flex-column h-100 bg-light">
        <main class="flex-shrink-0">
            <!-- Navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
                <div class="container px-5">
                    <a class="navbar-brand" href="inicio.php"><span class="fw-bolder text-primary">GradeBook ESMEFIS</span></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                            <li class="nav-item"><a class="nav-link" href="inicio.php">Inicio</a></li>
                            <li class="nav-item"><a class="nav-link" href="calificaciones.php">Calificaciones</a></li>
                            <li class="nav-item"><a class="nav-link" href="horarios.php">Horarios</a></li>
                            <li class="nav-item"><a class="nav-link" href="https://teams.microsoft.com/v2/">Microsoft Teams</a></li>
                            <li class="nav-item"><a id="closeSession" class="nav-link" href="#">Cerrar sesión</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Projects Section-->
            <section class="py-5">
                <div class="container px-5 mb-5">
                    <div class="text-center mb-5">
                        <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">Mis practicas clinicas</span></h1>                        
                    </div>                    
                </div>
            </section>
            <section id="">
                <div class="container px-5 mb-5">
                    <div id="calendar"></div>
                </div>
            </section>
            <!-- Call to action section-->
        </main>
        <!-- Footer-->
        <footer class="bg-white py-4 mt-auto">
            <div class="container px-5">
                <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                <div class="col-auto"><div class="small m-0">Copyright &copy; Esmefis 2024</div></div>
                    <div class="col-auto">
                        <a class="small" href="https://esmefis.edu.mx/aviso-de-privacidad/">Aviso de privacidad</a>
                        <!--<span class="mx-1">&middot;</span>
                        <a class="small" href="#!">Terminos y condiciones</a>-->
                        <span class="mx-1">&middot;</span>
                        <a class="small" href="contacto.html">Contacto</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- jquery -->
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js" integrity="sha256-J8ay84czFazJ9wcTuSDLpPmwpMXOm573OUtZHPQqpEU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
        <!-- SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!--fullcalendar-->
        <script src="js/common/fullcalendar/dist/index.global.js"></script>
        <script src="js/common/fullcalendar/packages/google-calendar/index.global.js"></script>
        <!-- Core theme JS-->
        <script type="module" src="js/pages/horas-practicas.js"></script>
        <script type="module" src="js/common/closeSession.js"></script>
    </body>
</html>

<!-- Modal -->
<div class="modal fade" id="practices" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="practicesLabel" aria-hidden="true">
  <div class="modal-dialog modal-center modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="practicesLabel">Solicitar horas practicas</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="requestP">
            <div class="card border-light">
                <img src="assets/esmefis.png" class="card-img-top" style="width: 70px; height: 70px;">
                <div class="card-body">
                    <h5 class="card-title">Solicitud de practicas</h5>
                    <p class="card-text">Recuerda que los horarios de la clínica son: Lun-Vie de 9 am a 5:30 pm y Dom de 8 am a 2 pm. Sábado cerrado</p>
                    <p class="card-text">Fecha y hora actual: <?php echo date('d-m-Y H:i'); ?></p>
                    <div class="mb-3">
                        <label for="studentName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="studentName" readonly onkeydown="return false;" onmousedown="return false;" value="<?php echo $_SESSION['userName'] ?>">
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="timeStart" class="form-label">Fecha seleccionada</label>
                            <input type="text" readonly onkeydown="return false;" onmousedown="return false;" class="form-control" value="" name="dateStart" id="dateStart">
                        </div>
                        <div class="col-4">
                            <label for="timeStart" class="form-label">Hora de entrada</label>
                            <select class="form-select" value="" id="timeStart" name="timeStart">
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="timeEnd" class="form-label">Hora de salida</label>
                            <input type="text" class="form-select" value="" id="timeEnd" name="timeEnd"/>
                        </div>
                    </div>
                    <div class="mb-3 py-3">
                        <label for="activity" class="form-label">Actividad solicitada:</label>
                        <input type="text" class="form-control" id="activity" readonly onkeydown="return false;" onmousedown="return false;" value="Practicas clinicas">
                    </div>                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" id="verifyDis" class="btn btn-success">Reservar</button>
        </div>
        </form>
    </div>
  </div>
</div>