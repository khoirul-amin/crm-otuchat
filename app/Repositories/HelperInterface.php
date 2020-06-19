<?php namespace App\Repositories;

interface HelperInterface
{
    // Send Email
    public function sendEmail($to, $subject, $view, $param);


    // Cek Key Authenticator
    public function cekKeyAuthenticator($key);


    // Cek Permission
    public function cekUserPermission($id_permission);


    // Log Action
    public function logAction($id_activity_detail, $to, $desc);
}
