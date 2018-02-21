<?php

namespace CryptoPolice\Platform\Classes;

class Helpers
{

    public function setImagePath($diskName)
    {
        return '/storage/app/uploads/public/' . substr($diskName, 0, 3) . '/' . substr($diskName, 3, 3) . '/' . substr($diskName, 6, 3) . '/' . $diskName;
    }

}