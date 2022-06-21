<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

use Ruga\Datatables\ConfigurationInterface;
use Ruga\Datatables\ConfigurationTrait;

/**
 * Abstract column.
 *
 * @see      Column
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 *
 *
 * $config options:
 * - fullname:       Display name of the column.
 * - dbData:         Fieldname to use in serverSide database request.
 * - className:      The css class.
 * - data:           Name of the data property. (@see https://datatables.net/reference/option/columns.data)
 * - name:           Name the column for API use and DB search (if dbData is empty). (@see
 * https://datatables.net/reference/option/columns.name)
 * - orderData:      Allows a column's sorting to take either the data from a different (often hidden) column as the
 * data to sort, or data from multiple columns. (@see https://datatables.net/reference/option/columns.orderData)
 * - orderDir:       If set, order the column in this direction.
 * - defaultContent: Static content for a column. (@see https://datatables.net/reference/option/columns.defaultContent)
 * - orderable:      Disable ability to order by column. (@see
 * https://datatables.net/reference/option/columns.orderable)
 * - searchable:     Disable ability to search by column. (@see
 * https://datatables.net/reference/option/columns.searchable)
 * - visible:        Initial visibility of the column. (@see https://datatables.net/reference/option/columns.visible)
 *
 */
abstract class AbstractColumn implements ColumnInterface, ConfigurationInterface
{
    use ConfigurationTrait;
    
    
    public function __construct(array $config = [])
    {
        if (array_key_exists('orderable', $config)) {
            $config['orderable'] = $this->parseBool($config['orderable']);
        }
        if (array_key_exists('searchable', $config)) {
            $config['searchable'] = $this->parseBool($config['searchable'] ?? true);
        }
        
        $this->setConfig($config);
    }
    
    
    
    /**
     * Returns the name of the column.
     * This name is used to identify the column in the API of datatables. If no name is specified, 'data' will be used.
     * If 'data' is not specified, a lowercase representation of the fullname will be used.
     *
     * @return string
     */
    public function getName(): string
    {
        if ($this->getConfig('name')) {
            return $this->getConfig('name');
        }
        if ($this->getConfig('data')) {
            return $this->getConfig('data');
        }
        if ($this->getConfig('fullname')) {
            return strtolower($this->getConfig('fullname'));
        }
        return '' . uniqid();
    }
    
    
    
    /**
     * Returns the name of the database column to use in serverSide queries for the column.
     * The 'dbData' attribute is transferred to the server with every request.
     *
     * @return string
     */
    public function getDbData(): string
    {
        if ($this->getConfig('dbData')) {
            return $this->getConfig('dbData');
        }
        if ($this->getConfig('data')) {
            return $this->getConfig('data');
        }
        return $this->getName();
    }
    
    
    
    /**
     * Returns true, if the column can be ordered.
     *
     * @return boolean
     */
    public function isOrderable(): bool
    {
        return $this->getConfig('orderable', true);
    }
    
    
    
    /**
     * Returns the order direction or null, if column is currently not selected for ordering.
     *
     * @return OrderDir
     * @throws \ReflectionException
     */
    public function getOrderDir(): OrderDir
    {
        if (!$this->isOrderable()) {
            return OrderDir::NONE();
        }
        return new OrderDir($this->getConfig('orderDir', 'none'));
    }
    
    
    
    /**
     * Returns true, if the column can be used in searches.
     *
     * @return boolean
     */
    public function isSearchable(): bool
    {
        return $this->getConfig('searchable', true);
    }
    
    
    
    /**
     * Return the javascript configuration of the column.
     *
     * @return string
     */
    public function renderJavascript(): string
    {
        $options = ['name' => $this->getName()];
        
        if ($this->getConfig('data') !== null) {
            $options['data'] = $this->getConfig('data');
        }
        
        if ($this->getConfig('defaultContent') !== null) {
            $options['defaultContent'] = $this->getConfig('defaultContent');
        }
        
        if ($this->getConfig('orderData') !== null) {
            $options['orderData'] = $this->getConfig('orderData');
        }
        
        if ($this->getConfig('dbData') !== null) {
            $options['dbData'] = $this->getConfig('dbData');
        }
        
        if ($this->getConfig('className') !== null) {
            $options['className'] = $this->getConfig('className');
        }
        
        $options['orderable'] = (bool)$this->getConfig('orderable', true);
        $options['searchable'] = (bool)$this->getConfig('searchable', true);
        $options['visible'] = (bool)$this->getConfig('visible', true);
        
        
        $str = json_encode($options, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_INVALID_UTF8_IGNORE);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg() . "\n" . var_export($options, true));
        }
        return $str;
    }
    
    
    
    /**
     * Return the html code to insert into the table for the column.
     *
     * @return string
     */
    public function renderHtml(): string
    {
        $str = '<th';
        $str .= ' data-name="' . $this->getName() . '"';


// 		if($this->render !== null)			$str.=' data-render="'.$this->render.'"';
        
        /*
        $class = '';
        if ($this->getConfig('visible') === false) {
            $class .= 'hide';
        }
        if ($this->getConfig('class') !== null) {
            $class .= ' ' . $this->getConfig('class') . '';
        }
        if ($class) {
            $str .= ' data-class="' . trim($class) . '"';
        }
        */
        
        $str .= '>';
        $str .= $this->getConfig('fullname');
        $str .= '</th>';
        return $str;
    }
    
}
