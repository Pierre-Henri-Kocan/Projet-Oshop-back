#!/bin/bash

RED="\e[31m"
GREEN="\e[32m"
YELLOW="\e[33m"
BLUE="\e[34m"
MAGENTA="\e[35m"
CYAN="\e[36m"
ENDCOLOR="\e[0m"

stashCommandChoice () {
    echo ""
    echo "${YELLOW}----------------------------------------------------- Vérification de l'existance de modifications non commitées${ENDCOLOR}"
    echo ""

    # Si il y a des changements dans le code
    if [ -n "$(git status --porcelain)" ]
    then
        # Changes
        echo "${BLUE}Il y a des modifiations non commitées${ENDCOLOR}"
        echo ""

        echo "${CYAN}Que veux-tu faire ?"
        echo "  - Écraser tes modifications (elles seront perdu à jamais) → tapes \"1\" puis appuies sur \"Entrée\""
        echo "  - Garder tes modifications                                → tapes \"2\" puis appuies sur \"Entrée\""
        echo "  - Arrêter ce script                                       → appuies juste sur \"Entrée\""
        read -p "Ton choix : " choice
        echo "${ENDCOLOR}"

        if [ "$choice" = "1" ]
        then
            echo "${BLUE}Tu as choisi d'écraser tes modifications${ENDCOLOR}"
            echo "${MAGENTA}  - git add .${ENDCOLOR} → ${BLUE}Valide les fichiers en vue de la création du stash${ENDCOLOR}"
            git add .
            echo "${MAGENTA}  - git stash${ENDCOLOR} → ${BLUE}Crée le stash pour faciliter la suppression des modifications non commitées qui ont été trouvées${ENDCOLOR}"
            git stash
            echo "${MAGENTA}  - git stash drop${ENDCOLOR} → ${BLUE}Supprime le stash (et par conséquent le code) que tu as souhaité écraser juste avant${ENDCOLOR}"
            git stash drop
        elif [ "$choice" = "2" ]
        then
            echo "${BLUE}Tu as choisi de garder tes modifications${ENDCOLOR}"
            echo "${MAGENTA}git add .${ENDCOLOR} → ${BLUE}Valide les fichiers en vue de la création du stash${ENDCOLOR}"
            git add .
            echo "${MAGENTA}git stash${ENDCOLOR} → ${BLUE}Crée le stash pour pouvoir réappliquer plus tard les modifications${ENDCOLOR}"
            git stash
            needStashApply=true
        else
            echo "${BLUE}Tu as choisi d'arrêter ce script${ENDCOLOR}"
            echo "${BLUE}Vous devriez exécuter la commande \"git status\" afin de vérifier les modifications non commitées et ensuite annuler les modifications ou bien les commiter${ENDCOLOR}"
            exit 0
        fi
    else
        echo "${BLUE}Aucune modification en attente${ENDCOLOR}"
    fi
}

