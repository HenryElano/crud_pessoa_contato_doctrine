<?php

namespace App\Controller;

use App\Controller\ControllerPadrao;
use App\View\ViewHome;

class ControllerHome extends ControllerPadrao {

    /**
     * Executa o processamento da tela.
     */
    protected function processPage() {
        $sTitle = 'Home Page';

        $sContent = ViewHome::render([
            'homeContent' => '<h1>Olá, esse é o crud de Pessoas e Contatos!</h1>'
        ]);

        $this->footerVars = [
            'footerContent' => null
        ];

        return parent::getPage(
            $sTitle,
            $sContent
        );
    }
}
