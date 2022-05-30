<?php
/**
 * Pessoa Active Record
 * @author  <your-name-here>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $filme;
    private $pais;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('pais_id');
    }

    
    /**
     * Method set_filme
     * Sample of usage: $pessoa->filme = $object;
     * @param $object Instance of Filme
     */
    public function set_filme(Filme $object)
    {
        $this->filme = $object;
        $this->filme_id = $object->id;
    }
    
    /**
     * Method get_filme
     * Sample of usage: $pessoa->filme->attribute;
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
     * Method set_pais
     * Sample of usage: $pessoa->pais = $object;
     * @param $object Instance of Pais
     */
    public function set_pais(Pais $object)
    {
        $this->pais = $object;
        $this->pais_id = $object->id;
    }
    
    /**
     * Method get_pais
     * Sample of usage: $pessoa->pais->attribute;
     * @returns Pais instance
     */
    public function get_pais()
    {
        // loads the associated object
        if (empty($this->pais))
            $this->pais = new Pais($this->pais_id);
    
        // returns the associated object
        return $this->pais;
    }
    


}