copyTeacherMasterCode() {
    echo ""
    echo ""
    echo "${YELLOW}----------------------------------------------------- Récupération du code du prof${ENDCOLOR}"
    echo ""

    echo "${MAGENTA}git checkout master${ENDCOLOR} → ${BLUE}Se place sur la branche master de ton dépôt étudiant${ENDCOLOR}"
    git checkout master

    echo "${MAGENTA}git clone $1 prof-master${ENDCOLOR} → ${BLUE}Clone temporairement le dépôt du prof en le mettant dans un répertoire prof-master${ENDCOLOR}"
    git clone $1 prof-master

    echo "${MAGENTA}cp -Rf prof-master/* .${ENDCOLOR} → ${BLUE}Copie le code du répertoire prof-master dans ton projet${ENDCOLOR}"
    cp -Rf prof-master/* .

    # Delete clone
    echo "${MAGENTA}rm -Rf prof-master .${ENDCOLOR} → ${BLUE}Supprime le répertoire prof-master${ENDCOLOR}"
    rm -Rf prof-master
}

commitAndPushOnStudentMaster () {
    echo ""
    echo ""
    echo "${YELLOW}----------------------------------------------------- Mise à jour de ta branche \"master\"${ENDCOLOR}"
    echo ""

    # Stage files
    echo "${MAGENTA}git add .${ENDCOLOR}"
    git add .

    # Commit
    # Get argument
    currentDate=`date +"%Y-%m-%d"`
    echo "${MAGENTA}git commit -m \"[BASH IERU $currentDate] Importation depuis le master du prof\"${ENDCOLOR}"
    git commit -m "[BASH IERU $currentDate] Importation depuis le master du prof"

    # Push branch to origin
    echo "${MAGENTA}git push -f${ENDCOLOR} : ${BLUE}Push en force mode (truc de bourrin)${ENDCOLOR}"
    git push -f
}

applyStash() {
    echo ""
    echo ""
    echo "${YELLOW}----------------------------------------------------- Réapplication du stash conservé${ENDCOLOR}"
    echo ""

    # Restore or delete Stash
    if [ "$1" = true ]
    then
        echo "${MAGENTA}git stash apply${ENDCOLOR} : ${BLUE}Réapplique si possible les modifications que tu as souhaité garder juste avant${ENDCOLOR}"
        git stash apply
        echo "${MAGENTA}git stash drop${ENDCOLOR} : ${BLUE}Supprime le stash qui vient d'être réappliquer (pas besoin de le conserver)${ENDCOLOR}"
        git stash drop
    else
        echo "${BLUE}Rien n'a été stashé, tu n'avais pas de modifications en cours au moment de lancer ce script : Pas de stash à réappliquer${ENDCOLOR}"
    fi
}

createNewBranch () {
    echo ""
    echo ""
    echo "${YELLOW}----------------------------------------------------- Création nouvelle branche de travail${ENDCOLOR}"
    echo ""


    echo "${CYAN}Tu veux créer une nouvelle branche pour un :"
    echo "  - atelier                            → tapes \"1\" puis appuies sur \"Entrée\""
    echo "  - cours                              → tapes \"2\" puis appuies sur \"Entrée\""
    echo "  - challenge                          → tapes \"3\" puis appuies sur \"Entrée\""
    echo "  - non merci, pas de nouvelle branche → appuies juste sur \"Entrée\""
    read -p "Ton choix : " type
    echo "${ENDCOLOR}"

    if [ "${type}" = "1" ]
    then
        branch="atelier"
        whichEpisode "atelier"
    elif [ "${type}" = "2" ]
    then
        branch="cours"
        whichEpisode
    elif [ "${type}" = "3" ]
    then
        branch="challenge"
        whichEpisode
    fi
}

whichEpisode () {
    read -p "$(echo ${CYAN})En quel épisode sommes-nous ? " episodeNumber
    echo "${ENDCOLOR}"

    branch="$branch-e$episodeNumber"

    echo "${MAGENTA}git checkout -b ${branch}${ENDCOLOR} : ${BLUE}Crée la nouvelle branche et se place dessus${ENDCOLOR}"
    git checkout -b $branch
}

recapitulation () {
    echo ""
    echo ""
    echo "${YELLOW}----------------------------------------------------- Fin du script${ENDCOLOR}"
    echo ""
    echo "${GREEN}Récap :${ENDCOLOR}"
    echo "${GREEN}- Votre branche master a été mise à jour avec le code du prof"

    if [ "$1" = "master" ]
    then
        echo "${GREEN}- Vous êtes sur la branche $1 de votre projet${ENDCOLOR}"
        echo "${GREEN}- Si vous voulez passer sur un atelier/cours/challenge, vous devez créer la branche correspondante${ENDCOLOR}"
    else
        echo "${GREEN}- La branche $1 a été créée sur votre projet à partir de votre branche master à jour : elle est donc également à jour avec le code du prof${ENDCOLOR}"
        echo "${GREEN}- Vous avez été placé sur cette branche $1${ENDCOLOR}"
        echo "${GREEN}- Vous êtes fin prêt à coder la suite${ENDCOLOR}"
    fi

    echo ""
}

# Lance tout le process de récupération du code du prof et de la création
# de la nouvelle branche de travail
launch () {
    # Si on trouve des modifications, on va demander ce que l'utilisateur veut faire.
    # Si il décide de les conserver, elles seront réappliquée si possible, après
    # qu'on ait mis le master à jour
    local needStashApply=false
    stashCommandChoice

    # Copie la branche master du code du prof
    copyTeacherMasterCode $1

    # Met à jour la branche master de l'apprenant sur son dépôt
    commitAndPushOnStudentMaster

    # Si l'utilisateur avait souhaité garder ses modifications non commitées,
    # alors elles sont réappliquer
    if [ "$needStashApply" = true ]
    then
        applyStash $needStashApply
    fi

    # Crée une nouvelle branche pour la suite (atelier/cours/challenge)
    local branch="master"
    createNewBranch

    # Petit récap des familles
    recapitulation $branch
}


launch $1