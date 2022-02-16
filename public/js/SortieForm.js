
let city = document.getElementById("sortie_ville");
let ville = document.querySelector("#sortie_ville");
let zipcode = document.getElementById("sortie_codePostal");
let adresse = document.getElementById("sortie_rue");
let latitude = document.getElementById("sortie_latitude");
let longitude = document.getElementById("sortie_longitude");
const format = '&format=json';
city.addEventListener("keyup", searchCities);
ville.addEventListener("blur", findZipCodes);
//ville.addEventListener("blur", setCoordinates);
adresse.addEventListener("keyup", findAdresses);



function searchCities() {
    let sValeur = city.value;
    if (sValeur.length >= 3) {
        const sApiGeoUrl = "https://geo.api.gouv.fr/communes?nom="+sValeur+"&fields=codesPostaux"+format;

        fetch(sApiGeoUrl, {method: 'get'}).then(
            response => response.json().then(
                results => {
                    if (results.length) {
                        let sHtmlDatalist = '';
                        results.forEach((value) => {
                            sHtmlDatalist += "<option value='" + value.nom + "'>" + value.nom + " (" + value.codesPostaux + ")</option>\n";
                        });
                        document.getElementById("list_villes").innerHTML = sHtmlDatalist;
                        console.log(results);
                    } else {
                        console.log("Aucun résultat.")
                    }
                }
            )
                .catch(error => {
                    console.log(error);
                })
        )
    }
}

function findZipCodes() {
    let oVille = $(ville).val();
    const ApiGeoUrl = "https://geo.api.gouv.fr/communes?nom=" + oVille + "&fields=codesPostaux" + format;
    console.log(ApiGeoUrl);

    fetch(ApiGeoUrl, {method: 'get'}).then(
        response => response.json().then(
            results => {
                $(zipcode).find('option').remove();
                if (results.length) {
                    $.each(results, function (key, value) {
                        console.log(value.nom + " - " + oVille);
                        if (value.nom === oVille) {
                            $.each(value.codesPostaux, function (key2, value2) {
                                console.log(results);
                                console.log(oVille);
                                $(zipcode).append('<option value="' + value2 + '">' + value2 + '</option>');
                            })
                        }



                    });
                } else {
                    if ($(zipcode).val()) {
                        console.log("Erreur de ville")
                    }
                }
            })
    )
}


function findAdresses() {
    let adr = adresse.value;
    //const ApiAdresseUrl = 'https://api-adresse.data.gouv.fr/search/?q=117%20Route%20de%20Schirmeck%2067200%20Strasbourg&type=housenumber&autocomplete=1';
    //curl 'https://api-adresse.data.gouv.fr/search/?q=route%20de&type=housenumber&autocomplete=1'
    if (adr.length >= 6) {
        let adrFormatee = adr.replace(/ /g, "+");
        const ApiAdresseUrl = "https://api-adresse.data.gouv.fr/search/?q="+adrFormatee+"&postcode="+zipcode.value+"autocomplete=1&limit=15";
        console.log(adrFormatee);

        fetch(ApiAdresseUrl, {method: 'get'}).then(
            response => response.json().then(
                results => {
                    if (results.length) {
                        let pHtmlDatalist = '';
                        results.forEach((value) => {
                            pHtmlDatalist += "<option value='" + value.name + "'>" + value.name + "</option>\n";
                        });
                        document.getElementById("list_rues").innerHTML = pHtmlDatalist;
                    } else {
                        console.log("Aucun résultat.");
                    }
                }
            )
                .catch(error => {
                    console.log(error);
                })
        )
    }
}

function setCoordinates() {
    let oVille = $(ville).val();
    const ApiGeoCoor = "https://geo.api.gouv.fr/communes?nom=" + oVille + "&fields=lon" + format;

    fetch(ApiGeoCoor, {method: 'get'}).then(
        response => response.json().then(
            results => {

            }
        )
    )
}


    /*let form = this.closest("form");
    let data = this.name + "=" + this.value;

    fetch(form.action, {
        method: form.getAttribute("method"),
        body: data,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset:utf-8"
        }
    })
        .then(response => response.text())
        .then(html => {
            let content = document.createElement("html");
            content.innerHTML = html;
            let nouveauSelect = content.querySelector("#sortie_codePostal");
            document.querySelector("#sortie_codePostal").replaceWith(nouveauSelect);
        })
        .catch(error => {
            console.log(error);
        })*/



