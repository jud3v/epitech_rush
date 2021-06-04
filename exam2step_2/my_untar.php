<?php

class MyUnTar
{

    private $arguments;

    public function __construct($argv)
    {
        $this->arguments = $argv;
    }

    /**
     *  Entry point of uncompression.
     */
    public function make()
    {
        $this->destructUnwantedVariable($this->arguments);
        foreach ($this->arguments as $archive) {
            if (!file_exists($archive)) {
                echo "Erreur : L'archive est inexistante !\n";
                exit;
            }
        }
        if ($this->unarchive($this->arguments)) {
            echo "Décompression terminé !\n";
        } else {
            echo "Une erreur est survenue lors de la décompression !\n";
        }
    }

    /**
     * Décompresse le fichier donné en param.
     * @param $archiveNames
     * @return bool
     */
    private function unarchive($archiveNames): bool
    {
        $error = false;

        foreach ($archiveNames as $archive) {
            $data = file_get_contents($archive);
            $data = gzuncompress($data);
            $jsonArray = json_decode($data, true);
            foreach ($jsonArray as $file) {
                if (!is_dir($file['path'])) {
                    mkdir($file['path'], 0755, true);
                }
                if (!file_exists($file['path'] . '/' . $file['name'])) {
                    if (file_put_contents($file['path'] . '/' . $file['name'], utf8_decode($file['content'])) === false) {
                        echo 'Erreur lors de l\'écriture du fichier : ' . $file['name'] . "\n";
                    } else {
                        echo 'Le fichier a été décompressé : ' . $file['name'] . "\n";
                    }
                } else {
                    echo 'Oups! Le fichier est déjà présent : ' . $file['name'] . "\n";
                    $error = true;
                }
            }
        }
        if ($error) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Détruit le point d'entrée php
     * @param array $arr
     * @return bool
     */
    private function destructUnwantedVariable(array &$arr): bool
    {
        unset($arr[0]); // delete script entry
        if (!isset($arr[0])) {
            return true;
        } else {
            return false;
        }
    }
}

// Faire la gestion d'erreur ici.
if (empty($argv[1])) {
    echo "Erreur: Aucun fichier n'a été spécifié !";
    exit();
}
$tar = new MyUnTar($argv);
$tar->make();