<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

use Laminas\Db\Sql\Select;

/**
 * A column by configured for row selection.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class SelectColumn extends AbstractColumn implements ColumnInterface
{
    public function __construct(array $config = [])
    {
        $config['className'] = 'select-checkbox';
        $config['orderable'] = false;
        $config['searchable'] = false;
        
        $config['fullname'] = $config['fullname'] ?? 'Auswahl';
        $config['defaultContent'] = $config['defaultContent'] ?? '';
        
        $this->setConfig($config);
    }
    
    
    
    /**
     * Return the chosen select style.
     *
     * @see https://datatables.net/reference/option/select.style
     * @return SelectStyle
     * @throws \ReflectionException
     */
    public function getSelectStyle(): SelectStyle
    {
        return new SelectStyle($this->getConfig('selectStyle', 'os'));
    }
    
}
