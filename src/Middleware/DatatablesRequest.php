<?php

declare(strict_types=1);

namespace Ruga\Datatables\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Ruga\Datatables\Columns\Column;

/**
 * Class DatatablesRequest
 *
 * @see     https://datatables.net/manual/server-side
 */
class DatatablesRequest
{
    /** @var ServerRequestInterface */
    private $request;
    
    /** @var array */
    private $data;
    
    
    
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        
        if ($this->request->getMethod() == 'GET') {
            $this->data = (array)$this->request->getQueryParams() ?? null;
        } else {
            $this->data = (array)$this->request->getParsedBody() ?? null;
        }
//        file_put_contents('tmp/DatatablesRequest.json', json_encode($this->data, JSON_PRETTY_PRINT));
    }
    
    
    
    /**
     * Return an array containing all the path components.
     *
     * @return array
     */
    public function getRequestPathParts(): array
    {
        $uriPath = trim($this->request->getUri()->getPath(), " /\\");
        return explode('/', $uriPath);
    }
    
    
    
    /**
     * Returns the alias name of the desired datasource plugin.
     *
     * @return string
     */
    public function getPluginAlias(): string
    {
        return $this->getRequestPathParts()[0] ?? '';
    }
    
    
    
    /**
     * Return an array of all the columns.
     * The columns are created from the serverSide request and do not necessarily contain alle the attributes.
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = (array)($this->data['columns'] ?? []);
        $order = (array)($this->data['order'] ?? []);
        
        foreach ($order as $orderItem) {
            $columns[$orderItem['column']]['orderDir'] = $orderItem['dir'];
        }
        
        $a = [];
        foreach ($columns as $column) {
            $a[] = new Column($column);
        }
        
        return $a;
    }
    
    
    
    /**
     * Return the draw attribute.
     * Draw counter. This is used by DataTables to ensure that the Ajax returns from server-side processing requests
     * are drawn in sequence by DataTables
     *
     * @see https://datatables.net/manual/server-side
     * @return int
     */
    public function getDraw(): int
    {
        return (int)($this->data['draw'] ?? -1);
    }
    
    
    
    /**
     * Return the start attribute.
     * Paging first record indicator.
     *
     * @see https://datatables.net/manual/server-side
     * @return int
     */
    public function getStart(): int
    {
        return (int)($this->data['start'] ?? 0);
    }
    
    
    
    /**
     * Return the length attribute.
     * Number of records that the table can display in the current draw.
     *
     * @see https://datatables.net/manual/server-side
     * @return int
     */
    public function getLength(): int
    {
        return (int)($this->data['length'] ?? -1);
    }
    
    
    
    /**
     * Return the global search string.
     * To be applied to all columns which have searchable as true.
     *
     * @see https://datatables.net/manual/server-side
     * @return string
     */
    public function getSearch(): string
    {
        return (string)(($this->data['search'] ?? [])['value'] ?? '');
    }
    
    
    
    /**
     * Return the filter form data.
     *
     * @return array
     */
    public function getFilter(): array
    {
        if (!is_array($this->data['filter'] ?? null)) {
            return [];
        }
        return $this->data['filter'];
    }
    
    
    
    /**
     * Returns the original request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}