function create_new_Charts(
  saleslastthirtysalesarr,
  saleslastthirtydatearr,
  title
) {
  var array = saleslastthirtysalesarr.split(",");
  var last_dates = saleslastthirtydatearr.split(",");
  let flag = [];

  for (let index = 0; index < array.length; index++) {
    let flag2 = [];
    flag2.push(last_dates[index].replace(/[\[\]']+/g, ""));
    flag2.push(parseFloat(array[index]));
    flag.push(flag2);
  }
  anychart.onDocumentReady(function () {
    // create data set on our data
    var dataSet = anychart.data.set(getData());

    // map data for the first series, take x from the zero column and value from the first column of data set
    var firstSeriesData = dataSet.mapAs({
      x: 0,
      value: 1,
    });
    // create line chart
    var chart = anychart.line();

    // see https://docs.anychart.com/Axes_and_Grids/Scales#integer
    chart.yScale().ticks().allowFractional(false);

    // turn on chart animation
    chart.animation(true);

    // set chart padding
    chart.padding([10, 20, 5, 20]);

    // turn on the crosshair
    chart.crosshair().enabled(true).yLabel(false).yStroke(null);

    // set tooltip mode to point
    chart.tooltip().positionMode("point");

    // set yAxis title
    chart.yAxis().title("Total Sales");
    chart.xAxis().labels().padding(5);

    // create first series with mapped data
    var firstSeries = chart.line(firstSeriesData);
    firstSeries.name("Sales");
    firstSeries.hovered().markers().enabled(true).type("circle").size(4);
    firstSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // turn the legend on
    chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

    // set container id for the chart
    chart.container(title);
    // initiate chart drawing
    chart.draw();
  });

  function getData() {
    return flag;
  }
}
function create_new_Charts_website_visits(
  viewslastthirtydatearr,
  viewslastthirtyviewsarr,
  convertslastthirtyconvertsarr,
  title
) {
  var views = viewslastthirtyviewsarr.split(",");
  var converts = convertslastthirtyconvertsarr.split(",");
  var last_dates = viewslastthirtydatearr.split(",");
  let flag = [];

  for (let index = 0; index < views.length; index++) {
    let flag2 = [];
    flag2.push(last_dates[index].replace(/[\[\]']+/g, ""));
    flag2.push(parseFloat(views[index]));
    flag2.push(parseFloat(converts[index]));
    flag.push(flag2);
  }
  anychart.onDocumentReady(function () {
    // create data set on our data
    var dataSet = anychart.data.set(getData());

    // map data for the first series, take x from the zero column and value from the first column of data set
    var firstSeriesData = dataSet.mapAs({
      x: 0,
      value: 1,
    });

    // map data for the second series, take x from the zero column and value from the second column of data set
    var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });

    // create line chart
    var chart = anychart.line();

    // see https://docs.anychart.com/Axes_and_Grids/Scales#integer
    chart.yScale().ticks().allowFractional(false);

    // turn on chart animation
    chart.animation(true);

    // set chart padding
    chart.padding([10, 20, 5, 20]);

    // turn on the crosshair
    chart.crosshair().enabled(true).yLabel(false).yStroke(null);

    // set tooltip mode to point
    chart.tooltip().positionMode("point");

    // set yAxis title
    chart.yAxis().title("Website visits & converted visitors");
    chart.xAxis().labels().padding(5);

    // create first series with mapped data
    var firstSeries = chart.line(firstSeriesData);
    firstSeries.name("Views");
    firstSeries.hovered().markers().enabled(true).type("circle").size(4);
    firstSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // create second series with mapped data
    var secondSeries = chart.line(secondSeriesData);
    secondSeries.name("Conversion");
    secondSeries.hovered().markers().enabled(true).type("circle").size(4);
    secondSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // turn the legend on
    chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

    // set container id for the chart
    chart.container(title);
    // initiate chart drawing
    chart.draw();
  });

  function getData() {
    return flag;
  }
}

function create_new_mail(
  sentmailslastthirtydatearr,
  totalmailslastthirtytotalarr,
  opensmailslastthirtyopensarr,
  linksvisitsthirdaysarr,
  unsubsmailslastthirtyunsubsarr,
  title
) {
  var sent_mails = totalmailslastthirtytotalarr.split(",");
  var opened = opensmailslastthirtyopensarr.split(",");
  var visits = linksvisitsthirdaysarr.split(",");
  var unSubscribers = unsubsmailslastthirtyunsubsarr.split(",");

  var last_dates = sentmailslastthirtydatearr.split(",");
  let flag = [];

  for (let index = 0; index < sent_mails.length; index++) {
    let flag2 = [];
    flag2.push(last_dates[index].replace(/[\[\]']+/g, ""));
    flag2.push(parseFloat(sent_mails[index]));
    flag2.push(parseFloat(opened[index]));
    flag2.push(parseFloat(visits[index]));
    flag2.push(parseFloat(unSubscribers[index]));
    flag.push(flag2);
  }
  anychart.onDocumentReady(function () {
    // create data set on our data
    var dataSet = anychart.data.set(getData());

    // map data for the first series, take x from the zero column and value from the first column of data set
    var firstSeriesData = dataSet.mapAs({
      x: 0,
      value: 1,
    });

    // map data for the second series, take x from the zero column and value from the second column of data set
    var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });

    // map data for the third series, take x from the zero column and value from the third column of data set
    var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });

    // map data for the fourth series, take x from the zero column and value from the fourth column of data set
    var fourthSeriesData = dataSet.mapAs({ x: 0, value: 4 });

    // create line chart
    var chart = anychart.line();

    // see https://docs.anychart.com/Axes_and_Grids/Scales#integer
    chart.yScale().ticks().allowFractional(false);

    // turn on chart animation
    chart.animation(true);

    // set chart padding
    chart.padding([10, 20, 5, 20]);

    // turn on the crosshair
    chart.crosshair().enabled(true).yLabel(false).yStroke(null);

    // set tooltip mode to point
    chart.tooltip().positionMode("point");

    // set yAxis title
    chart.yAxis().title("Mail sending report");
    chart.xAxis().labels().padding(5);

    // create first series with mapped data
    var firstSeries = chart.line(firstSeriesData);
    firstSeries.name("Sent mails");
    firstSeries.hovered().markers().enabled(true).type("circle").size(4);
    firstSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // create second series with mapped data
    var secondSeries = chart.line(secondSeriesData);
    secondSeries.name("Opened");
    secondSeries.hovered().markers().enabled(true).type("circle").size(4);
    secondSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // create third series with mapped data
    var thirdSeries = chart.line(thirdSeriesData);
    thirdSeries.name("Links visits");
    thirdSeries.hovered().markers().enabled(true).type("circle").size(4);
    thirdSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // create fourth series with mapped data
    var fourthSeries = chart.line(fourthSeriesData);
    fourthSeries.name("Unsubscribers");
    fourthSeries.hovered().markers().enabled(true).type("circle").size(4);
    fourthSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // turn the legend on
    chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

    // set container id for the chart
    chart.container(title);
    // initiate chart drawing
    chart.draw();
  });

  function getData() {
    return flag;
  }
}

function create_new_membership(
  lastthirtydaysmembersdatearr,
  lastthirtydaysmemberscountarr,
  title
) {
  var array = lastthirtydaysmemberscountarr.split(",");
  var last_dates = lastthirtydaysmembersdatearr.split(",");
  let flag = [];

  for (let index = 0; index < array.length; index++) {
    let flag2 = [];
    flag2.push(last_dates[index].replace(/[\[\]']+/g, ""));
    flag2.push(parseFloat(array[index]));
    flag.push(flag2);
  }
  anychart.onDocumentReady(function () {
    // create data set on our data
    var dataSet = anychart.data.set(getData());

    // map data for the first series, take x from the zero column and value from the first column of data set
    var firstSeriesData = dataSet.mapAs({
      x: 0,
      value: 1,
    });
    // create line chart
    var chart = anychart.line();

    // see https://docs.anychart.com/Axes_and_Grids/Scales#integer
    chart.yScale().ticks().allowFractional(false);

    // turn on chart animation
    chart.animation(true);

    // set chart padding
    chart.padding([10, 20, 5, 20]);

    // turn on the crosshair
    chart.crosshair().enabled(true).yLabel(false).yStroke(null);

    // set tooltip mode to point
    chart.tooltip().positionMode("point");

    // set yAxis title
    chart.yAxis().title("Membership report");
    chart.xAxis().labels().padding(5);

    // create first series with mapped data
    var firstSeries = chart.line(firstSeriesData);
    firstSeries.name("Members");
    firstSeries.hovered().markers().enabled(true).type("circle").size(4);
    firstSeries
      .tooltip()
      .position("right")
      .anchor("left-center")
      .offsetX(5)
      .offsetY(5);

    // turn the legend on
    chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

    // set container id for the chart
    chart.container(title);
    // initiate chart drawing
    chart.draw();
  });

  function getData() {
    return flag;
  }
}
