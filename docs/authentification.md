# Authentification

## Qu'est ce qu'on doit faire ?

* Faire une page de connexion

  * Formulaire avec 2 inputs (email et password)
  * Envoyé en POST
* Vérification user valide

  * Récupéré en DB si on a quelque chose qui correspond au couple email + password
  * Si Vérification OK
    * Stocker l'état "connecté" quelque part → on stock l'objet AppUser en entier en session
  * Sinon
    * Remplir un tableau d'erreur à transmettre à la View