<?php
/**
 * GeneroUpdateList Listing
 * @author  <your name here>
 */
class GeneroUpdateList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $saveButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('crudflix');            // defines the database
        $this->setActiveRecord('Genero');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('generoDescricao', 'like', 'generoDescricao'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_update_Genero');
        $this->form->setFormTitle('Genero');
        

        // create the form fields
        $id = new TEntry('id');
        $generoDescricao = new TEntry('generoDescricao');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Generodescricao') ], [ $generoDescricao ] );


        // set sizes
        $id->setSize('100%');
        $generoDescricao->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_generoDescricao = new TDataGridColumn('generoDescricao', 'Generodescricao', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_generoDescricao);

        
        $column_generoDescricao->setTransformer( function($value, $object, $row) {
            $widget = new TEntry('generoDescricao' . '_' . $object->id);
            $widget->setValue( $object->generoDescricao );
            //$widget->setSize(120);
            $widget->setFormName('form_update_Genero');
            
            $action = new TAction( [$this, 'onSaveInline'], ['column' => 'generoDescricao' ] );
            $widget->setExitAction( $action );
            return $widget;
        });
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Save the datagrid objects
     */
    public static function onSaveInline($param)
    {
        $name   = $param['_field_name'];
        $value  = $param['_field_value'];
        $column = $param['column'];
        
        $parts  = explode('_', $name);
        $id     = end($parts);
        
        try
        {
            // open transaction
            TTransaction::open('crudflix');
            
            $object = Genero::find($id);
            if ($object)
            {
                $object->$column = $value;
                $object->store();
            }
            
            TToast::show('success', 'Record saved', 'bottom center', 'far:check-circle');
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            // show the exception message
            TToast::show('error', $e->getMessage(), 'bottom center', 'fa:exclamation-triangle');
        }
    }
}
