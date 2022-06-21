<?php

declare(strict_types=1);


namespace Ruga\Datatables\DatasourcePlugins;


use Laminas\Db\Sql\Having;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Ruga\Datatables\Columns\Column;
use Ruga\Datatables\Columns\OrderDir;
use Ruga\Datatables\Exception\InvalidTableException;
use Ruga\Datatables\Middleware\DatatablesRequest;
use Ruga\Datatables\Middleware\DatatablesResponse;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Db\Row\RowInterface;
use Ruga\Db\Table\AbstractTable;
use Ruga\Db\Table\TableInterface;

/**
 * The model plugin handles all the requests directed at the already existing database model.
 * Class name of the table is expected at the second position in the uri of the serverSide request.
 *
 * @see      ModelFactory
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Model implements DatasourcePluginInterface
{
    /** @var AdapterInterface */
    private $adapter;
    
    
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    
    
    /**
     * Handle the request from datatables and return the response.
     *
     * @param DatatablesRequest $datatablesRequest
     *
     * @return DatatablesResponse
     * @throws \Exception
     */
    public function process(DatatablesRequest $datatablesRequest): DatatablesResponse
    {
        \Ruga\Log::functionHead($this);
        
        $datatablesResponse = new DatatablesResponse($datatablesRequest);
        /** @var AbstractTable $table */
        $table = $this->getModelFromRequest($datatablesRequest);
        
        /** @var Select $select */
        $select = $table->getSql()->select();
//        $datatablesResponse->setQuery($select->getSqlString($table->getAdapter()->getPlatform()));
        
        // Customize sql
        $customSqlSelectName = $this->getCustomSqlSelectNameFromRequest($datatablesRequest);
        if ($customSqlSelectName && method_exists($table, 'customizeSqlSelectFromRequest')) {
//            \Ruga\Log::log_msg("\$customSqlSelectName={$customSqlSelectName}");
            $table->customizeSqlSelectFromRequest($customSqlSelectName, $select, $datatablesRequest->getRequest());
        }
        
        // Reset parts used by datatables
//        $select->reset(Select::GROUP);
        $select->reset(Select::ORDER);
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        
        // Count records without filter and search applied
        $datatablesResponse->setRecordsTotal(count($table->selectWith($select)));
//        $datatablesResponse->setQuery($select->getSqlString($table->getAdapter()->getPlatform()));
        
        // Apply filter form
        $filter = $datatablesRequest->getFilter();
        if (method_exists($table, 'applyFilterToSqlSelect')) {
//            \Ruga\Log::log_msg('$filter=' . print_r($filter, true));
            $table->applyFilterToSqlSelect($filter, $select);
            $datatablesResponse->setFilter($filter);
        }
        
        // Count records with filter applied
        $datatablesResponse->setRecordsFiltered(count($table->selectWith($select)));
//        $datatablesResponse->setQuery($select->getSqlString($table->getAdapter()->getPlatform()));
        
        // Apply search pattern
        $select->having(
            function (Having $having) use ($datatablesRequest) {
                $globalFilter = $having->NEST;
                $globalSearch = $datatablesRequest->getSearch();
                
                $columnFilter = $having->NEST;
                $columnSearch = '';
                
                /** @var Column $column */
                foreach ($datatablesRequest->getColumns() as $column) {
                    if (!empty($globalSearch) && $column->isSearchable()) {
                        $globalFilter->OR->like($column->getDbData(), "%{$globalSearch}%");
                    }
                    if (!empty($columnSearch) && $column->isSearchable()) {
                        $columnFilter->like($column->getDbData(), "%");
                    }
                }
                
                if ($globalFilter->count() == 0) {
                    $globalFilter->expression('TRUE', []);
                }
                if ($columnFilter->count() == 0) {
                    $columnFilter->expression('TRUE', []);
                }
            }
        );

//        $datatablesResponse->setQuery($select->getSqlString($table->getAdapter()->getPlatform()));
        
        // Apply order
        $aOrder = [];
        /** @var Column $column */
        foreach ($datatablesRequest->getColumns() as $column) {
            if ($column->getOrderDir() != OrderDir::NONE) {
                $aOrder[$column->getDbData()] = $column->getOrderDir();
            }
        }
        if (!empty($aOrder)) {
            $select->order($aOrder);
        }
        
        // Apply offset and limit
        $select->offset($datatablesRequest->getStart());
        if ($datatablesRequest->getLength() >= 0) {
            $select->limit($datatablesRequest->getLength());
        }
        
        $datatablesResponse->setQuery($select->getSqlString($table->getAdapter()->getPlatform()));
        
        /** @var RowInterface $row */
        foreach ($table->selectWith($select) as $row) {
            $datatablesResponse->addRow($row->toArray());
        }
        
        return $datatablesResponse;
    }
    
    
    
    /**
     * Find and instantiate the model by information from the serverSide request.
     *
     * @param DatatablesRequest $datatablesRequest
     *
     * @return TableInterface
     */
    public function getModelFromRequest(DatatablesRequest $datatablesRequest): TableInterface
    {
        if (count($datatablesRequest->getRequestPathParts()) < 2) {
            throw new InvalidTableException("No table specified", 400);
        }
        
        $modelName = $datatablesRequest->getRequestPathParts()[1] ?? null;
        
        try {
            return $this->adapter->tableFactory($modelName);
        } catch (ServiceNotFoundException $e) {
            throw new InvalidTableException("Model {$modelName} not found", 404);
        }
    }
    
    
    
    /**
     * Find the name of the customization for the Sql Select object.
     *
     * @param DatatablesRequest $datatablesRequest
     *
     * @return string|null
     */
    public function getCustomSqlSelectNameFromRequest(DatatablesRequest $datatablesRequest): ?string
    {
        return $datatablesRequest->getRequestPathParts()[2] ?? null;
    }
    
}