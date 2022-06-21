<?php

declare(strict_types=1);

namespace Ruga\Datatables\Buttons;

use Ruga\Datatables\ConfigurationInterface;
use Ruga\Datatables\ConfigurationTrait;

/**
 * Class HrefButton.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 *
 * $config options:
 * - text:        Displayed on the button
 * - url:         Destination url if button clicked
 *
 */
class HrefButton extends AbstractButton implements ButtonInterface
{
    
    /**
     * Return the javascript configuration of the button.
     *
     * @return string
     */
    public function renderJavascript(): string
    {
        $str = "{";
        $str .= "text: '{$this->getConfig('text')}',";
        $str .= "action: function ( e, dt, node, config ) {";
        $str .= "var url='{$this->getConfig('url')}';";
        $str .= "document.location.href=url;";
        $str .= "}";
        $str .= "}";
        return $str;
    }
}