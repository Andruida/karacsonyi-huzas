<?php
ob_start();

function exception_handler(Throwable $e)
{
    file_put_contents('php://stdout', "Unhandled exception:\n" . $e->getMessage() . "\nLine: " . $e->getLine() . "\n\n");
    ob_end_clean();
    http_response_code(500);
    include("./error.php");
    die();
}
set_exception_handler('exception_handler');

require_once("./config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) || 
    empty($_POST["name"]) ||
    empty($_POST["wish"])) {
        http_response_code(400);
        header("Content-Type: application/json");
        echo '{"message":"Invalid inputs"}';
        die();
    }

    $dbh = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);

    $stmt = $dbh->prepare("SELECT `id` FROM `users` WHERE `email` LIKE :EMAIL");
    $stmt->bindValue("EMAIL", $_POST["email"]);
    $userID = null;
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $userID = $stmt->fetch(PDO::FETCH_ASSOC)["id"];
    } else {
        $stmt = $dbh->prepare("INSERT INTO `users`(`name`, `email`) VALUES (:UNAME, :EMAIL)");
        $stmt->bindValue("EMAIL", $_POST["email"]);
        $stmt->bindValue("UNAME", $_POST["name"]);
        
        $stmt->execute();
        $userID = $dbh->lastInsertId();
    }

    $stmt = $dbh->prepare("INSERT INTO `wishes`(`user_id`, `wish`) VALUES (:USERID, :WISH)");
    $stmt->bindValue("USERID", $userID);
    $stmt->bindValue("WISH", $_POST["wish"]);

    $stmt->execute();

    header("Content-Type: application/json");
    echo '{"message":"ok"}';
    die();
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    $CLIENT_ERROR = true;
    ob_end_clean();
    include("./error.php");
    die();
}

// $dbh = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);



?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>get karácsony'd xddd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        .wrapper {
            max-width: 600px;
        }

        .myform {
            max-width: 450px;
            border: 6px groove;
            padding: 25px;
        }
    </style>
</head>

<body>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="errorModalLabel">A fene vigye el...</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Ez nem jött össze! Valamiért a manók nem tudták a kalapba venni a cetlidet. Sebaj, próbáld újra!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Ez nem szegheti kedvem!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="successModalLabel">Siker!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    A cetlid már biztos helyen van egy párhuzamos univerzumban a kalap legalján.<br> Ha még valami kívánság, információ eszedbe jut,
                    töltsd ki mégegyszer, és aki téged húz, mindegyiket megkapja.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Kiváló!</button>
                </div>
            </div>
        </div>
    </div>


    <div class="wrapper mx-auto mt-3">
        <div class="container">
            <h1 class="mt-4">Nicsak...</h1>
            <p class="mb-5">
                Azok ott... Csak nem... Újra visszatértek a karácsonyi manók!
                Immáron erősebbek, és többen vannak, mint valaha! Idén is felfedték magukat, hogy egy kis mókát űzzenek egy kalappal.
                Amint megérkeztél, az arcodba nyomtak egy kis cetlit, amin az alábbi mezők szerepelnek.
                Ha kitöltöd, beleteszik a feneketlen kalapjukba és <strong>november 13-án</strong> kihúzzák neked valaki más cetlijét.
                A szupertitkos karácsonyi feladatod, hogy lepd meg a cetli kitöltőjét! Cserébe valaki más feladata az lesz, hogy pont
                téged lepjen meg! Benne vagy?
            </p>
            <div class="myform mx-auto">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="email" placeholder="name@example.com">
                    <label for="email">E-mail címem</label>
                    <div class="invalid-feedback">
                        Nagy a Karácsony ereje, de nem tudja a fejedbe ültetni, hogy kit lepj meg. Adj meg egy olyan címet amin elérnek!
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" placeholder="Name">
                    <label for="name">Becsületes nevem</label>
                    <div class="invalid-feedback">
                        Psszt! Elég ha csak megsúgod a neved, de halkan!
                    </div>
                </div>
                <div class="form-floating">
                    <textarea class="form-control" id="wish" placeholder="Wish" style="height: 150px;"></textarea>
                    <label for="wish">Szívesen fogadok</label>
                    <div class="invalid-feedback">
                        Sok mindenre képesek, de gondolatokat olvasni <i>még</i> nem tudnak.
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-4 mb-3">
                <button class="btn btn-success" id="submitBtn" onclick="submit()">A kalapba vele!</button>
            </div>
            <div class="d-flex justify-content-center mb-5">
                <div class="spinner-border text-success" style="display: none;" id="loading" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="main.js"></script>
</body>

</html>
