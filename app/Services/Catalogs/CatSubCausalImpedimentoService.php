<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatSubCausalImpedimento;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatSubCausalImpedimentoService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatSubCausalImpedimento::with('cat_causal_impedimento')->search($filters)->orderBy('subcausal_impedimento', 'asc');

        if ($request->has('rowsPerPage')) {
            return $query->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $data['id_usuario_alta'] = $user ? $user->id : null;

        if (!empty($data['subcausal_impedimento'])) {
            if (mb_strlen(trim($data['subcausal_impedimento']), 'UTF-8') > 120) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subcausal_impedimento' => ['El texto excede el límite permitido de 120 caracteres.']
            ]);
        }
        $exists = ImCatSubCausalImpedimento::whereRaw('LOWER(subcausal_impedimento) = ?', [strtolower($data['subcausal_impedimento'])])->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subcausal_impedimento' => ['Ya existe un registro similar, favor de verificar.']
            ]);
        }
    }

    return ImCatSubCausalImpedimento::create($data);
    }

    public function update(string $id, array $data)
    {
        $catalog = $this->findById($id);
        if ($catalog) {

            $user = Auth::user();
            $data['id_usuario_modificacion'] = $user ? $user->id : null;
            if (!empty($data['subcausal_impedimento'])) {
                if (mb_strlen(trim($data['subcausal_impedimento']), 'UTF-8') > 120) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'subcausal_impedimento' => ['El texto excede el límite permitido de 120 caracteres.']
                    ]);
                }   
            }
            $exists = ImCatSubCausalImpedimento::whereRaw('LOWER(subcausal_impedimento) = ?', [strtolower($data['subcausal_impedimento'])])->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subcausal_impedimento' => ['Ya existe un registro similar, favor de verificar.']
            ]);
        }
            $catalog->update($data);
        }
        return $catalog;
    }

    public function delete(string $id)
    {
        $catalog = $this->findById($id);
         // Estado previo para el log
            if ($catalog) {
            $prev = (bool) $catalog->bol_eliminado;
            $catalog->bol_eliminado = !$prev;
            $catalog->update();
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó la subcausal con ID: '.$catalog->id_subcausal_impedimento.', nombre: "'.$catalog->subcausal_impedimento.'", en el catalogo: Subcausales');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatSubCausalImpedimento::where('id_subcausal_impedimento', decrypt($id))->first();
    }
}
