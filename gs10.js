(function() {
    function postIfValid(event) {
        event.preventDefault();
        var formElement = document.querySelector('main > section > div#formContainer > form');
        var isFormValid = formElement.checkValidity();
        if (isFormValid) {
            grecaptcha.execute();
        }
    }

    window.submitRegistration = function(token) {
        var formElement = document.querySelector('main > section > div#formContainer > form');
        
        var data = new FormData(formElement);
        data.append('token',token);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "https://script.google.com/macros/s/AKfycbzZNfBDF0OtXRkym1J8w4vBf48zc-wYlbqXRYPnhM3zbnJ-xqE0xfL06Wh8iTeip_M3/exec");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var responseObject = JSON.parse(xhr.responseText);
                    var formContainer = document.getElementById('formContainer');
                    if (responseObject.success) {
                        formContainer.innerHTML = "<div class='alert alert-success'><h3>Anmeldung erhalten!</h3></div>";
                    } else {
                        var div = document.createElement('div');
                        div.className = 'alert alert-warning';
                        div.innerHTML = '<strong>Oups</strong> das hat nicht geklappt. Captcha?';
                        
                        formContainer.insertAdjacentElement('beforeend',div);
                    }
                }
            }
        };
        xhr.send(data);
        grecaptcha.reset();
    };

    function loadTeams() {
        var insertTable = function(teams) {
            var section = document.getElementById('teamsSection');
            if (teams && teams.length && teams.length > 0) {
                var leTable = document.createElement('table');
                leTable.className = 'table';
                var tableHeader = leTable.createTHead();
                ['Team','Stadt','bezahlt'].forEach(function(x) {
                    var cell = document.createElement('td');
                    cell.appendChild(document.createTextNode(x));
                    tableHeader.appendChild(cell);
                });
                teams.forEach(function(team){
                    var row = leTable.insertRow();
                    row.insertCell().appendChild(document.createTextNode(team.team));
                    row.insertCell().appendChild(document.createTextNode(team.city));
                    var span = document.createElement('span');
                    span.className= team.paid && team.paid === true ? 'glyphicon glyphicon-check' : 'glyphicon glyphicon-unchecked';
                    row.insertCell().appendChild(span);
                }
                             );
                section.appendChild(leTable);
            } else {
                section.appendChild(document.createElement('p').appendChild(document.createTextNode('TBA')));
            }
        };
        fetch('https://script.google.com/macros/s/AKfycbzOaBQ7foVlyJLaF7J9QQIdP_vwkJzxDP_m3hFXHwmen5BR66eWspKSgKO665O3V0QF/exec',
              { method:'GET',
                redirect:'follow',
                cache:'no-cache',
                mode:'cors'
              }).then(function(resp) {
                  if(resp.ok) {
                      return resp.json();
                  }
                  return [];
                  
              }).then(insertTable).catch(function(err){console.log(err);});
    }


    function setupMap() {
        var gs10Map = L.map('gs10map').setView([54.11,13.85],16);
        var OpenTopoMap = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
	    maxZoom: 19,
	    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
        }).addTo(gs10Map);
        var gucArea = L.polygon([
            [54.114560959934, 13.8492461441],
            [54.1148219478,13.849822819038],
            [54.11460183766, 13.850099086567],
            [54.114507504386, 13.849704801842],
            [54.114578254362, 13.849584102436],
            [54.114485493257, 13.849366843506]
        ]).bindPopup('Frühstück, Infos und get-together'),
            tentArea = L.polygon([
                [ 54.11495086891,13.850415587231],
                [ 54.115298325029,13.85126048307],
                [ 54.114620704289,13.851976632877],
                [ 54.114211925402,13.850895702645],
                [ 54.114546809943,13.850474595829],
                [ 54.114862825268,13.850085675522]
            ]).bindPopup('Zelte'),
            showers = L.marker([54.113779558727,13.85050678234]).bindPopup('Duschen &amp; Toiletten');
        var camping = L.layerGroup([gucArea,tentArea,showers]);
        L.control.layers({"Basemap":OpenTopoMap}, {"Campingplatz":camping}).addTo(gs10Map);
        
    }

    setupMap();
    loadTeams();
})();
