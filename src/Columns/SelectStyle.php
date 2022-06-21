<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

use Ruga\Std\Enum\AbstractEnum;

/**
 * Class SelectStyle
 *
 * @see     https://datatables.net/reference/option/select.style
 */
class SelectStyle extends AbstractEnum
{
    const API = 'api';
    const SINGLE = 'single';
    const MULTI = 'multi';
    const OS = 'os';
    const MULTISHIFT = 'multi+shift';
    
}