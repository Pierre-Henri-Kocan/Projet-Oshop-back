<?php

namespace App\Controllers;

use App\Models\AppUser;

class AppUserController extends CoreController
{
    public function list()
    {
        // Est-ce que t'as le droit d'être là ?
        $this->checkAuthorization([self::ROLE_ADMIN]);

        $users = AppUser::findAll();

        $this->show('app-user/list', [
            'users' => $users
        ]);
    }

    public function form(?int $id = null)
    {
        $this->checkAuthorization([self::ROLE_ADMIN]);

        if ($id !== null) {
            $user = AppUser::find($id);

            if ($user === false) {
                $this->show404();
            }
        }

        $this->displayRecordForm(
            $id !== null ? $user : null
        );
    }

    public function record(?int $id = null)
    {
        $this->checkAuthorization([self::ROLE_ADMIN]);

        global $router;

        /*
            $_POST["name"] ?? ''
                IDENTIQUE À
            isset($_POST["name"]) ? $_POST["name"] : ''
        */
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $lastname = trim(htmlspecialchars($_POST["lastname"] ?? ''));
        $firstname = trim(htmlspecialchars($_POST["firstname"] ?? ''));
        $role = trim(htmlspecialchars($_POST["role"] ?? ''));
        // $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
        $status = isset($_POST["status"]) ? (int)$_POST["status"] : 0;
        // (int) permet de «caster» la valeur à sa droite. Quand aucun statut n'est sélectionné
        // on obtient une string vide "". Avec (int) devant, on obtient l'entier 0
        // On doit faire ce cast pour s'assurer qu'on envoie un entier en argument de statStatus() plus bas
        // On vérifie les valeurs du formulaire encore plus bas, donc c'est pas grave si on met 0 dans setStatus()
        // alors que ce n'est pas une valeur attendue à cet endroit

        $errors = self::checkInputs([
            'email' => $email,
            'password' => $password,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'role' => $role,
            'status' => $status,
        ]);

        // Il nous faut unr instance de AppUser
        $user = $id === null ? new AppUser() : AppUser::find($id);
        $user->setEmail($email)
            ->setPassword(password_hash($password, PASSWORD_DEFAULT))
            ->setLastname($lastname)
            ->setFirstname($firstname)
            ->setRole($role)
            ->setStatus($status);

        // Si j'ai aucune erreur
        if (empty($errors)) {
            // On enregistre en BDD
            if ($user->save()) {
                if ($id === null) {
                    // Si la sauvegarde a fonctionné, on redirige vers la liste des catégories.
                    header('Location: '. $router->generate('AppUser-list'));
                }
                else {
                    // Si la sauvegarde a fonctionné, on redirige vers le formulaire d'édition en mode GET
                    header('Location: '. $router->generate('AppUser-edit', ['id' => $user->getId()]));
                }
            }
            else {
                $errors[] = "La sauvegarde a échoué";
            }
        }

        // S'il y a au moins une erreur dans les données ou à l'enregistrement
        if (!empty($errors)) {
            // On réaffiche le formulaire, mais pré-rempli avec les (mauvaises)
            // données proposées par l'utilisateur.
            // On transmet aussi le tableau d'erreurs, pour avertir l'utilisateur.

            $this->displayRecordForm($user, $errors);
        }
    }

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

    /**
     * @param AppUser|null $user Si c'est pas une objet AppUser, ça peut être null.
     *                           Par défaut, si on donne pas de valeur, ce sera null.
     * @param array        $errors
     */
    private function displayRecordForm(?AppUser $user = null, array $errors = [])
    {
        $this->show('app-user/record', [
            'user' => $user ?? new AppUser(),
            'errors' => $errors,
        ]);
    }

    /**
     * @param array $inputs
     *
     * @return string[]
     */
    private static function checkInputs(array $inputs): array
    {
        // On va lister toutes les erreurs qu'on va pouvoir rencontrer
        $errors = [];

        // filter_var fonctionne presque exactement comme filter_input, sauf qu'on
        // lui passe directement la valeur à vérifier au lieu de lui dire de la
        // chercher dans $_POST/$_GET/etc
        if (empty($inputs['email']) || !filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas correcte.";
        }

        // On peut ajouter des vérifications pour chacun des champs
        // password : vérifier qu'il est non vide, peut-être même vérifier certains critères. ex: 8 caractères minimum, présence de majuscules, minuscules, chiffres et caractères spéciaux
        // firstname et lastname : S'assurer qu'on a au moins 1 caractère dans les strings
        if ($inputs['status'] !== 1 && $inputs['status'] !== 2) {
            $errors[] = 'Vous devez impérativement sélectionner un statut Actif ou Inactif.';
        }

        if ($inputs['role'] !== self::ROLE_ADMIN && $inputs['role'] !== self::ROLE_CATALOG_MANAGER) {
            $errors[] = 'Vous devez impérativement sélectionner un rôle.';
        }

        // Password :
        //      - Au moins 8 caractères
        //      - Au moins une minuscule
        //      - Au moins une majuscule
        //      - Au moins un chiffre
        //      - Au moins un caractère spécial
        if (strlen($inputs['password']) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if (!preg_match('/^(?=.*[a-z])/', $inputs['password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins 1 minuscule.';
        }

        if (!preg_match('/^(?=.*[A-Z])/', $inputs['password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins 1 majuscule.';
        }

        if (!preg_match('/^(?=.*\d)/', $inputs['password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins 1 chiffre.';
        }

        if (!preg_match('/^(?=.*[_\W])/', $inputs['password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins 1 caractère spécial';
        }

        return $errors;
    }
}
