<?php

namespace SentryPHPManager
{

    use CustomCode\Errors\Error;
    use CustomCode\Errors\ErrorHandler;
    use Exception;
    use function Sentry\init;

    /**
     * Class Sentry.
     *
     * Fornece uma interface para configurar e
     * manipular uma instância do Sentry pelo
     * sistema.
     *
     * @link SentryInterface Documentação da classe Sentry.
     * @author Guilherme Lourenço <suporte3.dev@epraja.com.br>
     */
    final class Sentry implements SentryInterface
    {
        use ErrorHandler;

        // =============== Class variables =============== //

        /**
         * @var Sentry|null $instance Instance of Sentry.
         */
        public static ?Sentry $instance = null;

        /**
         * @var Sentry|null $old_instance Instance of Sentry before rebuilding.
         */
        public static ?Sentry $old_instance = null;

        /**
         * @var string|null $dsn Client DSN.
         */
        protected static ?string $dsn = null;

        /**
         * @var bool $debug Indicates either the application
         *                  is in production or development mode.
         */
        protected static bool $debug = false;

        /**
         * @var bool $safe Whether the Sentry class has been
         *                 called securely or not.
         */
        private static bool $safe = false;

        // =============== Protected init =============== //

        /**
         * Chama o \Sentry\init() com as opções atuais.
         * @return Sentry
         * @throws Exception
         * @link \tests\SentryManager\SentryTest::testRun()
         */
        protected function initialize():
        Sentry
        {
            try
            {
                init($this->getOptions());
            }
            catch (Exception $e)
            {
                $this->getErrorHandler()
                    ->setError(424, "Sentry initialization error: may be wrong dsn provided");
            }
            return $this;
        }

        // =============== Sentry constructors =============== //

        /**
         * Cria ou carrega uma instância do Sentry.
         *
         * @return Sentry
         * @throws Exception
         */
        public static function get():
        Sentry
        {
            if (empty(self::$instance))
            {
                self::$safe = true;
                self::$instance = new Sentry();
            }
            return self::$instance;
        }

        /**
         * @throws Exception
         */
        public function __construct()
        {
            if (!self::$safe)
            {
                throw new Exception("'Sentry::get()' must be called instead of 'new Sentry()'");
            }
            self::$safe = false; // Reset the flag.
        }

        // =============== Métodos de Inicialização =============== //

        /**
         * Inicializa o Sentry e retorna sua instância.
         *
         * @throws Exception
         */
        public static function run($dsn = null):
        Sentry
        {
            $sentry = self::get();
            // Se não houver DSN, usa o padrão.
            // @link SentryInterface :_DSN_KEY_:
            if (empty($dsn) && empty($sentry->getDSN()))
            {
                $sentry->setDSN(self::_DSN_KEY_);
            }
            else
            {   // Configura o DSN passado por parâmetro ou o setado anteriormente.
                $sentry->setDSN($dsn ?? $sentry->getDSN());
            }
            return $sentry->initialize();
        }

        /**
         * Inicializa o Sentry em modo debug.
         *
         * Isso fará com que todos os erros capturados
         * sejam enviados para o Sentry no ambiente de
         * testes ao invés de produção.
         *
         * @throws Exception
         */
        public static function debug(string $dsn = null):
        Sentry
        {
            return self::get()->setDebug()->init($dsn);
        }

        /**
         * Inicializa o Sentry com o DSN de forma
         * não-estática.
         *
         * ex:
         *      \Sentry::get()->init($dsn);
         *      $sentry->rebuild()->init($dsn);
         *
         * @param null $dsn
         * @return Sentry
         * @throws Exception
         */
        public function init($dsn = null):
        Sentry
        {
            return self::run($dsn);
        }

        /**
         * Retorna as opções atuais do Sentry.
         *
         * @return array
         * @link SentryInterface :_ENV_DEVELOPMENT_: && :_ENV_PRODUCTION_:
         */
        public function getOptions():
        array
        {
            return [
                'dsn' => $this->getDSN(),
                'environment' => $this->isDebugging() ? self::_ENV_DEVELOPMENT_ : self::_ENV_PRODUCTION_,
            ];
        }

        // =============== Getters & Setters =============== //

        /**
         * Retorna o DSN atualmente configurado.
         *
         * @return string|null
         */
        public function getDSN():
        ?string
        {
            return self::$dsn;
        }

        /**
         * Configura o DSN do Sentry.
         *
         * @param array|string|null $dsn
         * @return Sentry
         * @throws Exception
         */
        public function setDSN($dsn = null):
        Sentry
        {
            // Realiza validação de tipagem do parâmetro.
            if (is_array($dsn) && isset($dsn['dsn']))
            {
                $dsn = $dsn['dsn'];
            }
            else if (!is_string($dsn))
            {
                $actual_type = gettype($dsn);
                $this->setErrorHandler(Error::create())
                    ->setError(418, "Invalid DSN type. Expected array or string, got $actual_type instead.");
                return $this;
            }
            self::$dsn = $dsn;
            return $this;
        }

        // =============== Métodos de Reconfiguração =============== //

        /**
         * Retorna as variáveis ao estado inicial.
         *
         * @return Sentry
         */
        public function rebuild():
        Sentry
        {
            self::$dsn = null;
            $this->resetErrorHandler();
            return $this;
        }

        /**
         * Reinicializa o Sentry de forma estática.
         *
         * @return Sentry
         * @throws Exception
         */
        public static function recreate():
        Sentry
        {
            return self::get()->rebuild();
        }

        /**
         * Salva instância atual do Sentry.
         *
         * @return Sentry
         */
        public static function stash():
        ?Sentry
        {
            return self::$old_instance = self::$instance;
        }

        /**
         * Restaura o Sentry para última instância salva.
         *
         * @return Sentry
         */
        public static function restore():
        ?Sentry
        {
            if (!empty(self::$old_instance))
            {
                self::$instance = self::$old_instance;
                self::$old_instance = null;
            }
            return self::$instance;
        }

        // =============== DEBUGGING METHODS =============== //

        /**
         * Configura o estado de debug.
         * @param bool $debug
         * @return Sentry
         */
        protected function setDebug(bool $debug = true):
        Sentry
        {
            self::$debug = $debug;
            return $this;
        }

        /**
         * Retorna o estado de debugging atual.
         * @return bool
         */
        public function isDebugging():
        bool
        {
            return self::$debug;
        }
    }
}
