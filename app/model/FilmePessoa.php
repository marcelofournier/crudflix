<?php
/**
 * FilmePessoa Active Record
 * @author  <your-name-here>
 */
class FilmePessoa extends TRecord
{
    const TABLENAME = 'filmeator';
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
        parent::addAttribute('filme_id');
        parent::addAttribute('pessoa_id');
    }

    
    /**
     * Method set_filme
     * Sample of usage: $filme_pessoa->filme = $object;
     * @param $object Instance of Filme
     */
    public function set_filme(Filme $object)
    {
        $this->filme = $object;
        $this->filme_id = $object->id;
    }
    
    /**
     * Method get_filme
     * Sample of usage: $filme_pessoa->filme->attribute;
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
     * Sample of usage: $filme_pessoa->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $filme_pessoa->pessoa->attribute;
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
