<?php

namespace App\Console\Commands;

use App\Services\InventarioCierreService;
use Illuminate\Console\Command;

class CierreInventario extends Command
{
    protected $signature = 'inventario:cierre
                            {--periodo= : Período a cerrar en formato YYYY-MM (default: mes anterior)}
                            {--recalcular : Solo recalcula el stock actual sin hacer cierre}';

    protected $description = 'Ejecuta el cierre de inventario mensual y genera saldo inicial del nuevo período';

    public function handle(InventarioCierreService $servicio): int
    {
        if ($this->option('recalcular')) {
            $this->info('Recalculando stock actual...');
            $count = $servicio->recalcularStockActual();
            $this->info("✓ {$count} ingredientes actualizados.");
            return Command::SUCCESS;
        }

        $periodo = $this->option('periodo');

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('  CIERRE DE INVENTARIO MENSUAL');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if (!$this->confirm("¿Confirmar cierre del período " . ($periodo ?? 'mes anterior') . "?")) {
            $this->warn('Operación cancelada.');
            return Command::SUCCESS;
        }

        try {
            $resumen = $servicio->ejecutarCierre($periodo);

            $this->newLine();
            $this->line("  Período cerrado : <fg=yellow>{$resumen['periodo_cerrado']}</>");
            $this->line("  Nuevo período   : <fg=green>{$resumen['periodo_nuevo']}</>");
            $this->line("  Procesados      : <fg=green>{$resumen['procesados']} ingredientes</>");

            if (!empty($resumen['errores'])) {
                $this->newLine();
                $this->warn('Errores encontrados:');
                foreach ($resumen['errores'] as $error) {
                    $this->error("  • {$error}");
                }
            }

            $this->newLine();
            $this->info('✓ Cierre ejecutado correctamente.');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
