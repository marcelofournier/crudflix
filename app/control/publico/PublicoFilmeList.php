<?php
/**
 * FilmeList Listing
 * @author  <your name here>
 */
class PublicoFilmeList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('crudflix');            // defines the database
        $this->setActiveRecord('Filme');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('titulo', 'like', 'titulo'); // filterField, operator, formField
        $this->addFilterField('ano', 'like', 'ano'); // filterField, operator, formField
        $this->addFilterField('genero_id', '=', 'genero_id'); // filterField, operator, formField
        $this->addFilterField('pessoa_id', '=', 'pessoa_id'); // filterField, operator, formField
        $this->addFilterField('avaliacao', '>=', 'avaliacao'); // filterField, operator, formField
        $this->addFilterField('pais_id', '=', 'pais_id'); // filterField, operator, formField

        
        $this->form = new TForm('form_search_Filme');
        
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $genero_id = new TDBCombo('genero_id', 'crudflix', 'Genero', 'id', 'generoDescricao');
        $pessoa_id = new TDBCombo('pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');
        $avaliacao = new TEntry('avaliacao');
        $pais_id = new TDBCombo('pais_id', 'crudflix', 'Pais', 'id', 'paisDescricao');

        $id->exitOnEnter();
        $titulo->exitOnEnter();
        $ano->exitOnEnter();
        $avaliacao->exitOnEnter();

        $id->setSize('100%');
        $titulo->setSize('100%');
        $ano->setSize('100%');
        $genero_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $avaliacao->setSize('100%');
        $pais_id->setSize('100%');

        $id->tabindex = -1;
        $titulo->tabindex = -1;
        $ano->tabindex = -1;
        $genero_id->tabindex = -1;
        $pessoa_id->tabindex = -1;
        $avaliacao->tabindex = -1;
        $pais_id->tabindex = -1;

        $id->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $titulo->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $ano->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $genero_id->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $pessoa_id->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $avaliacao->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $pais_id->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
            
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->enablePopover('Popover', '<b> {Pais->paisDescricao} </b>');
        
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_titulo = new TDataGridColumn('titulo', 'Titulo', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        $column_genero_id = new TDataGridColumn('{Genero->generoDescricao}', 'Genero', 'left');
        $column_pessoa_id = new TDataGridColumn('pessoa_id', 'Diretor', 'left');
        $column_sinopse = new TDataGridColumn('sinopse', 'Sinopse', 'left');
        $column_avaliacao = new TDataGridColumn('avaliacao', 'Avaliacao', 'left');
        $column_poster = new TDataGridColumn('poster', 'Poster', 'left');  
        $column_pais_id = new TDataGridColumn('{Pais->paisDescricao}', 'Origem', 'left');

         // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_ano);
        $this->datagrid->addColumn($column_genero_id);
        $this->datagrid->addColumn($column_pessoa_id);
        //$this->datagrid->addColumn($column_sinopse);
        $this->datagrid->addColumn($column_avaliacao);
        $this->datagrid->addColumn($column_pais_id);      
        //$this->datagrid->addColumn($column_poster);

        // creates the datagrid column actions
        $column_ano->setAction(new TAction([$this, 'onReload']), ['order' => 'ano']);
        $column_genero_id->setAction(new TAction([$this, 'onReload']), ['order' => 'genero_id']);
        $column_pessoa_id->setAction(new TAction([$this, 'onReload']), ['order' => 'pessoa_id']);
        $column_avaliacao->setAction(new TAction([$this, 'onReload']), ['order' => 'avaliacao']);
        $column_pais_id->setAction(new TAction([$this, 'onReload']), ['order' => 'pais_id']);

        // define the transformer method over image
        $column_avaliacao->setTransformer( function($value, $object, $row) {
            $bar = new TProgressBar;
            $bar->setMask('<b>{value}</b>%');
            $bar->setValue($value);
            
            if ($value >= 85) {
                $bar->setClass('success');
            }
            else if ($value >= 75) {
                $bar->setClass('info');
            }
            else if ($value >= 50) {
                $bar->setClass('warning');
            }
            else {
                $bar->setClass('danger');
            }
            return $bar;
        });
               
        
        // define the transformer method over image
        $column_poster->setTransformer( function($value, $object, $row) {
            if (file_exists($value)) {
              return new TImage($value);
            }
        });

        
        $action1 = new TDataGridAction(['FilmeForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // add datagrid inside form
        $this->form->add($this->datagrid);
        
        // create row with search inputs
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        //$tr->add( TElement::tag('td', ''));
        //$tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $id));
        $tr->add( TElement::tag('td', $titulo));
        $tr->add( TElement::tag('td', $ano));
        $tr->add( TElement::tag('td', $genero_id));
        $tr->add( TElement::tag('td', $pessoa_id));
        $tr->add( TElement::tag('td', $avaliacao));
        $tr->add( TElement::tag('td', $pais_id));

        $this->form->addField($id);
        $this->form->addField($titulo);
        $this->form->addField($ano);
        $this->form->addField($genero_id);
        $this->form->addField($pessoa_id);
        $this->form->addField($avaliacao);
        $this->form->addField($pais_id);

        // keep form filled
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('Filme');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        $panel->addHeaderActionLink( _t('New'),  new TAction(['FilmeForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
