<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enerclic</title>
    <!-- bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- HighCharts CDN -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    @vite('resources/js/apiCall.js')
</head>

<body>
    <main class="container-fluid">
        <div class="row">
            <div class="col-3 mt-2">
                <div class="row justify-content-center">
                    <div class="col-9">
                        <input type="date" id="date" name="date" class="p-2 border-2 border-black">
                        <button id="calculateBtn" class="p-2 border-2 border-black text-bg-info">Calculate</button>
                    </div>
                    <div class="border border-2 border-black col-9 mt-2">
                        <h5 class="text-start">Contador 1</h5>
                        <div class="row justify-content-center">
                            <h6 class="col-9">Power: <span id="avgPower" class="text-primary">AvgPower</span> Kw</h6>
                            <h6 class="col-9">Energy: <span id="totalEnergy" class="text-primary">Total</span> Kwh</h6>
                        </div>
                    </div>
                    <div class="border border-2 border-black col-9 mt-2">
                        <h5 class="text-start">Contador 2</h5>
                        <div class="row justify-content-center">
                            <h6 class="col-9">Power: <span id="avgPower2" class="text-primary">AvgPower</span> Kw</h6>
                            <h6 class="col-9">Energy: <span id="totalEnergy2" class="text-primary">Total</span> Kwh</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div id="powerChart"></div>
                <div id="energyChart"></div>
            </div>
        </div>
    </main>
</body>

</html>