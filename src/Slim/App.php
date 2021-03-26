<?php

namespace App\Slim;

use App\Traits\BootTrait;
use Ronanchilvers\Foundation\Slim\App as BaseApp;

/**
 * Local application subclass
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class App extends BaseApp
{
    use BootTrait;
}
