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
        <title>Gradebook - Inicio</title>
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
                        <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">Bienvenid@, <?php echo $_SESSION["userName"] ?></span></h1>
                        <div class="text-center py-2">
                            <img src="<?php echo $_SESSION["userPhoto"]?>" alt="Profile Photo" class="rounded-circle" width="120" height="120">
                        </div>
                    </div>
                    <div class="row gx-5 justify-content-center">
                        <div class="col-lg-6 col-md-6 mb-5">
                            <!-- Project Card 1-->
                            <div class="card overflow-hidden shadow rounded-4 border-0 mb-5">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center">
                                        <div class="p-5">
                                            <h2 class="fw-bolder">Calificaciones</h2>
                                            <p>En esta sección podrás consultar tus calificaciones que hayan sido registradas en el sistema.</p>
                                            <a href="calificaciones.php" class="btn btn-primary">Ver mis calificaciones</a>
                                        </div>
                                        <img class="img-fluid" src="assets/grades.jpg" alt="..." />
                                    </div>
                                </div>
                            </div>
                       </div>    
                       <div class="col-lg-6 col-md-6 mb-5"> 
                            <!-- Project Card 2-->
                            <div class="card overflow-hidden shadow rounded-4 border-0">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center">
                                        <div class="p-5">
                                            <h2 class="fw-bolder">Horarios</h2>
                                            <p>En esta sección podrás consultar los horarios de tus clases, según las tengas programadas.</p>
                                            <a href="horarios.php" class="btn btn-primary">Ver mis horarios</a>
                                        </div>
                                        <img class="img-fluid" src="assets/horarios.jpg" alt="..." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="container px-5 mb-5">                
                    <div class="row gx-5 justify-content-center">
                        <div class="col-lg-6 col-md-6 mb-5">
                            <!-- Project Card 1-->
                            <div class="card overflow-hidden shadow rounded-4 border-0 mb-5">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center">
                                        <div class="p-5">
                                            <h2 class="fw-bolder">Mis horas practicas</h2>
                                            <p>En esta sección podrás solicitar horas practicas dependiendo la disponibilidad.</p>
                                            <a href="horas-practicas.php" class="btn btn-primary">Solicitar practicas</a>
                                        </div>
                                        <img class="img-fluid" src="assets/practicas.jpg" alt="..." />
                                    </div>
                                </div>
                            </div>
                       </div>                           
                    </div>
                </div>
            </section>

            <!-- Call to action section-->
            <section class="py-5 bg-gradient-primary-to-secondary text-white">
                <div class="container px-5 my-5">
                    <div class="text-center">
                        <h2 class="display-4 fw-bolder mb-4">"El éxito es de todos, tú decides"</h2>
                        <!--<a class="btn btn-outline-light btn-lg px-5 py-3 fs-6 fw-bolder" href="contact.html">Mi perfil</a>-->
                    </div>
                </div>
            </section>
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
        <script type="module" src="js/pages/inicio.js"></script>
        <script type="module" src="js/common/closeSession.js"></script>
    </body>
</html>

<!-- Modal -->
<div class="modal modal-xl" id="TyCModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="TyCModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable ">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="TyCModalLabel">Reglamento General De Estudiantes De Esmefis, Centro Universitario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php include "../backend/views/StudentRegulations.php" ?>
      </div>
      <div class="modal-footer">
        <button type="button"  id="acceptTyC" disabled class="btn btn-success">He leido y acepto el reglamento</button>
      </div>
    </div>
  </div>
</div>
