/*Fonction qui permet de supprimmer les options déjà selectionner*/
function updateOption(element){
    const parentTr = document.getElementById("row-choix");
    const valueSelected = element.value;
    const idSelected = element.id;

    if (element.value == "default") return;

    for (var i = 0; i < parentTr.childElementCount; i++) {
      if (idSelected != "c"+i) {
        let a = parentTr.children[i].children;
        let select = a.namedItem("c"+i);

        /*Find the index of the option to remove*/
        let optionIndex = -1;
        for (var j = 0; j < select.options.length; j++) {
          if (select.options[j].value == valueSelected) {
            optionIndex = j;
          }
        }

        if (optionIndex == -1 )
          console.log("error");
        else
          select.remove(optionIndex);
      }
    }
}
