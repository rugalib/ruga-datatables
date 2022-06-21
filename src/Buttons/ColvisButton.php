<?php

declare(strict_types=1);

namespace Ruga\Datatables\Buttons;

/**
 * Class ColvisButton.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 *
 * $config options:
 * - text:        Displayed on the button
 *
 */
class ColvisButton extends AbstractButton implements ButtonInterface
{
    /**
     * Return the javascript configuration of the button.
     *
     * @return string
     */
    public function renderJavascript(): string
    {
        /**
         * @see https://datatables.net/reference/api/buttons.exportData()
         */
        
        $data = [
            'extend' => 'colvis',
            'columns' => ':visible',
        ];
        
        if ($val = $this->getConfig('text')) {
            $data['text'] = $val;
        }
        
        return json_encode($data);
    }
}