<?php

namespace App\Controller;

use App\Controller\ControllerPadrao;
use App\Model\Contato;
use App\Model\Pessoa;
use App\View\ViewContato;

class ControllerConsultaContato extends ControllerPadrao {

    /**
     * Executa o processamento da tela.
     */
    protected function processPage() {
        $sTitle = 'Consultar Contatos';

        $sContent = ViewContato::render([
            'contentConsulta' => ViewContato::getHtmlLinhasConsulta(self::getAllContatos())
        ]);

        $this->footerVars = [
            'footerContent' => null
        ];

        return parent::getPage(
            $sTitle,
            $sContent
        );
    }

    /**
     * Retorna todos os contratos
     * @return Array
     */
    private static function getAllContatos(){
        $oEntity = static::getConexao()->getEntity();
        $oContatoRepositorio = $oEntity->getRepository(Contato::class);
        $aContatos = $oContatoRepositorio->findAll();
        foreach($aContatos as $oContato) {
            $oContato->setPessoa($oEntity->getRepository(Pessoa::class)->findOneBy(['id' => $oContato->getPessoa()->getId()]));
        }
        return $aContatos;
    }
}
