<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

use Ruga\Std\Enum\AbstractEnum;

/**
 * Class OrderDir
 *
 * @method static self ASC()
 * @method static self DESC()
 * @method static self NONE()
 */
class OrderDir extends AbstractEnum
{
    const ASC = 'asc';
    const DESC = 'desc';
    const NONE = 'none';
}