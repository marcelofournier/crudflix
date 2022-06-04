<?php
/**
 * FilmeListTeste Listing
 * @author  <your name here>
 */
class FilmeListTeste extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
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
        $this->addFilterField('sinopse', 'like', 'sinopse'); // filterField, operator, formField
        $this->addFilterField('avaliacao', 'like', 'avaliacao'); // filterField, operator, formField
        $this->addFilterField('poster', 'like', 'poster'); // filterField, operator, formField
        $this->addFilterField('pais_id', '=', 'pais_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Filme');
        $this->form->setFormTitle('Filme');
        

        // create the form fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $genero_id = new TDBUniqueSearch('genero_id', 'crudflix', 'Genero', 'id', 'descricao');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');
        $sinopse = new TEntry('sinopse');
        $avaliacao = new TEntry('avaliacao');
        $poster = new TEntry('poster');
        $pais_id = new TDBUniqueSearch('pais_id', 'crudflix', 'Pais', 'id', 'paisDescricao');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Titulo') ], [ $titulo ] );
        $this->form->addFields( [ new TLabel('Ano') ], [ $ano ] );
        $this->form->addFields( [ new TLabel('Genero Id') ], [ $genero_id ] );
        $this->form->addFields( [ new TLabel('Pessoa Id') ], [ $pessoa_id ] );
        $this->form->addFields( [ new TLabel('Sinopse') ], [ $sinopse ] );
        $this->form->addFields( [ new TLabel('Avaliacao') ], [ $avaliacao ] );
        $this->form->addFields( [ new TLabel('Poster') ], [ $poster ] );
        $this->form->addFields( [ new TLabel('Pais Id') ], [ $pais_id ] );


        // set sizes
        $id->setSize('100%');
        $titulo->setSize('100%');
        $ano->setSize('100%');
        $genero_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $sinopse->setSize('100%');
        $avaliacao->setSize('100%');
        $poster->setSize('100%');
        $pais_id->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FilmeForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_titulo = new TDataGridColumn('titulo', 'Titulo', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        $column_genero_id = new TDataGridColumn('genero_id', 'Genero Id', 'left');
        $column_pessoa_id = new TDataGridColumn('pessoa_id', 'Pessoa Id', 'left');
        $column_sinopse = new TDataGridColumn('sinopse', 'Sinopse', 'left');
        $column_avaliacao = new TDataGridColumn('avaliacao', 'Avaliacao', 'left');
        $column_poster = new TDataGridColumn('poster', 'Poster', 'left');       
        
        $column_pais_id = new TDataGridColumn('pais_id', 'Pais Id', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_ano);
        $this->datagrid->addColumn($column_genero_id);
        $this->datagrid->addColumn($column_pessoa_id);
        $this->datagrid->addColumn($column_sinopse);
        $this->datagrid->addColumn($column_avaliacao);
        $this->datagrid->addColumn($column_poster);
        $this->datagrid->addColumn($column_pais_id);

        // define the transformer method over image
        $column_avaliacao->setTransformer( function($value, $object, $row) {
            $bar = new TProgressBar;
            $bar->setMask('<b>{value}</b>%');
            $bar->setValue($value);
            
            if ($value == 100) {
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
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
