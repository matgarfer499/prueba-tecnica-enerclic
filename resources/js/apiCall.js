"use strict";
// función para extraer los datos y procesarlos
function fetchDataAndProcess() {
    const date = $("input[name=date]").val();
    const token = $('meta[name="csrf-token"]').attr("content");

    if(!date){
        return;
    }
    // petición AJAX para obtener los datos
    $.ajax({
        type: "POST",
        url: "/api/datos",
        headers: {
            "X-CSRF-TOKEN": token,
        },
        data: { date: date },
        dataType: "json",
        success: function (response) {
            processData(response);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}

// Funciones para actualizar las tablas y graficas
function processData(data) {
    const colors = ["#7cb5ec", "#f45b5b"];
    console.log(data["contador1"]["power_data"]);
    updateTableData(data);
    createPowerChart(data, colors);
    createEnergyChart(data, colors);
}

function updateTableData(data) {
    $("#avgPower").text(data["contador1"]["avg_power"]);
    $("#avgPower2").text(data["contador2"]["avg_power"]);
    $("#totalEnergy").text(data["contador1"]["total_energy"]);
    $("#totalEnergy2").text(data["contador2"]["total_energy"]);
}

// grafico de potencias
function createPowerChart(data, colors) {
    Highcharts.chart("powerChart", {
        title: {
            text: "Datos minutales en potencia",
        },
        xAxis: {
            type: "datetime",
            tickInterval: 1,
        },
        yAxis: {
            title: {
                text: "Pac(kw)",
            },
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, colors[0]],
                        [
                            1,
                            Highcharts.color(colors[0])
                                .setOpacity(0)
                                .get("rgba"),
                        ],
                    ],
                },
                lineWidth: 2,
                marker: {
                    radius: 4,
                },
                states: {
                    hover: {
                        lineWidth: 2,
                    },
                },
            },
            line: {
                lineWidth: 2,
                marker: {
                    radius: 4,
                },
                states: {
                    hover: {
                        lineWidth: 2,
                    },
                },
            },
        },
        colors: colors,
        series: [
            {
                type: "area",
                data: data["contador1"]["power_data"],
                name: "Contador 1",
                color: colors[0],
            },
            {
                type: "area",
                data: data["contador2"]["power_data"],
                name: "Contador 2",
                color: colors[1],
            },
        ],
    });
}

//grafico de energías
function createEnergyChart(data, colors) {
    Highcharts.chart("energyChart", {
        chart: {
            type: "column",
        },
        title: {
            text: "Datos horarios de energía",
        },
        xAxis: {
            crosshair: true,
            tickInterval: 1,
        },
        yAxis: {
            min: 0,
            title: {
                text: "Energy (kwh)",
            },
        },
        colors: colors,
        series: [
            {
                data: data["contador1"]["energy_per_hour"],
                name: "Contador 1",
                color: colors[0],
            },
            {
                data: data["contador2"]["energy_per_hour"],
                name: "Contador 2",
                color: colors[1],
            },
        ],
    });
}

$(document).ready(function () {
    $("#calculateBtn").click(fetchDataAndProcess);
});
