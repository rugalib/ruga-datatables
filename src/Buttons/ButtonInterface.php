<?php

declare(strict_types=1);

namespace Ruga\Datatables\Buttons;


/**
 * Interface to a button.
 *
 * @see      AbstractButton
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface ButtonInterface
{
    /**
     * Return the javascript configuration of the button.
     *
     * @return string
     */
    public function renderJavascript(): string;
}
