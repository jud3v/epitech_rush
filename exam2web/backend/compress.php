<?php
declare(strict_types=1);
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/plain; charset=utf-8');
define('STORAGE', 'storage/');

class Compress
{

    private $arguments;
    private $name;
    private $tree;
    private $jsonArray;

    /**
     * MyTar constructor.
     * @param $argv
     * @param $archiveName
     */
    public function __construct($argv, $archiveName = "output.mytar")
    {
        $this->arguments = $argv;
        $this->name = $archiveName . '.mytar';
        $this->tree = [];
        $this->jsonArray = [];
    }

    /**
     *  Entry point of app.
     */
    public function make()
    {
        $this->tree = $this->getTree();
        $this->jsonArray = $this->treeToJson($this->tree, $this->jsonArray);
        $this->compress($this->addFilesToArchive($this->name, $this->jsonArray));
        $this->deleteStorage();
    }

    /**
     * Get the list of file into array
     *
     * @return array
     */
    private function getTree(): array
    {
        $this->destructUnwantedVariable($this->arguments);
        foreach ($this->arguments as $file) {
            if (is_dir($file)) {
                $this->tree = $this->scanFolder('/' . $file . '/');
            } elseif (file_exists($file)) {
                array_push($this->tree, './' . $file);
            } else {
                echo "Erreur : $file n'existe pas !\n";
                exit;
            }
        }
        return $this->tree;
    }

    /**
     * Créé une archive qui pourras être donné en params.
     * Sinon créé un archive nommé par défault: "output.mytar".
     * @param $name
     * @return bool
     */
    private function createArchive($name): bool
    {
        $fp = fopen($name, 'a');
        fclose($fp);
        echo "Création de l'archive : " . $name . "\n";
        if (file_exists($name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Parcours le tableau donné en params et récupère chaque fichier,
     * pour les envoyés dans la tarball.
     * @param $archiveName
     * @param $jsonArray
     * @return mixed
     */
    private function addFilesToArchive($archiveName, $jsonArray)
    {
        $this->createArchive($archiveName);
        $nbrFile = count($jsonArray);
        foreach ($jsonArray as $file) {
            echo "Ajout du fichier : " . $file['name'] . "\n";
            file_put_contents($archiveName, utf8_encode(json_encode($jsonArray)));
        }
        echo "Ajout de $nbrFile fichier dans l'archive: $this->name!\n";
        return $archiveName;
    }

    /**
     * Compresse le contenu de l'arbre de chemin, nom et contenu donné en params.
     * @param $archiveName
     * @return bool
     */
    private function compress($archiveName): bool
    {
        $tmp = file_get_contents($archiveName);
        $tmp = gzcompress($tmp);
        if (file_put_contents($archiveName, $tmp)) {
            echo "L'opération est terminer ! \n";
            return true;
        } else {
            echo "Une erreur s'est produite... !\n";
            return false;
        }
    }

    /**
     * Prend un le tree en params et ajout chaque fichier dans l'array.
     * @param $tree
     * @param $jsonArray
     * @return mixed
     */
    private function treeToJson($tree, $jsonArray)
    {
        foreach ($tree as $file) {
            $tmp = explode('/', $file);
            $filename = end($tmp);
            $fileContent = file_get_contents($file);
            $path = array_slice($tmp, 0, -1);
            $path = implode("/", $path);
            $jsonArray[] = array('name' => $filename, 'path' => $path, 'content' => $fileContent);
        }
        return $jsonArray;
    }

    /**
     * Retourne l'arbre du dossier donné en params.
     * @param $directory
     * @return array
     */
    private function scanFolder($directory): array
    {
        $path = '.' . $directory;
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    if (!is_dir($path . $file)) {
                        array_push($this->tree, "." . $directory . $file);
                    } else {
                        $this->scanFolder($directory . $file . '/');
                    }
                }
            }
        }
        return $this->tree;
    }

    /**
     * Détruit le point d'entrée php
     * @param array $arr
     * @return bool
     */
    private function destructUnwantedVariable(array &$arr): bool
    {
        return true;
    }

    private function deleteStorage()
    {
        $files = glob(STORAGE . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

}

if (isset($_FILES)) {
    foreach ($_FILES as $file) { // upload files
        $uploadFile = STORAGE . $file['name'];
        move_uploaded_file($file['tmp_name'], $uploadFile);
    }
    $dir = scandir(STORAGE);
    unset($dir[0]);
    unset($dir[1]);
    foreach ($dir as &$files) { // add ./storage/ to the files
        $files = STORAGE . $files;
    }
    $tar = new Compress($dir, $_GET['name']);
    $tar->make();
} else {
    return false;
}