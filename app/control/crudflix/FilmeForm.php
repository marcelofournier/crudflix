<?php
/**
 * FilmeForm Form
 * @author  <your name here>
 */
class FilmeForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
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
        $this->form->addFields( [ new TLabel('Pessoa') ], [ $pessoa_id ] );
        $this->form->addFields( [ new TLabel('Sinopse') ], [ $sinopse ] );
        $this->form->addFields( [ new TLabel('Avaliacao') ], [ $avaliacao ] );
        $this->form->addFields( [ new TLabel('Poster') ], [ $poster ] );
        $this->form->addFields( [ new TLabel('Pais') ], [ $pais_id ] );



        // set sizes
        $id->setSize('5%');
        $titulo->setSize('50%');
        $ano->setSize('5%');
        $genero_id->setSize('40%');
        $pessoa_id->setSize('50%');
        $sinopse->setSize('100%');
        $avaliacao->setSize('5%');
        $poster->setSize('40%');
        $pais_id->setSize('30%');



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
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
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
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
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
}
