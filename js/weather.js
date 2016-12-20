(function(window, document){
  var kPaTommHgMultiplier =  7.50061561303;

  function callAjax(url, callback, before, after){
    var xmlhttp;
    xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function(){
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
       after();
       callback(xmlhttp.responseText);
    }
  }
    before();
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  function saveToLocalStorage(humidity, temperature, resulttime, loggiadht22, loggiabmp180) {
    var myStorage = window.localStorage;
    myStorage.setItem('humidity', humidity);
    myStorage.setItem('temperature', temperature);
    myStorage.setItem('resulttime', resulttime);
  }

  function showDataFromStorage()
  {
     var myStorage = window.localStorage;
     showData(  myStorage.getItem('humidity'), 
		myStorage.getItem('temperature'), 
		myStorage.getItem('resulttime')
		);
  }

  function showData(humidity, temperature, resulttime, loggiadht22, loggiabmp180) {
     document.getElementById('humidity').innerHTML = humidity;
     document.getElementById('temperature').innerHTML = temperature;
     document.getElementById('resulttime').innerHTML = resulttime;

     var currentTemp = 0;
     var temperaturesCount = 1;

     if (loggiadht22 != null)
     {
        document.getElementById('humidityloggia').innerHTML = loggiadht22.humidity;
        if (loggiadht22.temperature > -271)
          currentTemp +=  loggiadht22.temperature;
     }
     var pressure = 100;
     if (loggiabmp180 != null)
     {
        currentTemp +=  loggiabmp180.temperature;
        if (loggiabmp180.pressure > -271)
        {
            pressure = loggiabmp180.pressure;
            temperaturesCount++;
        }
     }
     currentTemp = currentTemp / temperaturesCount;

     document.getElementById('temperatureloggia').innerHTML = currentTemp.toFixed(2);
     document.getElementById('pressureHg').innerHTML = (pressure*kPaTommHgMultiplier).toFixed(0);
     document.getElementById('pressureKPa').innerHTML = pressure.toFixed(2);
  }

  function cb(response) {
     var data = JSON.parse(response);
     var result = data.main;
     var loggiadht22 = data.loggiadht22;
     var loggiabmp180 = data.loggiabmp180;
     showData(result.humidity, result.temperature, result.resulttime, loggiadht22, loggiabmp180);
     saveToLocalStorage(result.humidity, result.temperature, result.resulttime, loggiadht22, loggiabmp180);
  }

  function openModal() {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('fade').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('fade').style.display = 'none';
  }


  document.addEventListener("DOMContentLoaded", function(event) {
     showDataFromStorage();
     callAjax("getnow.php", cb, openModal, closeModal);
     window.setInterval(function(){ callAjax("getnow.php", cb, openModal, closeModal); }, 60 * 1000);
  });
})(window, document)
