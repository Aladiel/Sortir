$(document).ready(function() {
    const apiURL = 'https://geo.api.gouv.fr/communes?codePostal=';
    const apiURLVilles = 'https://geo.api.gouv.fr/communes?nom=';
    const apiURLVilles2 = '&fields=departement&boost=population&limit=5'
    const format = '&format=json';

    let zipcode = $('#ville_codePostal');
    let city = $('#ville_nom');
    let errorMessage = $('#error-message');

    $(zipcode).on('blur', function () {
        let code = $(this).val();
        //console.log(code);
        let url = apiURL+code+format;
        //let url = apiURLVilles+code+apiURLVilles2+format;
        //console.log(url);

        fetch(url, {method:'get'}).then(response => response.json()).then(results => {
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
        }).catch(err => {
            console.log(err);
        });
    });
});