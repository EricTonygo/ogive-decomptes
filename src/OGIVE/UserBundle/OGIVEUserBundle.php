<?php

namespace OGIVE\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OGIVEUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
