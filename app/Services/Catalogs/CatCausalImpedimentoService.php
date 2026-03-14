<?php

namespace App\Services\Catalogs;

use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Traits\BinnacleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CatCausalImpedimentoService
{
    use BinnacleTrait;
    public function getAll(Request $request)
    {
        $filters = (object)$request->all();
        $query = ImCatCausalImpedimento::with('cat_subcausal_impedimento')->search($filters);

        if ($request->has('rowsPerPage')) {
            return $query->orderBy('updated_at', 'asc')->paginate($request->get('rowsPerPage', 10));
        }
        return $query->where('bol_eliminado', false)->get();
    }

    public function create(array $data)
    {
        $user = Auth::user();
        // Validación para evitar nombres duplicados
        if (mb_strlen(trim($data['causal_impedimento']), 'UTF-8') > 300) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subcausal_impedimento' => ['El texto excede el límite permitido de 120 caracteres.']
            ]);
        }
        $exists = ImCatCausalImpedimento::whereRaw('LOWER(causal_impedimento) = LOWER(?)',[$data['causal_impedimento']])
        ->exists();
    if ($exists) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'nombre' => ['Ya existe un registro similar, favor de verificar.']
        ]);
    }
        $data['id_usuario_alta'] = $user ? $user->id : null;
        return ImCatCausalImpedimento::create($data);
    }

    public function update(string $id, array $data)
    {
        $catalog = $this->findById($id);
        if ($catalog) {
            if (mb_strlen(trim($data['causal_impedimento']), 'UTF-8') > 300) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subcausal_impedimento' => ['El texto excede el límite permitido de 120 caracteres.']
            ]);
        }
            $exists = ImCatCausalImpedimento::whereRaw('LOWER(causal_impedimento) = LOWER(?)',[$data['causal_impedimento']])
            ->where('id_causal_impedimento', '!=', $catalog->id_causal_impedimento)
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'nombre' => ['Ya existe un registro similar, favor de verificar.']
            ]);
        }

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
            $this->guardarMovimiento(Auth::user()->id,10,4,'Se eliminó la causal ID: '.$catalog->id_causal_impedimento.', nombre: "'.$catalog->causal_impedimento.'", en el catalogo: Causales');
            return true;
        }
        return false;
    }

    public function findById(string $id)
    {
        return ImCatCausalImpedimento::where('id_causal_impedimento', decrypt($id))->first();
    }
}
