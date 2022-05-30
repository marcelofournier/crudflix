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
    private $pessoas;

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
     * Method addPessoa
     * Add a Pessoa to the Filme
     * @param $object Instance of Pessoa
     */
    public function addPessoa(Pessoa $object)
    {
        $this->pessoas[] = $object;
    }
    
    /**
     * Method getPessoas
     * Return the Filme' Pessoa's
     * @return Collection of Pessoa
     */
    public function getPessoas()
    {
        return $this->pessoas;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->pessoas = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Pessoa objects
        $repository = new TRepository('FilmePessoa');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $id));
        $filme_pessoas = $repository->load($criteria);
        if ($filme_pessoas)
        {
            foreach ($filme_pessoas as $filme_pessoa)
            {
                $pessoa = new Pessoa( $filme_pessoa->pessoa_id );
                $this->addPessoa($pessoa);
            }
        }
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related FilmePessoa objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $this->id));
        $repository = new TRepository('FilmePessoa');
        $repository->delete($criteria);
        // store the related FilmePessoa objects
        if ($this->pessoas)
        {
            foreach ($this->pessoas as $pessoa)
            {
                $filme_pessoa = new FilmePessoa;
                $filme_pessoa->pessoa_id = $pessoa->id;
                $filme_pessoa->filme_id = $this->id;
                $filme_pessoa->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related FilmePessoa objects
        $repository = new TRepository('FilmePessoa');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
