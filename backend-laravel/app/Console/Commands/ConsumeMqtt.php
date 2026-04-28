<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use App\Models\MachineEvent;
use Carbon\Carbon;

class ConsumeMqtt extends Command
{
    protected $signature = 'mqtt:consume';
    protected $description = 'Consome eventos MQTT industriais';

    public function handle()
    {
        $mqtt = new MqttClient('localhost', 1883, 'laravel-consumer');
        $mqtt->connect();

        $this->info("📡 Conectado ao MQTT, escutando eventos...");

        $mqtt->subscribe('factory/machine/events', function ($topic, $message) {

            echo "📥 CHEGOU EVENTO\n";

            $data = json_decode($message, true);

            try {

                MachineEvent::create([
                    'machine_id' => $data['machine_id'],
                    'state' => $data['state'],
                    'produced' => $data['produced'],
                    'rejected' => $data['rejected'],
                    'total_production' => $data['total_production'],
                    'good_count' => $data['good_count'],
                    'reject_count' => $data['reject_count'],
                    'temperature' => (float) $data['temperature'],
                    'vibration' => (float) $data['vibration'],
                    'availability' => (float) $data['availability'],
                    'performance' => (float) $data['performance'],
                    'quality' => (float) $data['quality'],
                    'oee' => (float) $data['oee'],
                    'event_time' => \Carbon\Carbon::parse($data['timestamp']),
                ]);

                echo "✅ SALVO\n";
            } catch (\Throwable $e) {
                echo "❌ ERRO: " . $e->getMessage() . "\n";
            }
        }, 0);

        $mqtt->loop(true, true);
    }
}
