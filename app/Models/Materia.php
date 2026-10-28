<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Materia extends Model
{
    use HasFactory;
    protected $fillable = ['carrera_id','codigo','nombre','semestre','descripcion','activa','imagen'];
    protected function casts(): array { return ['activa'=>'boolean']; }
    public function carrera(): BelongsTo { return $this->belongsTo(Carrera::class); }
    public function paralelos(): HasMany { return $this->hasMany(Paralelo::class); }
    public function prerrequisitos(): BelongsToMany { return $this->belongsToMany(Materia::class,'materia_prerrequisitos','materia_id','prerrequisito_id'); }
    public function materiasDependientes(): BelongsToMany { return $this->belongsToMany(Materia::class,'materia_prerrequisitos','prerrequisito_id','materia_id'); }
    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) { return asset('storage/'.$this->imagen); }
        return asset('storage/defaults/materia.png');
    }
}
