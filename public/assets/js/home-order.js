const form = document.querySelector('#home-order-form');

form.addEventListener('submit', handleCheckOrders);

/**
 *
 * @param {Event} event
 */
function handleCheckOrders(event)
{
    // On bloque la soumission du formulaire
    event.preventDefault();

    const selects = document.querySelectorAll('#home-order-form select[name="emplacement[]"]');

    let list = [];
    for (const select of selects) {
        // Identifier l'option qui est sélectionné
        // On récupère la valeur de l'option
        const selectedValue = select.options[select.selectedIndex].value;

        // On l'ajoute à notre liste de valeur
        if (selectedValue !== "") {
            list.push(selectedValue);
        }
    }

    // On dédoublonne la liste (si on a moins de 5 éléments, c'est qu'on en a en double)
    list = [...new Set(list)];

    if (list.length !== 5) {
        alert("Il faut choisir très exactement 5 catégories différentes");
    }
    else {
        form.submit();
    }
}