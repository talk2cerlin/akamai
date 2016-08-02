<?php

namespace Akamai\Util;

use Akamai\Services\TokenGenerator;

class Token
{

    public function generateVideoToken($duration, $type = "hdnea")
    {
        if (!in_array($type, ['hdnts', 'hdnea'])) {
            $type = "hdnts";
        }
        $this->tokenGenerator = new TokenGenerator($duration);
        return strtolower($type) . "=" . $this->tokenGenerator->getToken();
    }
}
