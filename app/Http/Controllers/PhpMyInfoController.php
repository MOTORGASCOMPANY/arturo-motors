<?php

namespace App\Http\Controllers;

class PhpMyInfoController extends Controller
{
    /**
     * phpinfo() imprime directo al buffer de salida; no se retorna nada
     * para no enviar body extra a la response (igual que el closure original).
     */
    public function __invoke(): void
    {
        phpinfo();
    }
}
