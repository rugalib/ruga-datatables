<?php

declare(strict_types=1);

namespace Ruga\Datatables\DatasourcePlugins;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Ruga\Datatables\DatasourcePlugins\DatasourcePluginManager;
use Ruga\Datatables\Datatable;

/**
 * @see     DatasourcePluginManager
 * @author  Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class DatasourcePluginManagerFactory
{
    public function __invoke(ContainerInterface $container): DatasourcePluginManager
    {
        $config = ($container->get('config') ?? [])[Datatable::class]['datasourcePlugins'] ?? [];
        return new DatasourcePluginManager($container, $config);
    }
}
