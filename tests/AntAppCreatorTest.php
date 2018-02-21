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
     * @covers getTemplate
     */

    public function testCloneTemplate() {
        $targetDir = '/tmp/_phpant_template/';
        $Creator = new AppCreator(".");
        $Creator->getTemplate($targetDir);

        $targetFiles = [];
        $targetFiles[] = $targetDir . 'ant-app-template.php';
        $targetFiles[] = $targetDir . 'autoloader-template.php';

        foreach($targetFiles as $target) {
            $this->assertFileExists($target, "$target is missing, and should have been cloned from the repo.");
            $this->assertIsReadable($target);
        }
    }

    /**
     * @dataProvider providerAppData
     * @covers findAndReplace
     * @depends testCloneTemplate
     */

    public function testSubstitutions($data , $expectedCounts) {
        $targetDir = '/tmp/_phpant_template/';
        $Creator = new AppCreator(".");
        $Creator->getTemplate($targetDir);
        $Creator->findAndReplace($data);

        foreach($data as $field => $value) {
            $this->assertContains($value,$Creator->fileBuffer);
        }

        foreach($expectedCounts as $field => $count) {
            $this->assertSame( $count
                             , substr_count($Creator->fileBuffer,$data[$field])
                             , sprintf("Find and replace fail. The string %s should have occured %s times in the resulting fileBuffer %s"
                                      , $data[$field]
                                      , $count
                                      , $Creator->fileBuffer
                                      )
                             );
        }
    }

    /**
     * @covers verifyPrerequisites
     */

    public function testVerifyPrerequisites() {
        $Creator = new AppCreator(".");
        $this->assertTrue($Creator->verifyPrerequisites());
    }

    public function providerAppData() {

        $data = [];
        $data['PROJECT']        = 'AAvYisqiEBqlGvUPZBv';
        $data['SUBSPACE']       = 'MON';
        $data['FRIENDLYNAME']   = 'DKPGtjIcOwbs';
        $data['APPDESCRIPTION'] = 'JbYwLaHuYCFvOgS';
        $data['SYSTEMNAME']     = 'Xsi';
        $data['CATEGORY']       = 'CbbkTweJiZr';
        $data['AUTHORNAME']     = 'ZLnKkfWeY';
        $data['AUTHOREMAIL']    = 'fIzIntm';

        $expectedCounts = [];
        $expectedCounts['PROJECT']        = 2;
        $expectedCounts['SUBSPACE']       = 2;
        $expectedCounts['FRIENDLYNAME']   = 4;
        $expectedCounts['APPDESCRIPTION'] = 1;
        $expectedCounts['SYSTEMNAME']     = 10;
        $expectedCounts['CATEGORY']       = 1;
        $expectedCounts['AUTHORNAME']     = 4;
        $expectedCounts['AUTHOREMAIL']    = 3;

        return  [ [ $data , $expectedCounts] ];
    }

    /**
     * @dataProvider providerRepoURIs
     * @covers parseTargetDirName
     */

    public function testParseDirname($repoURI, $expectedDirName) {
        $Creator = new AppCreator(".");
        $targetDirName = $Creator->parseTargetDirName($repoURI);
        $this->assertSame($expectedDirName, $targetDirName);
    }

    public function providerRepoURIs() {
        return  [ [ 'git@git.highpoweredhelp.com:michael/bugzy-work-timer.git', 'bugzy-work-timer' ] ];
    }
}