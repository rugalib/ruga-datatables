<?php

declare(strict_types=1);

namespace Ruga\Datatables;

use Ruga\Datatables\Buttons\ButtonInterface;
use Ruga\Datatables\Columns\ColumnInterface;
use Ruga\Datatables\Columns\OrderDir;
use Ruga\Datatables\Columns\SelectColumn;

/**
 * Class Datatable
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 *
 * $config options:
 *  id:            Define html id
 *  style:         Use predefined style (default, compact)
 *  debug:         Create debug button
 *  customSqlData: Data for the custom sql
 *
 *
 *  ajax.url       URL of the data endpoint (@see https://datatables.net/reference/option/ajax)
 *  ajax.type      Method to use for ajax request (@see https://datatables.net/reference/option/ajax)
 *  ajax.dataSrc   Name of the attribute containing the data (@see https://datatables.net/reference/option/ajax)
 *  rowId:         Data property name for the tr.id (@see https://datatables.net/reference/option/rowId)
 *  paging:        Enable/Disable paging (@see https://datatables.net/reference/option/paging)
 *  serverSide:    Control server-side processing mode (@see https://datatables.net/reference/option/serverSide)
 *  stateSave:     Enable or disable state saving (@see https://datatables.net/reference/option/stateSave)
 *  fixedHeader:   Stick headers on top of page when scrolling (@see
 *  https://datatables.net/reference/option/fixedHeader)
 *
 */
class Datatable implements ConfigurationInterface
{
    use ConfigurationTrait;
    
    /** @var string Id of the html element. */
    private $id;
    
    private $columns = [];
    private $buttons = [];
    
    
    private $defaultOrderCol = null;
    private $defaultOrderDir = 'asc';
    
    private $select = null;
    
    private $filterFormSelector = null;
    private $initialFilter = null;
    
    
    
    public function __construct(array $config = [])
    {
        // Set rowId to "uniqueid" by default
        $config['rowId'] = $config['rowId'] ?? 'uniqueid';
        
        $this->setConfig($config);
        
        if (isset($config['id'])) {
            $this->id = $config['id'];
        }
        
        // If paging is disabled enable fixedHeader by default
        if (($this->getConfig('paging') === false) && ($this->getConfig('fixedHeader') === null)) {
            $this->config['fixedHeader'] = true;
        }
    }
    
    
    
    /**
     * Add a column to the datatable.
     *
     * @param ColumnInterface $column
     *
     * @return $this
     * @throws \ReflectionException
     */
    public function addColumn(ColumnInterface $column)
    {
        $this->columns[] = $column;
        
        if (is_a($column, SelectColumn::class)) {
            $this->select =
                [
                    'style' => $column->getSelectStyle()->getValue(),
                    'selector' => 'td.select-checkbox',
                ];
        }
        
        // Set first orderable column as default
        if (($this->defaultOrderCol === null) && ($column->isOrderable())) {
            $this->defaultOrderCol = count($this->columns) - 1;
//            $this->defaultOrderCol = $column->getName();
        }
        
        
        if ($column->getOrderDir() != OrderDir::NONE) {
            $this->defaultOrderCol = count($this->columns) - 1;
//            $this->defaultOrderCol = $column->getName();
            $this->defaultOrderDir = $column->getOrderDir();
        }
        
        return $this;
    }
    
    
    
    /**
     * Add a button to the datatable
     *
     * @param ButtonInterface $button
     *
     * @return $this
     */
    public function addButton(ButtonInterface $button)
    {
        $this->buttons[] = $button;
        return $this;
    }
    
    
    
    /**
     * Add a filter form by selector.
     *
     * @param string $selector
     */
    public function addFilterForm(string $selector)
    {
        $this->filterFormSelector = $selector;
    }
    
    
    
    public function setInitialFilter(array $initialFilter)
    {
        $this->initialFilter = $initialFilter;
    }
    
    
    
