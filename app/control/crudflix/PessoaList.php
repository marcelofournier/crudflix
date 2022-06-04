<?php
/**
 * PessoaList Listing
 * @author  <your name here>
 */
class PessoaList extends TPage
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
        $this->setActiveRecord('Pessoa');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('pais_id', '=', 'pais_id'); // filterField, operator, formField

        $this->form = new TForm('form_search_Pessoa');
        
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $pais_id = new TDBCombo('pais_id', 'crudflix', 'Pais', 'id', 'paisDescricao');

        $id->exitOnEnter();
        $nome->exitOnEnter();

        $id->setSize('100%');
        $nome->setSize('100%');
        $pais_id->setSize('100%');

        $id->tabindex = -1;
        $nome->tabindex = -1;
        $pais_id->tabindex = -1;

        $id->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $nome->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $pais_id->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        //$column_pais_id = new TDataGridColumn('pais_id', 'Pais', 'left');
        $column_pais_id = new TDataGridColumn('{Pais->paisDescricao', 'Pais', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_pais_id);

        
        $action1 = new TDataGridAction(['PessoaForm', 'onEdit'], ['id'=>'{id}']);
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
        
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $id));
        $tr->add( TElement::tag('td', $nome));
        $tr->add( TElement::tag('td', $pais_id));

        $this->form->addField($id);
        $this->form->addField($nome);
        $this->form->addField($pais_id);

        // keep form filled
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('Pessoa');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        $panel->addHeaderActionLink( _t('New'),  new TAction(['PessoaForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
