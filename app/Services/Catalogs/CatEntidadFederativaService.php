<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatEntidadFederativa;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatEntidadFederativaService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatEntidadFederativa::with('cat_pais')->search($filters)->orderBy('entidad_federativa', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatEntidadFederativa::create($data);
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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó la entidad ID: '.$catalog->id_entidad_federativa.', nombre: "'.$catalog->entidad_federativa.'", en el catalogo: Entidad');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatEntidadFederativa::where('id_entidad_federativa', decrypt($id))->first();
    }
}
