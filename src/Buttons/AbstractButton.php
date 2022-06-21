<?php

declare(strict_types=1);

namespace Ruga\Datatables\Buttons;

use Ruga\Datatables\ConfigurationInterface;
use Ruga\Datatables\ConfigurationTrait;

/**
 * Add a button to the datatables gui.
 */
abstract class AbstractButton implements ConfigurationInterface, ButtonInterface
{
    use ConfigurationTrait;
    
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }
}