<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatPerfil;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatPerfilService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatPerfil::search($filters)->orderBy('perfil', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatPerfil::create($data);
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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó el perfil con ID: '.$catalog->id_perfil.', nombre: "'.$catalog->perfil.'", en el catalogo: Perfiles');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatPerfil::where('id_perfil', decrypt($id))->first();
    }
}
