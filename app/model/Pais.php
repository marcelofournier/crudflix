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
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('paisDescricao');
    }


}
