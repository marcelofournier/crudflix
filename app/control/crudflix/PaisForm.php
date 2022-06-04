<?php
/**
 * PaisForm Registration
 * @author  <your name here>
 */
class PaisForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        
        $this->setDatabase('crudflix');              // defines the database
        $this->setActiveRecord('Pais');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Pais');
        $this->form->setFormTitle('Pais');
        

        // create the form fields
        $id = new TEntry('id');
        $paisDescricao = new TEntry('paisDescricao');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Pais') ], [ $paisDescricao ] );

        $paisDescricao->addValidation('Pais', new TRequiredValidator);


        // set sizes
        $id->setSize('20%');
        $paisDescricao->setSize('40%');


        
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
}
