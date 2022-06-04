<?php
/**
 * Filme Active Record
 * @author  <your-name-here>
 */
class Filme extends TRecord
{
    const TABLENAME = 'filme';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $genero;
    private $pais;
    private $pessoa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('ano');
        parent::addAttribute('genero_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('sinopse');
        parent::addAttribute('avaliacao');
        parent::addAttribute('poster');
        parent::addAttribute('pais_id');
    }

    
    /**
     * Method set_genero
     * Sample of usage: $filme->genero = $object;
     * @param $object Instance of Genero
     */
    public function set_genero(Genero $object)
    {
        $this->genero = $object;
        $this->genero_id = $object->id;
    }
    
    /**
     * Method get_genero
     * Sample of usage: $filme->genero->attribute;
     * @returns Genero instance
     */
    public function get_genero()
    {
        // loads the associated object
        if (empty($this->genero))
            $this->genero = new Genero($this->genero_id);
    
        // returns the associated object
        return $this->genero;
    }
    
    
    /**
     * Method set_pais
     * Sample of usage: $filme->pais = $object;
     * @param $object Instance of Pais
     */
    public function set_pais(Pais $object)
    {
        $this->pais = $object;
        $this->pais_id = $object->id;
    }
    
    /**
     * Method get_pais
     * Sample of usage: $filme->pais->attribute;
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
    
    
    /**
     * Method set_pessoa
     * Sample of usage: $filme->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $filme->pessoa->attribute;
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
