<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvControl;
use App\Services\InventarioCierreService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioCierreController extends Controller
{
    public function __construct(private InventarioCierreService $servicio) {}

    /**
     * GET /api/inventario/control?sucursal_id=1
     * Lista los períodos abiertos y cerrados de una sucursal.
     */
    public function index(Request $request)
    {
        $sucursalId = $request->sucursal_id ?? 1;

        $periodos = InvControl::where('sucursal_id', $sucursalId)
            ->orderBy('annomes', 'desc')
            ->with('usuario')
            ->get()
            ->map(fn($p) => [
                'id'      => $p->id,
                'annomes' => $p->annomes,
                'periodo' => Carbon::createFromFormat('Ym', $p->annomes)->translatedFormat('F Y'),
                'status'  => $p->status,
                'estado'  => $p->estaCerrado() ? 'Cerrado' : 'Abierto',
                'usuario' => $p->usuario?->usuario,
            ]);

        return response()->json($periodos);
    }

    /**
     * GET /api/inventario/control/preview?annomes=202604&sucursal_id=1
     * Muestra el stock final que se va a traspasar — sin modificar nada.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'annomes'     => 'required|string|size:6',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
        ]);

        $annomes    = $request->annomes;
        $sucursalId = (int) $request->sucursal_id;

        $stocks = DB::table('mov_inv_det as d')
            ->join('mov_inv as m',       'm.id', '=', 'd.mov_inv_id')
            ->join('mov_inv_tipos as t', 't.id', '=', 'd.tipo_id')
            ->join('ingredientes as i',  'i.id', '=', 'd.ingrediente_id')
            ->join('bodegas as b',       'b.id', '=', 'd.bodega_id')
            ->where('m.annomes',     $annomes)
            ->where('m.sucursal_id', $sucursalId)
            ->whereNull('m.staanul')
            ->whereNull('m.deleted_at')
            ->whereNull('d.deleted_at')
            ->select([
                'i.nombre as ingrediente',
                'i.unidad_stock',
                'b.nombre as bodega',
                DB::raw('SUM(d.cantidad_stock * t.tipomov) AS stock_final'),
            ])
            ->groupBy('d.ingrediente_id', 'd.bodega_id', 'i.nombre', 'i.unidad_stock', 'b.nombre')
            ->orderBy('i.nombre')
            ->get();

        return response()->json([
            'annomes'      => $annomes,
            'annomes_nuevo'=> Carbon::createFromFormat('Ym', $annomes)->addMonth()->format('Ym'),
            'items'        => $stocks,
        ]);
    }

    /**
     * POST /api/inventario/control/apertura
     * Abre manualmente un nuevo período (solo para el primer mes del sistema).
     */
    public function apertura(Request $request)
    {
        $request->validate([
            'annomes'     => 'required|string|size:6',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
        ]);

        try {
            $control = $this->servicio->aperturarPeriodo(
                $request->annomes,
                (int) $request->sucursal_id,
                $request->user('sanctum')->id
            );
            return response()->json($control, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/inventario/control/cierre
     * Ejecuta el cierre del mes e inicia el nuevo período.
     * Equivale a procesarcierreini() de Plastiservi.
     */
    public function cierre(Request $request)
    {
        $request->validate([
            'annomes'     => 'required|string|size:6',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
        ]);

        try {
            $resumen = $this->servicio->procesarCierreIni(
                $request->annomes,
                (int) $request->sucursal_id,
                $request->user('sanctum')->id
            );
            return response()->json([
                'mensaje' => "Período {$resumen['periodo_cerrado']} cerrado. Período {$resumen['periodo_nuevo']} aperturado con {$resumen['procesados']} ingrediente(s).",
                ...$resumen,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/inventario/control/recalcular
     * Recalcula el cache stock_actual del período abierto actual.
     */
    public function recalcular(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|integer|exists:sucursales,id',
        ]);

        try {
            $resultado = $this->servicio->recalcularStockActual((int) $request->sucursal_id);
            return response()->json([
                'mensaje'    => "Stock recalculado para {$resultado['procesados']} ingrediente(s).",
                'annomes'    => $resultado['annomes'],
                'procesados' => $resultado['procesados'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/inventario/{ingredienteId}/historico?bodega_id=1&meses=6
     */
    public function historico(Request $request, int $ingredienteId)
    {
        $bodegaId = (int) ($request->bodega_id ?? 1);
        $meses    = min((int) ($request->meses ?? 6), 24);
        return response()->json($this->servicio->historicoStock($ingredienteId, $bodegaId, $meses));
    }
}
