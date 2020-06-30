<?php
session_start();
include ("bd.php");

if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
  {
  $login = $_SESSION['login'];
  $password = $_SESSION['password'];
  $result = mysqli_query($db,"SELECT id FROM users WHERE login='$login' AND password='$password'"); 
  $myrow = mysqli_fetch_array($result);
  }
?>
<!doctype html>
<head>
  <title>Главная</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://api-maps.yandex.ru/2.1/?apikey=70a8c0e9-65d4-4f3c-b414-2b4b4e4d50cc&lang=ru_RU" type="text/javascript">
    </script>
    <script type="text/javascript"> 
        function init(){ 
            var multiRoute, finishPlacemark;
            var currentMode = 'masstransit';
            var cb;

            var routingModeButton = new ymaps.control.Button({
            data: {content: "Пешком"},
            options: {selectOnClick: true}
            });
            var routingModeButtonCar = new ymaps.control.Button({
            data: {content: "На машине"},
            options: {selectOnClick: true}
            });            

            routingModeButton.events.add('select', function () {
                routingModeButtonCar.state.set('selected', false);
                currentMode = 'pedestrian';
                cb();
            });
            routingModeButton.events.add('deselect', function () {
                currentMode = 'masstransit';                
                cb();
            });

            routingModeButtonCar.events.add('select', function () {
                routingModeButton.state.set('selected', false);
                currentMode = 'auto';
                cb();
            });
            routingModeButtonCar.events.add('deselect', function () {
                currentMode = 'masstransit';
                cb();
            });

            var myMap = new ymaps.Map("map", {
                center: [56.99541712, 40.96794022],
                zoom: 13,
                controls: ['default', routingModeButton, routingModeButtonCar]
            }, {
              buttonMaxWidth: 300
            }); 

            myMap.events.add('click', function (e) {
                if (finishPlacemark){
                  myMap.geoObjects.remove(finishPlacemark);}
                var finishCoords = e.get('coords');

                finishPlacemark = new ymaps.Placemark(finishCoords, {iconContent: "Финиш"}, {
                draggable: true, 
                preset: 'islands#redStretchyIcon'            
                });

                myMap.geoObjects.add(finishPlacemark);

                cb();
            });

            var myPlacemark = new ymaps.Placemark([56.96542817939176, 40.97085846340494], {iconContent: "Старт"}, {
            draggable: true,
            preset: 'islands#blueStretchyIcon'            
            });

            var ccb = function(startCosts, finishCosts, startCoords, finishCoords) {
              if (multiRoute){
                myMap.geoObjects.remove(multiRoute);}

              var monuments = JSON.parse(document.getElementById("array").value);
              var coords = monuments.map(v => [parseFloat(v.latitude), parseFloat(v.longitude)]);

              var filtered = [];
              var monuids = monuments.map((v, i) => ({categoryId: v.categoryId, latitude: v.latitude, longitude: v.longitude, id: i}));
              if (document.getElementById("filter3").checked == false){
                  filtered = [...filtered, ...monuids.filter(v => v.categoryId == 3)];
              }
              if (document.getElementById("filter4").checked == false){
                  filtered = [...filtered, ...monuids.filter(v => v.categoryId == 4)];
              }
              if (document.getElementById("filter5").checked == false){
                  filtered = [...filtered, ...monuids.filter(v => v.categoryId == 5)];
              }
              if (document.getElementById("filter6").checked == false){
                  filtered = [...filtered, ...monuids.filter(v => v.categoryId == 6)];
              }
              if (document.getElementById("filter7").checked == false){
                  filtered = [...filtered, ...monuids.filter(v => v.categoryId == 7)];
              }

              var dataAuto = JSON.parse(document.getElementById("auto").value);
              var dataMasstransit = JSON.parse(document.getElementById("masstransit").value);
              var dataPedestrian = JSON.parse(document.getElementById("pedestrian").value);
              var data = {auto: dataAuto, masstransit: dataMasstransit, pedestrian: dataPedestrian};

              dataPedestrian = data[currentMode].map(p => ({from: parseInt(p.startId) - 1, to: parseInt(p.finishId) - 1, cost: parseInt(p.time)}));
              var costs = [];
              for (var i = 0; i < monuments.length; i++) {
                costs.push([]);
              }
              for (var i = 0; i < monuments.length; i++) {
                for (var j = 0; j < monuments.length; j++) {
                  costs[i].push(0);
                }
              }
              for (var i = 0; i < dataPedestrian.length; i++) {
                costs[dataPedestrian[i].from][dataPedestrian[i].to] = dataPedestrian[i].cost;
              }

              var placeCost = parseInt(document.getElementById("showTime").value) * 60;

              var prev_paths = [];
              for (var i = 0; i < monuments.length; i++) {
                if (filtered.some(f => f.id == i)) {
                  continue;
                }
                var path = {way: [i], cost: startCosts[i] + placeCost};
                prev_paths.push(path);
              }
              var maxCost = parseInt(document.getElementById("time").value) * 60;
              for (var k = 1; k < monuments.length; k++) {
                var paths = [];
                for (var i = 0; i < monuments.length; i++) {
                  if (filtered.some(f => f.id == i)) {
                    continue;
                  }
                  var tmp_paths = prev_paths.filter(p => !p.way.includes(i));
                  if (tmp_paths.length === 0) {
                    continue;
                  }
                  tmp_paths = tmp_paths.map(p => ({way: [...p.way, i], cost: p.cost + costs[p.way[p.way.length - 1]][i] + placeCost}));
                  tmp_paths.sort((a, b) => a.cost - b.cost);
                  paths.push(tmp_paths[0]);
                }
                var tmp_paths = paths.map(p => p.cost + finishCosts[p.way[p.way.length - 1]]);
                if (!tmp_paths.some(cost => cost <= maxCost)) {
                  break;
                }
                prev_paths = paths;
              }
              prev_paths = prev_paths.map(p => ({way: p.way, cost: p.cost + finishCosts[p.way[p.way.length - 1]]}));
              prev_paths.sort((a, b) => a.cost - b.cost);

              multiRoute = new ymaps.multiRouter.MultiRoute({
                referencePoints: [startCoords, ...prev_paths[0].way.map(p => coords[p]), finishCoords],
                params: {
                    routingMode: currentMode
                }
              }, {

                  boundsAutoApply: true
              });
              myMap.geoObjects.add(multiRoute);

              
              /*var lengths = "";
              var counter = 0;
              var ddata = costs.map(p => p.map(p => p));
              for (var i = 0; i < coords.length; i++) 
                for (var j = 0; j < coords.length; j++) {
                    if (i == j) continue;
                    var multiRouteDB = new ymaps.multiRouter.MultiRoute({
                    referencePoints: [coords[i], coords[j]],
                      params: {
                          routingMode: 'auto'
                      }
                    });
                    multiRouteDB.model.events.add('requestsuccess', function(multiRouteDB, i, j) { return function(){
                      var activeRouteDB = multiRouteDB.getActiveRoute();
                      var routeTimeDB = activeRouteDB.properties.get("duration").value;
                      lengths += ++counter + "," + routeTimeDB + "," + (i+1) + "," + (j+1) + "\n";
                      ddata[i][j] = routeTimeDB;
                      if (counter === (coords.length - 1) * (coords.length - 1)) {
                        console.log(ddata);
                      }
                      //console.log(lengths);
                      };
                    }(multiRouteDB, i, j));  
                }
              var dddata = costs.map(p => p.map(p => p));
              for (var i = 0; i < dataAuto.length; i++) {
                dddata[dataAuto[i].startId - 1][dataAuto[i].finishId - 1] = parseInt(dataAuto[i].time);
              }
              console.log(dddata);*/            
            };
            cb = function() {
              var startCoords = myPlacemark.geometry.getCoordinates();
              var finishCoords = finishPlacemark ? finishPlacemark.geometry.getCoordinates() : startCoords;
              var monuments = JSON.parse(document.getElementById("array").value);
              var coords = monuments.map(v => [parseFloat(v.latitude), parseFloat(v.longitude)]);
              var counter = 0;
              var startCosts = [];
              for (var i = 0; i < coords.length; i++) {
                startCosts.push(0);
                var rultiMoute = new ymaps.multiRouter.MultiRoute({
                  referencePoints: [startCoords, coords[i]],
                  params: {
                    routingMode: currentMode
                  }
                });
                rultiMoute.model.events.add('requestsuccess', function(rultiMoute, i) {
                  return function() {
                    var activeRoute = rultiMoute.getActiveRoute();
                    var cost = activeRoute.properties.get("duration").value;
                    startCosts[i] = cost;
                    counter++;
                    if (counter === coords.length * 2) {
                      ccb(startCosts, finishCosts, startCoords, finishCoords);
                    }
                  };
                } (rultiMoute, i));
              }
              var finishCosts = [];
              for (var i = 0; i < coords.length; i++) {
                finishCosts.push(0);
                var rultiMoute = new ymaps.multiRouter.MultiRoute({
                  referencePoints: [coords[i], finishCoords],
                  params: {
                    routingMode: currentMode
                  }
                });
                rultiMoute.model.events.add('requestsuccess', function(rultiMoute, i) {
                  return function() {
                    var activeRoute = rultiMoute.getActiveRoute();
                    var cost = activeRoute.properties.get("duration").value;
                    finishCosts[i] = cost;
                    counter++;
                    if (counter === coords.length * 2) {
                      ccb(startCosts, finishCosts, startCoords, finishCoords);
                    }
                  };
                } (rultiMoute, i));
              }
            };

            document.getElementById("apply").onclick = cb;
            myPlacemark.events.add('dragend', cb);
            myMap.geoObjects.add(myPlacemark);
        } ymaps.ready(init); 
    </script>
