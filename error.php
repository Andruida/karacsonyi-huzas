<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baj van</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
            <div>
                <h1 style="font-size: calc(1.5rem + 6vw);">Hát...</h1>
                <p style="font-size: calc((1.5rem + 6vw) / 3);">Valami nem stimmül</p>
                <?php if (isset($CLIENT_ERROR) && $CLIENT_ERROR) { ?>
                <p style="font-size: calc((1.5rem + 6vw) / 3);">De nem velem, veled</p>
                <?php } ?>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>

</html>
