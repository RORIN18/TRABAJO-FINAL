<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paralelo extends Model
{
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'gestion_id',
        'docente_id',
        'codigo',
        'turno',
        'hora_inicio',
        'hora_fin',
        'horario',
        'cupo',
        'activo',
        'imagen',
    ];

    protected function casts(): array
    {
        return [
            'cupo' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) {
            return asset('storage/'.$this->imagen);
        }

        return asset('storage/defaults/paralelo.png');
    }

    public function hayCruceHorario(self $otro): bool
    {
        if ((int) $this->gestion_id !== (int) $otro->gestion_id) {
            return false;
        }

        if (! $this->hora_inicio || ! $this->hora_fin || ! $otro->hora_inicio || ! $otro->hora_fin) {
            return $this->turno && $otro->turno && $this->turno === $otro->turno;
        }

        return $this->hora_inicio < $otro->hora_fin && $otro->hora_inicio < $this->hora_fin;
    }
}
