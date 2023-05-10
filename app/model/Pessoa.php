<?php 

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pessoa")
 */
class Pessoa {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $cpf;

    /**
     * Retorna o ID
     * @return int
     */
    public function getId() :?int {
        return $this->id;
    }

    /**
     * Define o Id
     * @return self
     */
    public function setId(int $iId) :self{
        $this->id = $iId;
        return $this;
    } 

    /**
     * Retorna o CPF
     * @return string
     */
    public function getCpf() :?string {
        return $this->cpf;
    }

    /**
     * Define o CPF
     * @return self
     */
    public function setCpf(string $cpf) :self {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * Retorna o nome
     * @return string
     */
    public function getNome() :?string {
        return $this->nome;
    }

    /**
     * Define o nome
     * @return self
     */
    public function setNome($nome) :self{
        $this->nome = $nome;
        return $this;
    }
}