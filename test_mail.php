<?php 

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$dsn = 'smtps://contact-marais%40borisdymak.fr:motdepasse@ssl0.ovh.net:465';
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('contact-marais@borisdymak.fr')
    ->to('contact-marais@borisdymak.fr')
    ->subject('Test Email')
    ->text('Ceci est un message de test.');

try {
    $mailer->send($email);
    echo "E-mail envoyé avec succès";
} catch (Exception $e) {
    echo "Échec de l'envoi de l'e-mail: " . $e->getMessage();
}
