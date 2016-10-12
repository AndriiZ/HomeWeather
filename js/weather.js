(function(window, document){
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

  function saveToLocalStorage(humidity, temperature, resulttime) {
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
		myStorage.getItem('resulttime'));
  }

  function showData(humidity, temperature, resulttime) {
     document.getElementById('humidity').innerHTML = humidity;
     document.getElementById('temperature').innerHTML = temperature;
     document.getElementById('resulttime').innerHTML = resulttime;
  }

  function cb(response) {
     var result = JSON.parse(response);
     showData(result.humidity, result.temperature, result.resulttime);
     saveToLocalStorage(result.humidity, result.temperature, result.resulttime);
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
