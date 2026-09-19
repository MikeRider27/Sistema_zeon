<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Mesero = 'mesero';
    case Cocina = 'cocina';
    case Caja = 'caja';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Mesero => 'Mesero',
            self::Cocina => 'Cocina',
            self::Caja => 'Caja',
        };
    }
}
