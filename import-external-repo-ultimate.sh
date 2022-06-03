#!/bin/bash

RED="\e[31m"
GREEN="\e[32m"
YELLOW="\e[33m"
BLUE="\e[34m"
MAGENTA="\e[35m"
CYAN="\e[36m"
ENDCOLOR="\e[0m"

updateGitignore() {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Create/Update .gitignore${ENDCOLOR}"

    # Si le fichier .gitignore existe, on va lui ajouter la ligne pour exclure ce bash des commits
    if [ -f .gitignore ]
    then
        echo "${BLUE}Le fichier .gitignore existe déjà${ENDCOLOR}"
        if ! grep -Fxq import-external-repo-ultimate.sh .gitignore
        then
            echo "${BLUE}Le fichier .gitignore ne contient pas l'exclusion de import-external-repo-ultimate.sh : on le met à jour${ENDCOLOR}"
            echo "" >> .gitignore
            echo "import-external-repo-ultimate.sh" >> .gitignore
        fi
    # Sinon le créer et ajouter la ligne pour exclure ce bash des commits
    else
        echo "${BLUE}Le fichier .gitignore n'existe pas : on le crée${ENDCOLOR}"
        echo "import-external-repo-ultimate.sh" > .gitignore
    fi
}

stashCommandChoice () {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Vérification de l'existance de modifications${ENDCOLOR}"

    # Si il y a des changements dans le code
    if [ -n "$(git status --porcelain)" ]
    then
        # Changes
        echo "${BLUE}Il y a des modifiations non commitées${ENDCOLOR}"
        read -p "Voulez-vous les écraser ou les garder (E = Écraser / G = Garder / A = Arrêter ce script) ? " choice

        if [ "$choice" = "E" ] || [ "$choice" = "e" ]
        then
            echo "${BLUE}Choix : Écraser${ENDCOLOR}"
            echo "${MAGENTA}git add .${ENDCOLOR}"
            git add .
            echo "${MAGENTA}git stash${ENDCOLOR}"
            git stash
            stashCommand="drop"
        elif [ "$choice" = "G" ] || [ "$choice" = "g" ]
        then
            echo "${BLUE}Choix : Garder${ENDCOLOR}"
            echo "${MAGENTA}git add .${ENDCOLOR}"
            git add .
            echo "${MAGENTA}git stash${ENDCOLOR}"
            git stash
            stashCommand="apply"
        else
            echo "${BLUE}Choix : Arrêter ce script${ENDCOLOR}"
            echo "${BLUE}Vous devriez exécuter la commande \"git status\" afin de vérifier les modifications non commitées et ensuite annuler les modifications ou bien les commiter${ENDCOLOR}"
            exit 0
        fi
    else
        echo "${BLUE}Aucune modification en attente${ENDCOLOR}"
    fi
}

pickCode() {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Récupération du code du prof${ENDCOLOR}"

    # Create branch if not exists (improbable)
    echo "${MAGENTA}git checkout master${ENDCOLOR} : ${BLUE}Se place sur la branche master de votre dépôt étudiant${ENDCOLOR}"
    git checkout master

    # Clone in subdirectory
    echo "${MAGENTA}git clone $1 prof-master${ENDCOLOR} : ${BLUE}Clone temporairement le dépôt du prof en le mettant dans un répertoire prof-master${ENDCOLOR}"
    git clone $1 prof-master

    echo "${MAGENTA}cp -Rf prof-master/* .${ENDCOLOR} : ${BLUE}Copie le code du répertoire prof-master dans votre projet${ENDCOLOR}"
    cp -Rf prof-master/* .

    # Delete clone
    echo "${MAGENTA}rm -Rf prof-master .${ENDCOLOR} : ${BLUE}Supprime le répertoire prof-master (le dépôt du prof sur votre machine)${ENDCOLOR}"
    rm -Rf prof-master
}

commitAndPush () {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Commit et Push du code à jour${ENDCOLOR}"

    # Stage files
    echo "${MAGENTA}git add .${ENDCOLOR}"
    git add .

    # Commit
    # Get argument
    currentDate=`date +"%A %d %B %Y"`
    echo "${MAGENTA}git commit -m \"Importation depuis le master du prof : $currentDate\"${ENDCOLOR}"
    git commit -m "Importation depuis le master du prof : $currentDate"

    # Push branch to origin
    echo "${MAGENTA}git push -f${ENDCOLOR} : ${BLUE}Push en force mode (truc de bourin)${ENDCOLOR}"
    git push -f
}

finalStash() {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Stash clean${ENDCOLOR}"

    # Restore or delete Stash
    if [ "$1" = "apply" ]
    then
        echo "${MAGENTA}git stash apply${ENDCOLOR} : ${BLUE}Remet si possible les modification que tu as souhaité garder juste avant${ENDCOLOR}"
        git stash apply
        echo "${MAGENTA}git stash drop${ENDCOLOR} : ${BLUE}Supprime le stash qui vient d'être réappliquer (pas besoin de le conserver)${ENDCOLOR}"
        git stash drop
    elif [ "$1" = "drop" ]
    then
        echo "${MAGENTA}git stash drop${ENDCOLOR} : ${BLUE}Supprime le stash que tu as souhaité écraser juste avant${ENDCOLOR}"
        git stash drop
    fi
}

createNewBranch () {
    echo ""
    echo "${YELLOW}------------------------------------------------------------- Création nouvelle branche de travail${ENDCOLOR}"

    read -p "Vous avez besoin de cette base de code à jour pour un (atelier = 1 / cours = 2 / challenge = 3) ? " type

    if [ "${type}" = "1" ]
    then
        branch="atelier"
        whichEpisode
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
    read -p "En quel épisode sommes-nous ? " episodeNumber

    branch="$branch-e$episodeNumber"

    echo "${MAGENTA}git checkout -b ${branch}$episodeNumber${ENDCOLOR}"
    git checkout -b $branch
}

importExternalRepoMaster () {
    local stashCommand=""
    stashCommandChoice

    updateGitignore

    pickCode $1

    commitAndPush

    # Si on a une commande stash attendue, on va le réappliquer ou supprimé selon la réponsé choisi précédemment


    if [ ! -z "$stashCommand" ]
    then
        finalStash $stashCommand
    fi

    local branch="master"
    createNewBranch

    echo ""
    echo "${YELLOW}------------------------------------------------------------- Fin du script${ENDCOLOR}"
    echo "${GREEN}Récap :${ENDCOLOR}"
    echo "${GREEN}- Votre branche master a été mise à jour avec le code du prof"
    echo "${GREEN}- Vous êtes sur la branche $branch de votre projet${ENDCOLOR}"
    if [ "$branch" = "master" ]
    then
        echo "${GREEN}- Si vous voulez passer sur un atelier/cours/challenge, vous devez créer la branche correspondante${ENDCOLOR}"
    else
        echo "${GREEN}- Cette branche $branch a été créée depuis votre branche master à jour : elle est donc également à jour avec le code du prof${ENDCOLOR}"
        echo "${GREEN}- Vous êtes fin prêt à coder la suite${ENDCOLOR}"
    fi

    echo ""
}


importExternalRepoMaster $1