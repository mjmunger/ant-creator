<?php

namespace AntAppCreator;

/**
 * {CLASS SUMMARY}
 *
 * Date: 2/21/18
 * Time: 7:05 AM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

class AppCreator
{
    /**
     * @var string fileBuffer Holds the class data that will be written to file.
     */
    public $fileBuffer = '';

    private $appTemplateFileName        = 'ant-app-template.php';
    private $autoLoaderTemplateFileName = 'autoloader-template.php';

    private $repoDir             = '/tmp/_phpant_template/';
    private $repo                = "https://github.com/mjmunger/ant-app-template.git";
    private $appTemplate         = null;
    private $autoLoaderTemplate  = null;

    public function __construct()
    {
        $this->appTemplate        = $this->repoDir . $this->appTemplateFileName;
        $this->autoLoaderTemplate = $this->repoDir . $this->autoLoaderTemplateFileName;
    }

    public function verifyPrerequisites() {
        //Verify we have a phpant key.
        $privateKeyFile = $home = getenv("HOME") . '/.phpant/ant_id';
        if( file_exists($privateKeyFile) == false ) {
            echo PHP_EOL;
            echo "No private PHPAnt signing key found at: $privateKeyFile. Please run publish -k to generate one." . PHP_EOL;
            echo PHP_EOL;
            die('Cannot continue.');
        }

        return true;
    }

    /**
     * @param $dir Recursively removes a directory.
     * @author Artefactor https://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
     */

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        $this->rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Clones the template repository to a local temp directory.
     */
    public function getTemplate() {

        if(file_exists($this->repoDir) && is_dir($this->repoDir)) $this->rrmdir($this->repoDir);

        $cmd    = "git clone $this->repo $this->repoDir 2>&1 ";
        $result = shell_exec($cmd);
    }

    /**
     * Performs substitutions of template fields from the app template, preparing the app to be written to file.
     */

    public function findAndReplace($replacements) {
        $this->fileBuffer = file_get_contents($this->appTemplate);

        foreach($replacements as $find => $replace) {
            $find = sprintf('${%s}', $find);
            $this->fileBuffer = str_replace($find, $replace,$this->fileBuffer);
        }
    }

    /**
     * @param $targetRepo
     * @return string The name of the directory in which to target repo was cloned.
     */

    public function checkoutTargetRepo($targetRepo) {
        $cmd    = "git clone $targetRepo 2>&1 ";
        $result = shell_exec($cmd);

        $buffer = explode($targetRepo,'/');
        $gitName = end($buffer);
        $buffer = explode($gitName , '.');
        $targetDir = __DIR__ . '/' . $buffer[0];
        return $targetDir;
    }

    public function writeAppFile($targetDirectory) {
        $targetFilePath = $targetDirectory . '/app.php';
        $fh = fopen($targetFilePath,'w');
        fwrite($fh,$this->fileBuffer);
        fclose($fh);

        return file_exists($targetFilePath);
    }
    public function publishApp($targetDir) {
        chdir($targetDir);
        $cmd = 'publish .';
        $result = shell_exec($cmd);
    }
    
    public function addAppFiles($targetDir) {
        chdir($targetDir);
        $cmd = 'git add *';
        $result = shell_exec($cmd);
    }

    public function commitApp($targetDir) {
        chdir($targetDir);
        $cmd = 'git commit -a -m\'auto published initial commit\'';
        $result = shell_exec($cmd);
    }

    public function pushApp($targetDir) {
        chdir($targetDir);
        $cmd = 'git push';
        $result = shell_exec($cmd);
    }

    public function createApp($data, $targetRepo) {
        $targetDir = '/tmp/_phpant_template/';
        $this->verifyPrerequisites();
        $this->getTemplate($targetDir);
        $this->findAndReplace($data);

        $targetDir = $this->checkoutTargetRepo();

        $result = $this->writeAppFile($targetDir);

        if($result == false) throw new Exception("app.php was not created in $targetDir!");

        $this->publishApp($targetDir);
        $this->addAppFiles($targetDir);
        $this->commitApp($targetDir);
        $this->pushApp($targetDir);
    }

    /**
     * Asks questions via the command prompt to collate data for app creation.
     */

    public function interview() {

        $data = [];

        $questions = [];
        $questions['TARGETREPO']       = 'What is the git repo URI for this app?';
        $questions['PROJECT']          = 'What is the namespace for the project?';
        $questions['SUBSPACE']         = 'What is the subspace for the project?';
        $questions['CATEGORY']         = 'What is the category for this app?';
        $questions['SYSTEMNAME']       = 'What is the system name for this app? (No spaces, punctuation, etc...)';
        $questions['FRIENDLYNAME']     = 'What is the friendly (human readable) name for this App?';
        $questions['APPDESCRIPTION']   = 'What does this app do?';
        $questions['AUTHORNAME']       = 'What\'s your name?';
        $questions['AUTHOREMAIL']      = 'What\'s your email?';

        foreach($questions as $key => $prompt) {
            printf("$prompt\n");
            $input = fgets(STDIN);
            $data[$key] = $input;
        }

        return $data;
    }

}