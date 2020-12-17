// -----------------------------------------------------------------------------
// Title: Demo code for Chart.js
// Location: charts.chartjs.html
// IDs: #chartjs_lineChart,#chartjs_barChart,#chartjs_radarChart,#chartjs_polarChart,#chartjs_pieChart,#chartjs_doughnutChart
// Dependency File(s): assets/vendor/chart.js/dist/Chart.bundle.min.js
// -----------------------------------------------------------------------------

(function (window, document, $, undefined) {
    "use strict";
    $(function () {

        if ($('#chart_user_type').length) {
            get_chart_user_type();
        }
        if ($('#usersChart').length) {
            get_usersChart();
        }
        if ($('#bounceRateChart').length) {
            get_bounceRateChart();
        }
        if ($('#sessionDuration').length) {
            get_sessionDuration();
        }
    });
    $(document).on("click", "#card_user_type .chart_duration .nav-item .nav-link", function () {
        get_chart_user_type();
    });
    $(document).on("click", "#card_custom .chart_duration .nav-item .nav-link", function () {
        get_chart_custom();
    });
    $(document).on("click", "#custom_form button", function () {
        get_chart_custom();
    });
    get_world_map();
    get_live_user();
    get_active_pages();
    get_session_by_device();
    setInterval(get_live_user, 30000);
    
})(window, document, window.jQuery);

function get_session_by_device()
{
    var data = {
        'site': $("#site").val(),
        'chartType': 'session_by_device'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
        var chart = c3.generate({
                bindto: "#session_by_device",
                data: {
                    columns: responce.data,
                    type: "donut",
                    onclick: function(d, i) {
                        console.log("onclick", d, i);
                    },
                    onmouseover: function(d, i) {
                        console.log("onmouseover", d, i);
                    },
                    onmouseout: function(d, i) {
                        console.log("onmouseout", d, i);
                    }
                },
                donut: {
                    label: {
                        show: false
                    },
                    title: "Device",
                    width: 25
                },

                legend: {
                    hide: true
                },
                color: {
                    pattern: [
                        QuantumPro.APP_COLORS.info,
                        QuantumPro.APP_COLORS.accent,
                        QuantumPro.APP_COLORS.primary
                    ]
                }
            });
        }
    });
}

function get_active_pages() {
    var data = {
        'site': $("#site").val(),
        'chartType': 'active_pages'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
            var html = '';
            $.each(responce.data, function(i, item) {
                html += '<tr>';
                html += '<th scope="row">'+(i+1)+'</th>';
                html += '<td>'+item.PageUrl+'</td>';
                html += '<td>'+item.Users+'</td>';
                html += '<td>'+item.NewSessions+'%</td>';
                html += '</tr>';
            });
            $("#active_pages").html(html);
        }
    });
}

function get_live_user() {
     var data = {
        'site': $("#site").val(),
        'chartType': 'live_user'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
            $("#live_users").html(responce.liveUser);
        }
    });
}

function get_world_map() {

    var data = {
        'site': $("#site").val(),
        'chartType': 'worldmap'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
            $('#world-map').vectorMap({
                map: 'world_mill_en',
                scaleColors: ['#F54086', '#695DB5'],
                normalizeFunction: 'polynomial',
                focusOn: {
                    x: 5,
                    y: 1,
                    scale: .85
                },
                zoomOnScroll: false,
                zoomMin: 0.65,
                hoverColor: false,
                regionStyle: {
                    initial: {
                        fill: '#c5d5ea',
                        "fill-opacity": 1,
                        stroke: '#c5d5ea',
                        "stroke-width": 0,
                        "stroke-opacity": 0
                    },
                    hover: {
                        "fill-opacity": 0.6
                    }
                },
                markerStyle: {
                    initial: {
                        fill: '#695DB5 ',
                        stroke: '#b3ace5',
                        "fill-opacity": 1,
                        "stroke-width": 6,
                        "stroke-opacity": 0.8,
                        r: 3
                    },
                    hover: {
                        stroke: '#b3ace5',
                        "stroke-width": 10
                    },
                    selected: {
                        fill: 'blue'
                    },
                    selectedHover: {}
                },
                backgroundColor: '#ffffff',
                markers: responce.data
            });
        }
    });
}

function get_chart_custom() {
    $("#container_custom_chart").html('<div class="text-center" id="chart_loader"><img src="assets/img/loader.gif"/></div><canvas id="chart_custom" style="display:none;"></canvas>');
    var ctx = document.getElementById('chart_custom').getContext('2d');
    var data = {
        'site': $("#site").val(),
        'chartType': 'chart_custom',
        'chart_duration': $(".duration").val(),
        'metrics': $("#custom_form .metrics").val(),
        'dimension': $("#custom_form .dimension").val(),
    };

    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
            $("#chart_loader").hide();
            $("#chart_custom").show();
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: responce.data
            });
        }
    });
}

function get_chart_user_type() {
    var ctx = document.getElementById('chart_user_type').getContext('2d');
    var data = {
        'site': $("#site").val(),
        'chartType': 'chart_user_type',
        'chart_duration': $("#card_user_type .chart_duration .nav-item .active").html()
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        if (responce.is_success) {
            $(".total_visitor").html(responce['total']['New Visitor']);
            $(".total_returning_visitor").html(responce['total']['Returning Visitor']);
            var myChart = new Chart(ctx, {
                type: 'line',
                data: responce.data
            });
        }
    });
}

