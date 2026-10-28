<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    use HasFactory;
    protected $table = 'inscripciones';
    protected $fillable = ['estudiante_id','paralelo_id','fecha_inscripcion','estado'];
    protected function casts(): array { return ['fecha_inscripcion'=>'date']; }
    
    public function estudiante(): BelongsTo { return $this->belongsTo(Estudiante::class); }
    public function paralelo(): BelongsTo { return $this->belongsTo(Paralelo::class); }
    public function asistencias(): HasMany { return $this->hasMany(Asistencia::class); }
    public function notas(): HasMany { return $this->hasMany(Nota::class); }
    
    public function calcularNotaFinal(): ?float {
        $notas = $this->notas()->pluck('calificacion');
        if($notas->isEmpty()) return null;
        return round($notas->avg(),2);
    }
}