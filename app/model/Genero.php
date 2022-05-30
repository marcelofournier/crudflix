<?php
/**
 * Genero Active Record
 * @author  <your-name-here>
 */
class Genero extends TRecord
{
    const TABLENAME = 'genero';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $filme;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('generoDescricao');
    }

    
    /**
     * Method set_filme
     * Sample of usage: $genero->filme = $object;
     * @param $object Instance of Filme
     */
    public function set_filme(Filme $object)
    {
        $this->filme = $object;
        $this->filme_id = $object->id;
    }
    
    /**
     * Method get_filme
     * Sample of usage: $genero->filme->attribute;
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
    


}
