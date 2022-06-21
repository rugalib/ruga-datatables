<?php

declare(strict_types=1);

namespace Ruga\Datatables\Columns;

/**
 * Interface to a column.
 *
 * @see      AbstractColumn
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface ColumnInterface
{
    /**
     * Return the html code to insert into the table for the column.
     *
     * @return string
     */
    public function renderHtml(): string;
    
    
    
    /**
     * Return the javascript configuration of the column.
     *
     * @return string
     */
    public function renderJavascript(): string;
    
    
    
    /**
     * Returns the name of the column.
     * This name is used to identify the column in the API of datatables. If no name is specified, 'data' will be used.
     * If 'data' is not specified, a lowercase representation of the fullname will be used.
     *
     * @return string
     */
    public function getName(): string;
    
    
    
    /**
     * Returns the name of the database column to use in serverSide queries for the column.
     * The 'dbData' attribute is transferred to the server with every request.
     *
     * @return string
     */
    public function getDbData(): string;
    
    
    
    /**
     * Returns true, if the column can be ordered.
     *
     * @return boolean
     */
    public function isOrderable(): bool;
    
    
    
    /**
     * Returns the order direction or null, if column is currently not selected for ordering.
     *
     * @return OrderDir|null
     */
    public function getOrderDir(): ?OrderDir;
    
    
    
    /**
     * Returns true, if the column can be used in searches.
     *
     * @return boolean
     */
    public function isSearchable(): bool;
    
}
