<?php

namespace App\View;

use App\View\ViewPadrao;

class ViewContato extends ViewPadrao {

    /**
     * Realiza a criação dos registros da consulta.
     * @return String
     */
    public static function getHtmlLinhasConsulta($aContatos) {
        $sHmtl = '';
        foreach($aContatos as $oContato) {
            $sHmtl .= '<tr>
                          <td>'.$oContato->getId().'</td>
                          <td>'.$oContato->getTipoFormatString().'</td>
                          <td>'.$oContato->getDescricao().'</td>
                          <td>'.$oContato->getPessoa()->getNome().'</td>
                          <td>
                              <a href="index.php?pg=editarContato&value='.$oContato->getId().'" class="btn btn-primary">Editar</a>
                              <a href="index.php?pg=excluirContato&act=delete&value='.$oContato->getId().'" class="btn btn-primary">Excluir</a>
                              <a href="index.php?pg=visualizarContato&value='.$oContato->getId().'" class="btn btn-primary">Visualizar</a>
                         </td>
                      </tr>';
        }
        return $sHmtl;
    }

}
