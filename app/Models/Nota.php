<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    use HasFactory;
    protected $fillable = ['inscripcion_id','tipo','calificacion','fecha_evaluacion'];
    protected function casts(): array { return ['calificacion'=>'decimal:2','fecha_evaluacion'=>'date']; }
    public function inscripcion(): BelongsTo { return $this->belongsTo(Inscripcion::class); }
}