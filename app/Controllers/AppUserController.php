<?php

namespace App\Controllers;

use App\Models\AppUser;

class AppUserController extends CoreController
{
    public function login()
    {
        $this->displayLoginForm();
    }

    public function loginPost()
    {
        global $router;

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Requete pour retrouver l'user correspondant à l'email
        $user = AppUser::findByEmail($email);

        // Nos mot de passe en DB on été hashé par password_hash()
        if ($user && password_verify($password, $user->getPassword())) {
            // Comme les identifiants entrés sont les bons, on ajoute des informations dans $_SESSION
            $_SESSION['connectedUser'] = $user;

            header('Location: ' . $router->generate('Main-home'));
        }
        else {
            $this->displayLoginForm(
                (new AppUser())->setEmail($email)->setPassword($password),
                ["Identifiants invalides"]
            );
        }
    }

    public function logout()
    {
        global $router;

        unset($_SESSION['connectedUser']);

        header('Location: ' . $router->generate('AppUser-login'));
    }

    private function displayLoginForm(?AppUser $user = null, array $errors = [])
    {
        $this->show('app-user/login', [
            'user'   => $user ?? new AppUser(),
            'errors' => $errors,
        ]);
    }
}
