<?php

declare(strict_types=1);

namespace Ruga\Datatables\DatasourcePlugins;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * The DatasourcePluginManager loads plugin classes based on the first component of the serverSide uri.
 * The plugin then handles the serverSide request and returns a DatasourceResponse.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class DatasourcePluginManager extends AbstractPluginManager
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = DatasourcePluginInterface::class;
    
}