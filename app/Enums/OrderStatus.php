<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Abierto = 'abierto';
    case EnCocina = 'en_cocina';
    case Listo = 'listo';
    case Servido = 'servido';
    case Cerrado = 'cerrado';

    public function label(): string
    {
        return match ($this) {
            self::Abierto => 'Abierto',
            self::EnCocina => 'En cocina',
            self::Listo => 'Listo',
            self::Servido => 'Servido',
            self::Cerrado => 'Cerrado',
        };
    }
}
