<?php

namespace App\Services\Catalogs;

use App\Models\ImCatTipoSolicitud;
use Illuminate\Http\Request;

class CatTipoSolicitudService
{
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatTipoSolicitud::query();//search($filters);

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->orderBy('tipo_solicitud','asc')->get();
    }
}
