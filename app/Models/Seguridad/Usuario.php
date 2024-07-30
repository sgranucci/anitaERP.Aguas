<?php

namespace App\Models\Seguridad;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Admin\Rol;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Usuario extends Authenticatable
{
    use Notifiable;
    protected $remember_token = false;
    protected $table = 'usuario';
    protected $fillable = ['usuario', 'nombre', 'email', 'password', 'foto'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol');
    }

    public function setSession($roles)
    {
        Session::put([
            'usuario' => $this->usuario,
            'usuario_id' => $this->id,
            'nombre_usuario' => $this->nombre,
            'foto_usuario' => $this->foto
        ]);
        if (count($roles) == 1) {
            Session::put(
                [
                    'rol_id' => $roles[0]['id'],
                    'rol_nombre' => $roles[0]['nombre'],
                ]
            );
        } else {
            Session::put('roles', $roles);
        }
    }

    public static function setFoto($foto, $actual = false)
    {
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/fotos_usuarios/$actual");
            }
            $imageName = Str::random(20) . '.jpg';
            $imagen = Image::make($foto)->encode('jpg', 75);
            $imagen->resize(300, 300, function ($constraint) {
                $constraint->upsize();
            });
            Storage::disk('public')->put("imagenes/fotos_usuarios/$imageName", $imagen->stream());
            return $imageName;
        } else {
            return false;
        }
    }

    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }
}
