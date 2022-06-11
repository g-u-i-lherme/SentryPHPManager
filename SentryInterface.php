<?php

namespace SentryPHPManager
{

    use Exception;

    interface SentryInterface
    {
        const _DSN_KEY_ = 'https://a20d2a8e159c4c4aa5cf06b2be934d1d@o1282293.ingest.sentry.io/6489993';

        const _ENV_PRODUCTION_  = 'production';
        const _ENV_DEVELOPMENT_ = 'debug';

        /**
         * Inicializa o Sentry e retorna sua instância.
         *
         * @throws Exception
         */
        public static function run(): Sentry;

        /**
         * Inicializa o Sentry em modo debug.
         *
         * Isso fará com que todos os erros capturados
         * sejam enviados para o Sentry no ambiente de
         * testes ao invés de produção.
         *
         * @throws Exception
         */
        public static function debug(): Sentry;

        /**
         * Reinicializa o Sentry de forma estática.
         *
         * @return Sentry
         * @throws Exception
         */
        public static function recreate(): Sentry;

        /**
         * Inicializa o Sentry com o DSN de forma
         * não-estática.
         *
         * @param null $dsn
         * @return Sentry
         * @throws Exception
         */
        public function init($dsn): Sentry;

        /**
         * Configura o DSN do Sentry.
         *
         * @param array|string|null $dsn
         * @return Sentry
         * @throws Exception
         */
        public function setDSN(string $dsn): Sentry;

        /**
         * Retorna o DSN atualmente configurado.
         *
         * @return string|null
         */
        public function getDSN(): ?string;

        /**
         * Retorna as variáveis ao estado inicial.
         *
         * @return Sentry
         */
        public function rebuild(): Sentry;

        /**
         * Retorna o estado de debugging atual.
         * @return bool
         */
        public function isDebugging(): bool;

        /**
         * Retorna as opções atuais do Sentry.
         * @return array
         */
        public function getOptions(): array;
    }
}
