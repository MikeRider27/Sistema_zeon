<?php

namespace App\Enums;

enum OrderItemStatus: string
{
    case Pendiente = 'pendiente';
    case Preparando = 'preparando';
    case Listo = 'listo';
    case Entregado = 'entregado';
}
