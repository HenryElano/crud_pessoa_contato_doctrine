<?php

namespace App\Controller;

use App\Controller\ControllerPadrao;
use App\Model\Pessoa;
use App\View\ViewPessoa;
use Doctrine\ORM\EntityManager;

class ControllerConsultaPessoa extends ControllerPadrao {

    /** @var $nomePessoa Define o nome da pessoa no filtro */
    private $nomePessoa = null;

    /**
     * Executa o processamento da tela.
     */
    protected function processPage() {
        $sTitle = 'Consultar Pessoas';

        $sContent = ViewPessoa::render([
            'contentConsulta' => ViewPessoa::getHtmlLinhasConsulta($this->getPessoas()),
            'nomePessoa' => $this->nomePessoa
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
     * Retorna as pessoas para a consulta
     * @return Array
     */
    private function getPessoas() {
        $oEntity = static::getConexao()->getEntity();
        if(isset($_POST['nome'])) {
            return $this->getPessoasFiltro($_POST['nome'], $oEntity);
        }
        $oPessoaRepositorio = $oEntity->getRepository(Pessoa::class);
        return $oPessoaRepositorio->findAll();
    }

    /**
     * Retorna as Pessoa buscando através do filtro
     * @param String $sNome
     * @param EntityManager $oEntity
     * @return Array    
     */
    private function getPessoasFiltro($sNome, $oEntity) {
        $this->nomePessoa = $sNome;
        $qb = $oEntity->createQueryBuilder();
        $qb->select('p')->from(Pessoa::class, 'p')
                        ->where('p.nome LIKE :nome')
                        ->setParameter('nome', '%' . $this->nomePessoa . '%');
        return $qb->getQuery()->getResult();
    }

}
