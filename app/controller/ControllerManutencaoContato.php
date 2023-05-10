<?php

namespace App\Controller;

use App\Controller\ControllerPadrao;
use App\Model\Contato;
use App\Model\Pessoa;
use App\View\ViewContatoManutencao;
use Doctrine\ORM\EntityManager;

class ControllerManutencaoContato extends ControllerPadrao {

    /** Variáveis para definir cada conteúdo na tela */
    private $footerContent = null; 
    private $id = null;
    private $descricao = null;
    private $tipo = null;
    private $title = null;
    private $action = null;
    private $attr = 'require';
    private $opcoesPessoa = null;
    private $Contato;

    /**
     * Executa o processamento da tela.
     */
    protected function processPage() {
        $sPg = $_GET['pg'];
        if(in_array($sPg, ['editarContato', 'visualizarContato']) && isset($_GET['value'])) {
            $this->persisteInformacaoContato($_GET['value']);
        }

        $this->definePropriedades($sPg);
        
        $sContent = ViewContatoManutencao::render([
            'id'   => $this->id,
            'descricao' => $this->descricao,
            'nomeAcao' => $this->title,
            'action' => $this->action,
            'attr'   => $this->attr,
            'opcoesPessoa' => $this->opcoesPessoa,
            'tipo' => $this->tipo
        ]);

        $this->footerVars = [
            'footerContent' => $this->footerContent
        ];

        return parent::getPage(
            $this->title,
            $sContent
        );
    }

    /**
     * Realiza a inserção do registro.
     */
    protected function processInsert() {
        $oEntity = static::getConexao()->getEntity();
        $oContato = $this->getModelByPost($oEntity);
        if($this->validaDescricaoExistente($oContato, $oEntity)) {
            $oEntity->persist($oContato); 
            $oEntity->flush();
            $this->footerContent = '<div class="alert alert-success" role="alert"> Contato cadastrado com sucesso! </div> ';
        }else {
            $this->footerContent = '<div class="alert alert-warning" role="alert"> Essa descrição já está cadastrada! Se deseja continuar, informe um nova descrição.</div> ';
        }
        return $this->processPage();
    }

    /**
     * Realiza o update do registro
     */
    protected function processUpdate() {
        $oEntity = static::getConexao()->getEntity();
        $oModelPost = $this->getModelByPost($oEntity, true);
        $oContato = $oEntity->getRepository(Contato::class)->find($oModelPost->getId());
        if($this->validaDescricaoAlteracao($oModelPost, $oEntity)) {
            $oContato->setTipo($oModelPost->getTipo());
            $oContato->setDescricao($oModelPost->getDescricao());
            $oEntity->flush();
            $this->footerContent = '<div class="alert alert-success" role="alert"> Contato cadastrado com sucesso! </div> ';
        }else {
            $this->footerContent = '<div class="alert alert-warning" role="alert"> Essa descrição já está cadastrada! Se deseja continuar, informe um nova descrição.</div> ';
        }
        return $this->processPage();
    }

    /**
     * Realiza o delete do registro
     */
    protected function processDelete() {
        $oContato = new Contato();
        $oContato->setId($_GET['value']);
        $oEntity = static::getConexao()->getEntity();
        $oContato = $oEntity->getRepository(Contato::class)->find($oContato->getId());
        $oEntity->remove($oContato);
        $oEntity->flush();

        return (new ControllerConsultaContato())->processPage();
    }

    /**
     * Valida se a descrição já está cadastrada no banco de dados.
     * @param Contato $oContato
     * @param EntityManager $oEntity
     * @return boolean
     */
    private function validaDescricaoExistente($oContato, $oEntity) {
        return $oEntity->getRepository(Contato::class)->findOneBy(['descricao' => $oContato->getDescricao()]) == null;
    }

