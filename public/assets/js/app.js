




//Obtenir le modal
const modal = document.querySelector('#myModal');
//Cibler le boutton qui ouvre le modal
//Lorsque l'utilisateur clique sur le boutton supprimer,  le modal s'ouvre
let myBtn = document.querySelectorAll('#myBtn');
myBtn.forEach(element => {
    element.addEventListener('click', () => {
        let name = '#'+ element.getAttribute('model') + '_delete_id';
        let data = 'data-' + element.getAttribute('model') + '_id';
        //je dois cibler le champ type hidden qui contient le (name=ingredient_id)
        document.querySelector(name).value = element.getAttribute(data);
        modal.style.display = 'block';
    })
});

//Obtenir le span qui ferme le modal
//Lorsque l'utilisateur clique sur <span> (x), ferme le modal
const close = document.querySelector('.close_modal_ingredient_delete');
close.addEventListener('click', () => {
    modal.style.display = 'none';

});

//Lorsque l'utilisateur click sur le boutton fermer(btn_cancel)
const cancelBtn = document.querySelector('.btn_cancel');
cancelBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

//Lorsque l'utilisateur clique n'importe où en dehors du modal, ce dernier se ferme
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});