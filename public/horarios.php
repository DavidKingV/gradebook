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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
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
                        <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">Mis horarios de clases</span></h1>                        
                    </div>                    
                </div>
            </section>
            <section id="section-sched">
                <!--<div class="container px-5 mb-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-bolder mb-0">Mis horarios de clases</h4>                        
                    </div>
                    <div class="gx-6 justify-content-center">
                        <div class="card">
                            <div class="card-header">
                                10:00 am - 11:00 am
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Clase de Fisioatoligia de la inflamación</h5>
                                <p class="card-text"><i class="bi bi-geo-fill"></i> Reunión Online</p>
                                <a href="#" class="btn btn-primary">Go somewhere</a>
                            </div>
                        </div>
                    </div>
                </div>-->
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Core theme JS-->
        <script type="module" src="js/pages/horarios.js"></script>
        <script type="module" src="js/common/closeSession.js"></script>
    </body>
</html>