    private function validaDescricaoAlteracao($oContato, $oEntity) {
        $bValido = false;
        $xRetorno = $oEntity->getRepository(Contato::class)->findBy(['descricao' => $oContato->getDescricao()]);
        if(is_array($xRetorno)) {
            foreach($xRetorno as $oRetorno) {
                if($oContato->getId() == $oRetorno->getId()) {
                    $bValido = true;
                }
            }
        }
        return $bValido;
    }
    
    /**
     * Retorna o Modelo baseado no $_POST recebido
     * @param EntityManager $oEntityManager
     * @param Boolean $bUtilizaId 
     * @return Contato
     */
    private function getModelByPost($oEntityManager, $bUtilizaId = false) {
        $oPessoa = $oEntityManager->getReference(Pessoa::class, $_POST['idPessoa']);
        $this->descricao = $_POST['descricao'];
        $oContato = new Contato();
        $oContato->setPessoa($oPessoa);
        $oContato->setTipo($_POST['tipo']);
        $oContato->setDescricao($_POST['descricao']);
        if($bUtilizaId) {
            $oContato->setId($_POST['id']);
        }
        return $oContato;
    }

    /**
     * Define as propriedades da tela.
     */
    private function definePropriedades($sPagina) {
        switch($sPagina) {
            case 'incluirContato': 
                $this->title = 'Incluir Contato';
                $this->action = '?pg=incluirContato&act=insert';
                $this->opcoesPessoa = $this->getOptionsPessoas();
                $this->tipo  = $this->getOptionsTipo();
                break;
            case 'editarContato':
                $this->title = 'Editar Contato';
                $this->action = '?pg=editarContato&act=update';
                $this->opcoesPessoa = $this->getOptionsPessoas();
                $this->tipo  = $this->getOptionsTipo();
                break;
            case 'visualizarContato' :
                $this->title = 'Visualizar Contato';
                $this->attr = 'disabled';
                $this->opcoesPessoa = $this->getOptionsPessoas();
                $this->tipo         = $this->getOptionsTipo();
                break;
        }
    }

    /**
     * Persite a informação do contato
     * @param Integer $sId 
     */
    private function persisteInformacaoContato($sId) {
        $this->Contato = self::getConexao()->getEntity()->getRepository(Contato::class)->findOneBy(['id' => $sId]);
        $this->descricao = $this->Contato->getDescricao();
        $this->id        = $this->Contato->getId();
    }
    
    /**
     * Retorna as options de Pessoa.
     * Quando for Editar ou Visualizar irá selecionar a do registro existente.
     * @return String
     */
    private function getOptionsPessoas() {
        $sHtml = '';
        foreach($this->getAllPessoas() as $oPessoa) {
            if(!is_null($this->Contato) && $this->Contato->getPessoa()->getId() == $oPessoa->getId()) {
                $sHtml .='<option value="'.$oPessoa->getId().'" selected>'.$oPessoa->getNome().'</option>';
            }else{
                $sHtml .='<option value="'.$oPessoa->getId().'">'.$oPessoa->getNome().'</option>';  
            }
        }
        return $sHtml;
    }

    /**
     * Retorna as options de Tipo.
     * Quando for Editar ou Visualizar irá selecionar a do registro existente.
     * @return String
     */
    private function getOptionsTipo() {
        $sHmtl = '';
        foreach(self::getListaTipo() as $iKey => $sTipo) {
            if(!is_null($this->Contato) && $this->Contato->getTipo() == $iKey) {
                $sHmtl .= '<option value="'.$iKey.'" selected>'.$sTipo.'</option>';
            }else {
                $sHmtl .= '<option value="'.$iKey.'">'.$sTipo.'</option>';
            }
        }
        return $sHmtl;
        
    }

    /**
     * Retorna a lista de Tipo do Contato
     * @return Array
     */
    private static function getListaTipo() {
        return ['0' => 'Telefone',
                '1' => 'E-mail'];
    }

    /**
     * Retorna todas as pessoas.
     * @return Array
     */
    private function getAllPessoas() {
        return self::getConexao()->getEntity()->getRepository(Pessoa::class)->findAll();
    }


}