</head>
<body style="background-color:#FFFFE1">
<body >
  <div class="container" style="padding: 0px; background-color:#FAFFFF">
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="index.php" style="margin-right: 10px">Главная<span class="sr-only">(current)</span></a>
          </li>          
          <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle" href="category.php?id=1" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Категории достопримечательностей
             </a>
             <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
               <a class="dropdown-item" href="category.php?id=3">Памятники</a>
               <a class="dropdown-item" href="category.php?id=4">Музеи</a>
               <a class="dropdown-item" href="category.php?id=5">Религиозные места</a>
               <a class="dropdown-item" href="category.php?id=6">Парки</a>
               <a class="dropdown-item" href="category.php?id=7">Театры</a>
             </div>
          </li>
        </ul><?php if (!isset($myrow['id']) or $myrow['id']==0) {print <<<HERE
        <form class="form-inline my-2 my-lg-0" action="testreg.php" method="post">
          <input class="form-control mr-sm-2" name="login" type="text" size="15" maxlength="15" placeholder="Логин">
          <input class="form-control mr-sm-2" name="password" type="password" size="15" maxlength="15" placeholder="Пароль">
          <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="submit">Вход для администратора</button>
        </form>
HERE;
}
else {
print <<<HERE
Вы вошли на сайт, как $_SESSION[login]
<a style="margin-left: 15px" class="btn btn-outline-primary" role="button" href='exit.php'>Выход</a>
HERE;
}?>
      </div>
    </nav>
