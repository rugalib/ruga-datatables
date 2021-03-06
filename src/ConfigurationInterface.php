<?php

declare(strict_types=1);

namespace Ruga\Datatables;

/**
 * Interface to a configuration template.
 *
 * @see      ConfigurationTrait
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface ConfigurationInterface
{
    /**
     * Store the config.
     *
     * @param array $config
     *
     * @return mixed
     */
    public function setConfig(array $config);
    
    
    
    /**
     * Returns a value from $config or the $default if the key is not found.
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getConfig(string $name, $default = null);
    
    
    
    /**
     * Returns a value from $config as a JS boolean string.
     *
     * @param string $name
     * @param bool   $default
     *
     * @return string
     */
    public function getConfigAsJsBoolean(string $name, bool $default);
    
    
}
