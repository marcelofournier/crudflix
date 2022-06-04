<?php
/**
 * FilmeFormView Form
 * @author  <your name here>
 */
class FilmeFormView extends TPage
{
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_Filme_View');
        
        $this->form->setFormTitle('Filme');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        //$this->form->addHeaderActionLink( _t('Print'), new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1']), 'far:file-pdf red');
        //$this->form->addHeaderActionLink( _t('Edit'), new TAction(['FilmeForm', 'onEdit'], ['key'=>$param['key'], 'register_state'=>'true']), 'far:edit blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Show data
     */
    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('crudflix');
        
            $object = new Filme($param['key']);
            
            $label_id = new TLabel('Id:', '#333333', '', 'B');
            $label_titulo = new TLabel('Titulo:', '#333333', '', 'B');
            $label_ano = new TLabel('Ano:', '#333333', '', 'B');
            $label_genero_id = new TLabel('Genero:', '#333333', '', 'B');
            $label_pessoa_id = new TLabel('Diretor:', '#333333', '', 'B');
            $label_sinopse = new TLabel('Sinopse:', '#333333', '', 'B');
            $label_avaliacao = new TLabel('Avaliacao:', '#333333', '', 'B');
            $label_poster = new TLabel('Poster:', '#333333', '', 'B');
            $label_pais_id = new TLabel('Pais:', '#333333', '', 'B');

            $text_id  = new TTextDisplay($object->id, '#333333', '', '');
            $text_titulo  = new TTextDisplay($object->titulo, '#333333', '', '');
            $text_ano  = new TTextDisplay($object->ano, '#333333', '', '');
            $text_genero_id  = new TTextDisplay($object->genero_id, '#333333', '', '');
            $text_pessoa_id  = new TTextDisplay($object->pessoa_id, '#333333', '', '');
            $text_sinopse  = new TTextDisplay($object->sinopse, '#333333', '', '');
            $text_avaliacao  = new TTextDisplay($object->avaliacao, '#333333', '', '');
            $text_poster  = new TTextDisplay($object->poster, '#333333', '', '');
            $text_pais_id  = new TTextDisplay($object->pais_id, '#333333', '', '');

            $this->form->addFields([$label_id],[$text_id]);
            $this->form->addFields([$label_titulo],[$text_titulo]);
            $this->form->addFields([$label_ano],[$text_ano]);
            $this->form->addFields([$label_genero_id],[$text_genero_id]);
            $this->form->addFields([$label_pessoa_id],[$text_pessoa_id]);
            $this->form->addFields([$label_sinopse],[$text_sinopse]);
            $this->form->addFields([$label_avaliacao],[$text_avaliacao]);
            $this->form->addFields([$label_poster],[$text_poster]);
            $this->form->addFields([$label_pais_id],[$text_pais_id]);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Print view
     */
    public function onPrint($param)
    {
        try
        {
            $this->onEdit($param);
            
            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/Filme-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
