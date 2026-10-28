<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Carrera extends Model
{
    use HasFactory;
    protected $fillable = ['codigo','nombre','duracion_anios','descripcion','activa','imagen'];
    protected function casts(): array { return ['activa'=>'boolean']; }
    public function materias(): HasMany { return $this->hasMany(Materia::class); }
    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) { return asset('storage/'.$this->imagen); }
        return asset('storage/defaults/carrera.png');
    }
}
