<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatPais;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatPaisService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatPais::search($filters)->orderBy('cad_nombre_es', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatPais::create($data);
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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó el país con ID: '.$catalog->id_pais.', nombre: "'.$catalog->pais.'", en el catalogo: Pais');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatPais::where('id_pais', decrypt($id))->first();
    }
}
