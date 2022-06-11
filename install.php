<?php

// @todo: #hardcoded_for SentryPHPManager
namespace SentryPHPManager
{

    install_autoload_namespace(); // starts installation of autoload updates for SentryPHPManager

    // install_autoload_namespace('SentryPHPManager');
    function install_autoload_namespace():
    void
    {
        // hardcoded namespace
        $namespace = 'SentryPHPManager';
        $directory = 'app/src/SentryPHPManager/';
        $file = $output = 'composer.json';

        // sanitized outputs
        $_COLORED_NAMESPACE_STR_ = _put_colors($namespace);
        $_COLORED_DIRECTORY_STR_ = _put_colors($directory);
        $_COLORED_OUTPUT_STR_ = _put_colors($output);

        $data = json_decode(file_get_contents($file), true); // gets the data from the composer.json file
        $data["autoload"]["psr-4"][$namespace . '\\'] = $directory; // add the namespace to the autoload section

        ksort($data["autoload"]["psr-4"]); // sort the array by key alphabetically

        // =============== write the new json data to the output file ================== //
        file_put_contents($output, json_encode($data, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));

        // =============== Sanitizes the CLI output ================== //
        $namespace = "\033[33m" . $namespace . "\033[0m";
        $output = "\033[33m" . $output . "\033[0m";
        $directory = "\033[33m" . $directory . "\033[0m";

        color_log("\n- Adicionando '$_COLORED_NAMESPACE_STR_' ao '$_COLORED_OUTPUT_STR_' como link para a pasta '$_COLORED_DIRECTORY_STR_'");
        color_log("> Namespace adicionado!\n", 's');

        // =============== rebuild autoloader ================== //
        color_log("- Executando o comando 'composer dump-autoload' para atualizar o autoloader\n");

        $output = shell_exec('composer dump-autoload');

        color_log($output, 'w');
        color_log("> composer e autoloader foram atualizados com sucesso!\n", 's');
    }

    // Colorize the CLI output
    function color_log(string $str, string $type = 'i'):
    void
    {
        echo _put_colors($str, $type) . "\n";
    }

    function _put_colors(string $str, string $type = 'i'):
    ?string
    {
        switch ($type) {
            case 'e': //error
                return "\033[31m$str\033[0m";
            case 's': //success
                return "\033[32m$str\033[0m";
            case 'w': //warning
                return "\033[33m$str\033[0m";
            case 'i': //info
                return "\033[36m$str\033[0m";
            default:
                return null;
        }
    }
}
