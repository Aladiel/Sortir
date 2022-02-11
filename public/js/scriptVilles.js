$(document).ready(function() {
    const apiCodePostal = 'https://geo.api.gouv.fr/communes?codePostal=';
    const apiAdresses = 'https://api-adresse.data.gouv.fr/search/'
    // 'https://api-adresse.data.gouv.fr/search/?q=10%20rue%20de%20gaulle%2057356&type=housenumber&autocomplete=1'
    const apiURLVilles = 'https://geo.api.gouv.fr/communes?nom=';
    const apiURLVilles2 = '&fields=departement&boost=population&limit=5'
    const format = '&format=json';

    let zipcode = $('#sortie_codePostal');
    let city = $('#sortie_ville');
    let rue = $('#sortie_rue_list');
    let latitude = $('#sortie_latitude');
    let longitude = $('#sortie_longitude');
    let errorMessage = $('#error-message');


    $(zipcode).on('blur', function () {
        let code = $(this).val();
        let urlVille = apiCodePostal+code+format;
        //let url = apiURLVilles+code+apiURLVilles2+format;
        //console.log(url);

        fetch(urlVille, {method:'get'}).then(response => response.json()).then(results => {
            //console.log(results);
            $(city).find('option').remove();
            if (results.length) {
                $(errorMessage).text('').hide();
                $.each(results, function(key,value) {
                    //console.log(value);
                    console.log(value.nom);
                    $(city).append('<option value="'+value.nom+'">'+value.nom+'</option>');
                });
            }
            else {
                if($(zipcode).val()) {
                    console.log('Erreur de code postal');
                    $(errorMessage).text('Aucune commune avec ce code postal.').show();
                }
                else {
                    $(errorMessage).text('').hide();
                }
            }

           // ---------------------------------------------------------------------------------------------------

            let oRue = '';
            oRue = document.getElementById("sortie_ville");
            oRue.addEventListener("keyup", searchAdresses);

            function searchAdresses() {
                let currentInput = rue.value;
                let urlAdresse = $.get(apiAdresses, {
                    q: rue,
                    limit: 15,
                    autocomplete: 1,
                    postcode: zipcode.value
                })

                if (currentInput.length >= 3) {
                    fetch(urlAdresse, {method: 'get'}).then(response => response.json())
                        .then(results => {
                            if (results.length) {
                                let sHtmlDatalist = '';
                                results.forEach((value) => {
                                    sHtmlDatalist += '<option value="'+value.name+'">'+value.name+'</option>\n';
                                });
                                document.getElementById("list_rues").innerHTML = sHtmlDatalist;
                            } else {
                                console.log("Aucun résultat.");
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            }

        }).catch(err => {
            console.log(err);
        });

        $(city).on('change', function () {
            let ville = $(this).val();
        })


    });



});