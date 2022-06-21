<?php

declare(strict_types=1);

namespace Ruga\Datatables\Buttons;

/**
 * Class PrintButton.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 *
 * $config options:
 * - text:        Displayed on the button
 *
 */
class PrintButton extends AbstractButton implements ButtonInterface
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
            'extend' => 'print',
// 			'header' => true,
// 			'footer' => true,
// 			'messageTop' => 'messageTop',
            'messageBottom' => (new \DateTime())->format('c'),
// 			'title' => null,
            'exportOptions' => [
                'columns' => ':visible',
            ],
        ];
        
        if ($val = $this->getConfig('text')) {
            $data['text'] = $val;
        }
        
        return json_encode($data);
    }
}