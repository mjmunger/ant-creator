<?php

namespace AntAppCreator;

use PHPUnit\Framework\TestCase;

/**
 * {CLASS SUMMARY}
 *
 * Date: 2/21/18
 * Time: 6:54 AM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

class AntAppCreatorTest extends TestCase
{
    public static function setUpBeforeClass() {
        $dependencies = [];
        $dependencies[] = 'classes/AppCreator.class.php';


        foreach($dependencies as $dep) {
            require_once($dep);
        }
    }
    /**
     * Test the cloning ability to create a base template from a remote repo.
     * Tests:
     * 1. Can I clone into a target directory?
     * 2. Can I read the resulting files?
     */

    public function testCloneTemplate() {
        $targetDir = '/tmp/_phpant_template/';
        $Creator = new AppCreator();
        $Creator->getTemplate($targetDir);

        $targetFiles = [];
        $targetFiles[] = $targetDir . '/app-template.php';
        $targetFiles[] = $targetDir . '/autoloader-template.php';

        foreach($targetFiles as $target) {
            $this->assertFileExists($target, "$target is missing, and should have been cloned from the repo.");
            $this->assertIsReadable($target);
        }
    }
}