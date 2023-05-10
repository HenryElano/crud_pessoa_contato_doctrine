<?php

namespace App\db;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\ORMSetup;

class Connection {

    /** @var \Doctrine\DBAL\Connection $Conexao*/
    protected $Conexao;

    /** @var AbstractPlatform $SchemaManager */
    private $SchemaManager;

    public function __construct() {
        $this->iniciaConexao();
        $this->criaTabelas();
    }

    /**
     * Inícia a conexão com o banco de dados.
     */
    private function iniciaConexao() :void {
        $this->Conexao = DriverManager::getConnection($this->getConfig(), (new Configuration()));
    }

    public function getEntity() :EntityManager {
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/app/"), true, null, null, false);
        $entityManager = EntityManager::create($this->Conexao, $config);

        return $entityManager;
    }

    /**
     * Retorna um array com as Configurações do banco.
     * @return Array
     */
    private static function getConfig() :array {
        return ['driver'   => 'pdo_mysql',
                'host'     => $_ENV['host'],
                'port'     => $_ENV['port'],
                'user'     => $_ENV['user'],
                'password' => $_ENV['password'],
                'dbname'   => $_ENV['dbname']];
    }

    /**
     * Cria e retorna o objeto SchemaManager
     * @return AbstractSchemaManager
     */
    private function getSchemaManager() : AbstractSchemaManager {
        return $this->SchemaManager ?: $this->SchemaManager = $this->Conexao->createSchemaManager();
    }

    /**
     * Retorna a plataforma do banco 
     * @return AbstractPlatform
     */
    private function getDataBasePlataform() {
        return $this->Conexao->getDatabasePlatform();
    }

    /**
     * Realiza a criação das tabelas.
     */
    private function criaTabelas() {
        $this->criaTabelaPessoa();
        $this->criaTabelaContato();
    }

    /**
     * Criação das tabelas de Pessoa
     */
    private function criaTabelaPessoa() {
        if(!$this->getSchemaManager()->tablesExist('pessoa')) {
            $oTabela = new Table('pessoa');
            $this->adicionaColunasTabelaPessoa($oTabela);
            $oTabela->setPrimaryKey(['id']);
            $oTabela->addUniqueIndex(['cpf']);
            foreach($this->getDataBasePlataform()->getCreateTableSQL($oTabela) as $sSql) {
                $this->Conexao->executeStatement($sSql);
            } 
        }
    }

    /**
     * Criação das tabelas de Contato
     */
    private function criaTabelaContato() {
        if(!$this->getSchemaManager()->tablesExist('contato')) {
            $oTabela = new Table('contato');
            $this->adicionaColunasTabelaContato($oTabela);
            $oTabela->setPrimaryKey(['id']);
            $oTabela->addUniqueIndex(['descricao']);
            $oTabela->addForeignKeyConstraint('pessoa', ['idPessoa'], ['id']);
            foreach($this->getDataBasePlataform()->getCreateTableSQL($oTabela) as $sSql) {
                $this->Conexao->executeStatement($sSql);
            } 
        }
    }

    /**
     * Adiciona as colunas de Pessoa
     */
    private static function adicionaColunasTabelaPessoa($oTabela) {
        $oTabela->addColumn('id', 'integer',  ['unsigned' => true, 'autoincrement' => true]);
        $oTabela->addColumn('nome', 'string', ['length'   => 255]);
        $oTabela->addColumn('cpf', 'string',  ['length'   => 14]);
    } 

    /**
     * Adiciona as coluans de Contato
     */
    private static function adicionaColunasTabelaContato($oTabela) {
        $oTabela->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $oTabela->addColumn('tipo', 'boolean');
        $oTabela->addColumn('descricao', 'string' , ['length'   => 255]);
        $oTabela->addColumn('idPessoa' , 'integer');
    }

}