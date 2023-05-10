<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Contato")
 */
class Contato {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $tipo = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $descricao = null;

    /**
     * @ORM\ManyToOne(targetEntity="Pessoa", cascade={"persist"})
     * @ORM\JoinColumn(name="idPessoa", referencedColumnName="id")
     */
    private ?Pessoa $Pessoa = null;

    /**
     * Retoran o id
     * @return Integer
     */
    public function getId(): ?int {
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
     * Retorna o tipo
     * (0 - Telefone, 1 - Email)
     * @return boolean
     */
    public function getTipo(): ?bool {
        return $this->tipo;
    }

    /**
     * Define o tipo do contato
     * (0 - Telefone, 1 - Email)
     * @param Boolean $tipo
     * @return self
     */
    public function setTipo(bool $tipo): self {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Retorna a descrição
     * @return String
     */
    public function getDescricao(): ?string {
        return $this->descricao;
    }

    /**
     * Define a descrição
     * @param String $descricao
     * @return self
     */
    public function setDescricao(string $descricao): self {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Retorna a pessoa
     * @return Pessoa
     */
    public function getPessoa(): ?Pessoa {
        return $this->Pessoa;
    }

    /**
     * Define a Pessoa
     * @param Pessoa $oPessoa
     * @return self
     */
    public function setPessoa(?Pessoa $oPessoa): self {
        $this->Pessoa = $oPessoa;
        return $this;
    }

    /**
     * Retorna o tipo em formato string.
     * @return String
     */
    public function getTipoFormatString() {
        return $this->getTipo() == 1 ? 'E-mail' : 'Telefone';
    }

}