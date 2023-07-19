<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\D_Meter;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ApiController extends Controller
{
    public function data(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => 'Fecha no válida.'], 400);
        }
        //Consulta a la base de datos con la fecha solicitada
        $sql = D_meter::whereDate('datatime', $date)->get();

        $data = [];

        if ($sql->isEmpty()) { //Si no hay resultado se generan los datos y se guardan en la base de datos
            $sql = $this->insert_data($date);
            $sql = D_meter::whereDate('datatime', $date)->get();
        }
        $data = $this->process_data($sql);

        return response()->json($data);
    }

    // Función que se encarga de generar los datos de un día para poder guardarlos en la base de datos
    private function insert_data($date)
    {
        $contador1 = $this->generate_data($date, 'contador1');
        $contador2 = $this->generate_data($date, 'contador2');

        return [$contador1, $contador2];
    }

    public function generate_data($date, $contador)
    {
        $date_start = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $minutes_a_day = 60 * 24;

        //variables auxiliares para guardar los últimos datos de las energias y potencias
        $last_energy = 0;
        $last_power = null;

        //Generación de datos por cada minuto
        for ($i = 0; $i < $minutes_a_day; $i++) {
            $datetime = $date_start->format('Y-m-d H:i:s');

            // los primeros cinco minutos de cada hora incluido el 0 chequea el ultimo valor de power guardado y lo aplica en esos minutos
            if ($date_start->minute >= 0 && $date_start->minute <= 4) {
                // Ultimos valores guardados + la formula de energia
                $power = $last_power;

                $last_energy = $this->energy_formula($last_energy, $power);
            } else if ($date_start->minute >= 5 && $date_start->minute <= 15) { // los 10 minutos restantes volvemos poner a NULL los valores de power
                $power = null;
            } else {
                // Generamos los datos de power entre 1 y 1000 ya que al dividir entre 10 tedremos un numero entre 0 y 100 con un decimal incluido
                $power = (rand(1, 1000)) / 10;

                $last_power = $power;

                $last_energy = $this->energy_formula($last_energy, $power);
            }

            //Array que guarda los datos para poder insertarlo en la base de datos 
            $data[] = [
                'datatime' => $datetime,
                'contador' => $contador,
                'power' => $power ?? null,
                'energy' => $last_energy ?? 0,
            ];

            $date_start->addMinute();
        }
        D_meter::insert($data);
        return $data;
    }

    // funcion que se encarga generar los datos presentados en los charts
    private function process_data(Collection $data)
    {
        $sum_power1 = $data->where('contador', 'contador1')->sum('power');
        $sum_power2 = $data->where('contador', 'contador2')->sum('power');
        $total_energy1 = $data->where('contador', 'contador1')->sum('energy');
        $total_energy2 = $data->where('contador', 'contador2')->sum('energy');

        // agrupacion de los datos por hora para el contador1
        $avg_energy_hour1 = $data->where('contador', 'contador1')->groupBy(function ($item) {
            return Carbon::parse($item['datatime'])->format('H');
        })->map(function ($group) {
            $sum_energy = $group->sum('energy');
            $count = $group->count();
            //calculo del promedio de energía por hora
            return [
                'sum' => $sum_energy,
                'count' => $count,
                'avg' => round($sum_energy / $count, 2),
            ];
        });

        // formateo de los datos de contador1
        $avg_energy_contador1 = $avg_energy_hour1->map(function ($energy, $hour) {
            return [$hour, $energy['avg']];
        })->values()->all();

        // al igual que para el contador1, agrupamos los datos para contador2
        $avg_energy_hour2 = $data->where('contador', 'contador2')->groupBy(function ($item) {
            return Carbon::parse($item['datatime'])->format('H');
        })->map(function ($group) {
            $sum_energy = $group->sum('energy');
            $count = $group->count();
            //calculo del promedio
            return [
                'sum' => $sum_energy,
                'count' => $count,
                'avg' => round($sum_energy / $count, 2),
            ];
        });
        // formateo de los datos de contador2
        $avg_energy_contador2 = $avg_energy_hour2->map(function ($energy, $hour) {
            return [$hour, $energy['avg']];
        })->values()->all();

        // formateo general para los dos contadores y su presentación en las gráficas
        $dataFormatted = [
            'contador1' => [
                'power_data' => $data->where('contador', 'contador1')->map(function ($item) {
                    return [Carbon::parse($item['datatime'])->timestamp * 1000, $item['power'] ?? 0];
                })->values()->all(),
                'avg_power' => round($sum_power1 / $data->where('contador', 'contador1')->count(), 2),
                'total_energy' => round($total_energy1 / $data->where('contador', 'contador1')->count(), 2),
                'energy_per_hour' => $avg_energy_contador1,
            ],
            'contador2' => [
                'power_data' => $data->where('contador', 'contador2')->map(function ($item) {
                    return [Carbon::parse($item['datatime'])->timestamp * 1000, $item['power'] ?? 0];
                })->values()->all(),
                'avg_power' => round($sum_power2 / $data->where('contador', 'contador2')->count(), 2),
                'total_energy' => round($total_energy2 / $data->where('contador', 'contador2')->count(), 2),
                'energy_per_hour' => $avg_energy_contador2,
            ],
        ];

        return $dataFormatted;
    }

    // Función para calcular la energia
    private function energy_formula($last_energy, $power)
    {
        return $last_energy + ($power / 60);
    }
}
