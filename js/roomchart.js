(function(window, document){

var chart1,chart2,chart3;
var lasttime;
var kPaTommHgMultiplier =  7.50061561303;

function drawCurrent(data)
{
    var temp;
    if (data.mainroom_temperature_count > 0)
    {
       lasttime = data.mainroom_temperature[data.mainroom_temperature_count-1][0];
       temp  = data.mainroom_temperature[data.mainroom_temperature_count-1][1];
       document.querySelector("span#humidity").innerHTML = data.mainroom_humidity[data.mainroom_humidity_count-1][1];

       var date = new Date(lasttime);
       document.querySelector("span#time").innerHTML = date.toTimeString();
       document.querySelector("span#temperature").innerHTML =  data.mainroom_temperature[data.mainroom_temperature_count-1][1];
    }
    if (data.lodzhiabmp180_pressure_count > 0)
    {
	document.querySelector("span#pressure").innerHTML =  data.lodzhiabmp180_pressure[data.lodzhiabmp180_pressure_count-1][1];
    }
    if (data.lodzhiabmp180_temperature_count > 0)
    {
	document.querySelector("span#temperaturelog").innerHTML = 
		(data.lodzhiabmp180_temperature[data.lodzhiabmp180_temperature_count-1][1] + data.lodzhiadht22_temperature[data.lodzhiadht22_temperature_count-1][1]) / 2;
	document.querySelector("span#humiditylog").innerHTML = data.lodzhiadht22_humidity[data.lodzhiadht22_humidity_count-1][1];
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
	   for(i=0; i<data.mainroom_temperature_count;i++)
	      chart1.series[0].addPoint(data.mainroom_temperature[i], false, true);
           for(i=0; i<data.mainroom_humidity_count;i++)
              chart1.series[1].addPoint(data.mainroom_humidity[i], false, true);
           chart1.redraw();

           for(i=0; i<data.lodzhiadht22_temperature_count;i++)
              chart2.series[0].addPoint(data.lodzhiadht22_temperature[i], false, true);
           for(i=0; i<data.lodzhiadht22_humidity_count;i++)
              chart2.series[1].addPoint(data.lodzhiadht22_humidity[i], false, true);
           for(i=0; i<data.lodzhiabmp180_temperature_count;i++)
              chart2.series[2].addPoint(data.lodzhiabmp180_temperature[i], false, true);
            chart2.redraw();

           for(i=0; i<data.lodzhiabmp180_pressure_count;i++)
              chart3.series[0].addPoint((kPaTommHgMultiplier*data.lodzhiabmp180_pressure[i]).toFixed(2), false, true);
          chart3.redraw();
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
       chart1.series[0].setData(data.mainroom_temperature);
       chart1.series[1].setData(data.mainroom_humidity);
       chart2.series[0].setData(data.lodzhiadht22_temperature);
       chart2.series[1].setData(data.lodzhiadht22_humidity);
       chart2.series[2].setData(data.lodzhiabmp180_temperature);
       chart3.series[0].setData(multiplyPressure(data.lodzhiabmp180_pressure, kPaTommHgMultiplier));
       drawCurrent(data);
       setInterval(requestDelta, 60  *   1000);		
    }
    });
}

function multiplyPressure(arr, multiplier) {
   for (var i = 0; i < arr.length; i++)
   {
      arr[i][1] = 1 * (arr[i][1] * multiplier).toFixed(2); 
   }
   return arr;
}


$(document).ready(function() {

    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });
chart1 = new Highcharts.Chart({
     chart: {
        renderTo: 'room_graph',
        type: 'spline',
        events: {
            load: requestData
        }
     },
     title: {
        text: 'Main room monitoring'
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
                text: 'Temperature/Humidity main room',
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

chart2 = new Highcharts.Chart({
     chart: {
        renderTo: 'bmpdht_graph',
        type: 'spline'
     },
     title: {
        text: 'Loggia monitoring'
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
                text: 'Temperature/Humidity loggia',
                margin: 80
            }
     },
     series: [{
        name: 'Temperature DHT022',
        data: []
     },
     {
        name: 'Humidity',
        data: []
     },
     {
        name: 'Temperature BMP180',
        data: []
     }]
  });

chart3 = new Highcharts.Chart({
     chart: {
        renderTo: 'pressure_graph',
        type: 'spline'
     },
     title: {
        text: 'Pressure monitoring'
     },
    tooltip: {
      shared: true
    },
     xAxis: {
        type: 'datetime',
            maxZoom: 20 * 1000
     },
     yAxis: {
        min: 720,
        minPadding: 0.2,
        maxPadding: 0.2,
            title: {
                text: 'Pressure',
                margin: 80
            }
     },
     series: [{
        name: 'Pressure',
        data: []
     }]
  });


  $('select#daterange').change(function() {requestData();});
  });
})(window, document)
