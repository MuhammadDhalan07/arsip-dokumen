<?php

namespace App;

use Illuminate\Support\Facades\Session;

class ArsipDokumen
{
    public static function tahunAktif()
    {
        return Session::get('tahun-aktif', date('Y'));
    }
}
