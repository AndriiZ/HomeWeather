(function(window, document){

var chart;
var pressure_chart;
var lasttime;

function drawCurrent(data)
{
    var temp;
    if (data.temperature_count > 0)
    {
       lasttime = data.temperature[data.temperature_count-1][0];
       temp  = data.temperature[data.temperature_count-1][1];
       document.querySelector("span#humidityDHT022").innerHTML = data.humidity[data.humidity_count-1][1];

       var date = new Date(lasttime);
       document.querySelector("span#time").innerHTML = date.toTimeString();
       document.querySelector("span#temperature").innerHTML =  data.temperature[data.temperature_count-1][1];
    }
    document.querySelector("span#lastupdate").innerHTML = new Date().toTimeString();
}

function requestDelta()
{
    $.ajax({
    url: 'weather_new.php?mode='+lasttime,
    datatype: "json",
    success: function(data)
    {
	   var i;
	   for(i=0; i<data.temperature_count;i++)
	      chart.series[0].addPoint(data.temperature[i], false, true);
           for(i=0; i<data.humidity_count;i++)
              chart.series[1].addPoint(data.humidity[i], false, true);
           chart.redraw();
	  drawCurrent(data);
          
    }
    });

}

function requestData()
{
    var daterange = document.querySelector("select#daterange").value;
    if (!daterange)
	daterange = "today";
    $.ajax({
    url: 'weather_new.php?mode='+daterange,
    datatype: "json",
    success: function(data)
    {
       chart.series[0].setData(data.temperature);
       chart.series[1].setData(data.humidity);
       drawCurrent(data);
       setInterval(requestDelta, 60  *   1000);		
    }
    });
}
$(document).ready(function() {

    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });
chart = new Highcharts.Chart({
     chart: {
        renderTo: 'graph',
        type: 'spline',
        events: {
            load: requestData
        }
     },
     title: {
        text: 'Monitoring'
     },
    tooltip: {
      shared: true
    },
     xAxis: {
        type: 'datetime',
            maxZoom: 20 * 1000
     },
     yAxis: {
	min: 10,
        minPadding: 0.2,
            maxPadding: 0.2,
            title: {
                text: 'Temperature/Humidity',
                margin: 80
            }
     },
     series: [{
        name: 'Temperature',
        data: []
     },
     {
        name: 'Humidity',
        data: []	
     }
    ]
  });


  $('select#daterange').change(function() {requestData();});
  });
})(window, document)
