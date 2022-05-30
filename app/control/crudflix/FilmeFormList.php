<?php
/**
 * FilmeFormList Form List
 * @author  <your name here>
 */
class FilmeFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_Filme');
        $this->form->setFormTitle('Filme');
        

        // create the form fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $genero_id = new TDBCombo('genero_id', 'crudflix', 'Genero', 'id', 'generoDescricao');
        $pessoa_id = new TDBCombo('pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');
        $sinopse = new TText('sinopse');
        $avaliacao = new TEntry('avaliacao');
        $poster = new TFile('poster');
        $pais_id = new TDBCombo('pais_id', 'crudflix', 'Pais', 'id', 'paisDescricao');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Titulo') ], [ $titulo ] );
        $this->form->addFields( [ new TLabel('Ano') ], [ $ano ] );
        $this->form->addFields( [ new TLabel('Genero') ], [ $genero_id ] );
        $this->form->addFields( [ new TLabel('Diretor') ], [ $pessoa_id ] );
        $this->form->addFields( [ new TLabel('Sinopse') ], [ $sinopse ] );
        $this->form->addFields( [ new TLabel('Avaliacao') ], [ $avaliacao ] );
        $this->form->addFields( [ new TLabel('Poster') ], [ $poster ] );
        $this->form->addFields( [ new TLabel('Pais') ], [ $pais_id ] );

        $titulo->addValidation('Titulo', new TRequiredValidator);
        $ano->addValidation('Ano', new TRequiredValidator);
        $genero_id->addValidation('Genero', new TRequiredValidator);
        $pessoa_id->addValidation('Diretor', new TRequiredValidator);
        $avaliacao->addValidation('Avaliacao', new TRequiredValidator);
        $pais_id->addValidation('Pais', new TRequiredValidator);


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



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_titulo = new TDataGridColumn('titulo', 'Titulo', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        $column_genero_id = new TDataGridColumn('genero_id', 'Genero', 'left');
        $column_pessoa_id = new TDataGridColumn('pessoa_id', 'Diretor', 'left');
        $column_sinopse = new TDataGridColumn('sinopse', 'Sinopse', 'left');
        $column_avaliacao = new TDataGridColumn('avaliacao', 'Avaliacao', 'left');
        $column_poster = new TDataGridColumn('poster', 'Poster', 'left');
        $column_pais_id = new TDataGridColumn('pais_id', 'Pais', 'left');


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

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'crudflix'
            TTransaction::open('crudflix');
            
            // creates a repository for Filme
            $repository = new TRepository('Filme');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('crudflix'); // open a transaction with database
            $object = new Filme($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('crudflix'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new Filme;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('crudflix'); // open a transaction
                $object = new Filme($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
