(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.request = {
    attach: function (context) {

      var divstopop = document.getElementsByClassName("chartblock"),x;
      elementArray=[];
      for (var i = 0, n = divstopop.length; i < n; ++i) {
        // get id property from element and set as innerHTML
        divstopop[i].innerHTML = divstopop[i].id;
        elementArray.push(divstopop[i].id)
    }
   

      elementArray.forEach(function chartRepresent(elementId) {
        var datavalue = drupalSettings.githubci[elementId + "_datavalue"];
        console.log(elementId + '_datavalue');
        var chartType = drupalSettings.githubci[elementId + "_chartType"];

        console.log(datavalue);
        google.load("visualization", "1.0", { packages: ["corechart"] });
        google.charts.setOnLoadCallback(function () {
          const data = google.visualization.arrayToDataTable(datavalue);
          switch (chartType) {
            case "BarChart":
              chart = new google.visualization.BarChart(
                document.getElementById(elementId)
              );
              break;
            case "ColumnChart":
              chart = new google.visualization.ColumnChart(
                document.getElementById(elementId)
              );
              break;
            case "DonutChart":
            case "PieChart":
              chart = new google.visualization.PieChart(
                document.getElementById(elementId)
              );
              break;
            case "ScatterChart":
              chart = new google.visualization.ScatterChart(
                document.getElementById(elementId)
              );
              break;
            case "BubbleChart":
              chart = new google.visualization.BubbleChart(
                document.getElementById(elementId)
              );
              break;
            case "AreaChart":
              chart = new google.visualization.AreaChart(
                document.getElementById(elementId)
              );
              break;
            case "LineChart":
            case "SplineChart":
              chart = new google.visualization.LineChart(
                document.getElementById(elementId)
              );
              break;
            case "Gauge":
              chart = new google.visualization.Gauge(
                document.getElementById(elementId)
              );
              break;
            case "ComboChart":
              chart = new google.visualization.ComboChart(
                document.getElementById(elementId)
              );
              break;
            case "GeoChart":
              chart = new google.visualization.GeoChart(
                document.getElementById(elementId)
              );
              break;
            case "TableChart":
              chart = new google.visualization.Table(
                document.getElementById(elementId)
              );
          }

          chart.draw(data);
        });
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
