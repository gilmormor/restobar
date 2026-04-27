<?php

namespace App\Services;

use App\Models\InvControl;
use App\Models\MovInv;
use App\Models\MovInvDet;
use App\Models\MovInvTipo;
use App\Models\MovInvModulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventarioCierreService
{
    /**
     * Calcula el stock de un ingrediente en una bodega para un período.
     *
     * Todos los registros del mes (incluido el saldo_inicial del inicio de mes)
     * se suman con su tipomov como multiplicador:
     *
     *   stock = SUM(cantidad_stock * tipomov)
     *
     * El saldo_inicial tiene tipomov=+1, por lo que aporta positivamente.
     * Las ventas tienen tipomov=-1, las compras tipomov=+1, etc.
     * Un solo SUM lo resuelve todo — sin condiciones CASE.
     */
    public function calcularStock(int $ingredienteId, int $bodegaId, string $annomes): float
    {
        $stock = DB::table('mov_inv_det as d')
            ->join('mov_inv as m',       'm.id',  '=', 'd.mov_inv_id')
            ->join('mov_inv_tipos as t', 't.id',  '=', 'd.tipo_id')
            ->where('d.ingrediente_id', $ingredienteId)
            ->where('d.bodega_id',      $bodegaId)
            ->where('m.annomes',        $annomes)
            ->whereNull('m.staanul')       // excluir anulados
            ->whereNull('m.deleted_at')
            ->whereNull('d.deleted_at')
            ->selectRaw('COALESCE(SUM(d.cantidad_stock * t.tipomov), 0) AS stock')
            ->value('stock');

        return (float) $stock;
    }

    /**
     * Apertura de un nuevo período de inventario para una sucursal.
     * Se llama al iniciar el sistema por primera vez o al empezar a usar un nuevo mes
     * sin ejecutar el cierre del anterior (caso: primer mes).
     */
    public function aperturarPeriodo(string $annomes, int $sucursalId, int $usuarioId): InvControl
    {
        $existe = InvControl::where('annomes', $annomes)
            ->where('sucursal_id', $sucursalId)
            ->exists();

        if ($existe) {
            throw new \RuntimeException("El período {$annomes} ya existe para esta sucursal.");
        }

        return InvControl::create([
            'annomes'     => $annomes,
            'sucursal_id' => $sucursalId,
            'status'      => 0,
            'usuario_id'  => $usuarioId,
        ]);
    }

    /**
     * Cierre de mes e inicio del nuevo período.
     * Adaptado del procesarcierreini() de Plastiservi.
     *
     * Flujo:
     *  1. Valida que el mes a cerrar sea ANTERIOR al actual (no se puede cerrar el mes en curso)
     *  2. Verifica que el mes exista en inv_control y esté ABIERTO (status=0)
     *  3. Verifica que el nuevo mes NO exista aún en inv_control
     *  4. Crea inv_control para el nuevo mes (status=0 = abierto)
     *  5. Consulta TODOS los movimientos del mes que cierra agrupados por ingrediente+bodega
     *     → SUM(cantidad_stock * tipomov) da el stock final de cada ingrediente en cada bodega
     *  6. Crea UN registro mov_inv (cabecera) de tipo "Saldo Inicial" para el nuevo mes
     *  7. Crea los mov_inv_det (líneas) con el stock final de cada ingrediente/bodega
     *  8. Actualiza el cache en ingrediente_bodega.stock_actual
     *  9. Cierra el mes en inv_control (status=1)
     *
     * @param  string $annomes     Período a cerrar en formato AAAAMM (ej: '202604')
     * @param  int    $sucursalId
     * @param  int    $usuarioId
     */
    public function procesarCierreIni(string $annomes, int $sucursalId, int $usuarioId): array
    {
        // ── 1. No se puede cerrar el mes actual ni uno futuro ─────────────────
        if ($annomes >= date('Ym')) {
            throw new \RuntimeException('No se puede cerrar un mes mayor o igual al actual.');
        }

        // ── 2. Verificar que el mes exista y esté abierto ─────────────────────
        $control = InvControl::where('annomes', $annomes)
            ->where('sucursal_id', $sucursalId)
            ->first();

        if (!$control) {
            throw new \RuntimeException("El período {$annomes} no ha sido aperturado para esta sucursal.");
        }

        if ($control->estaCerrado()) {
            throw new \RuntimeException("El período {$annomes} ya fue cerrado.");
        }

        // ── 3. Calcular annomes del nuevo período ─────────────────────────────
        $annomesNuevo = Carbon::createFromFormat('Ym', $annomes)
            ->addMonth()
            ->format('Ym');

        // ── 4. Verificar que el nuevo período NO exista ya ─────────────────────
        $yaExisteNuevo = InvControl::where('annomes', $annomesNuevo)
            ->where('sucursal_id', $sucursalId)
            ->exists();

        if ($yaExisteNuevo) {
            throw new \RuntimeException("El período {$annomesNuevo} ya fue creado. No se puede duplicar el cierre.");
        }

        // ── 5. Obtener tipo "Saldo Inicial" y módulo "Cierre Mes" ──────────────
        $tipoSaldoInicial = MovInvTipo::where('es_apertura_mes', true)->firstOrFail();
        $moduloCierreMes  = MovInvModulo::where('id', MovInvModulo::CIERRE_MES)->firstOrFail();

        $resumen = [
            'periodo_cerrado' => $annomes,
            'periodo_nuevo'   => $annomesNuevo,
            'procesados'      => 0,
        ];

        DB::transaction(function () use (
            $annomes, $annomesNuevo, $sucursalId, $usuarioId,
            $tipoSaldoInicial, $moduloCierreMes, $control, &$resumen
        ) {
            // ── 4b. Crear inv_control para el nuevo mes ───────────────────────
            $controlNuevo = InvControl::create([
                'annomes'     => $annomesNuevo,
                'sucursal_id' => $sucursalId,
                'status'      => 0,         // abierto
                'usuario_id'  => $usuarioId,
            ]);

            // ── 5. Obtener stock final por ingrediente+bodega del mes que cierra
            // SUM(cantidad_stock * tipomov) da el saldo neto del mes completo,
            // incluyendo el saldo_inicial del inicio del mes anterior.
            // El resultado ES el stock a traspasar al nuevo mes.
            $stocksPorCombo = DB::table('mov_inv_det as d')
                ->join('mov_inv as m',       'm.id', '=', 'd.mov_inv_id')
                ->join('mov_inv_tipos as t', 't.id', '=', 'd.tipo_id')
                ->join('bodegas as b',       'b.id', '=', 'd.bodega_id')
                ->where('m.annomes',     $annomes)
                ->where('m.sucursal_id', $sucursalId)
                ->whereNull('m.staanul')
                ->whereNull('m.deleted_at')
                ->whereNull('d.deleted_at')
                ->select([
                    'd.ingrediente_id',
                    'd.bodega_id',
                    DB::raw('SUM(d.cantidad_stock * t.tipomov) AS stock_final'),
                    DB::raw('AVG(d.costo_unitario) AS costo_promedio'),
                ])
                ->groupBy('d.ingrediente_id', 'd.bodega_id')
                ->get();

            if ($stocksPorCombo->isEmpty()) {
                // Sin movimientos en el mes: traspasa el stock actual del cache
                $stocksPorCombo = DB::table('ingrediente_bodega as ib')
                    ->join('bodega_sucursal as bs', function ($j) use ($sucursalId) {
                        $j->on('bs.bodega_id', '=', 'ib.bodega_id')
                          ->where('bs.sucursal_id', $sucursalId);
                    })
                    ->select([
                        'ib.ingrediente_id',
                        'ib.bodega_id',
                        DB::raw('ib.stock_actual AS stock_final'),
                        DB::raw('0 AS costo_promedio'),
                    ])
                    ->get();
            }

            // ── 6. Crear cabecera del movimiento de apertura del nuevo mes ────
            $movCabecera = MovInv::create([
                'annomes'     => $annomesNuevo,
                'fechahora'   => now(),
                'descripcion' => "Movimiento inicio mes: {$annomesNuevo}",
                'observacion' => "Generado automáticamente en cierre del período {$annomes}",
                'tipo_id'     => $tipoSaldoInicial->id,
                'modulo_id'   => $moduloCierreMes->id,
                'idmovmod'    => $controlNuevo->id,  // referencia al inv_control
                'sucursal_id' => $sucursalId,
                'usuario_id'  => $usuarioId,
            ]);

            // ── 7. Crear líneas (una por ingrediente+bodega) ──────────────────
            foreach ($stocksPorCombo as $row) {
                $stockFinal = (float) $row->stock_final;

                MovInvDet::create([
                    'mov_inv_id'     => $movCabecera->id,
                    'ingrediente_id' => $row->ingrediente_id,
                    'bodega_id'      => $row->bodega_id,
                    'tipo_id'        => $tipoSaldoInicial->id,
                    'cantidad'       => $stockFinal,
                    'unidad_uso'     => 'stock',
                    'factor_conv'    => 1.000000,
                    'cantidad_stock' => $stockFinal,
                    'costo_unitario' => (float) $row->costo_promedio,
                    'stock_anterior' => $stockFinal,
                    'stock_nuevo'    => $stockFinal,
                ]);

                // ── 8. Actualizar cache en ingrediente_bodega ─────────────────
                DB::table('ingrediente_bodega')
                    ->where('ingrediente_id', $row->ingrediente_id)
                    ->where('bodega_id',      $row->bodega_id)
                    ->update([
                        'stock_actual' => $stockFinal,
                        'updated_at'   => now(),
                    ]);

                $resumen['procesados']++;
            }

            // ── 9. Cerrar el mes en inv_control ───────────────────────────────
            $control->update(['status' => 1]);
        });

        return $resumen;
    }

    /**
     * Recalcula y sincroniza el cache stock_actual para el período abierto actual.
     * Útil para corregir discrepancias sin hacer un cierre.
     */
    public function recalcularStockActual(int $sucursalId): array
    {
        $control = InvControl::periodoAbierto($sucursalId);

        if (!$control) {
            throw new \RuntimeException('No hay un período abierto para esta sucursal.');
        }

        $annomes = $control->annomes;
        $count   = 0;

        DB::table('ingrediente_bodega as ib')
            ->join('bodega_sucursal as bs', function ($j) use ($sucursalId) {
                $j->on('bs.bodega_id', '=', 'ib.bodega_id')
                  ->where('bs.sucursal_id', $sucursalId);
            })
            ->select('ib.ingrediente_id', 'ib.bodega_id')
            ->orderBy('ib.ingrediente_id')
            ->chunk(100, function ($rows) use ($annomes, &$count) {
                foreach ($rows as $row) {
                    $stock = $this->calcularStock($row->ingrediente_id, $row->bodega_id, $annomes);
                    DB::table('ingrediente_bodega')
                        ->where('ingrediente_id', $row->ingrediente_id)
                        ->where('bodega_id',      $row->bodega_id)
                        ->update(['stock_actual' => $stock, 'updated_at' => now()]);
                    $count++;
                }
            });

        return ['annomes' => $annomes, 'procesados' => $count];
    }

    /**
     * Historial de stock mes a mes para un ingrediente en una bodega.
     */
    public function historicoStock(int $ingredienteId, int $bodegaId, int $meses = 12): array
    {
        $resultado = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $annomes = Carbon::now()->subMonths($i)->format('Ym');
            $resultado[] = [
                'annomes' => $annomes,
                'periodo' => Carbon::createFromFormat('Ym', $annomes)->translatedFormat('M Y'),
                'stock'   => $this->calcularStock($ingredienteId, $bodegaId, $annomes),
            ];
        }
        return $resultado;
    }
}
