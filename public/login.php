<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Esmefis\Gradebook\verifyAuth;

if(isset($_COOKIE['LoSessionToken'])){
    $verifyLocalSession = verifyAuth::LocalSession($_COOKIE['LoSessionToken']);
    if($verifyLocalSession['success'] && $verifyLocalSession['uID'] != NULL){
        header('Location: inicio.php?session=restored');
        exit;
    }
} else if (isset($_SESSION["adnanhussainturki/microsoft"]["accessToken"])) {
    $verifyMicrosoftSession = verifyAuth::MicrosoftSession($_SESSION["adnanhussainturki/microsoft"]["accessToken"]);
    if($verifyMicrosoftSession['success']){
        header('Location: inicio.php?session=restored');
        exit;
    }
} else {
    header('Location: login.html?session=expired');
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
        <title>Iniciar sesión</title>
        <!-- Favicon-->
        <link rel="icon" type="image" href="assets/esmefis_icon.ico" />
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
                    <a class="navbar-brand" href="index.html"><span class="fw-bolder text-primary">GradeBook ESMEFIS</span></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                            <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                            <li class="nav-item"><a class="nav-link" href="contacto.html">Contacto</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page Content-->
            <div class="container px-5 my-5">
                <div class="text-center mb-5">
                    <div class="feature bg-primary bg-gradient-primary-to-secondary text-white rounded-3 mb-3"><i class="bi bi-person-check-fill"></i></div>
                    <h1 class="fw-bolder">Iniciar sesión</h1>
                    <p class="lead fw-normal text-muted mb-0">Ingresa tus datos</p>
                </div>
                <div class="row gx-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <form id="loginForm">
                            <!-- Name input-->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="user" name="user" type="text" placeholder="Ingresa tu usuario..." data-sb-validations="required" />
                                <label for="user">Usuario</label>
                                <div class="invalid-feedback" data-sb-feedback="user:required">El Usuario es requerido.</div>
                            </div>
                            <!-- Password input-->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="password" name="password" type="password" placeholder="Ingresa tu contraeña..." data-sb-validations="required" />
                                <label for="user">Contraseña</label>
                                <div class="invalid-feedback" data-sb-feedback="password:required">Ingresa una contraseña.</div>
                            </div>    
                            <div class="form-floating mb-3 py-2">
                               <p class="position-absolute top-100 start-50 translate-middle mt-1"> Or </p>                            
                            </div>  
                            <div class="form-floating mb-3 py-2">
                                <a href="javascript:void(0);" id="openInNewWindow">
                                    <img src="assets/microsoft-sign.png" alt="Descripción de la imagen">
                                </a>                                
                            </div>  
                            <div class="form-floating mb-3">
                                <p class="muted"><a href="#" data-bs-toggle="tooltip" data-bs-title="Por favor ponte en contacto con tu administrador">¿Olvisate tu contraseña?</a></p>
                            </div>                         
                            <!-- Submit success message-->
                            <!---->
                            <!-- Submit Button-->
                            <div class="d-grid"><button class="btn btn-primary btn-lg" id="submitButton" type="submit">Entrar</button></div>
                        </form>
                    </div>
                </div>
            </div>
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
        <script type="module" src="js/pages/login.js"></script>
    </body>
</html>
