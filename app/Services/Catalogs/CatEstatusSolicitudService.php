<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatEstatusSolicitud;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatEstatusSolicitudService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatEstatusSolicitud::search($filters)->orderBy('estatus_solicitud', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatEstatusSolicitud::create($data);
    }

    public function update(string $id, array $data)
    {
        $catalog = $this->findById($id);
        if ($catalog) {

            $user = Auth::user();
            $data['id_usuario_modificacion'] = $user ? $user->id : null;
            $catalog->update($data);
        }
        return $catalog;
    }

    public function delete(string $id)
    {
        $catalog = $this->findById($id);
        if ($catalog) {
            $catalog->delete();
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó el estatus ID: '.$catalog->id_estatus_solicitud.', nombre: "'.$catalog->estatus_solicitud.'", en el catalogo: Estatus');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatEstatusSolicitud::where('id_estatus_solicitud', decrypt($id))->first();
    }
}
