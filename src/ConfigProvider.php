<?php

declare(strict_types=1);

namespace Ruga\Datatables;

use Ruga\Datatables\DatasourcePlugins\DatasourcePluginManager;
use Ruga\Datatables\DatasourcePlugins\DatasourcePluginManagerFactory;
use Ruga\Datatables\DatasourcePlugins\Model;
use Ruga\Datatables\DatasourcePlugins\ModelFactory;
use Ruga\Datatables\Middleware\DatatablesMiddleware;
use Ruga\Datatables\Middleware\DatatablesMiddlewareFactory;

/**
 * ConfigProvider.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * @see    https://docs.mezzio.dev/mezzio/v3/features/container/config/
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'ruga' => [
                'asset' => [
                    'rugalib/ruga-datatables' => [
                        'scripts' => ['populate.js'],
                        'stylesheets' => [],
                    ],
                ],
            ],
            'dependencies' => [
                'services' => [],
                'aliases' => [],
                'factories' => [
                    DatatablesMiddleware::class => DatatablesMiddlewareFactory::class,
                    DatasourcePluginManager::class => DatasourcePluginManagerFactory::class,
                ],
                'invokables' => [],
                'delegators' => [],
            ],
            Datatable::class => [
                'datasourcePlugins' => [
                    'aliases' => [
                        'model' => Model::class,
                    ],
                    'factories' => [
                        Model::class => ModelFactory::class,
                    ],
                ],
            ],
        ];
    }
}
