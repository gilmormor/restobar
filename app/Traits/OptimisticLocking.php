<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait OptimisticLocking
 *
 * Implementa bloqueo optimista comparando el updated_at que
 * envía el frontend (tomado en el momento del clic) contra
 * el updated_at actual en la base de datos.
 *
 * Uso en cualquier controller:
 *   use App\Traits\OptimisticLocking;
 *
 *   if ($lock = $this->checkLock($request, $model)) return $lock;
 */
trait OptimisticLocking
{
    protected function checkLock(Request $request, Model $model, string $mensaje = null): ?\Illuminate\Http\JsonResponse
    {
        // Si el frontend no envió updated_at, se omite la validación
        if (!$request->has('updated_at') || is_null($request->updated_at)) {
            return null;
        }

        $clientTs = Carbon::parse($request->updated_at)->setTimezone(config('app.timezone'));
        $dbTs     = Carbon::parse($model->updated_at)->setTimezone(config('app.timezone'));

        if (!$clientTs->eq($dbTs)) {
            return response()->json([
                'error'      => 'conflict',
                'message'    => $mensaje ?? 'Este registro fue modificado por otro usuario mientras tenías la pantalla abierta. Recarga los datos e intenta de nuevo.',
                'updated_at' => $model->updated_at,
            ], 409);
        }

        return null;
    }
}
