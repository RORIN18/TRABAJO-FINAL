<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estudiante extends Model
{
    use HasFactory;

    protected $fillable = ['carrera_id','codigo','ci','nombres','apellidos','correo','telefono','fecha_nacimiento','imagen','estado'];

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function scopeFiltro(Builder $q, array $f)
    {
        if (!empty($f['q'])) {
            $s = $f['q'];
            $q->where(function ($qq) use ($s) {
                $qq->where('codigo', 'like', "%$s%")
                   ->orWhere('nombres', 'like', "%$s%")
                   ->orWhere('apellidos', 'like', "%$s%");
            });
        }
        return $q;
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen && Storage::disk('public')->exists($this->imagen)) {
            return Storage::url($this->imagen);
        }
        return '/storage/defaults/avatar.png';
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}
