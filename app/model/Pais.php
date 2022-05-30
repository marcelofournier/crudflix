<?php
/**
 * Pais Active Record
 * @author  <your-name-here>
 */
class Pais extends TRecord
{
    const TABLENAME = 'pais';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $filme;
    private $pessoa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('paisDescricao');
    }

    
    /**
     * Method set_filme
     * Sample of usage: $pais->filme = $object;
     * @param $object Instance of Filme
     */
    public function set_filme(Filme $object)
    {
        $this->filme = $object;
        $this->filme_id = $object->id;
    }
    
    /**
     * Method get_filme
     * Sample of usage: $pais->filme->attribute;
     * @returns Filme instance
     */
    public function get_filme()
    {
        // loads the associated object
        if (empty($this->filme))
            $this->filme = new Filme($this->filme_id);
    
        // returns the associated object
        return $this->filme;
    }
    
    
    /**
     * Method set_pessoa
     * Sample of usage: $pais->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $pais->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
    
        // returns the associated object
        return $this->pessoa;
    }
    


}
