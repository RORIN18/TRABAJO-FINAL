<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;
    protected $fillable = ['inscripcion_id','fecha','presente','observacion'];
    protected function casts(): array { return ['fecha'=>'date','presente'=>'boolean']; }
    public function inscripcion(): BelongsTo { return $this->belongsTo(Inscripcion::class); }
}