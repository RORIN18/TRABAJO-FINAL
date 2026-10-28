<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Docente extends Model
{
    use HasFactory;
    protected $fillable = ['codigo','ci','nombres','apellidos','correo','telefono','especialidad','activo','imagen'];
    protected function casts(): array { return ['activo'=>'boolean']; }
    public function paralelos(): HasMany { return $this->hasMany(Paralelo::class); }
    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) { return asset('storage/'.$this->imagen); }
        return asset('storage/defaults/avatar.png');
    }
}
