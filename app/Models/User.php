<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\CleanText;
use App\Models\Catalogs\ImCatOficina;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'second_name',
        'acronimo',//TODO: Agregado
        'email',
        'usuario_directorio_activo',//TODO: Agregado
        'puesto',//TODO: Agregado
        'bol_eliminado',//TODO: Agregado
        'password',
        'username',
        'id_oficina',//TODO: Agregado
        'id_usuario_alta',//TODO: Agregado
        'id_usuario_modificacion'//TODO: Agregado
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['hash_id', 'full_name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email' => CleanText::class,
            'username' => CleanText::class,
            'name' => CleanText::class,
            'first_name' => CleanText::class,
            'second_name' => CleanText::class,
        ];
    }

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id);
    }

    public function getFullNameAttribute(): string
    {
        return "$this->name $this->first_name $this->second_name";
    }

    public function usuarioPerfil(){
        return $this->hasOne(ImUsuarioPerfil::class,'id_usuario','id');
    }

    public function oficina(){
        return  $this->hasOne(ImCatOficina::class,'id_oficina','id_oficina');
    }

    public function permissions(){
        return $this->belongsToMany(Permission::class,'user_permission','id_usuario','id_permission');
    }

    public function permissionIds(){
        return $this->hasMany(UserPermission::class,'id_usuario');
    }

        public function scopeSearch($query, $search)
	{
		return $query->when(!empty ($search), function ($query) use ($search) {
            //Aquí agregas una condición personalizada antes
//            $query->where('bol_eliminado', true);


            $this->applyStringSearchColumn($query, 'username', $search);
            // Buscar en username y email con AND
//            $this->applyStringSearchMultiColumns($query, ['username', 'email'], $search);

            // Buscar en el concatenado: name + first_name + last_name
            // Buscar en concatenado como OR
            $this->applySearchConcatenatedColumns($query, ['name', 'first_name', 'second_name'], $search, true);

            // Busca en relación directa
            $this->applySearchRelationColumn($query, 'usuarioPerfil.perfil', 'perfil', $search, true);
            // Busca en relación con múltiples columnas
//            $this->applySearchRelationMultiColumns($query, 'oficina', ['cad_oficina','nombre_corto'], $search, true);

            return $query;

		});
	}

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }

        return $this->permissions->contains('name', $permissionName);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