function get_usersChart() {
    var ctx = document.getElementById('chart_user_type').getContext('2d');
    var data = {
        'site': $("#site").val(),
        'chartType': 'usersChart',
        'chart_duration': 'Year'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        console.log(responce);

        if (responce.is_success) {
            $(".total_user").html(responce['total']['Users']);
            var ctx = document.getElementById("usersChart").getContext("2d");
            var gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, Chart.helpers.color(QuantumPro.APP_COLORS.info).alpha(0.9).rgbString());
            gradient.addColorStop(1, Chart.helpers.color('#ffffff').alpha(0).rgbString());
            var config = {
                type: 'line',
                data: {
                    labels: responce['data']['labels'],
                    datasets: [{
                        label: responce['data']['datasets'][0]['label'],
                        backgroundColor: gradient,
                        borderWidth: 2,
                        borderColor: QuantumPro.APP_COLORS.info,
                        pointBackgroundColor: Chart.helpers.color(QuantumPro.APP_COLORS.info).alpha(1).rgbString(),
                        pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                        pointHoverBackgroundColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        pointHoverBorderColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        data: responce['data']['datasets'][0]['data']
						}]
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'nearest',
                        intersect: false,
                        position: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
						  }],
                        yAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            },
                            ticks: {
                                beginAtZero: true
                            }
						  }]
                    },
                    elements: {
                        line: {
                            tension: 0.000001
                        },
                        point: {
                            radius: 4,
                            borderWidth: 8
                        }
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 50,
                            bottom: 0
                        }
                    }
                }
            };

            var chart = new Chart(ctx, config);
        }
    });
}

function get_bounceRateChart() {
    var ctx = document.getElementById('bounceRateChart').getContext('2d');
    var data = {
        'site': $("#site").val(),
        'chartType': 'bounceRateChart',
        'chart_duration': 'Year'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        console.log(responce);

        if (responce.is_success) {
            $(".total_bounce_rate").html(responce['total']['Bounce Rate']);
            var ctx = document.getElementById("bounceRateChart").getContext("2d");
            var gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, Chart.helpers.color(QuantumPro.APP_COLORS.warning).alpha(0.9).rgbString());
            gradient.addColorStop(1, Chart.helpers.color('#ffffff').alpha(0).rgbString());
            var config = {
                type: 'line',
                data: {
                    labels: responce['data']['labels'],
                    datasets: [{
                        label: responce['data']['datasets'][0]['label'],
                        backgroundColor: gradient,
                        borderWidth: 2,
                        borderColor: QuantumPro.APP_COLORS.warning,
                        pointBackgroundColor: Chart.helpers.color(QuantumPro.APP_COLORS.warning).alpha(1).rgbString(),
                        pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                        pointHoverBackgroundColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        pointHoverBorderColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        data: responce['data']['datasets'][0]['data']
						}]
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'nearest',
                        intersect: false,
                        position: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
						  }],
                        yAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            },
                            ticks: {
                                beginAtZero: true
                            }
						  }]
                    },
                    elements: {
                        line: {
                            tension: 0.000001
                        },
                        point: {
                            radius: 4,
                            borderWidth: 8
                        }
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 50,
                            bottom: 0
                        }
                    }
                }
            };

            var chart = new Chart(ctx, config);
        }
    });
}

function get_sessionDuration() {
    var ctx = document.getElementById('sessionDuration').getContext('2d');
    var data = {
        'site': $("#site").val(),
        'chartType': 'sessionDuration',
        'chart_duration': 'Year'
    };
    $.post(url, data, function (responce) {
        responce = JSON.parse(responce);
        console.log(responce);

        if (responce.is_success) {
            $(".total_session_duration").html(responce['total']['Session Duration']);
            var ctx = document.getElementById("sessionDuration").getContext("2d");

            var gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, Chart.helpers.color(QuantumPro.APP_COLORS.primary).alpha(0.9).rgbString());
            gradient.addColorStop(1, Chart.helpers.color('#ffffff').alpha(0).rgbString());
            var config = {
                type: 'line',
                data: {
                    labels: responce['data']['labels'],
                    datasets: [{
                        label: responce['data']['datasets'][0]['label'],
                        backgroundColor: gradient,
                        borderWidth: 2,
                        borderColor: QuantumPro.APP_COLORS.primary,
                        pointBackgroundColor: Chart.helpers.color(QuantumPro.APP_COLORS.primary).alpha(1).rgbString(),
                        pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                        pointHoverBackgroundColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        pointHoverBorderColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),
                        data: responce['data']['datasets'][0]['data']
						}]
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'nearest',
                        intersect: false,
                        position: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
						  }],
                        yAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            },
                            ticks: {
                                beginAtZero: true
                            }
						  }]
                    },
                    elements: {
                        line: {
                            tension: 0.000001
                        },
                        point: {
                            radius: 4,
                            borderWidth: 8
                        }
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 50,
                            bottom: 0
                        }
                    }
                }
            };

            var chart = new Chart(ctx, config);
        }
    });
}
