<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatMunicipio;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatMunicipioService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatMunicipio::with('cat_entidad_federativa', 'cat_pais')->search($filters)->orderBy('municipio', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatMunicipio::create($data);
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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó el municipio con ID: '.$catalog->id_municipio.', nombre: "'.$catalog->municipio.'", en el catalogo: Municipio');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatMunicipio::where('id_municipio', decrypt($id))->first();
    }
}