<div id="map" style="width: 100%; height: 650px"></div>
<input type="hidden" id="array" value="<?php $result = mysqli_query($db,"SELECT latitude, longitude, name, categoryId FROM showplace"); 
$showplace = mysqli_fetch_all($result, MYSQL_ASSOC); echo htmlspecialchars(json_encode($showplace)); ?>">

<input type="hidden" id="auto" value="<?php $result = mysqli_query($db,"SELECT time, startId, finishId FROM car"); 
$auto = mysqli_fetch_all($result, MYSQL_ASSOC); echo htmlspecialchars(json_encode($auto)); ?>">
<input type="hidden" id="masstransit" value="<?php $result = mysqli_query($db,"SELECT time, startId, finishId FROM transport"); 
$masstransit = mysqli_fetch_all($result, MYSQL_ASSOC); echo htmlspecialchars(json_encode($masstransit)); ?>">
<input type="hidden" id="pedestrian" value="<?php $result = mysqli_query($db,"SELECT time, startId, finishId FROM pedestrian"); 
$pedestrian = mysqli_fetch_all($result, MYSQL_ASSOC); echo htmlspecialchars(json_encode($pedestrian)); ?>">

<h5>Фильтры:</h5>
<input type="checkbox" id="filter3" checked><label>Памятники</label>
<input type="checkbox" id="filter4" checked><label>Музеи</label>
<input type="checkbox" id="filter5" checked><label>Религиозные места</label>
<input type="checkbox" id="filter6" checked><label>Парки</label>
<input type="checkbox" id="filter7" checked><label style="margin-right: 5px">Театры</label>
<input style="margin-right: 15px" type="button" class='btn btn-secondary btn-sm' value="Применить" id="apply">
<label style="margin-right: 10px">Ограничение:</label ><input style="margin-right: 10px" type="text" size="2" id="time" value="90"><label>мин</label>
<label style="padding-left: 20px; margin-right: 10px">Время на осмотр:</label ><input style="margin-right: 10px" type="text" size="2" id="showTime" value="3"><label>мин</label>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>