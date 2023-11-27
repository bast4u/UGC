document.addEventListener('DOMContentLoaded', (e) => {

    const url = window.location.href
    const route = url.substring(url.length - 11);

    /**
     * Active la gestion du click sur la page nouvelle connection
     */
    if(document.getElementById('btn-new-connection') != undefined){

        document.getElementById('btn-new-connection').addEventListener('click', (e) => {
            // annule le comportement normal du lien
            e.preventDefault();

            // lance l'url cible dans la modale
            fetch('/modals/new_connection')
            .then(function(response) {
                return response.text();
            })
            .then(function(html) {
                document.querySelector('#modal .modal-body').innerHTML = html;
                addEventOnButton();
            });

            // affiche la modal
            let modal = new bootstrap.Modal('#modal', []);
            modal.show();
        });

    } 

    /**
     * Lance la modal accueil sur la page mon-compte
     */
    if(route == 'mon-compte/'){

        // demande si c'est la première connection
        fetch('/user/firstTime')
        .then(function(response) {
            return response.text();
        })
        .then(function(json) {
            json = JSON.parse(json);
            if (json == true) {
                // lance l'url cible dans la modale
                fetch('/modals/accueil')
                .then(function(response) {
                    return response.text();
                })
                .then(function(html) {
                    document.querySelector('#modal .modal-content').innerHTML = html;
                    addEventOnButton();

                });
                let modal = new bootstrap.Modal('#modal', []);
                modal.show();
            }
        });       
    }

    /**
     * Ajoute un evenement click sur le bouton à l'intérieur d'un formulaire
     */
    function addEventOnButton(){
        const button = document.querySelector('#modal-form button#submit');

        if(!button) return;

        button.addEventListener('click', (e) => {
                // annule le comportement normal du lien
                e.preventDefault();

                // récupère la destination du fetch
                const destination = document.getElementById('step').value;
                const formData = new FormData(document.getElementById("modal-form"))
                console.log(formData);
                fetch(destination, {
                    body: formData,
                    method: "post"
                })
                .then(function(response) {
                    return response.text();
                })
                .then(function(html) {

                    // charge le nouveau html dans la modal
                    document.querySelector('#modal .modal-content').innerHTML = html;
                    
                    // vérifie si il faut activer un js spécial en fonction de la modale sur laquelle on se trouve
                    if (document.getElementById('modal-choose-location-cinema')) {
                        initCinemaAutocompletion();
                    }

                    if (document.getElementById('modal-choose-actor-cinema')) {
                        initActorAutocompletion();
                    }

                    if (document.getElementById('modal-choose-director-cinema')) {
                        initDirectorAutocompletion();
                        reinitInputActor();
                    }
                   
                    if (document.getElementById('modal-final')) {
                        initFinalModal();
                    }                   
                    
                    // initialise le bouton submit (si il y'en a un)
                    addEventOnButton();
                });
        });
    }

    /**
     * Initialise l'auto completion pour le choix du cinema
     * 
     * @returns 
     */
    function initCinemaAutocompletion() {

        // si la modal n'est pas la bonne quitter
        if (!document.getElementById('modal-choose-location-cinema'))
            return;

        // ajout de l'evenement keyup sur le champ de saisie
        document.getElementById('modal-choose-location-cinema').addEventListener('keyup', (e) => {

            let search = document.getElementById('modal-choose-location-cinema').value.trim().toLowerCase();
            fetch('/cinemas/' + search)
            .then(function(response) {
                return response.json();
            })
            .then(function(json) {  
                refresh(json);
            });
        });
    }

    /**
     * Recharge la liste des suggestions de l'autocomplétion à partir du json
     * 
     * @param {*} suggestions 
     */
    function refresh (suggestions) {

        // vide la div des suggestions
        document.getElementById('suggestions').innerHTML = '';

        // pour chaque suggestions reçues en json
        suggestions.forEach( suggestion => {

            // crée le titre
            let titre = document.createElement('h5');
            titre.innerHTML = suggestion['name'];
            titre.classList.add('maClasse');

            // crée le nom de la ville
            let city = document.createElement('p');
            city.innerHTML = suggestion['city'];
            city.classList.add('maClasse');

            // crée un autre élément
            let image = document.createElement('div');
            image.innerHTML = `<img class="cinema" src="${suggestion['image']}"></img></br>`;

            // crée un autre élément
            let box = document.createElement('checkbox');
            box.innerHTML = `<input type="checkbox" class="checkboxCinema form-check-input" name ="locations[]" id ="locations" value="${suggestion['name']}">`;

            // crée la div générale pour cette suggestion
            let el = document.createElement('div');
            el.classList.add('suggestionClass');

            // ajoute les éléments dans l'ordre
            el.appendChild(box);
            el.appendChild(titre);
            el.appendChild(city);
            el.appendChild(image);
            

            // ajout de la div de la suggestion dans la div des suggestions
            
            document.getElementById('suggestions').appendChild(el);
        });

       // scanCheckBoxCinema();
    }

    // let imgCinema = "";
    // let nameCinema = "";
    // let cityCinema = "";

    // let tabCinema = "";

    // function scanCheckBoxCinema(){
    //     let checkboxCinema = document.querySelectorAll('.checkboxCinema');
    //     checkboxCinema.forEach(element => {
    //         element.addEventListener('click', (e) => {
    //             imgCinema += e.currentTarget.parentNode.nextElementSibling.nextElementSibling.nextElementSibling.innerHTML;
    //             nameCinema += e.currentTarget.parentNode.nextElementSibling.innerHTML;
    //             cityCinema += e.currentTarget.parentNode.nextElementSibling.nextElementSibling.innerHTML;

    //             if(tabCinema != ""){
    //                 tabCinema += "||";
    //             }
    //             tabCinema += nameCinema;
    //             document.getElementById('cinemaTab').value = tabCinema;
                
                // let content = `
                // <article class="col-6 col-md-4 col-lg-4">
                // <button class="coeur">
                // <img src="assets/icones/favori-full.svg" alt="favori full">
                // </button>
                // <h3><img src=`+imgCinema+`</img></br>`+nameCinema+` - <span class="ville-cine">`+cityCinema+`</span></h3>
                // </article>
                // `;
                
    //             document.getElementById('cardSuggestion').innerHTML = content;
    //         })
    //     });
    // };
    
    /**
     * Initialisation de l'autocompletion sur le choix d'acteur
     */
    function initActorAutocompletion() { 

        // ajout de l'event "keyup" sur l'input des acteurs
        document.getElementById('modal-choose-actor-cinema').addEventListener('keyup', (e) => { 

            // interrogation de notre API maison pour récupérer la liste des acteurs
            fetch('/api/actors/' + e.target.value + '/5').then(function (response) {
                return response.text();
            })
            .then(function (json) {
                const datas = JSON.parse(json);
                console.log(datas);
                
                const destinationEl = document.querySelector('#actors-container .suggestions-container');
                destinationEl.innerHTML = "";
                console.log(destinationEl);
                datas.forEach(suggestion => {

                    let box = document.createElement('checkbox');
                    box.innerHTML = `<input type="checkbox" class="checkboxActor form-check-input" id ="actors" value="${suggestion}">`

        
                    let p = document.createElement('span');
                    p.innerHTML = '' + suggestion;
                    
                    let div = document.createElement('div');
                    div.appendChild(box);
                    div.appendChild(p);
        
                    destinationEl.appendChild(div);

                    
                });
                scanCheckBoxActor();
            })
        })
    }

    let tabActor = "";
    /**
     * Enregistre en base de données les acteurs sélectionnés
     */
    function scanCheckBoxActor(){
        let checkboxActors = document.querySelectorAll('.checkboxActor');
        checkboxActors.forEach(element => {
            element.addEventListener('click', (e) => {
                if(tabActor != ""){
                    tabActor += "||";
                }
                tabActor += e.currentTarget.value;
                document.getElementById('actorsTab').value = tabActor;

                //permet l'affichage de l'acteur sélectionné dans une 'card'
                let content = document.getElementById('actor-tag').innerHTML;
                content += `<div class="col-12 col-md-3"><button class="suppression-tag-modale">`+e.currentTarget.value+`</button></div>`;

                document.getElementById('actor-tag').innerHTML = content;
            })
        });
    }

    /**
     * Initialisation de l'autocompletion sur le choix du réalisateur
     */
    function initDirectorAutocompletion() { 

        // ajout de l'event "keyup" sur l'input des directeurs
        document.getElementById('modal-choose-director-cinema').addEventListener('keyup', (e) => { 

            // interrogation de notre API maison pour récupérer la liste des directeurs
            fetch('/api/actors/' + e.target.value + '/5').then(function (response) {
                return response.text();
            })
            .then(function (json) {
                const datas = JSON.parse(json);
                console.log(datas);
                
                const destinationEl = document.querySelector('#directors-container .suggestions-container');
                destinationEl.innerHTML = "";
                console.log(destinationEl);
                datas.forEach(suggestion => {

                    let box = document.createElement('checkbox');
                    box.innerHTML = `<input type="checkbox" class="checkboxDirector form-check-input" id ="directors" value="${suggestion}">`
        
                    let p = document.createElement('span');
                    p.innerHTML = suggestion;
                    
                    let div = document.createElement('div');
                    div.appendChild(box);
                    div.appendChild(p);
        
                    destinationEl.appendChild(div);
                });
                 scanCheckBoxDirector();
                
            })
        })
    }

    
    let tabDirector = "";
    /**
     * enregistre en base de données les réalisateurs sélectionnés
    */
   function scanCheckBoxDirector(){
       let checkboxDirectors = document.querySelectorAll('.checkboxDirector');
       checkboxDirectors.forEach(element => {
           element.addEventListener('click', (e) => {
               if(tabDirector != ""){
                   tabDirector += "||";          
                }
                tabDirector += e.currentTarget.value;
                document.getElementById('directorTab').value = tabDirector;

                 //permet l'affichage du réalisateur sélectionné dans une 'card'                
                let content = document.getElementById('director-tag').innerHTML;
                content += `<div class="col-12 col-md-3"><button class="suppression-tag-modale">`+e.currentTarget.value+`</button></div>`;
                
                document.getElementById('director-tag').innerHTML = content;
            })
        });
    }
    
    /**
     * permet de faire disparaitre les éléments tapés dans un champ de recherche quand on passe à un autre champ
    */
   function reinitInputActor(){
       let listActor = document.querySelector('#actors-container .suggestions-container');
       let listDirector = document.querySelector('#directors-container .suggestions-container');
       document.getElementById('modal-choose-actor-cinema').addEventListener('focus', () => {
           listActor.innerHTML = '';
           listDirector.innerHTML = '';
        })
        document.getElementById('modal-choose-director-cinema').addEventListener('focus', () => {
            listActor.innerHTML = '';
            listDirector.innerHTML = '';
        })
        document.querySelectorAll('.radioInput').forEach((element) => {
            element.addEventListener('change', () => {
                listActor.innerHTML = '';
                listDirector.innerHTML = '';
            })
        })    
    }

    // const el = document.getElementById('test-auto');
    // if (el != null) {

    //     document.getElementById('test-auto').addEventListener('click', (e) => { // annule le comportement normal du lien
    //         e.preventDefault();
    //         console.log(e);
    //         // lance l'url cible dans la modale
    //         fetch('/modals/choose_actors').then(function (response) {
    //             return response.text();
    //         }).then(function (html) {
    //             document.querySelector('#modal .modal-content').innerHTML = html;
    //             // affiche la modal
    //             let modal = new bootstrap.Modal('#modal', []);
    //             modal.show();
            
    //             // *************** a copier dans modals.js
    //             if (document.getElementById('modal-choose-actor-cinema')) {
    //                 initActorAutocompletion();
    //             }
    //             // ***************
    //         });

    //     });
    // }
    // FIN DOMContentLoaded

    

    /**
     * déclenche le compte à rebours pour la redirection vers la page de connexion
     */
    function initFinalModal () {
       
        let chiffre = 5;
        // Affiche dans le navigateur les nombres à partir de 5
        let idInterval = setInterval (function() {
            //console.log('time ! ' + chiffre);
            chiffre--;
            document.getElementById('valueSecond').innerHTML = chiffre;
            
            if (chiffre == 0) {
                clearInterval(idInterval);
                document.location.replace('/login');
            }
        }, 1000);
    
        
        //setTimeout(clearInterval, 1000, idInterval);
    
    }
});


