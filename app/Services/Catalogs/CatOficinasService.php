<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatOficina;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatOficinasService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
    $filters = (object)$request->all();
    $query = ImCatOficina::with('cat_pais')
        ->search($filters)
        ->porTipoActivas(2)
        ->orderBy('cad_oficina', 'asc');
    if ($request->has('rowsPerPage')) {
        $paginated = $query->paginate($request->get('rowsPerPage', 10));

        // Transformar elementos para llenar correo electrónico
        $paginated->getCollection()->transform(function ($oficina) {
            if (is_null($oficina->correo_electronico)) {
                // $oficina->correo_electronico = auth()->user()->email;
                $oficina->save(); 
            }
            return $oficina;
        });

        return $paginated;
    }

    // Si no hay paginación
    $oficinas = $query->get();
    $oficinas->transform(function ($oficina) {
        if (is_null($oficina->correo_electronico)) {
            // $oficina->correo_electronico = auth()->user()->email;
            $oficina->save(); 
        }
        return $oficina;
    });

    return $oficinas;
    }


    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatOficina::create($data);
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
            $prev = (bool) $catalog->bol_eliminado;
            $catalog->bol_eliminado = !$prev;
            $catalog->update();
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó la oficina con ID: '.$catalog->id_oficina.', nombre: "'.$catalog->oficina.'", en el catalogo: Oficinas');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatOficina::where('id_oficina', decrypt($id))->first();
    }
}