    /**
     * Gibt die ID für das HTML-Element zurück.
     * Falls keine ID gesetzt ist, wird eine zufällige erstellt.
     *
     * @return string
     */
    public function getId($suffix = '')
    {
        if (!$this->id) {
            $this->id = 'rugalib_datatable_' . preg_replace(
                    '#[^A-Za-z0-9\-_]+#',
                    '',
                    md5('rugalib_datatable_' . $this->getConfig('ajax.url', '') . date('c'))
                );
        }
        return $this->id . ($suffix ? '-' . $suffix : '');
    }
    
    
    
    public function renderHtml()
    {
        $str = '<table id="' . $this->getId() . '" ';
        
        if ($this->getConfig('style') == 'compact') {
            // @TODO Using both "table-condensed" and "table-sm" to support bootstrap 4.5 and older
            $str .= 'class="table table-bordered table-hover table-striped table-condensed table-sm"';
        } else {
            $str .= 'class="table table-bordered table-hover table-striped"';
        }
        $str .= '>';
        
        
        $str .= '<thead>';
        $str .= '<tr>';
        
        /** @var ColumnInterface $column */
        foreach ($this->columns as $column) {
            $str .= $column->renderHtml();
        }
        
        $str .= '</tr>';
        $str .= '</thead>';
        $str .= '</table>';
        
        $bugicon = file_get_contents(__DIR__ . '/../public/bug-outline.svg');
        $debug = <<<HTML
<button type="button" class="btn btn-xs btn-default" style="padding: 5px; border: 2px; width: 2.2em" disabled="disabled" data-toggle="modal" data-target="#{$this->getId(
            'debug-modal'
        )}">
{$bugicon}
</button>
<div class="modal fade" id="{$this->getId('debug-modal')}" tabindex="-1" aria-labelledby="{$this->getId(
            'debug-modal-label'
        )}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{$this->getId('debug-modal-label')}">Debug information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="pre-scrollable text-small" id="{$this->getId('debug')}"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
