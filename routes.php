<?php

/**
 * Rederiza o conteúdo da página solicitada
 * @param string $sPage
 * @return string
 */
function render($sPage) {
    switch ($sPage) {
        case 'home':
            return (new App\Controller\ControllerHome)->render();
        case 'consultaPessoa' :
            return (new App\Controller\ControllerConsultaPessoa)->render();
        case 'consultaContato' :
            return (new App\Controller\ControllerConsultaContato)->render();
        case 'incluirPessoa':
        case 'editarPessoa' :
        case 'visualizarPessoa':
        case 'excluirPessoa':
            return (new App\Controller\ControllerManutencaoPessoa)->render();
        case 'incluirContato':
        case 'editarContato':
        case 'visualizarContato':
        case 'excluirContato':
            return (new App\Controller\ControllerManutencaoContato)->render();
    }
    return 'Página não encontrada!';
}
