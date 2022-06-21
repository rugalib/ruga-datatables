<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

/**
 * A column for static text or html elements.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class StaticColumn extends AbstractColumn implements ColumnInterface
{
    public function __construct(array $config = [])
    {
        $config['orderable'] = $config['orderable'] ?? false;
        $config['searchable'] = $config['searchable'] ?? false;
        $config['defaultContent'] = $config['defaultContent'] ?? '';
        
        $this->setConfig($config);
    }
    
}
