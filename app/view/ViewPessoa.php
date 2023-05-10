<?php

namespace App\View;

use App\View\ViewPadrao;

class ViewPessoa extends ViewPadrao {

    /**
     * Monta o HTML da consulta de Pessoa
     * @return String
     */
    public static function getHtmlLinhasConsulta($aPessoas) {
        $sHmtl = '';
        foreach($aPessoas as $oPessoa) {
            $sHmtl .= '<tr>
                          <td>'.$oPessoa->getId().'</td>
                          <td>'.$oPessoa->getNome().'</td>
                          <td>'.$oPessoa->getCpf().'</td>
                          <td>
                              <a href="index.php?pg=editarPessoa&value='.$oPessoa->getId().'" class="btn btn-primary">Editar</a>
                              <a href="index.php?pg=excluirPessoa&act=delete&value='.$oPessoa->getId().'" class="btn btn-primary">Excluir</a>
                              <a href="index.php?pg=visualizarPessoa&value='.$oPessoa->getId().'" class="btn btn-primary">Visualizar</a>
                         </td>
                      </tr>';
        }
        return $sHmtl;
    }
}
