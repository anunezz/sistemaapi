<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatGeneralGenero;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatGeneralGeneroService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatGeneralGenero::search($filters)->orderBy('genero', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatGeneralGenero::create($data);
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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó el registro con ID: '.$catalog->id_genero.', nombre: "'.$catalog->genero.'", en el catalogo: General');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatGeneralGenero::where('id', decrypt($id))->first();
    }
}
