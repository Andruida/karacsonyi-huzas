<?php

die("INACTIVE");


ob_start();

function exception_handler(Throwable $e)
{
    file_put_contents('php://stdout', "Unhandled exception:\n" . $e->getMessage() . "\nLine: " . $e->getLine() . "\n\n");
    ob_end_clean();
    http_response_code(500);
    #include("./error.php");
    die("ERROR");
}
set_exception_handler('exception_handler');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

require_once("./config.php");

$dbh = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);

$dbh->exec("CALL draw();");

$stmt = $dbh->query("SELECT santa.id as s_id, santa.name as s_name,
                            santa.nickname as s_nickname, santa.email as s_email,
                            rcp.name as r_name, w.wish 
                    FROM link
                    JOIN users santa ON santa.id = link.secret_santa
                    JOIN users rcp ON rcp.id = link.recipient
                    JOIN wishes w ON w.user_id = link.recipient;");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($stmt->rowCount() == 0 || $result === false) {
    die("Noone to send mail to");
}

$santas = [];

foreach ($result as $row) {
    if (isset($santas[$row["s_id"]])) {
        $santas[$row["s_id"]]["wishes"][] = $row["wish"];
    } else {
        $santas[$row["s_id"]] = [
            "name" => (!empty($row["s_nickname"])) ? $row["s_nickname"] : $row["s_name"],
            "email" => $row["s_email"],
            "r_name" => $row["r_name"],
            "wishes" => [$row["wish"]]
        ];
    }
}


$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->SMTPAuth = true;
$mail->SMTPKeepAlive = true; //SMTP connection will not close after each email sent, reduces SMTP overhead
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Username = SMTP_USER;
$mail->Password = SMTP_PASS;
$mail->setFrom(SMTP_ADDR, SMTP_NAME);
$mail->addReplyTo(SMTP_ADDR, SMTP_NAME);

$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
$mail->isHTML(true);
$mail->Subject = 'Karácsonyi manók üzenete';

foreach ($santas as $santa) {
    try {
        $mail->addAddress($santa['email'], $santa['name']);
    } catch (Exception $e) {
        echo 'Invalid address skipped: ' . htmlspecialchars($santa['email']) . '<br>';
        continue;
    }

    $wishes = "";
    foreach($santa["wishes"] as $w) {
        $wishes .= "<p>".nl2br(htmlspecialchars($w))."</p>\n";
    }

    $mail->msgHTML(
        "<p>Kedves ".htmlspecialchars($santa['name'])."!</p>\n".
        "<p>A karácsonyi manók meglátogattak és itt hagytak a küszöbödön egy cetlit egy névvel és némi szöveggel:</p>\n".
        "<p>Név: <strong>".htmlspecialchars($santa["r_name"])."</strong></p>".
        $wishes.
        "<p>Boldog karácsonyt!<br>A karácsonyi főmanó</p>"
    );

    try {
        $mail->send();
    } catch (Exception $e) {
        echo 'Mailer Error (' . htmlspecialchars($santa['email']) . ') ' . $mail->ErrorInfo . '<br>';
        
        $mail->getSMTPInstance()->reset();
    }
    
    $mail->clearAddresses();
}


die("Done.");

?>