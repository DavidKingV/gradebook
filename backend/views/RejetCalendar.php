<?php
    require_once __DIR__ . '/../../vendor/autoload.php';

    use Esmefis\Gradebook\getEnv;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Error</title>
</head>
<body>
    <script>
        Swal.fire({
            title: "Hoy no es Viernes",
            text: "Recuerda que solo puedes solicitar practicas los viernes",
            icon: "question",
            showConfirmButton: false,
            backdrop: `
                rgba(0,0,123,0.4)
                url("https://media1.tenor.com/m/W7wWWPesIiwAAAAd/boxing-cat.gif")
                left top
                no-repeat
            `,
            timer: 8000,
        }).then(() => {
            window.location.href = '<?php echo $_ENV['INDEX_URL'] ?>';
        });
    </script>
</body>
</html>