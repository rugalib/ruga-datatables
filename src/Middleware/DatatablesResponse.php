<?php

declare(strict_types=1);

namespace Ruga\Datatables\Middleware;

use Laminas\Diactoros\Response;

class DatatablesResponse implements \JsonSerializable
{
    /** @var DatatablesRequest */
    private $request;
    
    /** @var array */
    private $data = [];
    
    /** @var int|null */
    private $recordsTotal = null;
    
    /** @var int|null */
    private $recordsFiltered = null;
    
    /** @var string */
    private $query;
    
    /** @var array */
    private $filter;
    
    
    
    public function __construct(DatatablesRequest $request)
    {
        $this->request = $request;
    }
    
    
    
    public function addRow(array $row)
    {
        $this->data[] = $row;
    }
    
    
    
    public function setRecordsTotal(int $recordsTotal)
    {
        $this->recordsTotal = $recordsTotal;
    }
    
    
    
    public function setRecordsFiltered(int $recordsFiltered)
    {
        $this->recordsFiltered = $recordsFiltered;
    }
    
    
    
    public function setQuery(string $query)
    {
        \Ruga\Log::functionHead();
        $this->query = $query;
    }
    
    
    
    public function getQuery(): string
    {
        return $this->query;
    }
    
    
    
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }
    
    
    
    public function getFilter(): array
    {
        return $this->filter;
    }
    
    
    
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize()
    {
        $o = new \stdClass();
        
        // The draw counter that this object is a response to
        $o->draw = $this->request->getDraw();
        $o->recordsTotal = null; // Total records, before filtering
        $o->recordsFiltered = null; // Total records, after filtering
        $o->error = null; // Optional: If an error occurs during the running of the server-side processing script
        $o->query = $this->query; // Optional: Show query to the developer
        $o->filter = $this->filter ?? $this->request->getFilter(); // Applied filter settings
        $o->data = null; // The data to be displayed in the table.
        
        
        try {
            $o->data = $this->data;
            $o->recordsTotal = $this->recordsTotal ?? count($this->data);
            $o->recordsFiltered = $this->recordsFiltered ?? $this->recordsTotal;
        } catch (\Exception $e) {
            $o->error = $e->getMessage();
        }

//        file_put_contents('tmp/DatatablesResponse.json', json_encode($o, JSON_PRETTY_PRINT));
        
        return $o;
    }
}