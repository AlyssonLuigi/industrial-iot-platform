<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use App\Events\MachineEventUpdated;
use App\Models\MachineEvent;
use Carbon\Carbon;

class ConsumeMqtt extends Command
{
    protected $signature = 'mqtt:consume';
    protected $description = 'Consome eventos MQTT industriais';

    public function handle()
    {
        $this->info("🚀 Iniciando consumer MQTT...");

        while (true) {
            try {
                $mqtt = new MqttClient('127.0.0.1', 1883, 'laravel-consumer');

                $mqtt->connect();
                $this->info("📡 Conectado ao MQTT");

                $mqtt->subscribe('factory/machine/events', function ($topic, $message) {

                    $this->line("📥 Evento recebido");

                    $data = json_decode($message, true);

                    // Validação básica
                    if (!$data || !isset($data['machine_id'], $data['timestamp'])) {
                        $this->error("⚠️ Payload inválido: " . $message);
                        return;
                    }

                    try {
                        MachineEvent::create([
                            'machine_id' => $data['machine_id'],
                            'state' => $data['state'] ?? 'UNKNOWN',
                            'produced' => (int) ($data['produced'] ?? 0),
                            'rejected' => (int) ($data['rejected'] ?? 0),
                            'total_production' => (int) ($data['total_production'] ?? 0),
                            'good_count' => (int) ($data['good_count'] ?? 0),
                            'reject_count' => (int) ($data['reject_count'] ?? 0),
                            'temperature' => (float) ($data['temperature'] ?? 0),
                            'vibration' => (float) ($data['vibration'] ?? 0),
                            'availability' => (float) ($data['availability'] ?? 0),
                            'performance' => (float) ($data['performance'] ?? 0),
                            'quality' => (float) ($data['quality'] ?? 0),
                            'oee' => (float) ($data['oee'] ?? 0),
                            'event_time' => Carbon::parse($data['timestamp']),
                        ]);
                        event(new MachineEventUpdated($data));
                        $this->info("✅ Evento salvo com sucesso");
                        $this->info("📡 Evento broadcast enviado");
                    } catch (\Throwable $e) {
                        $this->error("❌ Erro ao salvar: " . $e->getMessage());
                    }
                }, 0);

                // Loop principal
                $mqtt->loop(true);
            } catch (\Throwable $e) {
                $this->error("🔥 Erro de conexão: " . $e->getMessage());
                $this->warn("⏳ Tentando reconectar em 3 segundos...");
                sleep(3);
            }
        }
    }
}
