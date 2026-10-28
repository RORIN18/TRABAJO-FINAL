<?php
namespace Database\Factories;
use App\Models\{Asistencia,Inscripcion};
use Illuminate\Database\Eloquent\Factories\Factory;
class AsistenciaFactory extends Factory {
    protected $model = Asistencia::class;
    public function definition(): array {
        return [
            'inscripcion_id'=>Inscripcion::inRandomOrder()->first()?->id ?? Inscripcion::factory(),
            'fecha'=>fake()->dateTimeBetween('-3 months','now')->format('Y-m-d'),
            'presente'=>fake()->boolean(85),
            'observacion'=>null,
        ];
    }
}