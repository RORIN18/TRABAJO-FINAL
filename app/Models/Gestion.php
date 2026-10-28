<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gestion extends Model
{
    use HasFactory;
    protected $table = 'gestiones';
    protected $fillable = ['codigo','fecha_inicio','fecha_fin','vigente'];
    protected function casts(): array { return ['fecha_inicio'=>'date','fecha_fin'=>'date','vigente'=>'boolean']; }
    public function paralelos(): HasMany { return $this->hasMany(Paralelo::class); }
    public function scopeVigente($q){ return $q->where('vigente',true); }
}