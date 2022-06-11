<?php

namespace SentryPHPManager
{
    use Exception;
    use SilverStripe\CMS\Controllers\ContentController;

    /**
     * Class WatchedController
     *
     * Garante que a inicialização do Sentry
     * ocorra o mais cedo possível.
     */
    class WatchedController extends ContentController
    {
        /**
         * Inicializa o Sentry ao carregar a página.
         * @throws Exception
         */
        protected function init()
        {
            // Starts Sentry
//            Sentry::run();
            Sentry::debug(); // @todo: refatorar antes da release!
            parent::init();
        }
    }
}
