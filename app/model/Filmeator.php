<?php
/**
 * Filmeator Active Record
 * @author  <your-name-here>
 */
class Filmeator extends TRecord
{
    const TABLENAME = 'filmeator';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $filmes;
    private $pessoas;

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
     * Method addFilme
     * Add a Filme to the Filmeator
     * @param $object Instance of Filme
     */
    public function addFilme(Filme $object)
    {
        $this->filmes[] = $object;
    }
    
    /**
     * Method getFilmes
     * Return the Filmeator' Filme's
     * @return Collection of Filme
     */
    public function getFilmes()
    {
        return $this->filmes;
    }
    
    /**
     * Method addPessoa
     * Add a Pessoa to the Filmeator
     * @param $object Instance of Pessoa
     */
    public function addPessoa(Pessoa $object)
    {
        $this->pessoas[] = $object;
    }
    
    /**
     * Method getPessoas
     * Return the Filmeator' Pessoa's
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
        $this->filmes = array();
        $this->pessoas = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Filme objects
        $repository = new TRepository('FilmeatorFilme');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $id));
        $filmeator_filmes = $repository->load($criteria);
        if ($filmeator_filmes)
        {
            foreach ($filmeator_filmes as $filmeator_filme)
            {
                $filme = new Filme( $filmeator_filme->filme_id );
                $this->addFilme($filme);
            }
        }
    
        // load the related Pessoa objects
        $repository = new TRepository('FilmeatorPessoa');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $id));
        $filmeator_pessoas = $repository->load($criteria);
        if ($filmeator_pessoas)
        {
            foreach ($filmeator_pessoas as $filmeator_pessoa)
            {
                $pessoa = new Pessoa( $filmeator_pessoa->pessoa_id );
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
    
        // delete the related FilmeatorFilme objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $this->id));
        $repository = new TRepository('FilmeatorFilme');
        $repository->delete($criteria);
        // store the related FilmeatorFilme objects
        if ($this->filmes)
        {
            foreach ($this->filmes as $filme)
            {
                $filmeator_filme = new FilmeatorFilme;
                $filmeator_filme->filme_id = $filme->id;
                $filmeator_filme->filmeator_id = $this->id;
                $filmeator_filme->store();
            }
        }
        // delete the related FilmeatorPessoa objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $this->id));
        $repository = new TRepository('FilmeatorPessoa');
        $repository->delete($criteria);
        // store the related FilmeatorPessoa objects
        if ($this->pessoas)
        {
            foreach ($this->pessoas as $pessoa)
            {
                $filmeator_pessoa = new FilmeatorPessoa;
                $filmeator_pessoa->pessoa_id = $pessoa->id;
                $filmeator_pessoa->filmeator_id = $this->id;
                $filmeator_pessoa->store();
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
        // delete the related FilmeatorFilme objects
        $repository = new TRepository('FilmeatorFilme');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the related FilmeatorPessoa objects
        $repository = new TRepository('FilmeatorPessoa');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filmeator_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
