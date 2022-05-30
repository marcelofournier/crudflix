<?php
/**
 * FilmeList Listing
 * @author  <your name here>
 */
class FilmeList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Filme');
        $this->form->setFormTitle('Filme');
        

        // create the form fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $genero_id = new TDBUniqueSearch('genero_id', 'crudflix', 'Genero', 'id', 'generoDescricao');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');
        $sinopse = new TEntry('sinopse');
        $avaliacao = new TEntry('avaliacao');
        $poster = new TEntry('poster');
        $pais = new TDBSelect('pais', 'crudflix', 'Pais', 'id', 'paisDescricao');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Titulo') ], [ $titulo ] );
        $this->form->addFields( [ new TLabel('Ano') ], [ $ano ] );
        $this->form->addFields( [ new TLabel('Genero') ], [ $genero_id ] );
        $this->form->addFields( [ new TLabel('Pessoa') ], [ $pessoa_id ] );
        $this->form->addFields( [ new TLabel('Sinopse') ], [ $sinopse ] );
        $this->form->addFields( [ new TLabel('Avaliacao') ], [ $avaliacao ] );
        $this->form->addFields( [ new TLabel('Poster') ], [ $poster ] );
        $this->form->addFields( [ new TLabel('Pais') ], [ $pais ] );


        // set sizes
        $id->setSize('100%');
        $titulo->setSize('100%');
        $ano->setSize('100%');
        $genero_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $sinopse->setSize('100%');
        $avaliacao->setSize('100%');
        $poster->setSize('100%');
        $pais->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
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
        $column_genero_id = new TDataGridColumn('genero_id', 'Genero', '');
        $column_pessoa_id = new TDataGridColumn('pessoa_id', 'Pessoa', 'left');
        $column_sinopse = new TDataGridColumn('sinopse', 'Sinopse', 'left');
        $column_avaliacao = new TDataGridColumn('avaliacao', 'Avaliacao', 'left');
        $column_poster = new TDataGridColumn('poster', 'Poster', 'left');
        $column_pais = new TDataGridColumn('pais', 'Pais', '');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_ano);
        $this->datagrid->addColumn($column_genero_id);
        $this->datagrid->addColumn($column_pessoa_id);
        $this->datagrid->addColumn($column_sinopse);
        $this->datagrid->addColumn($column_avaliacao);
        $this->datagrid->addColumn($column_poster);
        $this->datagrid->addColumn($column_pais);

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
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('crudflix'); // open a transaction with database
            $object = new Filme($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue(__CLASS__.'_filter_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_titulo',   NULL);
        TSession::setValue(__CLASS__.'_filter_ano',   NULL);
        TSession::setValue(__CLASS__.'_filter_genero_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_pessoa_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_sinopse',   NULL);
        TSession::setValue(__CLASS__.'_filter_avaliacao',   NULL);
        TSession::setValue(__CLASS__.'_filter_poster',   NULL);
        TSession::setValue(__CLASS__.'_filter_pais',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->titulo) AND ($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_titulo',   $filter); // stores the filter in the session
        }


        if (isset($data->ano) AND ($data->ano)) {
            $filter = new TFilter('ano', 'like', "%{$data->ano}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_ano',   $filter); // stores the filter in the session
        }


        if (isset($data->genero_id) AND ($data->genero_id)) {
            $filter = new TFilter('genero_id', '=', $data->genero_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_genero_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pessoa_id) AND ($data->pessoa_id)) {
            $filter = new TFilter('pessoa_id', '=', $data->pessoa_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_pessoa_id',   $filter); // stores the filter in the session
        }


        if (isset($data->sinopse) AND ($data->sinopse)) {
            $filter = new TFilter('sinopse', 'like', "%{$data->sinopse}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_sinopse',   $filter); // stores the filter in the session
        }


        if (isset($data->avaliacao) AND ($data->avaliacao)) {
            $filter = new TFilter('avaliacao', 'like', "%{$data->avaliacao}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_avaliacao',   $filter); // stores the filter in the session
        }


        if (isset($data->poster) AND ($data->poster)) {
            $filter = new TFilter('poster', 'like', "%{$data->poster}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_poster',   $filter); // stores the filter in the session
        }


        if (isset($data->pais) AND ($data->pais)) {
            $filter = new TFilter('pais', '=', $data->pais); // create the filter
            TSession::setValue(__CLASS__.'_filter_pais',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_titulo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_titulo')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_ano')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_ano')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_genero_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_genero_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_pessoa_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_pessoa_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_sinopse')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_sinopse')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_avaliacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_avaliacao')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_poster')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_poster')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_pais')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_pais')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
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
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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
            $key=$param['key']; // get the parameter $key
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
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
