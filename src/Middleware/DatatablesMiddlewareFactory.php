<?php

declare(strict_types=1);

namespace Ruga\Datatables\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Ruga\Datatables\DatasourcePlugins\DatasourcePluginManager;

/**
 * This factory creates a DatatablesMiddleware. DatatablesMiddleware is responsible for handling all the requests for
 * datatables serverSide processing.
 *
 * @see     DatatablesMiddleware
 * @author  Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class DatatablesMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        $middleware = new DatatablesMiddleware($container->get(DatasourcePluginManager::class));
        return $middleware;
    }
}
