<?php
/**
 * FilmeatorList Listing
 * @author  <your name here>
 */
class FilmeatorList extends TPage
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
        $this->setActiveRecord('Filmeator');   // defines the active record
        $this->setDefaultOrder('ator', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('filme', '=', 'filme'); // filterField, operator, formField
        $this->addFilterField('ator', 'like', 'ator'); // filterField, operator, formField

        $this->form = new TForm('form_search_Filmeator');
        
        $filme = new TDBUniqueSearch('filme', 'crudflix', 'Filme', 'id', 'titulo');
        $ator = new TEntry('ator');

        $ator->exitOnEnter();

        $filme->setSize('100%');
        $ator->setSize('100%');

        $filme->tabindex = -1;
        $ator->tabindex = -1;

        $filme->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $ator->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_filme = new TDataGridColumn('filme', 'Filme', 'left');
        $column_ator = new TDataGridColumn('ator', 'Ator', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_filme);
        $this->datagrid->addColumn($column_ator);

        
        $action1 = new TDataGridAction(['FilmeatorForm', 'onEdit'], ['ator'=>'{ator}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['ator'=>'{ator}']);
        
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
        $tr->add( TElement::tag('td', $filme));
        $tr->add( TElement::tag('td', $ator));

        $this->form->addField($filme);
        $this->form->addField($ator);

        // keep form filled
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('Filmeator');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        $panel->addHeaderActionLink( _t('New'),  new TAction(['FilmeatorForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