HTML;
        
        return $str . ($this->getConfig('debug', true) ? $debug : '');
    }
    
    
    
    private function renderJavascriptOrder()
    {
        if ($this->defaultOrderCol === null) {
            return '[]';
        }
        
        $str = '[';
        $str .= "['{$this->defaultOrderCol}', '{$this->defaultOrderDir}']";
        $str .= ']';
        return $str;
    }
    
    
    
    private function renderJavascriptSelect()
    {
        if ($this->select === null) {
            return 'false';
        }
        
        $str = '';
        $str .= json_encode($this->select);
        $str .= '';
        
        return $str;
    }
    
    
    
    /**
     * @return string
     * @deprecated
     */
    private function renderJavascriptDom()
    {
        if ($dom = $this->getConfig('dom', null)) {
            return $dom;
        }
        if ($this->getConfig('style') == 'compact') {
            return "'" . '<"row"<"col-md-12"f>>rt<"row"<"col-md-4"i><"col-md-8"p>>' . "'";
        }
        return "'" . '<"row"<"col-md-9"B><"col-md-3"f>><"row"<"col-md-4"l><"col-md-8"p>>rt<"row"<"col-md-4"i><"col-md-8"p>>' . "'";
    }
    
    
    
    /**
     * @return string
     * @deprecated
     */
    private function renderJavascriptButtons()
    {
        if (count($this->buttons) == 0) {
            return '[]';
        }
        
        $aButtonStr = [];
        /** @var ButtonInterface $button */
        foreach ($this->buttons as $button) {
            $aButtonStr[] = $button->renderJavascript();
        }
        
        $str = '[';
        $str .= implode(', ', $aButtonStr);
        $str .= ']';
        return $str;
    }
    
    
    
    private function renderJavascriptColumns()
    {
        return '[' . implode(
                ',',
                array_map(
                    function (ColumnInterface $column) {
                        return $column->renderJavascript();
                    },
                    $this->columns
                )
            ) . ']';
    }
    
    
    
    public function renderJavascript()
    {
        $initialFilterJson = json_encode($this->initialFilter);
        $customSqlData = json_encode($this->getConfig('customSqlData', null));
        
        $str = /** @lang JavaScript */
            <<<JS
(function($, window, document) {
    const filterFormSelector='{$this->filterFormSelector}';
    const initialFilter={$initialFilterJson};
    const customSqlData={$customSqlData};
    
    $(function() {
        var table_{$this->getId()}=$("#{$this->getId()}")
            .on('preXhr.dt', function (e, settings, data) {
                // Disable debug button
                $('button[data-target="#{$this->getId('debug-modal')}"]').removeClass(['text-danger', 'text-success']);
                $('button[data-target="#{$this->getId('debug-modal')}"]').prop('disabled', true);
                
                data.customSqlData=customSqlData;

                if( filterFormSelector !== '' ) {
                    // Convert form data to Object
                    // https://stackoverflow.com/questions/41431322/how-to-convert-formdatahtml5-object-to-json
                    const formData = Array.from(new FormData($(filterFormSelector)[0]));
                    const obj = formData.reduce((o, [k, v]) => {
                        let a = v, b, i, m = k.split('['), n = m[0], l = m.length;
                        if (l > 1) {
                            a = b = o[n] || [];
                            for (i = 1; i < l; i++) {
                                m[i] = (m[i].split(']')[0] || b.length) * 1;
                                b = b[m[i]] = ((i + 1) == l) ? v : b[m[i]] || [];
                            }
                        }
                        return { ...o, [n]: a };
                        }, {});
                    
                    // Disable filter form
                    $(':input', filterFormSelector).prop('disabled', true);
    
                    data.filter=obj;
    
                    if(data.draw === 1) {
                        data.filter=initialFilter;
                    }
                }
            })
            .on('xhr.dt', function(e, settings, data, xhr) {
                if((typeof data !== "object") || (data === null)) {
                    data={
                        query: '',
                        error: xhr.status + ' ' + xhr.statusText,
                        errorBody: xhr.responseText
                    };
                }
                
                // Store debug information to the modal
                $('#{$this->getId('debug')}').html(
                    (data.error ? ('<div class="alert alert-danger" role="alert">' + data.error + '</div>') : '')
                    +
                    (data.errorBody ? ('<pre class="small text-wrap"><code>' + data.errorBody + '</code></pre>') : '')
                    +
                    (data.query ? ('<pre class="small text-wrap"><code>' + data.query + '</code></pre>') : '')
                    );
                // Enable debug button
                $('button[data-target="#{$this->getId('debug-modal')}"]').prop('disabled', false);
                
                if(data.error) {
                    $('button[data-target="#{$this->getId('debug-modal')}"]').addClass('text-danger');
                } else if(data.query) {
                    $('button[data-target="#{$this->getId('debug-modal')}"]').addClass('text-success');
                }
                
                if( filterFormSelector !== '' ) {
                    // Enable filter form
                    $(':input', filterFormSelector).prop('disabled', false);
                    $(filterFormSelector).trigger('reset');
                    
                    // Populate form from filter object
                    populate($(filterFormSelector)[0], data.filter);
                }
            })
                
        .DataTable({
            ajax: {
                url: '{$this->getConfig('ajax.url')}',
                dataSrc: '{$this->getConfig('ajax.dataSrc', 'data')}',
                type: '{$this->getConfig('ajax.type', 'GET')}',
                data: function(data, settings) {
                    $.each(data.columns, function(index, element) {
                        if(settings.aoColumns[index].dbData !== undefined)
                        element.dbData=settings.aoColumns[index].dbData;
                    })
                },
                beforeSend: function(request) {
                    request.setRequestHeader('X-Rugalib-Component', 'rugalib-datatables');
                }
            },
			rowId: '{$this->getConfig('rowId')}',
			searching: {$this->getConfigAsJsBoolean('searching', true)},
			paging: {$this->getConfigAsJsBoolean('paging', true)},
			pageLength: 10,
			lengthChange: true,
			lengthMenu: [
				[ 10, 25, 50, -1 ],
				[ '10', '25', '50', 'Alle' ]
			],
			searchHighlight: true,
			serverSide: {$this->getConfigAsJsBoolean('serverSide', true)},
			processing: true,
			stateSave: {$this->getConfigAsJsBoolean('stateSave', true)},
			order: {$this->renderJavascriptOrder()},
			select: {$this->renderJavascriptSelect()},
			fixedHeader: {$this->getConfigAsJsBoolean('fixedHeader', false)},
			//responsive: {
				//columns: ':not(:first-child)',
				//focus: 'click'
			//},
			
// 			formatNumber: function ( toFormat ) {
// 				return toFormat.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "'");
// 			},
			columnDefs: [{
				targets: "_all",
				render: function ( data, type, row, meta ) {
					var dataname=meta.settings.aoColumns[meta.col].data;
					if( (type === 'filter') && (!!row[dataname+"@filter"]) ) {
						return row[dataname+"@filter"];
					}
					if( (type === 'filter') && (!row[dataname+"@filter"]) ) {
						if( (!!row[dataname+"@display"]) ) data+= ' ' + row[dataname+"@display"];
						return data;
					}
					if( (type === 'display') && (!!row[dataname+"@display"]) ) {
						return row[dataname+"@display"];
					}
					if( (type === 'type') && (!!row[dataname+"@type"]) ) {
						return row[dataname+"@type"];
					}
					if( (type === 'sort') && (!!row[dataname+"@sort"]) ) {
						return row[dataname+"@sort"];
					}
					return data;
				}
			}],
			
			columns: {$this->renderJavascriptColumns()},
			
			rowCallback: function ( row, data ) {
				var row_tr=$(row).closest('tr');
				if(data.isNew == 1)				row_tr.addClass('isNew');
				if(data.isHidden == 1)			row_tr.addClass('isHidden');
				if(data.isDeleted == 1)			row_tr.addClass('isDeleted');
				if(data.isDisabled == 1)		row_tr.addClass('isDisabled');
				if(data.isDeletable == 1)		row_tr.addClass('isDeletable');
				
				if(data.canBeChangedBy == 1)	row_tr.addClass('canBeChangedBy');
				if(data.canBeChangedBy == 0)	row_tr.addClass('canNotBeChangedBy');
				if(data.canBeViewedBy == 1)		row_tr.addClass('canBeViewedBy');
				if(data.canBeViewedBy == 0)		row_tr.addClass('canNotBeViewedBy');
			},
			
			dom: {$this->renderJavascriptDom()},
			buttons: {$this->renderJavascriptButtons()},
			language: {
				"sEmptyTable":   	"Keine Daten in der Tabelle vorhanden",
				"sInfo":         	"_START_ bis _END_ von _TOTAL_ Einträgen",
				"sInfoEmpty":    	"0 bis 0 von 0 Einträgen",
				"sInfoFiltered": 	"(gefiltert von _MAX_ Einträgen)",
				"sInfoPostFix":  	"",
				"sInfoThousands":  	".",
				"sLengthMenu":   	"_MENU_ Einträge anzeigen",
				"sLoadingRecords": 	"Wird geladen...",
				"sProcessing":   	"Bitte warten...",
				"sSearch":       	"Suchen",
				"sZeroRecords":  	"Keine Einträge vorhanden.",
				"oPaginate": {
					"sFirst":    	"Erste",
					"sPrevious": 	"Zurück",
					"sNext":     	"Nächste",
					"sLast":     	"Letzte"
				},
				"oAria": {
					"sSortAscending":  ": aktivieren, um Spalte aufsteigend zu sortieren",
					"sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
				},
				buttons: {
					copy: "Kopieren",
					print: "Drucken",
					colvis: "Spalten ein-/ausblenden",
				}
			}
		});
    });
}(window.jQuery, window, document));
JS;
        return $str;
    }
    
    
    
    /**
     * Returns the datatable as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->renderHtml() . $this->renderJavascript();
    }
    
}
