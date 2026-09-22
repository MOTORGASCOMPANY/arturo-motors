<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlacaPeruana implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('La placa es obligatoria.');
            return;
        }

        $placaNormalizada = $this->normalizar($value);

        if (!$this->esFormatoValido($placaNormalizada)) {
            $fail('La placa no tiene un formato válido para Perú. Use formatos como ABC-123 (particulares) o AB-1234 (motos).');
        }
    }

    /**
     * Normaliza la placa: mayúsculas, quita espacios, asegura guión correcto.
     */
    public function normalizar(string $placa): string
    {
        $placa = strtoupper(trim($placa));
        $placa = str_replace(' ', '', $placa);

        // Detectar formato ya con guiones
        if (str_contains($placa, '-')) {
            return $placa;
        }

        // Sin guiones - detectar patrón y agregar guiones
        // ORDEN IMPORTANTE: Especiales PRIMERO (empiezan con E), luego Particular, luego Menores
        
        // Especiales: E + 2 letras + 3 números (EAB123 -> E-AB-123)
        if (preg_match('/^E[A-Z]{2}[0-9]{3}$/', $placa)) {
            return 'E-' . substr($placa, 1, 2) . '-' . substr($placa, 3, 3);
        }

        // Particular: 3 letras + 3 números (ABC123 -> ABC-123)
        if (preg_match('/^[A-Z]{3}[0-9]{3}$/', $placa)) {
            return substr($placa, 0, 3) . '-' . substr($placa, 3, 3);
        }

        // Menores (motos): 2 letras + 4 números (AB1234 -> AB-1234) O 4 números + 2 letras (1234AB -> 1234-AB)
        if (preg_match('/^[A-Z]{2}[0-9]{4}$/', $placa)) {
            return substr($placa, 0, 2) . '-' . substr($placa, 2, 4);
        }
        if (preg_match('/^[0-9]{4}[A-Z]{2}$/', $placa)) {
            return substr($placa, 0, 4) . '-' . substr($placa, 4, 2);
        }

        // Si ya tiene guiones pero formato raro, devolver como está para validación
        return $placa;
    }

    /**
     * Verifica si el formato es válido según estándares peruanos.
     */
    public function esFormatoValido(string $placa): bool
    {
        // Particular / Livianos / Pesados: ABC-123 (zona registral en primera letra)
        if (preg_match('/^[A-Z]{3}-[0-9]{3}$/', $placa)) {
            $zona = $placa[0];
            $zonasValidas = ['A','B','C','D','F','H','L','M','P','S','T','U','V','W','X','Y','Z'];
            return in_array($zona, $zonasValidas);
        }

        // Menores (motos, mototaxis): AB-1234 o 1234-AB
        if (preg_match('/^[A-Z]{2}-[0-9]{4}$/', $placa)) {
            return true;
        }
        if (preg_match('/^[0-9]{4}-[A-Z]{2}$/', $placa)) {
            return true;
        }

        // Especiales: E-AB-123 (diplomáticos, emergencia, gobierno)
        if (preg_match('/^E-[A-Z]{2}-[0-9]{3}$/', $placa)) {
            $subtipo = substr($placa, 2, 2); // E-AB-123 -> pos 2,3 = AB
            $subtiposValidos = ['UA','EX','CD','GA','PE','PB','PC','PD'];
            return in_array($subtipo, $subtiposValidos);
        }

        return false;
    }

    /**
     * Obtiene el tipo de vehículo según la placa.
     */
    public function getTipoVehiculo(string $placa): ?string
    {
        $normalizada = $this->normalizar($placa);

        if (preg_match('/^[A-Z]{3}-[0-9]{3}$/', $normalizada)) {
            return 'particular'; // autos, camionetas, buses, camiones
        }

        if (preg_match('/^[A-Z]{2}-[0-9]{4}$/', $normalizada) || preg_match('/^[0-9]{4}-[A-Z]{2}$/', $normalizada)) {
            return 'menor'; // motos, mototaxis
        }

        if (preg_match('/^E-[A-Z]{2}-[0-9]{3}$/', $normalizada)) {
            return 'especial'; // diplomático, emergencia, gobierno
        }

        return null;
    }

    /**
     * Obtiene la zona registral (solo para placas particulares).
     */
    public function getZonaRegistral(string $placa): ?string
    {
        $normalizada = $this->normalizar($placa);
        
        if (preg_match('/^([A-Z])[A-Z]{2}-[0-9]{3}$/', $normalizada, $matches)) {
            $zonas = [
                'A' => 'Lima y Callao',
                'B' => 'Lima y Callao',
                'C' => 'Lima y Callao',
                'D' => 'Lima y Callao',
                'F' => 'Lima y Callao',
                'H' => 'Áncash',
                'L' => 'Loreto',
                'M' => 'Amazonas, Cajamarca, Lambayeque',
                'P' => 'Tumbes, Piura',
                'S' => 'San Martín',
                'T' => 'La Libertad',
                'U' => 'Ucayali',
                'V' => 'Arequipa',
                'W' => 'Huánuco, Junín, Pasco',
                'X' => 'Apurímac, Cusco, Madre de Dios',
                'Y' => 'Ayacucho, Ica, Huancavelica',
                'Z' => 'Moquegua, Puno, Tacna',
            ];
            return $zonas[$matches[1]] ?? 'Desconocida';
        }

        return null;
    }
}