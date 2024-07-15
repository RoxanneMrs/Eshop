<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Paramètres du serveur SMTP
    $mail->isSMTP();
    $mail->Host       = 'ssl0.ovh.net';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contact-marais@borisdymak.fr';
    $mail->Password   = 'motdepasse'; // Utilisez le mot de passe correct
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Utilisez PHPMailer::ENCRYPTION_STARTTLS si vous utilisez le port 587
    $mail->Port       = 465; // Utilisez 587 pour STARTTLS

    // Destinataires
    $mail->setFrom('contact-marais@borisdymak.fr', 'Mailer');
    $mail->addAddress('votre-adresse-email@test.com', 'Destinataire');

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'Ceci est un message de test.';

    $mail->send();
    echo 'E-mail envoyé avec succès';
} catch (Exception $e) {
    echo "Échec de l'envoi de l'e-mail. Erreur de PHPMailer: {$mail->ErrorInfo}";
}
?>
