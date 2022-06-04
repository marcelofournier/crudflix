<?php
/**
 * FilmeForm Master/Detail
 * @author  <your name here>
 */
class FilmeForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Filme');
        $this->form->setFormTitle('Filme');
        
        // master fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $genero_id = new TDBUniqueSearch('genero_id', 'crudflix', 'Genero', 'id', 'generoDescricao');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');
        $sinopse = new TEntry('sinopse');
        $avaliacao = new TEntry('avaliacao');
        $poster = new TEntry('poster');
        $pais_id = new TDBUniqueSearch('pais_id', 'crudflix', 'Pais', 'id', 'paisDescricao');

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_pessoa_id = new TDBCombo('detail_pessoa_id', 'crudflix', 'Pessoa', 'id', 'nome');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // master fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Titulo')], [$titulo] );
        $this->form->addFields( [new TLabel('Ano')], [$ano] );
        $this->form->addFields( [new TLabel('Genero Id')], [$genero_id] );
        $this->form->addFields( [new TLabel('Pessoa Id')], [$pessoa_id] );
        $this->form->addFields( [new TLabel('Sinopse')], [$sinopse] );
        $this->form->addFields( [new TLabel('Avaliacao')], [$avaliacao] );
        $this->form->addFields( [new TLabel('Poster')], [$poster] );
        $this->form->addFields( [new TLabel('Pais Id')], [$pais_id] );
        
        // detail fields
        $this->form->addContent( ['<h4>Details</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $this->form->addFields( [new TLabel('Pessoa Id')], [$detail_pessoa_id] );

        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Register', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add] );
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('Filmeator_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('pessoa_id', 'Pessoa Id', 'left', 100) );

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['pessoa_id'] = $data->detail_pessoa_id;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Filmeator_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_pessoa_id = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Filme', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_pessoa_id = $param['pessoa_id'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Filme', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_pessoa_id = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Filme', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('Filmeator_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('crudflix');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Filme($key);
                $items  = Filmeator::where('filme_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('crudflix');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Filme;
            $master->fromArray( (array) $data);
            $master->store();
            
            Filmeator::where('filme_id', '=', $master->id)->delete();
            
            if( $param['Filmeator_list_pessoa_id'] )
            {
                foreach( $param['Filmeator_list_pessoa_id'] as $key => $item_id )
                {
                    $detail = new Filmeator;
                    $detail->pessoa_id  = $param['Filmeator_list_pessoa_id'][$key];
                    $detail->filme_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Filme', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
