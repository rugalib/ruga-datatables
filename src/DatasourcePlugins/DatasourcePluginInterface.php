<?php

declare(strict_types=1);

namespace Ruga\Datatables\DatasourcePlugins;

use Ruga\Datatables\Middleware\DatatablesRequest;
use Ruga\Datatables\Middleware\DatatablesResponse;

interface DatasourcePluginInterface
{
    /**
     * Handle the request from datatables and return the response.
     *
     * @param DatatablesRequest $datatablesRequest
     *
     * @return DatatablesResponse
     */
    public function process(DatatablesRequest $datatablesRequest): DatatablesResponse;
}