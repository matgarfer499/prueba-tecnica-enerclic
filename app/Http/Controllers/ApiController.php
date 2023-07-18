<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\D_meter;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function data(Request $request)
    {
        $date = $request->input('date');
        $date_format = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        $sql = D_meter::whereDate('datatime', $date_format)->get();

        $data = [];
        if (count($sql) == 0) {

            $data_bd = [];
            $date = Carbon::createFromFormat('d/m/Y', $date)->startOfDay();

            $registers = 60 * 24;

            $last_energy = 0;
            $last_energy2 = 0;

            for ($i = 0; $i < $registers; $i++) {
                $datetime = $date->format('Y-m-d H:i:s');


                if ($date->minute > 15) {
                    $power = rand(1, 100) / 10;
                    $power2 = rand(1, 100) / 10;

                    $energy = $this->energy_formula($last_energy, $power);
                    $energy2 = $this->energy_formula($last_energy2, $power2);

                    $last_energy = $energy;
                    $last_energy2 = $energy2;
                }

                $data_bd[] = [
                    'datatime' => $datetime,
                    'power' => $power ?? null,
                    'power2' => $power2 ?? null,
                    'energy' => $energy ?? 0,
                    'energy2' => $energy2 ?? 0,
                ];

                $date->addMinute();
                $power = null;
                $power2 = null;
            }

            D_Meter::insert($data_bd);

            $sql = $data_bd;
        }

        $sum_power1 = 0;
        $sum_power2 = 0;
        $total_energy1 = 0;
        $total_energy2 = 0;

        foreach ($sql as $item) {

            $data['Contador1'][] = [
                'datetime' => $item['datatime'],
                'power' => $item['power'],
                'energy' => $item['energy'],
            ];

            $data['Contador2'][] = [
                'datetime' => $item['datatime'],
                'power' => $item['power2'],
                'energy' => $item['energy2'],
            ];

            // Calcular la suma de power y la suma total de energía
            $sum_power1 += $item['power'];
            $sum_power2 += $item['power2'];
            $total_energy1 += $item['energy'];
            $total_energy2 += $item['energy2'];
        }

        $data['contador1']['avg_power'] = round($sum_power1 / count($sql), 2);
        $data['contador2']['avg_power'] = round($sum_power2 / count($sql), 2);
        $data['contador1']['total_energy'] = round($total_energy1, 2);
        $data['contador2']['total_energy'] = round($total_energy2, 2);

        return response()->json($data);
    }

    public function energy_formula($last_energy, $power)
    {
        return $last_energy + ($power / 60);
    }
}
