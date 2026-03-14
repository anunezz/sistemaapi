<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

trait SearchableTrait
{
    protected bool $unaccentChecked = false;

    protected function ensureUnaccentExtension(): void
    {
        if ($this->unaccentChecked) {
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            $this->unaccentChecked = true;
            return;
        }

        $exists = DB::selectOne("SELECT EXISTS (SELECT 1 FROM pg_extension WHERE extname = 'unaccent') AS installed");
        if (!$exists->installed) {
            DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
        }

        $this->unaccentChecked = true;
    }

    protected function prepareSearchEnvironment(): array
    {
        $driver = DB::getDriverName();
        $like = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';

        if ($driver === 'pgsql') {
            $this->ensureUnaccentExtension();
        }

        return [$driver, $like];
    }

    public function applyStringSearchColumn(EloquentBuilder|QueryBuilder $query, string $column, string $search, bool $useOrWhere = false)

    {
        [$driver, $like] = $this->prepareSearchEnvironment();

        $clause = $useOrWhere ? 'orWhereRaw' : 'whereRaw';

        if ($driver === 'pgsql') {
            return $query->{$clause}("unaccent($column) $like unaccent(?)", ["%{$search}%"]);
        }

        // Para otros motores
        return $useOrWhere
            ? $query->orWhere($column, $like, "%{$search}%")
            : $query->where($column, $like, "%{$search}%");
    }

    /**
     * Búsqueda en múltiples columnas tipo string, configurable como where/orWhere.
     */
    public function applyStringSearchMultiColumns(EloquentBuilder|QueryBuilder $query, array $columns, string $search, bool $useOrWhere = false): void
    {
        [$driver, $like] = $this->prepareSearchEnvironment();

        $clause = $useOrWhere ? 'orWhere' : 'where';

        $query->{$clause}(function ($q) use ($columns, $search, $like, $driver) {
            foreach ($columns as $column) {
                if ($driver === 'pgsql') {
                    $q->orWhereRaw("unaccent($column) $like unaccent(?)", ["%{$search}%"]);
                } else {
                    $q->orWhere($column, $like, "%{$search}%");
                }
            }
        });
    }

    /**
     * Búsqueda segura sobre concatenación de columnas, configurable como where/orWhere.
     */
    public function applySearchConcatenatedColumns(EloquentBuilder|QueryBuilder $query, array $columns, string $search, bool $useOrWhere = false): void
    {
        [$driver, $like] = $this->prepareSearchEnvironment();

        $clause = $useOrWhere ? 'orWhereRaw' : 'whereRaw';

        $concat = "CONCAT(" . implode(", ' ', ", $columns) . ")";

        if ($driver === 'pgsql') {
            $query->{$clause}("unaccent($concat) $like unaccent(?)", ["%{$search}%"]);
        } else {
            $query->{$clause}("$concat $like ?", ["%{$search}%"]);
        }
    }

    /**
     * Búsqueda segura en columnas de una relación (hasOne, belongsTo, etc.).
     *
     * @param EloquentBuilder $query
     * @param string $relation Nombre de la relación
     * @param string $column Nombre de la columna de la relación
     * @param string $search Texto a buscar
     * @param bool $useOrWhere Si debe usarse como OR (por defecto es AND)
     */
    public function applySearchRelationColumn(EloquentBuilder $query, string $relation, string $column, string $search, bool $useOrWhere = false): void
    {
        [$driver, $like] = $this->prepareSearchEnvironment();

        $method = $useOrWhere ? 'orWhereHas' : 'whereHas';

        $query->{$method}($relation, function ($q) use ($column, $search, $driver, $like) {
            if ($driver === 'pgsql') {
                $q->whereRaw("unaccent($column) $like unaccent(?)", ["%{$search}%"]);
            } else {
                $q->where($column, $like, "%{$search}%");
            }
        });
    }

    public function applySearchRelationMultiColumns(
        EloquentBuilder $query, string $relation, array $columns, string $search, bool $useOrWhere = false): void
    {
        [$driver, $like] = $this->prepareSearchEnvironment();

        $method = $useOrWhere ? 'orWhereHas' : 'whereHas';

        $query->{$method}($relation, function ($q) use ($columns, $search, $driver, $like) {
            $q->where(function ($subQ) use ($columns, $search, $driver, $like) {
                foreach ($columns as $column) {
                    if ($driver === 'pgsql') {
                        $subQ->orWhereRaw("unaccent($column) $like unaccent(?)", ["%{$search}%"]);
                    } else {
                        $subQ->orWhere($column, $like, "%{$search}%");
                    }
                }
            });
        });
    }

}
