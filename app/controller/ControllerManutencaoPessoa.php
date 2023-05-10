<?php

namespace App\Controller;

use App\Controller\ControllerPadrao;
use App\Model\Pessoa;
use App\View\ViewPessoaManutencao;
use Doctrine\ORM\EntityManager;

class ControllerManutencaoPessoa extends ControllerPadrao {

    /** Variáveis para definir cada conteúdo na tela */
    private $footerContent = null; 
    private $id = null;
    private $nome = null;
    private $cpf = null;
    private $title = null;
    private $action = null;
    private $attr = 'require';

    /**
     * Executa o processamento da tela.
     */
    protected function processPage() {
        $sPg = $_GET['pg'];
        $this->definePropriedades($sPg);

        if(in_array($sPg, ['editarPessoa', 'visualizarPessoa']) && isset($_GET['value'])) {
            $this->persisteInformacaoPessoa($_GET['value']);
        }
        
        $sContent = ViewPessoaManutencao::render([
            'id'   => $this->id,
            'nome' => $this->nome,
            'cpf'  => $this->cpf,
            'nomeAcao' => $this->title,
            'action' => $this->action,
            'attr'   => $this->attr
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
        $oPessoa = $this->getModelByPost();
        $oEntity = static::getConexao()->getEntity();
        if($this->validaCpf($oPessoa, $oEntity)) {
            $oEntity->persist($oPessoa); 
            $oEntity->flush();
            $this->footerContent = '<div class="alert alert-success" role="alert"> Pessoa cadastrada com sucesso! </div> ';
        }else {
            $this->footerContent = '<div class="alert alert-warning" role="alert"> Esse CPF já está cadastrado! Se deseja continuar, informe um novo CPF.</div> ';
        }
        return $this->processPage();
    }

    /**
     * Realiza o update do registro
     */
    protected function processUpdate() {
        $oEntity = static::getConexao()->getEntity();
        $oModelPost = $this->getModelByPost(true);
        $oPessoa = $oEntity->getRepository(Pessoa::class)->find($oModelPost->getId());
        if($this->validaCpfAlteracao($oModelPost, $oEntity)) {
            $oPessoa->setNome($oModelPost->getNome());
            $oPessoa->setCpf($oModelPost->getCpf());
            $oEntity->flush();
            $this->footerContent = '<div class="alert alert-success" role="alert"> Pessoa Editada com sucesso! </div> ';
        }else {
            $this->footerContent = '<div class="alert alert-warning" role="alert"> Esse CPF já está cadastrado! Se deseja continuar, informe um novo CPF.</div> ';
        }
        return $this->processPage();
    }

    /**
     * Realiza o delete do registro
     */
    protected function processDelete() {
        $oPessoa = new Pessoa();
        $oPessoa->setId($_GET['value']);
        $oEntity = static::getConexao()->getEntity();
        $oPessoa = $oEntity->getRepository(Pessoa::class)->find($oPessoa->getId());
        $oEntity->remove($oPessoa);
        $oEntity->flush();

        return (new ControllerConsultaPessoa())->processPage();
    }

    /**
     * Valida se o CPF já está cadastrado
     * @param Pessoa $oPessoa
     * @param EntityManager $oEntity
     * @return Boolean
     */
    private function validaCpf($oPessoa, $oEntity) {
        return $oEntity->getRepository(Pessoa::class)->findOneBy(['cpf' => $oPessoa->getCpf()]) == null;
    }

    /**
     * Valida se o CPF já está cadastrado
     * @param Pessoa $oPessoa
     * @param EntityManager $oEntity
     * @return Boolean
     */
    private function validaCpfAlteracao($oPessoa, $oEntity) {
        $bValido = false;
        $xRetorno = $oEntity->getRepository(Pessoa::class)->findBy(['cpf' => $oPessoa->getCpf()]);
        if(is_array($xRetorno)) {
            foreach($xRetorno as $oRetorno) {
                if($oPessoa->getId() == $oRetorno->getId()) {
                    $bValido = true;
                }
            }
        }
        return $bValido;
    }
    
    /**
     * Retorna o modelo de acordo com o $_POST recebido
     */
    private function getModelByPost($bUtilizaId = false) {
        $oPost = $_POST;
        $this->nome = $oPost['nome'];
        $this->cpf  = $oPost['cpf'];
        $oPessoa = new Pessoa();
        $oPessoa->setNome($this->nome); 
        $oPessoa->setCpf($this->cpf);       
        if($bUtilizaId) {
            $oPessoa->setId((int)$_POST['id']);
        }
        
        return $oPessoa;
    }

    /**
     * Define as propriedades da página
     * @param String $sPagina
     */
    private function definePropriedades($sPagina) {
        switch($sPagina) {
            case 'incluirPessoa': 
                $this->title = 'Incluir Pessoa';
                $this->action = '?pg=incluirPessoa&act=insert';
                break;
            case 'editarPessoa':
                $this->title = 'Editar Pessoa';
                $this->action = '?pg=editarPessoa&act=update';
                break;
            case 'visualizarPessoa' :
                $this->title = 'Visualizar Pessoa';
                $this->attr = 'disabled';
                break;
        }
    }

    /**
     * Persiste a informação da pessoa.
     */
    private function persisteInformacaoPessoa($sId) {
        $oPessoa = self::getConexao()->getEntity()->getRepository(Pessoa::class)->findOneBy(['id' => $sId]);
        $this->nome = $oPessoa->getNome();
        $this->cpf  = $oPessoa->getCpf();
        $this->id   = $oPessoa->getId();
    }


}
